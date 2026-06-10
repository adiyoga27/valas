<?php

namespace App\Console\Commands;

use App\Models\SancoDataset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnrichSancoEntities extends Command
{
    protected $signature = 'sanco:enrich
                            {dataset? : Dataset name to enrich (e.g. peps)}
                            {--compact : Enrich PEP + sanctions datasets}';

    protected $description = 'Perkaya data entitas dengan alias/weakAlias dari entities.ftm.json';

    public function handle(): int
    {
        if ($this->argument('dataset')) {
            $names = [$this->argument('dataset')];
        } elseif ($this->option('compact')) {
            $names = ['peps', 'sanctions', 'us_ofac_sdn', 'un_sc_sanctions', 'eu_fsf', 'gb_fcdo_sanctions'];
        } else {
            $this->error('Gunakan: php artisan sanco:enrich peps  atau  --compact');
            return self::FAILURE;
        }

        foreach ($names as $name) {
            $this->enrichDataset($name);
        }

        return self::SUCCESS;
    }

    protected function enrichDataset(string $datasetName): void
    {
        $dataset = SancoDataset::where('name', $datasetName)->first();
        if (!$dataset) {
            $this->error("Dataset '{$datasetName}' tidak ditemukan.");
            return;
        }

        $resources = $dataset->resources ?? [];
        $ftm = collect($resources)->firstWhere('name', 'entities.ftm.json');
        if (!$ftm || !isset($ftm['url'])) {
            $this->error("Dataset '{$datasetName}' tidak memiliki entities.ftm.json.");
            return;
        }

        $this->info("Enriching: {$datasetName}...");

        // Count existing entities (don't load all IDs into memory)
        $count = DB::table('sanco_entities')
            ->where('dataset_name', $datasetName)
            ->count();

        if ($count === 0) {
            $this->warn("  Tidak ada entitas untuk dataset ini. Import dulu dengan sanco:import-entities.");
            return;
        }

        $this->info("  {$count} entities exist. Building lookup index...");

        DB::statement('CREATE TEMPORARY TABLE sanco_enrich_lookup (entity_id VARCHAR(255) PRIMARY KEY) ENGINE=InnoDB');
        DB::statement("INSERT INTO sanco_enrich_lookup (entity_id) SELECT entity_id FROM sanco_entities WHERE dataset_name = ?", [$datasetName]);

        $this->info("  Lookup index ready.");

        $this->info("  Downloading FTM file ({$ftm['url']})...");
        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sanco_ftm_' . uniqid() . '.json';
        $fh = fopen($tmpFile, 'wb');
        if (!$fh) {
            $this->error("  Gagal membuat file temp: {$tmpFile}");
            return;
        }

        $ch = curl_init($ftm['url']);
        curl_setopt_array($ch, [
            CURLOPT_TIMEOUT => 3600,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_LOW_SPEED_LIMIT => 1024,
            CURLOPT_LOW_SPEED_TIME => 30,
            CURLOPT_FILE => $fh,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'ValasEnricher/1.0',
            CURLOPT_FAILONERROR => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_BUFFERSIZE => 65536,
        ]);

        $maxRetries = 3;
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $this->info("  Download attempt {$attempt}/{$maxRetries}...");
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $downloaded = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);

            if ($httpCode === 200 && empty($curlError)) {
                break;
            }

            if ($attempt < $maxRetries) {
                $this->warn("  Retrying in 5s... ({$curlError})");
                sleep(5);
            }
        }
        curl_close($ch);
        fclose($fh);

        if ($httpCode !== 200 || $curlError) {
            unlink($tmpFile);
            $this->error("  Download gagal (HTTP {$httpCode}): {$curlError}");
            return;
        }

        $fileSize = filesize($tmpFile);
        $this->info("  Downloaded: " . number_format($fileSize) . " bytes. Processing...");

        $handle = fopen($tmpFile, 'r');
        $processed = 0;
        $enriched = 0;

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if ($line === '' || $line[0] !== '{') continue;

            $obj = json_decode($line, true);
            if (!$obj) continue;

            $schema = $obj['schema'] ?? '';
            $entityId = $obj['id'] ?? '';

            if (!in_array($schema, ['Person', 'Organization'])) continue;

            // Check if entity exists in our DB (using temp table)
            $exists = DB::table('sanco_enrich_lookup')
                ->where('entity_id', $entityId)
                ->exists();

            if (!$exists) continue;

            $props = $obj['properties'] ?? [];

            $aliases = isset($props['alias']) ? implode('; ', $props['alias']) : null;
            $weakAliases = isset($props['weakAlias']) ? implode('; ', $props['weakAlias']) : null;
            $birthPlace = isset($props['birthPlace']) ? implode('; ', $props['birthPlace']) : null;
            $gender = isset($props['gender']) ? implode('; ', $props['gender']) : null;
            $nationality = isset($props['nationality']) ? implode('; ', $props['nationality']) : null;
            $position = isset($props['position']) ? implode('; ', $props['position']) : null;
            $notes = isset($props['notes']) ? implode('; ', $props['notes']) : null;

            // Store all non-empty properties as JSON for future use
            $propJson = [];
            $extraFields = ['birthPlace','gender','nationality','position','notes','website','wikidataId','wikipediaUrl','sourceUrl','education','religion','political','citizenship','firstName','lastName','fatherName','motherName','topics','program'];
            foreach ($extraFields as $f) {
                if (!empty($props[$f])) $propJson[$f] = $props[$f];
            }

            $update = ['updated_at' => now()->toDateTimeString()];
            if ($aliases) $update['aliases'] = $aliases;
            if ($weakAliases) $update['weak_aliases'] = $weakAliases;
            if ($birthPlace) $update['birth_place'] = $birthPlace;
            if ($gender) $update['gender'] = $gender;
            if ($nationality) $update['nationality'] = $nationality;
            if ($position) $update['position'] = $position;
            if ($notes) $update['notes'] = $notes;
            if (!empty($propJson)) $update['properties'] = json_encode($propJson);

            if (count($update) > 1) { // More than just updated_at
                DB::table('sanco_entities')
                    ->where('entity_id', $entityId)
                    ->update($update);
                $enriched++;
            }

            $processed++;

            if ($processed % 10000 === 0) {
                $this->info("  Processed: " . number_format($processed) . ", enriched: " . number_format($enriched));
            }
        }

        fclose($handle);
        unlink($tmpFile);

        DB::statement('DROP TEMPORARY TABLE IF EXISTS sanco_enrich_lookup');

        $this->info("  Done! Processed {$processed} lines, enriched {$enriched} entities.");
    }

}

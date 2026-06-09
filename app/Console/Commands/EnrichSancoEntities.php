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

        // Build set of existing entity IDs
        $this->info("  Loading entity IDs...");
        $entityIds = DB::table('sanco_entities')
            ->where('dataset_name', $datasetName)
            ->pluck('entity_id')
            ->flip()
            ->toArray();

        if (empty($entityIds)) {
            $this->warn("  Tidak ada entitas untuk dataset ini. Import dulu dengan sanco:import-entities.");
            return;
        }

        $this->info("  " . count($entityIds) . " entity IDs loaded.");

        $this->info("  Downloading FTM file ({$ftm['url']})...");
        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sanco_ftm_' . uniqid() . '.json';
        $fh = fopen($tmpFile, 'wb');
        if (!$fh) {
            $this->error("  Gagal membuat file temp: {$tmpFile}");
            return;
        }

        $ch = curl_init($ftm['url']);
        curl_setopt_array($ch, [
            CURLOPT_TIMEOUT => 600,
            CURLOPT_FILE => $fh,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'ValasEnricher/1.0',
            CURLOPT_FAILONERROR => true,
        ]);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
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
            if (!isset($entityIds[$entityId])) continue;

            $props = $obj['properties'] ?? [];
            $aliases = isset($props['alias']) ? implode('; ', $props['alias']) : null;
            $weakAliases = isset($props['weakAlias']) ? implode('; ', $props['weakAlias']) : null;

            if ($aliases || $weakAliases) {
                DB::table('sanco_entities')
                    ->where('entity_id', $entityId)
                    ->update([
                        'aliases' => $aliases,
                        'weak_aliases' => $weakAliases,
                        'updated_at' => now()->toDateTimeString(),
                    ]);
                $enriched++;
            }

            $processed++;

            if ($processed % 10000 === 0) {
                $this->info("  Processed: " . number_format($processed) . ", enriched: " . number_format($enriched));
            }
        }

        fclose($handle);
        unlink($tmpFile);

        $this->info("  Done! Processed {$processed} lines, enriched {$enriched} entities.");
    }

}

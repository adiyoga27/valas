<?php

namespace App\Services;

use App\Models\SancoDataset;
use Illuminate\Support\Facades\DB;

class SancoImporter
{
    protected int $chunkSize = 2000;

    public function importDataset(string $datasetName): array
    {
        ini_set('memory_limit', '512M');
        set_time_limit(600);

        $dataset = SancoDataset::where('name', $datasetName)->first();

        if (!$dataset) {
            return ['error' => "Dataset '{$datasetName}' tidak ditemukan di database."];
        }

        $resources = $dataset->resources ?? [];
        $csvResource = collect($resources)->firstWhere('name', 'targets.simple.csv');

        if (!$csvResource || !isset($csvResource['url'])) {
            return ['error' => "Dataset '{$datasetName}' tidak memiliki file targets.simple.csv."];
        }

        $url = $csvResource['url'];
        $size = $csvResource['size'] ?? null;

        DB::disableQueryLog();
        DB::table('sanco_entities')->where('dataset_name', $datasetName)->delete();

        $context = stream_context_create([
            'http' => [
                'timeout' => 300,
                'user_agent' => 'ValasSancoImporter/1.0',
            ],
        ]);

        $handle = @fopen($url, 'r', false, $context);

        if (!$handle) {
            DB::enableQueryLog();
            return ['error' => "Gagal membuka URL: {$url}"];
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            DB::enableQueryLog();
            return ['error' => 'Gagal membaca header CSV.'];
        }

        $headers = array_map('trim', $headers);
        $map = array_flip($headers);

        $idIdx = $map['id'] ?? null;
        $schemaIdx = $map['schema'] ?? null;
        $nameIdx = $map['name'] ?? null;
        $aliasesIdx = $map['aliases'] ?? null;
        $countriesIdx = $map['countries'] ?? null;
        $birthDateIdx = $map['birth_date'] ?? null;
        $addressesIdx = $map['addresses'] ?? null;
        $identifiersIdx = $map['identifiers'] ?? null;
        $emailsIdx = $map['emails'] ?? null;
        $firstSeenIdx = $map['first_seen'] ?? null;
        $lastSeenIdx = $map['last_seen'] ?? null;
        $lastChangeIdx = $map['last_change'] ?? null;

        if ($nameIdx === null || $idIdx === null) {
            fclose($handle);
            DB::enableQueryLog();
            return ['error' => 'CSV tidak memiliki kolom id atau name.'];
        }

        $rows = [];
        $total = 0;
        $now = now()->toDateTimeString();

        while (($line = fgetcsv($handle)) !== false) {
            $rows[] = [
                'entity_id' => $line[$idIdx] ?? '',
                'dataset_name' => $datasetName,
                'schema' => $line[$schemaIdx] ?? null,
                'name' => $line[$nameIdx] ?? '',
                'aliases' => $line[$aliasesIdx] ?? null,
                'countries' => $line[$countriesIdx] ?? null,
                'birth_date' => $line[$birthDateIdx] ?? null,
                'addresses' => $line[$addressesIdx] ?? null,
                'identifiers' => $line[$identifiersIdx] ?? null,
                'emails' => $line[$emailsIdx] ?? null,
                'first_seen' => $line[$firstSeenIdx] ?? null,
                'last_seen' => $line[$lastSeenIdx] ?? null,
                'last_change' => $line[$lastChangeIdx] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $total++;

            if (count($rows) >= $this->chunkSize) {
                $this->flush($rows);
            }
        }

        if (!empty($rows)) {
            $this->flush($rows);
        }

        fclose($handle);
        DB::enableQueryLog();

        return [
            'success' => true,
            'dataset' => $datasetName,
            'total' => $total,
            'url' => $url,
        ];
    }

    protected function flush(array &$rows): void
    {
        DB::table('sanco_entities')->insert($rows);
        $rows = [];
        gc_collect_cycles();
    }

    public function importDatasets(array $datasetNames): array
    {
        $results = [];

        foreach ($datasetNames as $name) {
            $results[] = $this->importDataset($name);
        }

        return $results;
    }
}

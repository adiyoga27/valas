<?php

namespace App\Console\Commands;

use App\Models\SancoDataset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncSancoDatasets extends Command
{
    protected $signature = 'sanco:sync';
    protected $description = 'Sinkronisasi dataset OpenSanctions (PEP, DTTOT, dll)';

    public function handle(): int
    {
        $this->info('Mengambil data dari OpenSanctions...');

        $response = Http::timeout(120)
            ->retry(3, 2000)
            ->get('https://data.opensanctions.org/datasets/latest/index.json');

        if (!$response->successful()) {
            $this->error('Gagal mengambil data: ' . $response->status());
            return self::FAILURE;
        }

        $data = $response->json();

        if (!isset($data['datasets'])) {
            $this->error('Format data tidak valid.');
            return self::FAILURE;
        }

        $datasets = $data['datasets'];
        $count = 0;

        $this->withProgressBar($datasets, function ($dataset) use (&$count) {
            SancoDataset::updateOrCreate(
                ['name' => $dataset['name']],
                [
                    'title' => $dataset['title'] ?? null,
                    'summary' => $dataset['summary'] ?? null,
                    'description' => $dataset['description'] ?? null,
                    'url' => $dataset['url'] ?? null,
                    'updated_at_source' => $dataset['updated_at'] ?? null,
                    'last_export' => $dataset['last_export'] ?? null,
                    'entity_count' => $dataset['entity_count'] ?? 0,
                    'thing_count' => $dataset['thing_count'] ?? 0,
                    'version' => $dataset['version'] ?? null,
                    'tags' => $dataset['tags'] ?? [],
                    'publisher_name' => $dataset['publisher']['name'] ?? null,
                    'publisher_url' => $dataset['publisher']['url'] ?? null,
                    'publisher_acronym' => $dataset['publisher']['acronym'] ?? null,
                    'publisher_country' => $dataset['publisher']['country'] ?? null,
                    'publisher_country_label' => $dataset['publisher']['country_label'] ?? null,
                    'publisher_official' => $dataset['publisher']['official'] ?? false,
                    'publisher_description' => $dataset['publisher']['description'] ?? null,
                    'coverage_start' => $dataset['coverage']['start'] ?? null,
                    'coverage_frequency' => $dataset['coverage']['frequency'] ?? null,
                    'resources' => $dataset['resources'] ?? [],
                ]
            );
            $count++;
        });

        $this->newLine();
        $this->info("Berhasil sinkronisasi {$count} dataset.");

        return self::SUCCESS;
    }
}

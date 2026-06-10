<?php

namespace App\Console\Commands;

use App\Services\SancoImporter;
use Illuminate\Console\Command;

class ImportSancoEntities extends Command
{
    protected $signature = 'sanco:import-entities
                            {dataset? : Nama spesifik dataset (contoh: peps, sanctions)}
                            {--all : Import semua dataset dari tabel sanco_datasets}
                            {--tag=* : Filter dataset by tag (contoh: --tag=peps --tag=sanctions)}';

    protected $description = 'Import data entitas dari OpenSanctions ke database lokal';

    public function handle(): int
    {
        $importer = new \App\Services\SancoImporter();

        if ($this->argument('dataset')) {
            $names = [$this->argument('dataset')];
        } else {
            $query = \App\Models\SancoDataset::query();

            if ($this->option('all')) {
                $this->info('Mengimport semua dataset...');
            } elseif ($tags = $this->option('tag')) {
                foreach ($tags as $tag) {
                    $query->whereJsonContains('tags', $tag);
                }
                $this->info('Mengimport dataset dengan tag: ' . implode(', ', $tags) . '...');
            } else {
                $this->error('Pilih salah satu: dataset spesifik, --all, atau --tag');
                $this->line('Contoh: php artisan sanco:import-entities peps');
                $this->line('Contoh: php artisan sanco:import-entities --all');
                $this->line('Contoh: php artisan sanco:import-entities --tag=peps --tag=sanctions');
                return self::FAILURE;
            }

            $names = $query->pluck('name')->toArray();

            if (empty($names)) {
                $this->warn('Tidak ada dataset yang cocok di tabel sanco_datasets.');
                return self::FAILURE;
            }

            $this->info('Ditemukan ' . count($names) . ' dataset.');
        }

        foreach ($names as $name) {
            $this->info("Memproses: {$name}...");
            $result = $importer->importDataset($name);

            if (isset($result['error'])) {
                $this->error("  {$result['error']}");
            } else {
                $this->info("  Berhasil! {$result['total']} entitas diimport.");
            }
        }

        $this->info('Selesai.');
        return self::SUCCESS;
    }
}

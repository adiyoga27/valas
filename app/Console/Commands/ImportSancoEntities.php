<?php

namespace App\Console\Commands;

use App\Services\SancoImporter;
use Illuminate\Console\Command;

class ImportSancoEntities extends Command
{
    protected $signature = 'sanco:import-entities
                            {dataset? : Nama spesifik dataset (contoh: peps, sanctions)}
                            {--compact : Import datasets ringkas PEP & sanctions utama saja}
                            {--terror : Import datasets terkait terorisme saja}';

    protected $description = 'Import data entitas dari OpenSanctions ke database lokal';

    protected array $compactDatasets = [
        'peps',
        'sanctions',
        'us_ofac_sdn',
        'un_sc_sanctions',
        'eu_fsf',
        'gb_fcdo_sanctions',
    ];

    protected array $terrorDatasets = [
        'un_sc_sanctions',
        'us_ofac_sdn',
        'ca_listed_terrorists',
        'gb_fcdo_sanctions',
        'eu_fsf',
    ];

    public function handle(): int
    {
        $importer = new SancoImporter();

        if ($this->argument('dataset')) {
            $names = [$this->argument('dataset')];
        } elseif ($this->option('compact')) {
            $names = $this->compactDatasets;
            $this->info('Mengimport dataset PEP & sanctions utama...');
        } elseif ($this->option('terror')) {
            $names = $this->terrorDatasets;
            $this->info('Mengimport dataset terorisme...');
        } else {
            $this->error('Pilih salah satu: dataset spesifik, --compact, atau --terror');
            $this->line('Contoh: php artisan sanco:import-entities peps');
            $this->line('Contoh: php artisan sanco:import-entities --compact');
            $this->line('Contoh: php artisan sanco:import-entities --terror');
            return self::FAILURE;
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

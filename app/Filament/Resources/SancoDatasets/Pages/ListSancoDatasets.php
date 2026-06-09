<?php

namespace App\Filament\Resources\SancoDatasets\Pages;

use App\Filament\Resources\SancoDatasets\SancoDatasetResource;
use App\Filament\Pages\CekNamaSanco;
use App\Services\SancoImporter;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListSancoDatasets extends ListRecords
{
    protected static string $resource = SancoDatasetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cek_nama')
                ->label('Cek Nama')
                ->icon('heroicon-o-magnifying-glass')
                ->color('primary')
                ->url(fn() => CekNamaSanco::getUrl()),

            Action::make('import_entities')
                ->label('Import Entitas')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('success')
                ->action(function () {
                    $importer = new SancoImporter();
                    $datasets = ['sanctions', 'us_ofac_sdn', 'un_sc_sanctions', 'eu_fsf', 'gb_fcdo_sanctions'];

                    $this->notify('info', 'Memulai import data entitas...');

                    foreach ($datasets as $name) {
                        $result = $importer->importDataset($name);
                        if (isset($result['error'])) {
                            $this->notify('warning', "{$name}: {$result['error']}");
                        } else {
                            $this->notify('success', "{$name}: {$result['total']} entitas berhasil diimport!");
                        }
                    }

                    $this->notify('success', 'Import entitas selesai! Reload halaman.');
                    $this->resetTable();
                })
                ->requiresConfirmation()
                ->modalHeading('Import Data Entitas')
                ->modalDescription('Ini akan mendownload dan mengimport data sanctions (OFAC, UN, EU, UK). Untuk dataset PEP (1.9M entitas), gunakan CLI: php artisan sanco:import-entities peps')
                ->modalSubmitActionLabel('Ya, Import'),

            Action::make('sync')
                ->label('Sync Dataset')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function () {
                    Artisan::call('sanco:sync');
                    $this->notify('success', 'Sinkronisasi dataset berhasil!');
                    $this->resetTable();
                }),
        ];
    }
}

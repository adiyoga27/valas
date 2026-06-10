<?php

namespace App\Filament\Resources\SancoDatasets\Pages;

use App\Filament\Resources\SancoDatasets\SancoDatasetResource;
use App\Filament\Pages\CekNamaSanco;
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
                    $datasets = \App\Models\SancoDataset::whereJsonContains('tags', 'sanctions')
                        ->pluck('name')
                        ->toArray();

                    if (empty($datasets)) {
                        $this->notify('warning', 'Tidak ada dataset dengan tag sanctions.');
                        return;
                    }

                    $artisan = base_path('artisan');
                    $logFile = storage_path('logs/sanco-import.log');
                    $count = count($datasets);

                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        $cmd = sprintf(
                            'start /B php "%s" sanco:import-entities --tag=sanctions > "%s" 2>&1',
                            $artisan,
                            $logFile
                        );
                    } else {
                        $cmd = sprintf(
                            'nohup php "%s" sanco:import-entities --tag=sanctions >> "%s" 2>&1 &',
                            $artisan,
                            $logFile
                        );
                    }

                    pclose(popen($cmd, 'r'));

                    $this->notify('success', "Import {$count} dataset dimulai di background. Cek log: storage/logs/sanco-import.log");
                    $this->redirect(ListSancoDatasets::getUrl());
                })
                ->requiresConfirmation()
                ->modalHeading('Import Data Entitas')
                ->modalDescription('Import akan berjalan di background (hindari gateway timeout). Cek progress di storage/logs/sanco-import.log. Untuk dataset PEP (1.9M entitas), gunakan CLI: php artisan sanco:import-entities peps')
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

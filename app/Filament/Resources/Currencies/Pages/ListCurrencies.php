<?php

namespace App\Filament\Resources\Currencies\Pages;

use App\Filament\Resources\Currencies\CurrencyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;
    protected ?string $heading = 'Data Master Negara'; // Gunakan $heading untuk Filament v3/v4

    
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Data Master'),
        ];
    }
}

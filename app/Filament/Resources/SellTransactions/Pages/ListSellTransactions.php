<?php

namespace App\Filament\Resources\SellTransactions\Pages;

use App\Filament\Resources\SellTransactions\SellTransactionsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSellTransactions extends ListRecords
{
    protected static string $resource = SellTransactionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

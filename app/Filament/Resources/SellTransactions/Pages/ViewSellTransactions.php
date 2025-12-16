<?php

namespace App\Filament\Resources\SellTransactions\Pages;

use App\Filament\Resources\SellTransactions\SellTransactionsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSellTransactions extends ViewRecord
{
    protected static string $resource = SellTransactionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

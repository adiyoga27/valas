<?php

namespace App\Filament\Resources\BuyTransactions\Pages;

use App\Filament\Resources\BuyTransactions\BuyTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBuyTransaction extends EditRecord
{
    protected static string $resource = BuyTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

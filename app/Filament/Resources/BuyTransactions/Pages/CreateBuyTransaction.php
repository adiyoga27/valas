<?php

namespace App\Filament\Resources\BuyTransactions\Pages;

use App\Filament\Resources\BuyTransactions\BuyTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBuyTransaction extends CreateRecord
{
    protected static string $resource = BuyTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
       dd($data);
        return $data;
    }
}

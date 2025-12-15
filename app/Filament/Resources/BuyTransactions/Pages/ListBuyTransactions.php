<?php

namespace App\Filament\Resources\BuyTransactions\Pages;

use App\Filament\Resources\BuyTransactions\BuyTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBuyTransactions extends ListRecords
{
    protected static string $resource = BuyTransactionResource::class;

    protected function getEmptyStateHeading(): ?string
    {
        return 'Belum ada transaksi pembelian';
    }

    protected function getEmptyStateDescription(): ?string
    {
        return 'Silakan tambahkan transaksi pembelian pertama.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Transaksi'),
        ];
    }
}

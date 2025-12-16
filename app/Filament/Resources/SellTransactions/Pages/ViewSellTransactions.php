<?php

namespace App\Filament\Resources\SellTransactions\Pages;

use App\Filament\Resources\SellTransactions\SellTransactionsResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSellTransactions extends ViewRecord
{
    protected static string $resource = SellTransactionsResource::class;

     protected function getHeaderActions(): array
    {
        return [
            // Tambahkan tombol untuk mencetak invoice
        Action::make('printInvoice')
            ->label('Cetak Invoice')
            ->icon('heroicon-o-printer')
            ->url(fn (): string => route('filament.admin.resources.sell-transactions.print', ['record' => $this->record->id]))
            ->openUrlInNewTab(),
            
            // Opsional: Tombol Edit jika diperlukan
            EditAction::make(),
        ];
    }
}

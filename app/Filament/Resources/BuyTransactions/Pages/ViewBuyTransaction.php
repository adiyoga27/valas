<?php

namespace App\Filament\Resources\BuyTransactions\Pages;

use App\Filament\Resources\BuyTransactions\BuyTransactionResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBuyTransaction extends ViewRecord
{
    protected static string $resource = BuyTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printInvoice')
                ->label('Cetak Invoice')
                ->icon('heroicon-o-printer')
                ->url(fn (): string => route('filament.admin.resources.buy-transactions.print', ['record' => $this->record->id]))
                ->openUrlInNewTab(),

            EditAction::make(),
        ];
    }
}

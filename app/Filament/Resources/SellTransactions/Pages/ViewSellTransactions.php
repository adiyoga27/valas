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
        Action::make('printInvoice')
            ->label('Cetak Invoice')
            ->icon('heroicon-o-printer')
            ->url(fn (): string => route('filament.admin.resources.sell-transactions.print', ['record' => $this->record->id]))
            ->openUrlInNewTab(),

        Action::make('downloadCdd')
            ->label('Download CDD')
            ->icon('heroicon-o-document-check')
            ->color('warning')
            ->url(fn (): string => route('filament.admin.resources.sell-transactions.cdd', ['record' => $this->record->id]))
            ->openUrlInNewTab()
            ->visible(fn () => $this->record->cdd()->exists()),
            
            EditAction::make(),
        ];
    }
}

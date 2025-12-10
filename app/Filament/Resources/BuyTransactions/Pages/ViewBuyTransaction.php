<?php

namespace App\Filament\Resources\BuyTransactions\Pages;

use App\Filament\Resources\BuyTransactions\BuyTransactionResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
class ViewBuyTransaction extends ViewRecord
{
    protected static string $resource = BuyTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tambahkan tombol untuk mencetak invoice
        Action::make('printInvoice')
            ->label('Cetak Invoice')
            ->icon('heroicon-o-printer')
            ->url(fn (): string => route('filament.admin.resources.buy-transactions.print', ['record' => $this->record->id]))
            ->openUrlInNewTab(),
            
            // Opsional: Tombol Edit jika diperlukan
            EditAction::make(),
        ];
    }

    
}

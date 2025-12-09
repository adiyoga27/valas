<?php

namespace App\Filament\Resources\BuyTransactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BuyTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('transaction_code'),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('customer_name'),
                TextEntry::make('total_amount')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}

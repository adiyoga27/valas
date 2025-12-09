<?php

namespace App\Filament\Resources\Currencies\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;

class CurrencyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code'),
                TextEntry::make('name'),
                ImageEntry::make('flag')
                    ->label('Flag')
                    ->disk('public')        // penting jika file di storage/app/public
                    ->visibility('public')
                    ->height(40)
                    ->width(40),

                TextEntry::make('buy_rate')->formatStateUsing(fn ($state) => number_format($state, 2, ',', '.')),
                TextEntry::make('sell_rate')->formatStateUsing(fn ($state) => number_format($state, 2, ',', '.')),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}

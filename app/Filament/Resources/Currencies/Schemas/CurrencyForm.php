<?php

namespace App\Filament\Resources\Currencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Mask;


use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class CurrencyForm
{
public static function configure(Schema $schema): Schema
{
    return $schema
        ->components([
            TextInput::make('code')
                ->required(),
                TextInput::make('country_code')
                ->required(),
            TextInput::make('name')
                ->required(),
            TextInput::make('buy_rate')
                ->label('Buy')
                ->numeric()
                ->required(),
            TextInput::make('sell_rate')
            ->label('Sell')
            ->numeric()
            ->required(),
            Toggle::make('is_active')
                ->required(),
        ]);
    }
}

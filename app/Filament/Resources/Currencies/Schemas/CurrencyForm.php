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
                TextInput::make('name')
                    ->required(),
                FileUpload::make('flag')
                ->label('Attachment')
                ->directory('attachments')  // folder penyimpanan
                ->preserveFilenames()       // opsional
                ->downloadable()            // bisa di-download
                ->previewable(true),    // jika gambar, tampilkan preview
          TextInput::make('buy')
    ->label('Buy')
    ->mask(RawJs::make(<<<'JS'
        {
            numeric: true,
            thousandsSeparator: ',',
            decimalSeparator: '.',
            decimalPlaces: 2
        }
    JS))->required(),
TextInput::make('sell')
    ->label('Sell')
    ->mask(RawJs::make(<<<'JS'
        {
            numeric: true,
            thousandsSeparator: ',',
            decimalSeparator: '.',
            decimalPlaces: 2
        }
    JS))
    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}

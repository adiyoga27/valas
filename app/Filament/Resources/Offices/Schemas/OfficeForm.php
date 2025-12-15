<?php

namespace App\Filament\Resources\Offices\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OfficeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Office Name')
                    ->required(),

                Textarea::make('address')
                    ->required(),

                TextInput::make('phone')
                    ->tel()
                    ->required(),

                FileUpload::make('logo')
                    ->image()
                    ->directory('office-logo')
                    ->maxSize(2048),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Currencies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurrenciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()->label('Kode'),
                TextColumn::make('name')
                    ->searchable()->label('Nama'),
                TextColumn::make('country_code')
                    ->searchable()->label('Kode Negara'),
                ImageColumn::make('flag')->label('Flag')
                    ->size(40)
                    ->disk('public'),
                TextColumn::make('buy_rate')
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.'))
                    ->sortable()->label('Rate Beli'),
                TextColumn::make('sell_rate')
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.'))
                    ->sortable()->label('Rate Jual'),
                IconColumn::make('is_active')
                    ->boolean()->label('Status'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])->defaultSort('is_active', 'desc');
    }
}

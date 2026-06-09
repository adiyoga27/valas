<?php

namespace App\Filament\Resources\SancoDatasets;

use App\Filament\Resources\SancoDatasets\Pages\ListSancoDatasets;
use App\Filament\Resources\SancoDatasets\Pages\ViewSancoDataset;
use App\Models\SancoDataset;
use App\Models\SancoEntity;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SancoDatasetResource extends Resource
{
    protected static ?string $model = SancoDataset::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ShieldExclamation;

    protected static string|\UnitEnum|null $navigationGroup = 'PEP & DTTOT';

    protected static ?string $navigationLabel = 'Dataset';

    protected static ?string $pluralLabel = 'Dataset Sanctions';

    protected static ?int $navigationSort = 100;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('name')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('imported_count')
                    ->label('Terimport')
                    ->state(fn(SancoDataset $record) => SancoEntity::where('dataset_name', $record->name)->count())
                    ->formatStateUsing(fn($state) => $state > 0 ? number_format($state, 0, ',', '.') : '-')
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->sortable(query: function ($query, $direction) {
                        // sort not supported for computed column, skip
                    }),
                TextColumn::make('publisher_name')
                    ->label('Publisher')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('entity_count')
                    ->label('Jumlah Entitas')
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                TextColumn::make('tags')
                    ->label('Tags')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        if (is_string($state)) {
                            $state = json_decode($state, true);
                        }
                        return implode(', ', $state ?? []);
                    }),
                TextColumn::make('updated_at_source')
                    ->label('Update Terakhir')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('entity_count', 'desc')
            ->filters([])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Dataset')
                    ->columns(2)
                    ->components([
                        \Filament\Infolists\Components\TextEntry::make('title')
                            ->label('Judul'),
                        \Filament\Infolists\Components\TextEntry::make('name')
                            ->label('ID'),
                        \Filament\Infolists\Components\TextEntry::make('summary')
                            ->label('Ringkasan')
                            ->columnSpanFull(),
                        \Filament\Infolists\Components\TextEntry::make('description')
                            ->label('Deskripsi')
                            ->markdown()
                            ->columnSpanFull(),
                        \Filament\Infolists\Components\TextEntry::make('url')
                            ->label('URL Sumber')
                            ->url(fn($state) => $state),
                        \Filament\Infolists\Components\TextEntry::make('entity_count')
                            ->label('Jumlah Entitas')
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                        \Filament\Infolists\Components\TextEntry::make('thing_count')
                            ->label('Jumlah Thing')
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                        \Filament\Infolists\Components\TextEntry::make('version')
                            ->label('Versi'),
                        \Filament\Infolists\Components\TextEntry::make('updated_at_source')
                            ->label('Update Dataset')
                            ->dateTime('d/m/Y H:i'),
                        \Filament\Infolists\Components\TextEntry::make('last_export')
                            ->label('Ekspor Terakhir')
                            ->dateTime('d/m/Y H:i'),
                    ]),

                \Filament\Schemas\Components\Section::make('Publisher')
                    ->columns(2)
                    ->components([
                        \Filament\Infolists\Components\TextEntry::make('publisher_name')
                            ->label('Nama'),
                        \Filament\Infolists\Components\TextEntry::make('publisher_acronym')
                            ->label('Akronim'),
                        \Filament\Infolists\Components\TextEntry::make('publisher_url')
                            ->label('URL')
                            ->url(fn($state) => $state),
                        \Filament\Infolists\Components\TextEntry::make('publisher_country_label')
                            ->label('Negara'),
                        \Filament\Infolists\Components\IconEntry::make('publisher_official')
                            ->label('Resmi')
                            ->boolean(),
                    ]),

                \Filament\Schemas\Components\Section::make('Coverage')
                    ->columns(2)
                    ->components([
                        \Filament\Infolists\Components\TextEntry::make('coverage_start')
                            ->label('Mulai'),
                        \Filament\Infolists\Components\TextEntry::make('coverage_frequency')
                            ->label('Frekuensi'),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSancoDatasets::route('/'),
            'view' => ViewSancoDataset::route('/{record}'),
        ];
    }
}

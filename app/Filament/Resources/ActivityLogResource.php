<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Schemas\Schema; // Keeping Schema based on UsersResource
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Spatie\Activitylog\Models\Activity;
// use Filament\Infolists\Components\KeyValue; // Removed
use Filament\Schemas\Components\Section; // Correct Schema component
use Filament\Infolists\Components\TextEntry; // Standard Infolist component
use Filament\Actions\ViewAction; // Based on BuyTransactionsTable

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Tools';
    
    protected static ?string $navigationLabel = 'Log Aktivitas';

    protected static ?string $pluralLabel = 'Log Aktivitas';

    protected static ?int $navigationSort = 999;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('causer.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Aktivitas')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                 TextColumn::make('properties.ip')
                    ->label('IP Address'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Umum')
                    ->columns(2)
                    ->components([
                        TextEntry::make('causer.name')
                            ->label('User'),
                        TextEntry::make('created_at')
                            ->dateTime('d/m/Y H:i:s')
                            ->label('Waktu'),
                         TextEntry::make('properties.ip')
                            ->label('IP Address'),
                         TextEntry::make('description')
                            ->label('Deskripsi'),
                    ]),

                Section::make('Perubahan Data')
                    ->columns(2)
                    ->components([
                        TextEntry::make('properties.old')
                            ->label('Data Lama (Old)')
                            ->formatStateUsing(fn ($state) => 
                                collect($state ?? [])->map(fn($v, $k) => "<strong>{$k}</strong>: {$v}")->implode('<br>')
                            )
                            ->html(),
                        
                        TextEntry::make('properties.attributes')
                            ->label('Data Baru (New)')
                            ->formatStateUsing(fn ($state) => 
                                collect($state ?? [])->map(fn($v, $k) => "<strong>{$k}</strong>: {$v}")->implode('<br>')
                            )
                            ->html(),
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}

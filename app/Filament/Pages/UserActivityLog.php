<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class UserActivityLog extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static string|\UnitEnum|null $navigationGroup = 'Report';
    protected static ?string $navigationLabel = 'Log User';
    protected static ?string $title = 'Log Aktivitas User';
    protected string $view = 'filament.pages.user-activity-log';

    protected function getTableQuery(): Builder
    {
        return Activity::query()
            ->with('causer')
            ->where('causer_id', auth()->id())
            ->orderByDesc('created_at');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Aksi')
                    ->badge()
                    ->searchable(),

                TextColumn::make('subject_type')
                    ->label('Data')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->searchable(),

                TextColumn::make('properties.ip')
                    ->label('IP')
                    ->searchable(
                        query: fn($q, $s) =>
                        $q->where('properties->ip', 'like', "%{$s}%")
                    ),

                TextColumn::make('properties.url')
                    ->label('Halaman')
                    ->limit(30)
                    ->tooltip(fn($state) => $state)
                    ->searchable(
                        query: fn($q, $s) =>
                        $q->where('properties->url', 'like', "%{$s}%")
                    ),

                TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable(),

                TextColumn::make('properties')
                    ->label('Detail')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(
                        query: fn($q, $s) =>
                        $q->where('properties', 'like', "%{$s}%")
                    ),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

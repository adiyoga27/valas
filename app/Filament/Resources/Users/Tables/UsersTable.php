<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
             ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                BadgeColumn::make('role')
                    ->label('Role')
                    ->colors([
                        'success' => 'admin',
                        'info'    => 'staff',
                        'warning' => 'kasir',
                        'primary' => 'manager',
                    ])
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
               SelectFilter::make('role')
                    ->options([
                        'admin'   => 'Admin',
                        'staff'   => 'Staff',
                        'kasir'   => 'Kasir',
                        'manager' => 'Manager',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (User $record) => auth()->id() !== $record->id),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UsersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
           ->schema([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(100),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn ($context) => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText('Kosongkan jika tidak ingin mengubah password'),

                Select::make('role')
                    ->label('Role')
                    ->required()
                    ->options([
                        'admin'   => 'Admin',
                        'staff'   => 'Staff',
                        'kasir'   => 'Kasir',
                        'manager' => 'Manager',
                    ]),
            ])
            ->columns(2);
    }
}

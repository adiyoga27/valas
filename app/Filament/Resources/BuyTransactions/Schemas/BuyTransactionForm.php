<?php

namespace App\Filament\Resources\BuyTransactions\Schemas;

use App\Models\Currency;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class BuyTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->columns(2)
            ->components([
                 TextInput::make('transaction_code')
                    ->default(fn () => 'BUY-' . time())
                    ->disabled()
                    ->columnSpan(2)
                    ->dehydrated(),

                TextInput::make('customer_name')
                    ->label('Customer Name')
                    ->columnSpan(2)
                    ->required(),
                
                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),

                TextInput::make('total_amount')
                    ->numeric()
                    ->disabled()
                    ->reactive()
                    ->columnSpan(2)->dehydrated(), 
                   


                Repeater::make('items')
                    ->dehydrated()
                    ->schema([
                        Grid::make(12)->schema([
                       Select::make('currency_id')
                    ->label('Currency')
                    ->required()
                    ->options(
                        Currency::all()->pluck('display_name', 'id')
                    )
                    ->searchable()
                    ->reactive()
                    ->columnSpan(3)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (! $state) return;

                        $currency = Currency::find($state);

                        $set('currency_code', $currency->code);
                        $set('currency_name', $currency->name);
                        $set('currency_flag', $currency->flag);
                        $set('buy_rate', $currency->buy_rate);
                    }),


                        TextInput::make('currency_code')
                            ->disabled()
                            ->dehydrated()
                            ->label('Code'),

                        TextInput::make('buy_rate')
                            ->disabled()
                            ->numeric()
                            ->dehydrated()
                              ->columnSpan(2)
                            ->label('Buy Rate'),

                        TextInput::make('qty')
                            ->label('Qty')
                            
                              ->dehydrated()
                            ->numeric()
                              ->columnSpan(2)
                            ->reactive()

                            ->afterStateUpdated(function ($state, $set, $get) {
                                $set('total', $get('buy_rate') * $state);
                                BuyTransactionForm::updateParentTotal($get, $set);
                            }),

                        TextInput::make('total')
                            ->label('Total')
                              ->columnSpan(2)
                              ->dehydrated()
                            ->numeric()
                            ->disabled(),
                        ])
                    ])
                    ->columns(1)
            ->columnSpan('full')
        ->afterStateUpdated(function (callable $get, callable $set) {
                BuyTransactionForm::updateParentTotal($get, $set);
            })
                    ]);
            }

    public static function updateParentTotal($get, $set)
    {
        $items = collect($get('items'));
        $total = $items->sum('total');
        $set('total_amount', $total);
    }
}

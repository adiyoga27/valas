<?php

namespace App\Filament\Resources\BuyTransactions\Schemas;

use App\Models\Currency;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                    ->default(fn() => 'BUY-' . time())
                    ->disabled()
                    ->columnSpan(2)
                    ->dehydrated()->label("No. Invoice"),

                TextInput::make('customer_name')
                    ->label('Customer Name')
                    ->columnSpan(2)
                    ->required()->label("Nama Pelanggan"),

                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpan(2)
                    ->rows(3)
                    ->nullable()
                    ->placeholder('Tambahkan catatan tambahan di sini...')->label("Catatan"),
                Hidden::make('user_id')
                    ->default(fn() => auth()->id()),

                Repeater::make('items')
                    ->dehydrated()
                    ->schema([
                        Grid::make(12)->schema([
                            Select::make('currency_id')
                                ->label('Currency')
                                ->required()
                                ->options(
                                    Currency::all()->where('buy_rate', '>', 0)->where('is_active', true)->pluck('display_name', 'id')
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
                                })->label("Mata Uang"),


                            // TextInput::make('currency_code')
                            //     ->disabled()
                            //     ->dehydrated()
                            //     ->label('Code'),
 Placeholder::make('currency_code_view')
                                ->label('Kode')
                                ->content(
                                    fn(callable $get) => $get('currency_code')
                                )
                                ->columnSpan(2),
                            // TextInput::make('buy_rate')
                            //     ->label('Kurs Beli')
                            //     ->disabled()
                            //     ->numeric()
                            //     ->columnSpan(2)
                            //     ->formatStateUsing(
                            //         fn($state) =>
                            //         $state ? number_format($state, 0, ',', '.') : null
                            //     )
                            //     ->dehydrateStateUsing(
                            //         fn($state) =>
                            //         (int) str_replace('.', '', $state)
                            //     ),
 Placeholder::make('buy_rate_view')
                                ->label('Kurs Beli')
                                ->content(
                                    fn(callable $get) =>
                                    number_format($get('buy_rate') ?? 0, 0, ',', '.')
                                )
                                ->columnSpan(2),

                            TextInput::make('qty')
                                ->label('Jumlah')
                                ->dehydrated()
                                ->numeric()
                                ->columnSpan(2)
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $set('total', $get('buy_rate') * $state);
                                    BuyTransactionForm::updateParentTotal($get, $set);
                                }),

                            // Hidden::make('total')
                            //     ->label('Total')
                            //     ->columnSpan(2)
                            //     ->dehydrated()
                            //     ->numeric()
                            //     ->disabled(),

                            Placeholder::make('total_view')
                                ->label('Total')
                                ->content(
                                    fn(callable $get) =>
                                    number_format($get('total') ?? 0, 0, ',', '.')
                                )
                                ->columnSpan(2),
                        ])
                    ])
                    ->columns(1)
                    ->columnSpan('full')
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        BuyTransactionForm::updateParentTotal($get, $set);
                    }),

                Placeholder::make('total_display')
                    ->label('Total Transaksi')
                    ->content(function (callable $get) {
                        $total = collect($get('items'))->sum('total');
                        return 'Rp ' . number_format($total ?? 0, 0, ',', '.');
                    })
                    ->extraAttributes([
                        'class' => 'text-right text-2xl font-bold',
                    ])
                    ->columnSpan(2),
            ]);
    }

    public static function updateParentTotal($get, $set)
    {
        $items = collect($get('items'));
        $total = $items->sum('total');
        $set('total_amount', $total);
    }
}

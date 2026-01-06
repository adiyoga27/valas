<?php

namespace App\Filament\Resources\BuyTransactions\Schemas;

use App\Models\Currency;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class BuyTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                DateTimePicker::make('created_at')
                    ->label('Tanggal Transaksi')
                    ->default(now())
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->columnSpan(2)
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if (! $state) return;
                        $date = \Carbon\Carbon::parse($state);
                        $today = $date->format('Ymd');
                        $countToday = \App\Models\BuyTransaction::where('transaction_code', 'like', "BUY-{$today}%")->count() + 1;
                        $set('transaction_code', "BUY-{$today}1000{$countToday}");
                    }),

                TextInput::make('transaction_code')
                    ->default(function () {
                        $today = now()->format('Ymd'); // TGL HARI INI tanpa spasi
                        // Hitung jumlah transaksi hari ini
                        $countToday = \App\Models\BuyTransaction::where('transaction_code', 'like', "BUY-{$today}%")->count() + 1;
                        return "BUY-{$today}1000{$countToday}";
                    })
                    ->disabled()
                    ->columnSpan(2)
                    ->dehydrated()
                    ->label("No. Invoice"),


                TextInput::make('customer_name')
                    ->label('Customer Name')
                    ->columnSpan(2)
                    ->required()->label("Nama Pelanggan"),
                TextInput::make('passport_number')
                    ->label('Passport Number')
                    ->columnSpan(2)
                    ->nullable()->label("Nomor Paspor"),
                TextInput::make('customer_address')
                    ->label('Customer Address')
                    ->columnSpan(2)
                    ->nullable()->label("Alamat Pelanggan"),
                TextInput::make('customer_country')
                    ->label('Customer Country')
                    ->columnSpan(2)
                    ->nullable()->label("Negara Pelanggan"),

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

                            Placeholder::make('currency_code_view')
                                ->label('Kode')
                                ->content(
                                    fn(callable $get) => $get('currency_code')
                                )
                                ->columnSpan(2),

                           

                            TextInput::make('qty')
                                ->label('Jumlah')
                                ->dehydrated()
                                ->numeric()
                                ->columnSpan(2)
                                ->reactive()
                                
                                ->debounce(800)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $set('total', $get('buy_rate') * $state);
                                    BuyTransactionForm::updateParentTotal($get, $set);
                                }),
 Placeholder::make('buy_rate_view')
                                ->label('Kurs Beli')
                                ->content(
                                    fn(callable $get) =>
                                    number_format($get('buy_rate') ?? 0, 0, ',', '.')
                                )
                                ->columnSpan(2),
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
                Repeater::make('additional_amounts')
                    ->label('Biaya Tambahan')
                    ->dehydrated()
                    ->schema([
                        Grid::make(12)->schema([
                            TextInput::make('name')
                                ->label('Nama Biaya')
                                ->columnSpan(7),

                            TextInput::make('amount')
                                ->label('Jumlah')
                                ->numeric()
                                ->reactive()
                                
                                ->debounce(800)
                                ->columnSpan(5)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    BuyTransactionForm::updateGrandTotal($get, $set);
                                }),
                        ]),
                    ])
                    ->columns(1)
                    ->columnSpan('full')
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        BuyTransactionForm::updateGrandTotal($get, $set);
                    })
                    ->addActionLabel('+ Tambah Biaya Tambahan'),


                Placeholder::make('total_display')
                    ->label('Total Transaksi')
                    ->content(function (callable $get) {
                        $total = collect($get('items'))->sum('total');
                        return 'Rp ' . number_format($total ?? 0, 0, ',', '.');
                    })
                    ->columnSpan(2),

                Placeholder::make('additional_display')
                    ->label('Total Biaya Tambahan')
                    ->content(function (callable $get) {
                        $additional = collect($get('additional_amounts'))->sum('amount');
                        return 'Rp ' . number_format($additional ?? 0, 0, ',', '.');
                    })
                    ->columnSpan(2),

                Placeholder::make('grand_total_display')
                    ->label('Grand Total')
                    ->content(function (callable $get) {
                        $itemsTotal = collect($get('items'))->sum('total');
                        $additional = collect($get('additional_amounts'))->sum('amount');
                        $grandTotal = $itemsTotal + $additional;

                        return 'Rp ' . number_format($grandTotal ?? 0, 0, ',', '.');
                    })
                    ->extraAttributes([
                        'class' => 'text-right text-3xl font-bold text-success',
                    ])
                    ->columnSpan(2),

            ]);
    }
    public static function updateGrandTotal($get, $set)
    {
        $itemsTotal = collect($get('items'))->sum('total');
        $additional = collect($get('additional_amounts'))->sum('amount');

        $set('total_amount', $itemsTotal);
        $set('grand_total', $itemsTotal + $additional);
    }


    public static function updateParentTotal($get, $set)
    {
        $items = collect($get('items'));
        $total = $items->sum('total');

        $set('total_amount', $total);

        self::updateGrandTotal($get, $set);
    }
}

<?php

namespace App\Filament\Resources\SellTransactions\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class SellTransactionsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invoice Pembelian (Buy Transaction)')
                    ->columnSpanFull()
                    ->schema([
                        // Header Transaksi: Code, Customer, Tanggal
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('transaction_code')
                                    ->label('Kode Transaksi')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('created_at')
                                    ->label('Tanggal Transaksi')
                                    ->dateTime('d M Y H:i')->timezone('Asia/Makassar'),
                            ]),

                        Fieldset::make('Informasi Pelanggan')
                            ->schema([
                                TextEntry::make('customer_name')
                                    ->label('Nama Pelanggan')
                                    ->default('N/A'),
                                TextEntry::make('passport_number')
                                    ->label('Nomor Paspor')
                                    ->default('N/A'),
                                TextEntry::make('customer_address')
                                    ->label('Alamat Pelanggan')
                                    ->default('N/A'),
                                TextEntry::make('customer_country')
                                    ->label('Negara Pelanggan')
                                    ->default('N/A'),
                                TextEntry::make('customer_birthdate')
                                    ->label('Tanggal Lahir Pelanggan')
                                    ->date('d M Y')
                                    ->default('N/A'),
                                TextEntry::make('user.name')
                                    ->label('Dibuat Oleh')
                                    ->default('Sistem'),
                            ])
                            ->columns(2),

                        // Detail Item Transaksi (Menggunakan Infolist Repeater atau Component lain)
                        Section::make('Detail Item Pembelian')
                            ->schema([
                                // Menggunakan Infolist RepeatableEntry untuk menampilkan Items
                                \Filament\Infolists\Components\RepeatableEntry::make('items')
                                    ->schema([
                                        Grid::make(6)
                                            ->schema([
                                                TextEntry::make('currency_name')
                                                    ->label('Mata Uang')
                                                    ->columnSpan(2),
                                                TextEntry::make('qty')
                                                    ->label('Jumlah (Qty)')
                                                    ->numeric()
                                                    ->columnSpan(1),
                                                TextEntry::make('sell_rate')
                                                    ->label('Kurs Beli')
                                                    ->money('IDR', 2) // Asumsi kurs ditampilkan sebagai IDR
                                                    ->columnSpan(1),
                                                TextEntry::make('total')
                                                    ->label('Subtotal (IDR)')
                                                    ->money('IDR')
                                                    ->weight(FontWeight::Bold)
                                                    ->columnSpan(2),
                                            ])
                                    ])
                                    ->contained(false) // Membuat repeater tanpa border luar
                                    ->columns(1)
                            ]),
                        Section::make('Biaya Tambahan')
                            ->schema([
                                RepeatableEntry::make('additional_amounts')
                                    ->schema([
                                        Grid::make(6)->schema([
                                            TextEntry::make('name')
                                                ->label('Nama Biaya')
                                                ->columnSpan(4),

                                            TextEntry::make('amount')
                                                ->label('Jumlah')
                                                ->money('IDR')
                                                ->weight(FontWeight::Bold)
                                                ->columnSpan(2),
                                        ]),
                                    ])
                                    ->contained(false)
                                    ->visible(
                                        fn($record) =>
                                        ! empty($record->additional_amounts)
                                    ),
                            ]),

                        Section::make('Ringkasan Transaksi')
                            ->schema([
                                // TOTAL ITEM, BIAYA TAMBAHAN, GRAND TOTAL
                                TextEntry::make('total_amount')
                                    ->label('Total Item')
                                    ->money('IDR')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('additional_total')
                                    ->label('Total Biaya Tambahan')
                                    ->state(
                                        fn($record) =>
                                        collect($record->additional_amounts)->sum('amount')
                                    )
                                    ->money('IDR'),

                                TextEntry::make('grand_total')
                                    ->label('GRAND TOTAL')
                                    ->money('IDR')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::ExtraBold)
                                    ->color('success'),

                                // CATATAN
                                \Filament\Forms\Components\Placeholder::make('notes_view')
                                    ->label('Catatan')
                                    ->content(fn($record) => $record->notes ?: '-')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->extraAttributes([
                                'class' => 'text-right',
                            ]),

                    ])->columns(1),
            ]);
    }
}

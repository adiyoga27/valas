<?php

namespace App\Filament\Resources\BuyTransactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
// Use Grid for layout instead of Table Split layout

class BuyTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
              Section::make('Invoice Pembelian (Buy Transaction)')
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
                                    ->dateTime('d M Y H:i:s')
                                    ->alignment(Alignment::End),
                            ]),

                        Fieldset::make('Informasi Pelanggan')
                            ->schema([
                                TextEntry::make('customer_name')
                                    ->label('Nama Pelanggan')
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
                                                TextEntry::make('buy_rate')
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

                        // Footer Transaksi: Total Amount
                        Grid::make(3)
                            ->schema([
                                // Placeholder untuk Balance/Keterangan
                                TextEntry::make('note')
                                    ->label('Catatan')
                                    ->default('-')
                                    ->columnSpan(2),
                                    
                                // Total Amount
                                TextEntry::make('total_amount')
                                    ->label('TOTAL PEMBAYARAN')
                                    ->money('IDR')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::ExtraBold)
                                    ->color('primary')
                                    ->columnSpan(1),
                            ]),
                    ])->columns(1),
            ]);
    }
}

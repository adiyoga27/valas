<?php

namespace App\Filament\Pages;

use App\Models\BuyTransactionItem;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\ReportBuyTransactionExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;


class ReportBuyTransaction extends Page implements Tables\Contracts\HasTable, Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use Forms\Concerns\InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-down';
    protected static ?string $navigationLabel = 'Report Pembelian Valas';
    protected static ?string $title = 'Report Transaksi Pembelian Valas';
    protected static string|\UnitEnum|null $navigationGroup = 'Report';

    protected  string $view = 'filament.pages.report-buy-transaction';

    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate   = now()->toDateString();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () =>
                    Excel::download(
                        new ReportBuyTransactionExport(
                            $this->startDate,
                            $this->endDate
                        ),
                        'report-pembelian-valas.xlsx'
                    )
                ),
        ];
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('startDate')
                    ->label('Tanggal Awal')
                    ->required()
                    ->live(),

                Forms\Components\DatePicker::make('endDate')
                    ->label('Tanggal Akhir')
                    ->required()
                    ->live(),
            ])
            ->columns(2);
    }

    public function updatedStartDate(): void
    {
        $this->resetTable();
    }

    public function updatedEndDate(): void
    {
        $this->resetTable();
    }

    protected function getTableQuery(): Builder
    {
        return BuyTransactionItem::query()
            ->select('buy_transaction_items.*')
            ->join('buy_transactions', 'buy_transaction_items.buy_transaction_id', '=', 'buy_transactions.id')
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('buy_transactions.created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay(),
                ]);
            })
            ;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction.created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i'),

                Tables\Columns\TextColumn::make('transaction.transaction_code')
                    ->label('Nomor Nota')
                    ->searchable(),

                Tables\Columns\TextColumn::make('currency_code')
                    ->label('Mata Uang'),

                Tables\Columns\TextColumn::make('qty')
                    ->label('Jumlah UKA')
                    ->numeric(2),

                Tables\Columns\TextColumn::make('buy_rate')
                    ->label('Rate/Kurs')
                    ->money('IDR', true),

                Tables\Columns\TextColumn::make('total')
                    ->label('Jumlah Rupiah')
                    ->money('IDR', true),

                Tables\Columns\TextColumn::make('transaction.customer_name')
                    ->label('Nama Nasabah')
                    ->searchable(),

                Tables\Columns\TextColumn::make('transaction.customer_address')
                    ->label('Alamat')
                    ->limit(30),

                Tables\Columns\TextColumn::make('transaction.passport_number')
                    ->label('Passport/KTP'),
            ])
            ->defaultSort('buy_transactions.created_at', 'desc');
    }
}

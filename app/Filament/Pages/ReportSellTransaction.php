<?php

namespace App\Filament\Pages;

use App\Exports\ReportSellTransactionExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;

use App\Models\SellTransaction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportSellTransaction extends Page implements Tables\Contracts\HasTable, Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use Forms\Concerns\InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationLabel = 'Report Penjualan Valas';
    protected static ?string $title = 'Report Transaksi Penjualan Valas';
    protected static string|\UnitEnum|null $navigationGroup = 'Report';

    protected  string $view = 'filament.pages.report-sell-transaction';

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
                    new ReportSellTransactionExport(
                        $this->startDate,
                        $this->endDate
                    ),
                    'report-penjualan-valas.xlsx'
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
                    ->required(),

                Forms\Components\DatePicker::make('endDate')
                    ->label('Tanggal Akhir')
                    ->required(),
            ])
            ->columns(2);
    }

    protected function getTableQuery(): Builder
    {
        return SellTransaction::query()
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay(),
                ]);
            })
            ->latest('created_at');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i'),

                Tables\Columns\TextColumn::make('transaction_code')
                    ->label('Kode Transaksi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer'),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Total')
                    ->money('IDR', true),
            ]);
    }
}

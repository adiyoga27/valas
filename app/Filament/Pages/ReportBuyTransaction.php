<?php

namespace App\Filament\Pages;

use App\Models\BuyTransaction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
        return BuyTransaction::query()
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

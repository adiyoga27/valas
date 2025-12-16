<?php

namespace App\Filament\Resources\BuyTransactions;

use App\Filament\Resources\BuyTransactions\Pages\CreateBuyTransaction;
use App\Filament\Resources\BuyTransactions\Pages\EditBuyTransaction;
use App\Filament\Resources\BuyTransactions\Pages\ListBuyTransactions;
use App\Filament\Resources\BuyTransactions\Pages\PrintBuyTransaction;
use App\Filament\Resources\BuyTransactions\Pages\ViewBuyTransaction;
use App\Filament\Resources\BuyTransactions\Schemas\BuyTransactionForm;
use App\Filament\Resources\BuyTransactions\Tables\BuyTransactionsTable;
use App\Models\BuyTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\BuyTransactions\Schemas\BuyTransactionInfolist;
use Illuminate\Database\Eloquent\Builder;

class BuyTransactionResource extends Resource
{
    protected static ?string $model = BuyTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShoppingBag;
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $recordTitleAttribute = 'Transaksi Tukar Uang';
    public static function getPluralLabel(): string
    {
        return 'Beli Mata Uang Asing';
    }

    public static function getEmptyStateHeading(): ?string
    {
        return 'Data transaksi masih kosong';
    }
    public static function form(Schema $schema): Schema
    {
        return BuyTransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BuyTransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BuyTransactionsTable::configure($table);
    }
    public static function getRelations(): array
    {
        return [];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('items'); // ⬅️ WAJIB
    }
    public static function getPages(): array
    {
        return [
            'index' => ListBuyTransactions::route('/'),
            'create' => CreateBuyTransaction::route('/create'),
            'edit' => EditBuyTransaction::route('/{record}/edit'),
            'view' => ViewBuyTransaction::route('/{record}'),
        ];
    }
}

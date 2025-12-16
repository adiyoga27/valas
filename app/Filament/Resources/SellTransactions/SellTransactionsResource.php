<?php

namespace App\Filament\Resources\SellTransactions;

use App\Filament\Resources\SellTransactions\Pages\CreateSellTransactions;
use App\Filament\Resources\SellTransactions\Pages\EditSellTransactions;
use App\Filament\Resources\SellTransactions\Pages\ListSellTransactions;
use App\Filament\Resources\SellTransactions\Pages\ViewSellTransactions;
use App\Filament\Resources\SellTransactions\Schemas\SellTransactionsForm;
use App\Filament\Resources\SellTransactions\Schemas\SellTransactionsInfolist;
use App\Filament\Resources\SellTransactions\Tables\SellTransactionsTable;
use App\Models\SellTransaction;
use App\Models\SellTransactions;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SellTransactionsResource extends Resource
{
    protected static ?string $model = SellTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'SellTransaction';

    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    public static function getPluralLabel(): string
    {
        return 'Jual Mata Uang Asing';
    }

    public static function getEmptyStateHeading(): ?string
    {
        return 'Data transaksi masih kosong';
    }

    public static function form(Schema $schema): Schema
    {
        return SellTransactionsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SellTransactionsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SellTransactionsTable::configure($table);
    }
  public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('items'); // ⬅️ WAJIB
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSellTransactions::route('/'),
            'create' => CreateSellTransactions::route('/create'),
            'view' => ViewSellTransactions::route('/{record}'),
            'edit' => EditSellTransactions::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

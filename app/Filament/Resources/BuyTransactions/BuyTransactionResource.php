<?php

namespace App\Filament\Resources\BuyTransactions;

use App\Filament\Resources\BuyTransactions\Pages\CreateBuyTransaction;
use App\Filament\Resources\BuyTransactions\Pages\EditBuyTransaction;
use App\Filament\Resources\BuyTransactions\Pages\ListBuyTransactions;
use App\Filament\Resources\BuyTransactions\Schemas\BuyTransactionForm;
use App\Filament\Resources\BuyTransactions\Tables\BuyTransactionsTable;
use App\Models\BuyTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BuyTransactionResource extends Resource
{
    protected static ?string $model = BuyTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Buy Transaction';

    public static function form(Schema $schema): Schema
    {
        return BuyTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BuyTransactionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBuyTransactions::route('/'),
            'create' => CreateBuyTransaction::route('/create'),
            'edit' => EditBuyTransaction::route('/{record}/edit'),
        ];
    }
}

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuyTransactionPrintController;
use App\Http\Controllers\SellTransactionPrintController;

Route::get('/', function () {
    return redirect('/admin');
});


Route::get('/filament/admin/buy-transactions/{record}/print', [BuyTransactionPrintController::class, 'print'])
    ->name('filament.admin.resources.buy-transactions.print');

Route::get('/filament/admin/sell-transactions/{record}/print', [SellTransactionPrintController::class, 'print'])
    ->name('filament.admin.resources.sell-transactions.print');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuyTransactionPrintController;
use App\Http\Controllers\SellTransactionPrintController;
use App\Http\Controllers\SancoEntityController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/sanco-entity/{entityId}', [SancoEntityController::class, 'show'])
    ->name('sanco.entity.show');

Route::get('/filament/admin/buy-transactions/{record}/print', [BuyTransactionPrintController::class, 'print'])
    ->name('filament.admin.resources.buy-transactions.print');

Route::get('/filament/admin/sell-transactions/{record}/print', [SellTransactionPrintController::class, 'print'])
    ->name('filament.admin.resources.sell-transactions.print');
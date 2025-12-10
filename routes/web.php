<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuyTransactionPrintController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/filament/admin/buy-transactions/{record}/print', [BuyTransactionPrintController::class, 'print'])
    ->name('filament.admin.resources.buy-transactions.print');


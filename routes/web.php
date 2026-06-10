<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\BuyTransactionController;
use App\Http\Controllers\Admin\SellTransactionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\SancoController;
use App\Http\Controllers\BuyTransactionPrintController;
use App\Http\Controllers\SellTransactionPrintController;
use App\Http\Controllers\SancoEntityController;
use App\Http\Controllers\CddPrintController;

// Root redirect
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Sanco public detail page
Route::get('/sanco-entity/{entityId}', [SancoEntityController::class, 'show'])->name('sanco.entity.show');

// Auth routes
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Protected admin routes
Route::middleware(['web', 'admin.auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Currencies
    Route::resource('currencies', CurrencyController::class);

    // Users
    Route::resource('users', UserController::class);

    // Office Settings
    Route::get('/office/setting', [OfficeController::class, 'setting'])->name('office.setting');
    Route::put('/office/update', [OfficeController::class, 'update'])->name('office.update');

    // Buy Transactions
    Route::resource('buy-transactions', BuyTransactionController::class);
    Route::get('/buy-transactions/{buyTransaction}/cdd', [BuyTransactionController::class, 'cdd'])->name('buy-transactions.cdd');
    Route::post('/buy-transactions/{buyTransaction}/cdd', [BuyTransactionController::class, 'cddStore'])->name('buy-transactions.cdd-store');
    Route::get('/buy-transactions/check-pep', [BuyTransactionController::class, 'checkPep'])->name('buy-transactions.check-pep');

    // Sell Transactions
    Route::resource('sell-transactions', SellTransactionController::class);

    // Reports
    Route::get('/reports/buy', [ReportController::class, 'buy'])->name('reports.buy');
    Route::get('/reports/buy-export', [ReportController::class, 'buyExport'])->name('reports.buy-export');
    Route::get('/reports/sell', [ReportController::class, 'sell'])->name('reports.sell');
    Route::get('/reports/sell-export', [ReportController::class, 'sellExport'])->name('reports.sell-export');
    Route::get('/reports/mutation', [ReportController::class, 'mutation'])->name('reports.mutation');

    // Activity Log
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

    // Sanco
    Route::get('/sanco/datasets', [SancoController::class, 'datasets'])->name('sanco.datasets.index');
    Route::get('/sanco/check', [SancoController::class, 'check'])->name('sanco.check');
});

// PDF Print routes (accessible to admin users)
Route::get('/admin/buy-transactions/{record}/print', [BuyTransactionPrintController::class, 'print'])->name('admin.buy-transactions.print');
Route::get('/admin/sell-transactions/{record}/print', [SellTransactionPrintController::class, 'print'])->name('admin.sell-transactions.print');
Route::get('/admin/buy-transactions/{record}/cdd-print', [CddPrintController::class, 'print'])->name('admin.buy-transactions.cdd-print');

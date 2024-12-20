<?php

use App\Http\Controllers\AccountPayableController;
use App\Http\Controllers\AccountReceivableController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\CreditCardPurchaseController;
use App\Http\Controllers\EnumsController;
use App\Http\Controllers\PurchaseInstallmentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Rotas para contas a pagar e receber
    Route::resource('accounts-payable', AccountPayableController::class);
    Route::resource('accounts-receivable', AccountReceivableController::class);

    // Rota para categorias
    Route::get('/categories', [CategoryController::class, 'getCategories'])->name('get-categories');

    Route::get('/enums/{enum}', [EnumsController::class, 'getEnumLabels'])->name('get-enum-labels');

    // Rotas para o módulo de cartões de crédito (CRUD completo)
    Route::resource('credit-cards', CreditCardController::class);

    // Rotas para o módulo de compras com cartão de crédito (CRUD completo)
    Route::resource('credit-card-purchases', CreditCardPurchaseController::class);

    // Rotas para o módulo de parcelas (apenas visualização e exclusão)
    Route::resource('purchase-installments', PurchaseInstallmentController::class)
        ->only(['update', 'destroy']);

    //Rotas de relatório
    Route::get('/dashboard', [ReportController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pdf', [ReportController::class, 'downloadPdf'])->name('dashboard.pdf');

    // Rota para logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

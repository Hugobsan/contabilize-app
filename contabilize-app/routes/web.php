<?php

use App\Http\Controllers\AccountPayableController;
use App\Http\Controllers\AccountReceivableController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\CreditCardPurchaseController;
use App\Http\Controllers\PurchaseInstallmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    // Rotas para contas a pagar e receber
    Route::resource('accounts-payable', AccountPayableController::class);
    Route::resource('accounts-receivable', AccountReceivableController::class);

    // Rota para categorias
    Route::get('/categories', [CategoryController::class, 'getCategories']);

    // Rotas para o módulo de cartões de crédito (CRUD completo)
    Route::resource('credit-cards', CreditCardController::class);

    // Rotas para o módulo de compras com cartão de crédito (CRUD completo)
    Route::resource('credit-card-purchases', CreditCardPurchaseController::class);

    // Rotas para o módulo de parcelas (apenas visualização e exclusão)
    Route::resource('purchase-installments', PurchaseInstallmentController::class)
        ->only(['show', 'destroy']);
});

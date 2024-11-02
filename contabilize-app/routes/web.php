<?php

use App\Http\Controllers\AccountPayableController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('accounts-payable', AccountPayableController::class);
    Route::get('/categories', [CategoryController::class, 'getCategories']);
});
<?php

use App\Http\Controllers\AccountPayableController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('accounts-payable', AccountPayableController::class);

Route::middleware(['auth'])->group(function () {
    
    Route::get('/categories', [CategoryController::class, 'getCategories']);
});
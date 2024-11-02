<?php

use App\Models\AccountPayable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Teste para verificar a criação de um registro de AccountPayable.
 */
it('can create an account payable', function () {
    $user = User::factory()->create();

    $accountPayable = AccountPayable::factory()->create([
        'user_id' => $user->id,
        'description' => 'Test Account',
        'value' => 150.75,
        'due_date' => now()->addDays(10),
        'status' => 0,
        'category' => 'alimentacao',
    ]);

    expect($accountPayable)->toBeInstanceOf(AccountPayable::class);
    expect($accountPayable->description)->toBe('Test Account');
});

/**
 * Teste para verificar o método de escopo de filtro (scopeFilter).
 */
it('can filter accounts payable by category', function () {
    $user = User::factory()->create();
    AccountPayable::factory()->count(3)->create(['user_id' => $user->id, 'category' => 'alimentacao']);
    AccountPayable::factory()->count(2)->create(['user_id' => $user->id, 'category' => 'transporte']);

    $filteredAccounts = AccountPayable::filter(['category' => 'alimentacao'])->get();

    expect($filteredAccounts)->toHaveCount(3);
    expect($filteredAccounts->first()->category)->toBe('alimentacao');
});

/**
 * Teste para verificar a formatação de valores (getFormattedValueAttribute).
 */
it('returns the formatted value correctly', function () {
    $accountPayable = AccountPayable::factory()->create(['value' => 1000.5]);

    expect($accountPayable->formatted_value)->toBe('1.000,50');
});

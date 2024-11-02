<?php

use App\Models\AccountPayable;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

/**
 * Teste de listagem de contas a pagar.
 */
it('can list accounts payable', function () {
    $user = User::factory()->create();
    AccountPayable::factory()->count(5)->create(['user_id' => $user->id]);

    actingAs($user)
        ->get('/accounts-payable')
        ->assertStatus(200)
        ->assertJsonCount(5);
});

/**
 * Teste de criação de uma nova conta a pagar.
 */
it('can create an account payable', function () {
    $user = User::factory()->create();
    $payload = [
        'description' => 'Test Account',
        'value' => 100.50,
        'due_date' => now()->addDays(10)->toDateString(),
        'status' => 0,
        'category' => 'alimentacao',
    ];

    actingAs($user)
        ->withoutMiddleware(VerifyCsrfToken::class)
        ->post('/accounts-payable', $payload)
        ->assertStatus(201);
});

/**
 * Teste de atualização de uma conta a pagar.
 */
it('can update an account payable', function () {
    $user = User::factory()->create();
    $accountPayable = AccountPayable::factory()->create(['user_id' => $user->id]);

    $payload = ['description' => 'Updated Account'];

    actingAs($user)
        ->withSession(['_token' => csrf_token()])
        ->put("/accounts-payable/{$accountPayable->id}", $payload)
        ->assertStatus(200)
        ->assertJsonFragment(['description' => 'Updated Account']);
});

/**
 * Teste de exclusão de uma conta a pagar.
 */
it('can delete an account payable', function () {
    $user = User::factory()->create();
    $accountPayable = AccountPayable::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->withSession(['_token' => csrf_token()])
        ->delete("/accounts-payable/{$accountPayable->id}")
        ->assertStatus(200);

    $this->assertDatabaseMissing('accounts_payable', ['id' => $accountPayable->id]);
});

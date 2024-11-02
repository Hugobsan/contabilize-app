<?php

namespace Tests\Feature;

use App\Models\AccountReceivable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountReceivableControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_accounts_receivable()
    {
        $user = User::factory()->create();
        AccountReceivable::factory()->count(5)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get('/accounts-receivable')
            ->assertStatus(200)
            ->assertJsonCount(5);
    }

    /** @test */
    public function it_can_create_an_account_receivable()
    {
        $user = User::factory()->create();
        $payload = [
            'description' => 'Test Account',
            'value' => 500.00,
            'due_date' => now()->addDays(5)->toDateString(),
            'status' => true,
            'category' => 'salario',
            'recurrence_period' => 'monthly',
        ];

        $this->actingAs($user)
            ->post('/accounts-receivable', $payload)
            ->assertStatus(201)
            ->assertJsonFragment(['description' => 'Test Account']);
    }

    /** @test */
    public function it_can_update_an_account_receivable()
    {
        $user = User::factory()->create();
        $accountReceivable = AccountReceivable::factory()->create(['user_id' => $user->id]);
        $payload = ['description' => 'Updated Account'];

        $this->actingAs($user)
            ->put("/accounts-receivable/{$accountReceivable->id}", $payload)
            ->assertStatus(200)
            ->assertJsonFragment(['description' => 'Updated Account']);
    }

    /** @test */
    public function it_can_delete_an_account_receivable()
    {
        $user = User::factory()->create();
        $accountReceivable = AccountReceivable::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete("/accounts-receivable/{$accountReceivable->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('accounts_receivable', ['id' => $accountReceivable->id]);
    }
}

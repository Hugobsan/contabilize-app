<?php

namespace Tests\Unit;

use App\Models\AccountReceivable;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountReceivableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $accountReceivable = AccountReceivable::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $accountReceivable->user);
        $this->assertEquals($user->id, $accountReceivable->user->id);
    }

    /** @test */
    public function it_has_correct_attributes()
    {
        $accountReceivable = AccountReceivable::factory()->create([
            'description' => 'Test Description',
            'value' => 150.50,
            'status' => true,
        ]);

        $this->assertEquals('Test Description', $accountReceivable->description);
        $this->assertEquals(150.50, $accountReceivable->value);
        $this->assertTrue($accountReceivable->status);
    }
}

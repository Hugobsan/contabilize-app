<?php

namespace Database\Factories;

use App\Models\CreditCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditCardFactory extends Factory
{
    protected $model = CreditCard::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'nickname' => $this->faker->word,
            'credit_limit' => $this->faker->randomFloat(2, 1000, 10000),
            'available_limit' => $this->faker->randomFloat(2, 500, 10000),
        ];
    }
}

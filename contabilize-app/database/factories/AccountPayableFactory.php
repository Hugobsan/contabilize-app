<?php

namespace Database\Factories;

use App\Enums\CategoryEnum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountPayable>
 */
class AccountPayableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'description' => $this->faker->sentence(3), 
            'value' => $this->faker->randomFloat(2, 50, 1000),
            'due_date' => Carbon::now()->addDays($this->faker->numberBetween(1, 60)),
            'status' => $this->faker->boolean(30),
            'category' => $this->faker->randomElement(CategoryEnum::cases())->value
        ];
    }
}

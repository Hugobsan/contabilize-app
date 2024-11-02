<?php

namespace Database\Factories;

use App\Enums\ReceivableCategoryEnum;
use App\Enums\RecurrencePeriodEnum;
use App\Models\AccountReceivable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountReceivable>
 */
class AccountReceivableFactory extends Factory
{
    protected $model = AccountReceivable::class;

    public function definition(): array
    {
        $dueDate = Carbon::parse($this->faker->dateTimeBetween('now', '+1 year'));

        // Decide se a receita será recorrente (50% de chance)
        $isRecurring = $this->faker->boolean(50);
        $recurrencePeriod = $isRecurring ? $this->faker->randomElement(RecurrencePeriodEnum::cases()) : null;

        $nextDueDate = $isRecurring 
            ? Carbon::parse($this->faker->dateTimeBetween($dueDate->copy()->addDay(), '+1 year'))
            : null;

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'description' => $this->faker->sentence(3),
            'value' => $this->faker->randomFloat(2, 100, 10000),
            'due_date' => $dueDate,
            'next_due_date' => $nextDueDate,
            'status' => $this->faker->boolean,
            'category' => $this->faker->randomElement(array_map(fn($e) => $e->value, ReceivableCategoryEnum::cases())),
            'recurrence_period' => $recurrencePeriod ? $recurrencePeriod->value : null,
        ];
    }
}

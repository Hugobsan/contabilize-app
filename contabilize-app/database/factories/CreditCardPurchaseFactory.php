<?php

namespace Database\Factories;

use App\Models\CreditCard;
use App\Models\CreditCardPurchase;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\CategoryEnum;

class CreditCardPurchaseFactory extends Factory
{
    protected $model = CreditCardPurchase::class;

    public function definition(): array
    {
        return [
            'credit_card_id' => CreditCard::factory(), // Cria um cartão de crédito associado
            'description' => $this->faker->sentence(3), // Descrição da compra
            'amount' => $this->faker->randomFloat(2, 50, 2000), // Valor da compra entre 50 e 2000
            'purchase_date' => $this->faker->dateTimeBetween('-1 year', 'now'), // Data da compra aleatória
            'category' => $this->faker->randomElement(CategoryEnum::cases()), // Categoria baseada no enum
            'installments_count' => $this->faker->numberBetween(1, 12), // Número de parcelas entre 1 e 12
        ];
    }
}

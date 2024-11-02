<?php

namespace Database\Factories;

use App\Models\CreditCardPurchase;
use App\Models\PurchaseInstallment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class PurchaseInstallmentFactory extends Factory
{
    protected $model = PurchaseInstallment::class;

    public function definition(): array
    {
        $purchase = CreditCardPurchase::factory()->create();
        $installmentNumber = $this->faker->numberBetween(1, $purchase->installments_count);
        $dueDate = Carbon::parse($purchase->purchase_date)->addMonths($installmentNumber - 1);

        return [
            'credit_card_purchase_id' => $purchase->id,
            'installment_number' => $installmentNumber,
            'due_date' => $dueDate, // Data de vencimento calculada
            'amount' => $purchase->amount / $purchase->installments_count, // Valor dividido por parcelas
            'status' => $this->faker->boolean(70), // Status com 70% de chance de ser `false` (pendente)
        ];
    }
}

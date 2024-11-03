<?php

namespace Database\Seeders;

use App\Models\CreditCard;
use App\Models\CreditCardPurchase;
use Illuminate\Database\Seeder;

class CreditCardPurchaseSeeder extends Seeder
{
    public function run()
    {
        $creditCards = CreditCard::all();

        foreach ($creditCards as $creditCard) {
            $availableLimit = $creditCard->available_limit;

            while ($availableLimit > 0) {
                $purchaseAmount = rand(100, 1000);

                if ($purchaseAmount <= $availableLimit) {
                    CreditCardPurchase::factory()->create([
                        'credit_card_id' => $creditCard->id,
                        'amount' => $purchaseAmount,
                    ]);

                    $availableLimit -= $purchaseAmount;
                } else {
                    break;
                }
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CreditCardPurchase;

class CreditCardPurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria 30 compras associadas a cartões de crédito existentes
        CreditCardPurchase::factory()->count(30)->create();
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PurchaseInstallment;

class PurchaseInstallmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria 100 parcelas associadas a compras de cartão de crédito existentes
        PurchaseInstallment::factory()->count(100)->create();
    }
}

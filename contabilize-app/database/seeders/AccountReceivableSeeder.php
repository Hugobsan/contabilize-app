<?php

namespace Database\Seeders;

use App\Models\AccountReceivable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountReceivableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccountReceivable::factory()->count(50)->create();
    }
}

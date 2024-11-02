<?php

namespace Database\Seeders;

use App\Models\AccountPayable;
use Illuminate\Database\Seeder;

class AccountPayableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccountPayable::factory(50)->create();
    }
}

<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::create(['name' => 'Cash', 'slug' => 'cash']);
        PaymentMethod::create(['name' => 'QRIS', 'slug' => 'qris']);
        PaymentMethod::create(['name' => 'Bank Transfer', 'slug' => 'bank-transfer']);
        PaymentMethod::create(['name' => 'Deposit', 'slug' => 'deposit']);
        PaymentMethod::create(['name' => 'Credit Card', 'slug' => 'credit-card']);
        PaymentMethod::create(['name' => 'Debit Card', 'slug' => 'debit-card']);
    }
}

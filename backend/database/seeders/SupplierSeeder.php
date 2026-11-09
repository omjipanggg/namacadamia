<?php

namespace Database\Seeders;

use App\Models\Supplier;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create([
            'name' => 'ENIN Collection',
            'phone' => '000000000000',
            'email' => 'enin.collection@namada.com',
            'joined_at' => now(),
        ]);
    }
}

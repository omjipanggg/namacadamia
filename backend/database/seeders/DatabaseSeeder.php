<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,
            UserSeeder::class,
            // CustomerSeeder::class,
            MeasureUnitSeeder::class,
            SupplierSeeder::class,
            PaymentSeeder::class,
            ProductCategorySeeder::class,
            ProductAttributeSeeder::class,
            // ProductSeeder::class,
            // StockSeeder::class,
            SalesChannelSeeder::class,
            BlogSeeder::class,
        ]);
    }
}

/*
* branch
* measureUnit
* supplier
* productCategory
* productAttribute
* product
* productSku
* stock
* user
*/
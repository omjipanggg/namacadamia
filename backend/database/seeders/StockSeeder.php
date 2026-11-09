<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ProductSku;
use App\Models\Stock;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all();
        $skus = ProductSku::all();

        foreach ($branches as $branch) {
            foreach ($skus as $sku) {
                Stock::firstOrCreate([
                    'branch_id' => $branch->id,
                    'product_sku_id' => $sku->id, 
                ], [
                    'quantity' => fake()->numberBetween(10, 42),
                    'quantity_minimum' => 1,
                    'quantity_maximum' => 5,
                    'quantity_reserved' => 0,
                ]);
            }
        }
    }
}

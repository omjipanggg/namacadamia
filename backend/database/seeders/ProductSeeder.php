<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductCategory;
use App\Models\ProductSku;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Gamis Aisha',
            'Gamis Bunda',
            'Gamis Naya',
            'Bunaya Bumil',
            'Sportswear Rajut',
            'Pashmina Polos',
            'Pashmina Motif',
            'Mukena Besar',
            'Mukena Kecil',
        ];

        $allCategoryIds = ProductCategory::pluck('id');
        $allAttributeValueIds = ProductAttributeValue::pluck('id');

        if ($allCategoryIds->isEmpty() || $allAttributeValueIds->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($names, $allCategoryIds, $allAttributeValueIds) {
            foreach ($names as $name) {
                $categoryCount = min(rand(1, 3), $allCategoryIds->count());
                $attributeCount = min(rand(1, 3), $allAttributeValueIds->count());

                $selectedCategoryIds = $allCategoryIds->random($categoryCount);
                $selectedAttributeIds = $allAttributeValueIds->random($attributeCount);

                $product = Product::factory()->create([
                    'name' => $name,
                    'slug' => Str::slug($name) . '-' . Str::lower(Str::random(6)),
                ]);

                $product->categories()->syncWithoutDetaching($selectedCategoryIds);

                $skus = ProductSku::factory(rand(1, 3))->create([
                    'product_id' => $product->id,
                ]);

                foreach ($skus as $sku) {
                    $sku->variants()->syncWithoutDetaching($selectedAttributeIds);
                }
            }
        });
    }
}
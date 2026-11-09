<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductAttribute::create(['name' => 'Color', 'slug' => 'color', 'sort_order' => 1]);
        ProductAttribute::create(['name' => 'Size', 'slug' => 'size', 'sort_order' => 2]);
        ProductAttribute::create(['name' => 'Weight', 'slug' => 'weight', 'sort_order' => 3, 'is_active' => false]);
        ProductAttribute::create(['name' => 'Material', 'slug' => 'material', 'sort_order' => 4]);

        ProductAttributeValue::create(['product_attribute_id' => 1, 'value' => 'Merah', 'abbr' => 'RED']);
        ProductAttributeValue::create(['product_attribute_id' => 1, 'value' => 'Hitam', 'abbr' => 'BLK']);
        ProductAttributeValue::create(['product_attribute_id' => 1, 'value' => 'Krem', 'abbr' => 'BEI']);
        ProductAttributeValue::create(['product_attribute_id' => 1, 'value' => 'Cokelat', 'abbr' => 'BRO']);
        ProductAttributeValue::create(['product_attribute_id' => 1, 'value' => 'Peach', 'abbr' => 'PEA']);
        ProductAttributeValue::create(['product_attribute_id' => 2, 'value' => 'All Size', 'sort_order' => 1, 'abbr' => 'ALL']);
        ProductAttributeValue::create(['product_attribute_id' => 2, 'value' => 'S', 'sort_order' => 2, 'abbr' => 'S01']);
        ProductAttributeValue::create(['product_attribute_id' => 2, 'value' => 'M', 'sort_order' => 3, 'abbr' => 'M01']);
        ProductAttributeValue::create(['product_attribute_id' => 2, 'value' => 'L', 'sort_order' => 4, 'abbr' => 'L01']);
        ProductAttributeValue::create(['product_attribute_id' => 2, 'value' => 'XL', 'sort_order' => 5, 'abbr' => 'XL1']);
        ProductAttributeValue::create(['product_attribute_id' => 3, 'value' => 'n < 1kg', 'sort_order' => 1, 'abbr' => '1KG']);
        ProductAttributeValue::create(['product_attribute_id' => 3, 'value' => '1kg > n > 5kg', 'sort_order' => 2, 'abbr' => '5KG']);
        ProductAttributeValue::create(['product_attribute_id' => 3, 'value' => '5kg > n', 'sort_order' => 3, 'abbr' => '9KG']);
        ProductAttributeValue::create(['product_attribute_id' => 4, 'value' => 'Linen', 'abbr' => 'LIN']);
        ProductAttributeValue::create(['product_attribute_id' => 4, 'value' => 'Rayon', 'abbr' => 'RAY']);
        ProductAttributeValue::create(['product_attribute_id' => 4, 'value' => 'Silk', 'abbr' => 'SIL']);
        ProductAttributeValue::create(['product_attribute_id' => 4, 'value' => 'Cotton', 'abbr' => 'COT']);
        ProductAttributeValue::create(['product_attribute_id' => 4, 'value' => 'Polyester', 'abbr' => 'PLY']);
    }
}

<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Support\Str;

/**
 * @extends Factory<ProductSku>
 */
class ProductSkuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cost_price = fake()->numberBetween(100000, 200000);
        
        return [
            'code' => Str::upper(fake()->unique()->bothify('SKU-####-#######')),
            'barcode' => fake()->ean13(),
            'cost_price' => $cost_price,
            'selling_price' => $cost_price + fake()->numberBetween(50000, 150000),
            'minimum_stock' => fake()->numberBetween(1, 10),
            'is_active' => fake()->boolean(82),
            'width' => fake()->optional()->randomFloat(2,6,80),
            'weight' => fake()->optional()->randomFloat(2,0.10,5),
            'length' => fake()->optional()->randomFloat(2,5,80),
            'height' => fake()->optional()->randomFloat(2,1,30),
            'description' => fake()->sentence(),
        ];
    }
}

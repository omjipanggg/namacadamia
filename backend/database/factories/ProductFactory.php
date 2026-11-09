<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $names = [
        //     'Gamis Aisha',
        //     'Gamis Bunda',
        //     'Gamis Naya',
        //     'Bunaya Bumil',
        //     'Blouse Bumil',
        //     'Sportswear Rajut',
        //     'Pashmina Polos',
        //     'Pashmina Motif',
        //     'Mukena Besar',
        //     'Mukena Kecil',
        // ];

        // $name = Arr::random($names);

        return [
            'code' => Str::upper(Str::random(6)),
            // 'name' => $name,
            // 'image_url' => fake()->imageUrl(640, 480, 'products', true),
            // 'slug' => Str::slug($name) . '-' . Str::lower(Str::random(6)),
            'price' => fake()->randomFloat(2, 1, 9),
            'is_active' => fake()->boolean(80),
            'description' => fake()->sentence(),
        ];
    }
}

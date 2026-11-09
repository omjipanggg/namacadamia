<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {               
        $categories = [
            ['name' => 'Gamis', 'slug' => 'gamis',],
            ['name' => 'Hijab', 'slug' => 'hijab',],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }

        // ProductCategory::factory()->count(20)->create();
    }
}

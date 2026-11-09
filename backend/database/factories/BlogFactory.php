<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->sentence();
        $shortenedName = Str::limit($name, 45, '', preserveWords: true);
        
        return [
            'name' => Str::headline($name),
            'content' => implode(' ', fake()->paragraphs(5, false)),
            'is_active' => fake()->boolean(80),
            'slug' => Str::slug($shortenedName) . '-' . Str::lower(Str::random(6)),
            'description' => fake()->text(),
            'created_by' => 1,
        ];
    }
}

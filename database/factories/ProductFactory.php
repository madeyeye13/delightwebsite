<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
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
        return [
            'name' => fake()->unique()->words(3, true),
            'slug' => fn (array $attrs) => Str::slug($attrs['name']),
            'price' => fake()->numberBetween(1000, 50000),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'status' => 'active',
        ];
    }
}

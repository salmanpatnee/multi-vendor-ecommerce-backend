<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
            'brand_id' => fake()->numberBetween(1, 3), 
            'user_id' => fake()->numberBetween(1, 2), 
            'name' => fake()->word(), 
            'qty' => fake()->numberBetween(1, 20), 
            'price' => fake()->numberBetween(10, 2000),
            'short_desc' => fake()->sentence(20), 
            'desc' => fake()->paragraph(5),
            'is_hot' => fake()->numberBetween(0, 1),
            'is_featured' => fake()->numberBetween(0, 1),
            'is_offer' => fake()->numberBetween(0, 1),
            'is_deal' => fake()->numberBetween(0, 1),
        ];
    }
}

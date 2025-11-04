<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

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
            'name' => fake()->word(2, true),
            'price' => fake()->randomFloat(2, 1, 10),
            'description' => fake()->sentence(),
            'in_stock' => fake()->boolean(80),
            'supplier_id' => Supplier::inRandomOrder()->first()?->id ?? Supplier::factory(),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'price' => $this->faker->randomFloat(2, 0, 1000),
            'stock' => $this->faker->randomNumber(),
            'status' => $this->faker->numberBetween(0, 5),
            'version' => $this->faker->randomNumber(),
        ];
    }
}

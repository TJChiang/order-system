<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => $this->faker->numberBetween(1, 100),
            'product_id' => $this->faker->numberBetween(1, 100),
            'product_name' => $this->faker->name,
            'sku' => $this->faker->uuid,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'quantity' => $this->faker->numberBetween(1, 10),
            'total' => $this->faker->randomFloat(2, 1, 100000),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'channel' => $this->faker->randomElement(['Amazon', 'Momo', 'HKTV', 'PChome']),
            'order_number' => $this->faker->unique()->randomLetter(),
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'customer_phone' => $this->faker->phoneNumber(),
            'shipping_address' => $this->faker->address(),
            'status' => $this->faker->numberBetween(0, 5),
            'total_amount' => $this->faker->randomFloat(2, 10, 10000),
            'shipping_fee' => $this->faker->randomFloat(2, 0, 1000),
            'discount' => $this->faker->randomFloat(2, 0, 1000),
            'discount_rate' => $this->faker->randomFloat(2, 0, 1),
            'order_date' => $this->faker->dateTime(),
        ];
    }
}

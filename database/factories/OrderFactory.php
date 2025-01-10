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
            'channel' => $this->faker->randomElement(['amazon', 'momo', 'hktvmall', 'pchome']),
            'order_number' => $this->faker->unique()->randomLetter(),
            'recipient_name' => $this->faker->name(),
            'recipient_email' => $this->faker->email(),
            'recipient_phone' => $this->faker->e164PhoneNumber(),
            'shipping_address' => $this->faker->address(),
            'status' => $this->faker->numberBetween(0, 5),
            'total_amount' => $this->faker->randomFloat(2, 10, 10000),
            'shipping_fee' => $this->faker->randomFloat(2, 0, 1000),
            'discount' => $this->faker->randomFloat(2, 0, 1000),
            'discount_rate' => $this->faker->randomFloat(2, 0, 1),
            'remark' => $this->faker->sentence(),
            'ordered_at' => $this->faker->dateTime(),
        ];
    }
}

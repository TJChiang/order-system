<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => $this->faker->numberBetween(1, 100),
            'shipment_number' => $this->faker->regexify('[A-Z]{2}[0-9]{8}'),
            'courier' => $this->faker->company,
            'tracking_number' => $this->faker->regexify('[A-Z]{2}[0-9]{8}'),
            'status' => $this->faker->numberBetween(0, 5),
            'shipped_at' => $this->faker->dateTime(),
            'delivered_at' => $this->faker->dateTime(),
            'remark' => $this->faker->text,
        ];
    }
}

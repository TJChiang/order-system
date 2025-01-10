<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShipmentItem>
 */
class ShipmentItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shipment_id' => $this->faker->numberBetween(1, 100),
            'order_item_id' => $this->faker->numberBetween(1, 100),
            'quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}

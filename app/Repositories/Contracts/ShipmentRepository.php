<?php

namespace App\Repositories\Contracts;

use App\Models\Shipment;
use InvalidArgumentException;

interface ShipmentRepository
{
    /**
     * @throws InvalidArgumentException
     */
    public function create(array $data, ?int $orderId = null): Shipment;

    public function createMany(array $data): bool;

    /**
     * @throws InvalidArgumentException
     */
    public function createWithItems(array $data, array $items, ?int $orderId = null): Shipment;
}

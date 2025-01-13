<?php

namespace App\Repositories\Contracts;

interface ShipmentItemRepository
{
    public function deleteByShipmentId(int|array $shipmentId, ?int $quantity = null, string $operator = '='): void;
}

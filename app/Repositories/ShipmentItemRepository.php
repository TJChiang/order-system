<?php

namespace App\Repositories;

use App\Models\ShipmentItem;
use App\Repositories\Contracts\ShipmentItemRepository as ShipmentItemRepositoryContract;

class ShipmentItemRepository implements ShipmentItemRepositoryContract
{
    public function __construct(protected readonly ShipmentItem $model)
    {
    }

    public function deleteByShipmentId(int|array $shipmentId, ?int $quantity = null, string $operator = '='): void
    {
        if (is_int($shipmentId)) {
            $shipmentId = [$shipmentId];
        }

        $query = $this->model->newQuery()->whereIn('shipment_id', $shipmentId);
        if ($quantity !== null) {
            $query->where('quantity', $operator, $quantity);
        }
        $query->delete();
    }
}

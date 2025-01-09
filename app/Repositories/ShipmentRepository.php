<?php

namespace App\Repositories;

use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepository as ShipmentRepositoryContract;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ShipmentRepository implements ShipmentRepositoryContract
{
    public function __construct(protected readonly Shipment $model)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function create(array $data, ?int $orderId = null): Shipment
    {
        if (empty($orderId) && empty($data['order_id'])) {
            throw new InvalidArgumentException('Order ID is required.');
        }
        $data['order_id'] = $data['order_id'] ?? $orderId;
        return $this->model->newQuery()->create($data);
    }

    public function createMany(array $data): Collection
    {
        return $this->model->newQuery()->createMany($data);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createWithItems(array $data, array $items, ?int $orderId = null): Shipment
    {
        $shipment = $this->create($data, $orderId);
        $shipment->orderItems()->createMany($items);

        return $shipment;
    }
}

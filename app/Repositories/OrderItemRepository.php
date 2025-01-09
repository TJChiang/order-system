<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\Contracts\OrderItemRepository as OrderItemRepositoryContract;
use InvalidArgumentException;

class OrderItemRepository implements OrderItemRepositoryContract
{
    public function __construct(protected readonly OrderItem $model)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function create(array $data, ?int $orderId = null): OrderItem
    {
        if (empty($orderId) && empty($data['order_id'])) {
            throw new InvalidArgumentException('Order ID is required.');
        }
        $data['order_id'] = $data['order_id'] ?? $orderId;

        return $this->model->newQuery()->create($data);
    }

    public function createMany(array $data): bool
    {
        return $this->model->newQuery()->insert($data);
    }
}

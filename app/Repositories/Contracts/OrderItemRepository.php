<?php

namespace App\Repositories\Contracts;

use App\Models\OrderItem;
use Illuminate\Support\Collection;
use InvalidArgumentException;

interface OrderItemRepository
{
    /**
     * @throws InvalidArgumentException
     */
    public function create(array $data, ?int $orderId = null): OrderItem;

    public function createMany(array $data): Collection;
}

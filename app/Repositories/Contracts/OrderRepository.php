<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Support\Collection;
use InvalidArgumentException;

interface OrderRepository
{
    public function getList(
        array $filters = [],
        array $columns = ['*'],
        int $offset = 0,
        int $limit = 50,
    ): Collection;

    public function create(array $data): Order;

    public function createMany(array $data): bool;

    /**
     * @throws InvalidArgumentException
     */
    public function createWithItems(array $data, array $items = []): Order;

    /**
     * @throws InvalidArgumentException
     */
    public function createManyWithItems(array $data): Collection;
}

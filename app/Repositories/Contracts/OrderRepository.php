<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

interface OrderRepository
{
    public function find(int $id, array $with = []): ?Order;

    /**
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $id, array $with = []): Order;

    public function getList(
        array $filters = [],
        array $columns = [],
        array $with = [],
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

    /**
     * @throws ModelNotFoundException
     */
    public function updateById(int $id, array $data): Order;

    public function updateByEntity(Order $order, array $data): Order;

    public function deleteById(int $id): void;
}

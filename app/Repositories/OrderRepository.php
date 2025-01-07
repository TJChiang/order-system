<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepository as OrderRepositoryContract;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class OrderRepository implements OrderRepositoryContract
{
    public function __construct(protected readonly Order $model)
    {
    }

    public function create(array $data): Order
    {
        return $this->model->newQuery()->create($data);
    }

    public function createMany(array $data): Collection
    {
        return $this->model->newQuery()->createMany($data);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createWithItems(array $data, array $items = []): Order
    {
        if (empty($items) && empty($data['items'])) {
            throw new InvalidArgumentException('Items cannot be empty.');
        }

        $items = empty($items) ? $data['items'] : $items;
        $entity = $this->create($data);
        $entity->items()->createMany($items);
        return $entity;
    }

    /**
     * @todo 優化批量 create
     *
     * @throws InvalidArgumentException
     */
    public function createManyWithItems(array $data): Collection
    {
        $orderCollection = collect([]);
        foreach ($data as $order) {
            $orderCollection->push($this->createWithItems($order, $order['items']));
        }

        return $orderCollection;
    }
}

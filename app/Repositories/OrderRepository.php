<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepository as OrderRepositoryContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class OrderRepository implements OrderRepositoryContract
{
    public function __construct(protected readonly Order $model)
    {
    }

    public function getList(
        array $filters = [],
        array $columns = [],
        int $offset = 0,
        int $limit = 50,
    ): Collection {
        $query = $this->model->newQuery()
            ->defaultSelect($columns);

        if (!empty($filters['start_time'])) {
            $query->where('ordered_at', '>=', $filters['start_time']);
        }
        if (!empty($filters['end_time'])) {
            $query->where('ordered_at', '<=', $filters['end_time']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['channel'])) {
            $query->where('channel', $filters['channel']);
        }
        if (!empty($filters['order_number'])) {
            $query->where('order_number', $filters['order_number']);
        }

        return $query->orderBy('ordered_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function create(array $data): Order
    {
        return $this->model->newQuery()->create($data);
    }

    public function createMany(array $data): bool
    {
        return $this->model->newQuery()->insert($data);
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
        $entity = $this->create(Arr::except($data, 'items'));
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

<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepository as OrderRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderRepository implements OrderRepositoryContract
{
    public function __construct(protected readonly Order $model)
    {
    }

    public function find(int $id, array $with = []): ?Order
    {
        return $this->model->newQuery()
            ->with($with)
            ->find($id);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $id, array $with = []): Order
    {
        return $this->model->newQuery()
            ->with($with)
            ->findOrFail($id);
    }

    public function getList(
        array $filters = [],
        array $columns = [],
        array $with = [],
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

        return $query->with($with)
            ->orderBy('ordered_at', 'desc')
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

    /**
     * @throws ModelNotFoundException
     */
    public function updateById(int $id, array $data): Order
    {
        $order = $this->model->newQuery()->find($id);
        if ($order === null) {
            throw new ModelNotFoundException();
        }

        if (!empty($data['recipient_name'])) {
            $order->recipient_name = $data['recipient_name'];
        }
        if (!empty($data['recipient_email'])) {
            $order->recipient_email = $data['recipient_email'];
        }
        if (!empty($data['recipient_phone'])) {
            $order->recipient_phone = $data['recipient_phone'];
        }
        if (!empty($data['shipping_address'])) {
            $order->shipping_address = $data['shipping_address'];
        }
        if (!empty($data['scheduled_shipping_date'])) {
            $order->scheduled_shipping_date = $data['scheduled_shipping_date'];
        }
        if (isset($data['status']) && $data['status'] >= 0) {
            $order->status = $data['status'];
        }
        if (isset($data['remark'])) {
            $order->remark = $data['remark'];
        }

        $order->save();
        return $order;
    }

    public function updateByEntity(Order $order, array $data): Order
    {
        if (!empty($data['recipient_name'])) {
            $order->recipient_name = $data['recipient_name'];
        }
        if (!empty($data['recipient_email'])) {
            $order->recipient_email = $data['recipient_email'];
        }
        if (!empty($data['recipient_phone'])) {
            $order->recipient_phone = $data['recipient_phone'];
        }
        if (!empty($data['shipping_address'])) {
            $order->shipping_address = $data['shipping_address'];
        }
        if (!empty($data['scheduled_shipping_date'])) {
            $order->scheduled_shipping_date = $data['scheduled_shipping_date'];
        }
        if (isset($data['status']) && $data['status'] >= 0) {
            $order->status = $data['status'];
        }
        if (isset($data['remark'])) {
            $order->remark = $data['remark'];
        }

        $order->save();
        return $order;
    }

    public function deleteById(int $id): void
    {
        $order = $this->model->newQuery()->find($id);
        if ($order === null) {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->shipmentItems()->delete();
            $order->shipments()->delete();
            $order->items()->delete();
            $order->delete();
        });
    }
}

<?php

namespace App\Repositories;

use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepository as ShipmentRepositoryContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ShipmentRepository implements ShipmentRepositoryContract
{
    public function __construct(protected readonly Shipment $model)
    {
    }

    public function getById(int|array $id, array $columns = ['*'], array $with = []): Collection
    {
        return is_integer($id)
            ? $this->get(['id' => $id], $columns, $with)
            : $this->get(['ids' => $id], $columns, $with);
    }

    public function getByShipmentNumber(
        string|array $shipmentNumber,
        array $columns = ['*'],
        array $with = []
    ): Collection {
        return is_string($shipmentNumber)
            ? $this->get(['shipment_number' => $shipmentNumber], $columns, $with)
            : $this->get(['shipment_numbers' => $shipmentNumber], $columns, $with);
    }

    public function get(
        array $filter = [],
        array $columns = ['*'],
        array $with = [],
        int $offset = 0,
        int $limit = 100,
    ): Collection {
        $query = $this->model->newQuery();

        if (!empty($with)) {
            $query->with($with);
        }
        if (!empty($filter['id'])) {
            is_array($filter['id'])
                ? $query->whereIn('id', $filter['id'])
                : $query->where('id', $filter['id']);
        }
        if (!empty($filter['order_id'])) {
            $query->where('order_id', $filter['order_id']);
        }
        if (!empty($filter['shipment_number'])) {
            is_array($filter['shipment_number'])
                ? $query->whereIn('shipment_number', $filter['shipment_number'])
                : $query->where('shipment_number', $filter['shipment_number']);
        }
        if (isset($filter['status']) && is_numeric($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        if (!empty($filter['courier'])) {
            $query->where('courier', $filter['courier']);
        }
        if (!empty($filter['tracking_number'])) {
            is_array($filter['tracking_number'])
                ? $query->whereIn('tracking_number', $filter['tracking_number'])
                : $query->where('tracking_number', $filter['tracking_number']);
        }
        if (!empty($filter['start_shipped_time'])) {
            $query->where('shipped_at', '>=', $filter['start_shipped_time']);
        }
        if (!empty($filter['end_shipped_time'])) {
            $query->where('shipped_at', '<=', $filter['end_shipped_time']);
        }
        if (!empty($filter['start_delivered_time'])) {
            $query->where('delivered_at', '>=', $filter['start_delivered_time']);
        }
        if (!empty($filter['end_delivered_time'])) {
            $query->where('delivered_at', '<=', $filter['end_delivered_time']);
        }

        return $query
            ->offset($offset)
            ->limit($limit)
            ->get($columns);
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

    public function createMany(array $data): bool
    {
        return $this->model->newQuery()->insert($data);
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

    public function upsert(array $data, array|string $updateFields, ?array $update = null): void
    {
        $this->model->newQuery()->upsert($data, $updateFields, $update);
    }

    public function deleteById(int|array $id, bool $single = false): void
    {
        if (is_int($id)) {
            $id = [$id];
        }

        if ($single) {
            $this->deleteByIdWithSingle($id);
            return;
        }

        $entities = $this->model->newQuery()->whereIn('id', $id)->with('orderItems')->get();
        if ($entities->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($entities, $id) {
            $entities->each(function (Shipment $entity) {
                $entity->orderItems()->detach();
            });
            $this->model->newQuery()->whereIn('id', $id)->delete();
        });
    }

    private function deleteByIdWithSingle(int|array $id): void
    {
        $this->model->newQuery()->whereIn('id', $id)->delete();
    }
}

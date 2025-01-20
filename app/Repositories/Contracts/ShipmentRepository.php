<?php

namespace App\Repositories\Contracts;

use App\Models\Shipment;
use Illuminate\Support\Collection;
use InvalidArgumentException;

interface ShipmentRepository
{
    public function getById(int|array $id, array $columns = ['*'], array $with = []): Collection;

    public function getByShipmentNumber(
        string|array $shipmentNumber,
        array $columns = ['*'],
        array $with = []
    ): Collection;

    public function get(
        array $filter = [],
        array $columns = ['*'],
        array $with = [],
        int $offset = 0,
        int $limit = 100,
    ): Collection;

    /**
     * @throws InvalidArgumentException
     */
    public function create(array $data, ?int $orderId = null): Shipment;

    public function createMany(array $data): bool;

    /**
     * @throws InvalidArgumentException
     */
    public function createWithItems(array $data, array $items, ?int $orderId = null): Shipment;

    public function upsert(array $data, array|string $updateFields, ?array $update = null): void;

    /**
     * @param int|array $id
     * @param bool $single 是否只刪除單個 table 資料，不處理關聯資料，預設為 false
     *
     * @return void
     */
    public function deleteById(int|array $id, bool $single = false): void;
}

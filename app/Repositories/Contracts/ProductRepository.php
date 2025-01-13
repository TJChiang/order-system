<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Support\Collection;

interface ProductRepository
{
    public function get(array $filter = [], array $columns = ['*']): Collection;

    public function find(int $id, array $columns = ['*'], array $with = []): ?Product;

    /**
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFailed(int $id, array $columns = ['*'], array $with = []): Product;

    public function getBySku(string $sku, array $columns = ['*']): Collection;

    public function getBySkuAndVersion(string $sku, int $version, array $columns = ['*']): Collection;

    public function getByIds(array $ids, array $columns = ['*']): Collection;

    public function create(array $data): Product;

    public function createMany(array $data): bool;

    public function deleteById(array|int $id): void;
}

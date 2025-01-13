<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepository as ProductRepositoryContract;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryContract
{
    public function __construct(protected readonly Product $model)
    {
    }

    public function get(array $filter = [], array $columns = ['*'], array $with = []): Collection
    {
        $query = $this->model->newQuery()->with($with);

        if (isset($filter['id'])) {
            $query->where('id', $filter['id']);
        }
        if (isset($filter['sku'])) {
            $query->where('sku', $filter['sku']);
        }
        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }
        if (isset($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        if (isset($filter['version'])) {
            $query->where('version', $filter['version']);
        }
        if (isset($filter['limit'])) {
            $query->limit($filter['limit']);
        }
        if (isset($filter['offset'])) {
            $query->offset($filter['offset']);
        }

        return $query->get($columns);
    }

    public function find(int $id, array $columns = ['*'], array $with = []): ?Product
    {
        return $this->model->newQuery()->with($with)->find($id, $columns);
    }

    public function findOrFailed(int $id, array $columns = ['*'], array $with = []): Product
    {
        return $this->model->newQuery()->with($with)->findOrFail($id, $columns);
    }

    public function getBySku(string $sku, array $columns = ['*']): Collection
    {
        return $this->get(['sku' => $sku], $columns);
    }

    public function getBySkuAndVersion(string $sku, int $version, array $columns = ['*']): Collection
    {
        return $this->get([
            'sku' => $sku,
            'version' => $version,
        ], $columns);
    }

    public function getByIds(array $ids, array $columns = ['*']): Collection
    {
        return $this->model->newQuery()
            ->whereIn('id', $ids)
            ->limit(100)
            ->get($columns);
    }

    public function create(array $data): Product
    {
        return $this->model->newQuery()->create($data);
    }

    public function createMany(array $data): bool
    {
        return $this->model->newQuery()->insert($data);
    }

    public function deleteById(array|int $id): void
    {
        if (is_int($id)) {
            $id = [$id];
        }

        $this->model->newQuery()->whereIn('id', $id)->delete();
    }
}

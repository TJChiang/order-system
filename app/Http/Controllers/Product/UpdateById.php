<?php

namespace App\Http\Controllers\Product;

use App\Exceptions\General\DataNotFoundException;
use App\Exceptions\General\InvalidDataException;
use App\Http\Requests\Product\UpdateByIdRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateById
{
    public function __construct(protected readonly ProductRepository $productRepository)
    {
    }

    /**
     * @throws DataNotFoundException
     * @throws InvalidDataException
     */
    public function __invoke(int $id, UpdateByIdRequest $request): JsonResource
    {
        try {
            $entity = $this->productRepository->findOrFailed($id);
        } catch (ModelNotFoundException) {
            throw new DataNotFoundException("Product not found: $id");
        }

        if ($this->needCreateNewVersion($request, $entity)) {
            $entity = $this->createNewVersionProduct($request, $entity);
        } else {
            if ($request->has('stock')) {
                $entity->stock = $request->input('stock');
            }
            if ($request->has('status')) {
                $entity->status = $request->input('status');
            }
            $entity->save();
        }

        return new ProductResource($entity);
    }

    /**
     * @throws InvalidDataException
     */
    private function createNewVersionProduct(Request $request, Product $oldVersionEntity): Product
    {
        $lastVersionEntity = $this->productRepository->findLastVersion($oldVersionEntity->sku);
        if ($lastVersionEntity === null) {
            throw new InvalidDataException("Invalid data from database: $oldVersionEntity->sku not found.");
        }

        return $this->productRepository->create([
            'sku' => $oldVersionEntity->sku,
            'name' => $request->input('name') ?? $oldVersionEntity->name,
            'description' => $request->has('description')
                ? $request->input('description')
                : $oldVersionEntity->description,
            'price' => $request->input('price') ?? $oldVersionEntity->price,
            'stock' => $request->input('stock') ?? $oldVersionEntity->stock,
            'status' => $request->input('status') ?? $oldVersionEntity->status,
            'version' => $lastVersionEntity->version + 1,
        ]);
    }

    private function needCreateNewVersion(Request $request, Product $entity): bool
    {
        if ($request->has('name') && $request->input('name') !== $entity->name) {
            return true;
        }
        if ($request->has('description') && $request->input('description') !== $entity->description) {
            return true;
        }
        if ($request->has('price') && $request->input('price') != $entity->price) {
            return true;
        }
        return false;
    }
}

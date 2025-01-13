<?php

namespace App\Http\Controllers\Product;

use App\Exceptions\General\DataNotFoundException;
use App\Http\Resources\ProductResource;
use App\Repositories\Contracts\ProductRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetById
{
    /**
     * @throws DataNotFoundException
     */
    public function __invoke(int $id, Request $request, ProductRepository $productRepository): JsonResource
    {
        try {
            $product = $productRepository->findOrFailed($id);
        } catch (ModelNotFoundException) {
            throw new DataNotFoundException("Product not found: $id");
        }

        return new ProductResource($product);
    }
}

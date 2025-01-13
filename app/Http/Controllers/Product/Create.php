<?php

namespace App\Http\Controllers\Product;

use App\Http\Requests\Product\CreateRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\Contracts\ProductRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class Create
{
    public function __invoke(CreateRequest $request, ProductRepository $productRepository): JsonResource
    {
        $entity = $productRepository->create([
            'sku' => Str::uuid()->toString(),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
            'status' => 0,
            'version' => 1,
        ]);

        return new ProductResource($entity);
    }
}

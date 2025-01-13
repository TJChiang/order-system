<?php

namespace App\Http\Controllers\Product;

use App\Http\Requests\Product\GetListRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\Contracts\ProductRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class GetList
{
    public function __invoke(GetListRequest $request, ProductRepository $productRepository): JsonResource
    {
        $limit = min($request->input('limit', 50), 50);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        $insertData = Arr::except($request->validated(), ['limit', 'page']);
        $insertData['limit'] = $limit;
        $insertData['offset'] = $offset;
        $collection = $productRepository->get($insertData);

        return ProductResource::collection($collection)
            ->additional([
                'page' => $page,
                'limit' => $limit,
                'count' => $collection->count(),
            ]);
    }
}

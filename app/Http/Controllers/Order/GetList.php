<?php

namespace App\Http\Controllers\Order;

use App\Http\Requests\Order\GetListRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\Contracts\OrderRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class GetList
{
    public function __invoke(GetListRequest $request, OrderRepository $orderRepository): JsonResource
    {
        $limit = min($request->input('limit', 50), 50);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        $collection = $orderRepository->getList(
            $request->validated(),
            [],
            ['shipments.orderItems'],
            $offset,
            $limit
        );

        return OrderResource::collection($collection)
            ->additional([
                'page' => $page,
                'limit' => $limit,
                'count' => $collection->count(),
            ]);
    }
}

<?php

namespace App\Http\Controllers\Order;

use App\Exceptions\General\DataNotFoundException;
use App\Http\Resources\OrderResource;
use App\Repositories\Contracts\OrderRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetById
{
    /**
     * @throws DataNotFoundException
     */
    public function __invoke(string $id, Request $request, OrderRepository $orderRepository): JsonResource
    {
        try {
            $order = $orderRepository->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new DataNotFoundException("Order not found: $id");
        }

        return new OrderResource($order);
    }
}

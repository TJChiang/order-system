<?php

namespace App\Http\Controllers\Order;

use App\Exceptions\General\DataNotFoundException;
use App\Http\Requests\Order\UpdateByIdRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\Contracts\OrderItemRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\ShipmentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UpdateById
{
    /**
     * @throws DataNotFoundException
     */
    public function __invoke(
        int $id,
        UpdateByIdRequest $request,
        OrderRepository $orderRepository,
        ShipmentRepository $shipmentRepository,
        OrderItemRepository $orderItemRepository,
    ): JsonResource {
        try {
            $orderEntity = $orderRepository->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new DataNotFoundException("Order not found: $id");
        }

        $inputData = $request->validated();

        try {
            $orderEntity = DB::transaction(function () use (
                $orderRepository,
                $shipmentRepository,
                $orderItemRepository,
                $orderEntity,
                $inputData,
            ) {
                $orderData = Arr::except($inputData, 'shipments');
                return $orderRepository->updateByEntity($orderEntity, $orderData);
            });
        } catch (ModelNotFoundException) {
            throw new DataNotFoundException("Shipments or order items not found: $id");
        }

        return new OrderResource($orderEntity);
    }
}

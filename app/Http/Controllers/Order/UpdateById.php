<?php

namespace App\Http\Controllers\Order;

use App\Exceptions\General\DataNotFoundException;
use App\Http\Requests\Order\UpdateByIdRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Repositories\Contracts\OrderItemRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\ShipmentItemRepository;
use App\Repositories\Contracts\ShipmentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UpdateById
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private readonly ShipmentItemRepository $shipmentItemRepository,
        private readonly OrderItemRepository $orderItemRepository,
    ) {
    }

    /**
     * @throws DataNotFoundException
     */
    public function __invoke(
        int $id,
        UpdateByIdRequest $request,
        OrderRepository $orderRepository,
    ): JsonResource {
        try {
            $orderEntity = $orderRepository->findOrFail($id, ['shipments.orderItems']);
        } catch (ModelNotFoundException) {
            throw new DataNotFoundException("Order not found: $id");
        }

        $inputData = $request->validated();

        try {
            $orderEntity = DB::transaction(function () use (
                $orderRepository,
                $orderEntity,
                $inputData,
            ) {
                if (isset($inputData['shipments'])) {
                    $this->updateShipments($orderEntity, $inputData['shipments']);
                }
                $orderData = Arr::except($inputData, 'shipments');
                return $orderRepository->updateByEntity($orderEntity, $orderData);
            });
        } catch (ModelNotFoundException) {
            throw new DataNotFoundException("Shipments or order items not found: $id");
        }

        return new OrderResource($orderEntity);
    }

    private function updateShipments(Order $order, array $shipments): void
    {
        // 要更新出貨單
        $needUpdates = [];
        // 要新增出貨單
        $needCreates = [];
        $updatedShipmentIds = [];

        foreach ($shipments as $shipment) {
            $shipment['order_id'] = $order->id;

            // 有指定出貨單 ID 表示要更新；否則表示要新增
            if (empty($shipment['id'])) {
                $needCreates[] = $shipment;
            } else {
                $needUpdates[] = $shipment;
                $updatedShipmentIds[] = $shipment['id'];
            }
        }

        // 處理要新增的出貨單
        if (!empty($needCreates)) {
            $this->shipmentRepository->createMany($needCreates);
            $numberIdMap = $this->shipmentRepository->getByShipmentNumber(
                Arr::pluck($needCreates, 'shipment_number'),
                ['id', 'shipment_number']
            )->keyBy('shipment_number');

            foreach ($needCreates as $shipment) {
                if (empty($shipment['items'])) {
                    continue;
                }

                $numberIdMap->get($shipment['shipment_number'])
                    ->orderItems()
                    ->attach(
                        collect($shipment['items'])->mapWithKeys(
                            fn ($item) => [$item['id'] => ['quantity' => $item['quantity']]]
                        )
                    );
            }
        }

        // 處理要更新的出貨單
        if (!empty($needUpdates)) {
            $this->shipmentRepository->upsert($needUpdates, ['id']);

            $numberIdMap = $this->shipmentRepository->getByShipmentNumber(
                Arr::pluck($needUpdates, 'id'),
                ['id']
            )->keyBy('id');

            foreach ($needUpdates as $shipment) {
                if (empty($shipment['items'])) {
                    continue;
                }

                $updatedEntity = $numberIdMap->get($shipment['id']);
                $updatedEntity->orderItems()->detach();

                $updatedEntity->orderItems()->attach(
                    collect($shipment['items'])->mapWithKeys(
                        fn ($item) => [$item['id'] => ['quantity' => $item['quantity']]]
                    )
                );
            }
        }

        // 刪除應該移除的出貨單
        $existingShipmentIds = $order->shipments->pluck('id')->filter()->values()->toArray();
        $deletedShipmentIds = array_diff($existingShipmentIds, $updatedShipmentIds);
        if (!empty($deletedShipmentIds) && !empty($updatedShipmentIds)) {
            $this->shipmentRepository->deleteById($deletedShipmentIds, true);
            $this->shipmentItemRepository->deleteByShipmentId($deletedShipmentIds);
        }
    }
}

<?php

namespace App\Order\Generator;

use App\Models\Order;
use App\Order\ChannelEnum;
use App\Repositories\Contracts\OrderItemRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\ShipmentRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

abstract class Generator
{
    protected ChannelEnum $channel;

    public function __construct(
        protected readonly OrderRepository $orderRepository,
        protected readonly OrderItemRepository $orderItemRepository,
        protected readonly ProductRepository $productRepository,
        protected readonly ShipmentRepository $shipmentRepository,
    ) {
    }

    /**
     * @todo 優化
     */
    public function generate(array $data): Collection
    {
        $products = $this->getProducts($data);

        return DB::transaction(function () use ($data, $products) {
            $collection = collect([]);

            foreach ($data as $orderData) {
                $orderEntity = $this->createOrder($orderData);
                $this->createShipments($orderData['shipments'], $orderEntity);
                $orderItemData = $this->flattenOrderItems($orderData)->toArray();
                $totalAmount = $this->createOrderItems($orderItemData, $orderEntity, $products);
                $orderEntity->update(['total_amount' => $totalAmount]);
                $collection->push($orderEntity);
            }
            return $collection;
        });
    }

    protected function flattenOrderItems(array $shipmentData): Collection
    {
        return collect($shipmentData)
            ->flatMap(fn (array $shipment) => array_map(
                function ($item) use ($shipment) {
                    $item['shipment_number'] = $shipment['shipment_number'];
                    return $item;
                },
                $shipment['items']
            ));
    }

    protected function createOrder(array $orderData): Order
    {
        $insertData = [
            'channel' => $this->channel->value,
            'order_number' => $orderData['order_number'],
            'user_id' => $orderData['user_id'] ?? null,
            'recipient_name' => $orderData['recipient_name'],
            'recipient_email' => $orderData['recipient_email'] ?? null,
            'recipient_phone' => $orderData['recipient_phone'] ?? null,
            'shipping_address' => $orderData['shipping_address'],
            'scheduled_shipping_date' => $orderData['scheduled_shipping_date'] ?? null,
            'status' => $orderData['status'],
            'shipping_fee' => $orderData['shipping_fee'] ?? 0,
            'remark' => $orderData['remark'] ?? null,
            'ordered_at' => $orderData['ordered_at'],
        ];
        return $this->orderRepository->create($insertData);
    }

    protected function createShipments(array $shipmentData, Order $order): Collection
    {
        $shipmentInsertData = [];
        foreach ($shipmentData as $shipment) {
            $shipmentInsertData[] = [
                'order_id' => $order->id,
                'shipment_number' => $shipment['shipment_number'],
                'courier' => $shipment['courier'],
                'tracking_number' => $shipment['tracking_number'],
                'status' => $shipment['status'] ?? 0,
                'remark' => $shipment['remark'],
            ];
        }
        return $this->shipmentRepository->createMany($shipmentInsertData);
    }

    protected function createOrderItems(array $orderItemData, Order $order, Collection $products): int
    {
        $totalAmount = 0;

        foreach ($orderItemData as $item) {
            $price = $products->get($item['product_id'])->price;
            $totalAmount += $price * $item['quantity'];

            $entity = $this->orderItemRepository->create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $products->get($item['product_id'])->name,
                'price' => $price,
                'sku' => $item['sku'],
                'quantity' => $item['quantity'],
            ]);
            $entity->shipments()->attach($entity->id, ['quantity' => $item['quantity']]);
        }

        return $totalAmount;
    }

    protected function getProducts(array $orders): Collection
    {
        $ids = collect($orders)
            ->flatMap(fn (array $order) => $order['shipments'])
            ->flatMap(fn (array $shipment) => $shipment['items'])
            ->pluck('product_id')
            ->unique()
            ->values()
            ->toArray();

        return $this->productRepository->getByIds($ids)->keyBy('id');
    }
}

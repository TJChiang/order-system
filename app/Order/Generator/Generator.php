<?php

namespace App\Order\Generator;

use App\Models\Order;
use App\Models\OrderItem;
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

    protected float $totalAmountPerOrder = 0;

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
                $this->totalAmountPerOrder = 0;

                $orderEntity = $this->createOrder($orderData);
                $this->createShipments($orderData['shipments'], $orderEntity, $products);
                $orderEntity->update(['total_amount' => $this->totalAmountPerOrder]);
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
            'total_amount' => 0,
            'status' => $orderData['status'],
            'shipping_fee' => $orderData['shipping_fee'] ?? 0,
            'remark' => $orderData['remark'] ?? null,
            'ordered_at' => $orderData['ordered_at'],
        ];
        return $this->orderRepository->create($insertData);
    }

    protected function createShipments(array $shipmentData, Order $order, Collection $products): void
    {
        foreach ($shipmentData as $shipment) {
            $orderItems = [];
            $entity = $this->shipmentRepository->create([
                'order_id' => $order->id,
                'shipment_number' => $shipment['shipment_number'],
                'courier' => $shipment['courier'],
                'tracking_number' => $shipment['tracking_number'],
                'status' => $shipment['status'] ?? 0,
                'remark' => $shipment['remark'] ?? null,
            ]);
            foreach ($shipment['items'] as $item) {
                $orderItemEntity = $this->createOrderItem($item, $order, $products);
                $orderItems[$orderItemEntity->id] = ['quantity' => $orderItemEntity->quantity];
            }
            $entity->orderItems()->attach($orderItems);
        }
    }

    protected function createOrderItem(array $orderItemData, Order $order, Collection $products): OrderItem
    {
        $price = $products->get($orderItemData['product_id'])->price;
        $total = $price * $orderItemData['quantity'];
        $this->totalAmountPerOrder += $total;

        return $this->orderItemRepository->create([
            'order_id' => $order->id,
            'product_id' => $orderItemData['product_id'],
            'product_name' => $products->get($orderItemData['product_id'])->name,
            'price' => $price,
            'sku' => $orderItemData['sku'],
            'quantity' => $orderItemData['quantity'],
            'total' => $total,
        ]);
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

<?php

namespace App\Order\Generator;

use App\Order\ChannelEnum;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\ProductRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

abstract class Generator
{
    protected ChannelEnum $channel;

    public function __construct(
        protected readonly OrderRepository $orderRepository,
        protected readonly ProductRepository $productRepository,
    ) {
    }

    public function generate(array $data): Collection
    {
        $products = $this->getProducts($data);

        return DB::transaction(function () use ($data, $products) {
            $collection = collect([]);

            foreach ($data as $orderData) {
                $totalAmount = 0;
                $orderItems = [];

                foreach ($orderData['items'] as $itemData) {
                    $product = $products->get($itemData['product_id']);
                    $subtotal = $product->price * $itemData['quantity'];

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'price' => $product->price,
                        'quantity' => $itemData['quantity'],
                        'total' => $subtotal,
                    ];

                    $totalAmount += $subtotal;
                }

                $insertData = [
                    'channel' => $this->channel->value,
                    'order_number' => $orderData['order_number'],
                    'user_id' => $orderData['user_id'] ?? null,
                    'recipient_name' => $orderData['recipient_name'],
                    'recipient_email' => $orderData['recipient_email'] ?? null,
                    'recipient_phone' => $orderData['recipient_phone'] ?? null,
                    'shipping_address' => $orderData['shipping_address'],
                    'status' => $orderData['status'],
                    'total_amount' => $totalAmount,
                    'shipping_fee' => $orderData['shipping_fee'] ?? 0,
                    'remark' => $orderData['remark'] ?? null,
                    'ordered_at' => $orderData['ordered_at'],
                ];
                $order = $this->orderRepository->createWithItems($insertData, $orderItems);
                $collection->push($order);
            }
            return $collection;
        });
    }

    protected function getProducts(array $orders): Collection
    {
        $ids = collect($orders)
            ->flatMap(fn (array $order) => collect($order['items'])->pluck('product_id'))
            ->unique()
            ->toArray();

        return $this->productRepository->getByIds($ids)->keyBy('id');
    }
}

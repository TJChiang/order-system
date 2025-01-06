<?php

namespace App\Order\Generator;

use App\Models\Order;
use App\Order\ChannelEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

abstract class Generator
{
    protected ChannelEnum $channel;

    public function generate(array $data): Collection
    {
        return DB::transaction(function () use ($data) {
            $collection = collect([]);
            foreach ($data as $orderData) {
                $order = $this->create($orderData);
                $collection->push($order);
            }
            return $collection;
        });
    }

    protected function create(array $data): Order
    {
        /** @var Order $order */
        $order = Order::create([
            'channel' => $this->channel->value,
            'order_number' => $data['order_number'],
            'user_id' => $data['user_id'] ?? null,
            'recipient_name' => $data['recipient_name'],
            'recipient_email' => $data['recipient_email'] ?? null,
            'recipient_phone' => $data['recipient_phone'] ?? null,
            'shipping_address' => $data['shipping_address'],
            'status' => $data['status'],
            'total_amount' => $data['total_amount'],
            'shipping_fee' => $data['shipping_fee'] ?? 0,
            'discount' => $data['discount'] ?? 0,
            'discount_rate' => $data['discount_rate'] ?? 0,
            'remark' => $data['remark'] ?? null,
            'ordered_at' => $data['ordered_at'],
        ]);
        $order->items()->createMany($data['items']);

        return $order;
    }
}

<?php

namespace Tests\Feature\Order;

use App\Http\Controllers\Order\Create;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Order\Generator\Manager;
use App\Order\Generator\MomoGenerator;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use PDOException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[CoversClass(Create::class)]
class CreateTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    #[Test]
    #[TestDox('測試輸入參數不合法，回傳 E001003')]
    #[DataProvider('provideInvalidArguments')]
    public function shouldReturnE001003WhenArgumentsAreInvalid(array $payload): void
    {
        $this->postJson(route('order.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonFragment([
                'error_code' => 'E001003',
            ]);
    }

    public static function provideInvalidArguments(): iterable
    {
        $uuid = Str::uuid()->toString();
        $orderedAt = CarbonImmutable::parse('2000-01-01 00:00:00')->toDate();
        yield 'channel 不合法' => [[
            'channel' => 'whatever',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                        'items' => [[
                            'product_id' => 1,
                            'sku' => 'whatever-sku',
                            'quantity' => 1,
                        ]],
                    ]],
                ],
            ],
        ]];
        yield '不是官網，但沒有 order_number' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                        'items' => [[
                            'product_id' => 1,
                            'sku' => 'whatever-sku',
                            'quantity' => 1,
                        ]],
                    ]],
                ],
            ],
        ]];
        yield '沒有收件人' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                        'items' => [[
                            'product_id' => 1,
                            'sku' => 'whatever-sku',
                            'quantity' => 1,
                        ]],
                    ]],
                ],
            ],
        ]];
        yield '不是官網，但沒有收件地址' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                        'items' => [[
                            'product_id' => 1,
                            'sku' => 'whatever-sku',
                            'quantity' => 1,
                        ]],
                    ]],
                ],
            ],
        ]];
        yield '沒有出貨單' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                ],
            ],
        ]];
        yield '出貨單為空陣列' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [],
                ],
            ],
        ]];
        yield '出貨單為空單' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[]],
                ],
            ],
        ]];
        yield '出貨單沒有 shipment_number' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                    ]],
                ],
            ],
        ]];
        yield '出貨單沒有 courier' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'tracking_number' => 'whatever-tracking-number',
                    ]],
                ],
            ],
        ]];
        yield '出貨單沒有 tracking_number' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                    ]],
                ],
            ],
        ]];
        yield '沒有訂單商品' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                    ]],
                ],
            ],
        ]];
        yield '訂單商品為空陣列' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                        'items' => [],
                    ]],
                ],
            ],
        ]];
        yield '訂單商品沒有資料' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                        'items' => [
                            [],
                        ],
                    ]],
                ],
            ],
        ]];
        yield '訂單商品沒有商品 ID' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                        'items' => [[
                            'sku' => 'whatever-sku',
                            'quantity' => 1,
                        ]],
                    ]],
                ],
            ],
        ]];
        yield '訂單商品沒有 sku' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                        'items' => [[
                            'product_id' => 1,
                            'quantity' => 1,
                        ]],
                    ]],
                ],
            ],
        ]];
        yield '訂單商品沒有數量' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                        'items' => [[
                            'product_id' => 1,
                            'sku' => 'whatever-sku',
                        ]],
                    ]],
                ],
            ],
        ]];
        yield '訂單商品數量小於 0' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => $uuid,
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $uuid,
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                        'items' => [[
                            'product_id' => 1,
                            'sku' => 'whatever-sku',
                            'quantity' => -1,
                        ]],
                    ]],
                ],
            ],
        ]];
    }

    #[Test]
    #[TestDox('測試資料庫錯誤，回傳 E001001')]
    public function shouldReturnE001001WhenDatabaseErrorOccurred(): void
    {
        $payload = [
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => Str::uuid()->toString(),
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => CarbonImmutable::parse('2000-01-01 00:00:00')->toDate(),
                    'shipments' => [[
                        'shipment_number' => Str::uuid()->toString(),
                        'courier' => 'whatever-courier',
                        'tracking_number' => 'whatever-tracking-number',
                        'items' => [[
                            'product_id' => 1,
                            'sku' => 'whatever-sku',
                            'quantity' => 1,
                        ]],
                    ]],
                ],
            ],
        ];

        $mockGenerator = $this->mock(MomoGenerator::class);
        $mockGenerator->shouldReceive('generate')
            ->once()
            ->andThrow(PDOException::class);

        $this->mock(Manager::class)
            ->shouldReceive('driver')
            ->once()
            ->andReturn($mockGenerator);

        $this->postJson(route('order.create'), $payload)
            ->assertServerError()
            ->assertJsonFragment([
                'error_code' => 'E001001',
            ]);
    }

    #[Test]
    #[TestDox('測試銷售渠道建立訂單成功，回傳 201')]
    #[DataProvider('provideChannels')]
    public function shouldReturn201WhenCreateOrdersFromChannelsSucceed(string $channel): void
    {
        $recipientName = 'whatever-recipient-name';
        $shippingAddress = 'whatever-shipping-address';
        $orderedAt = CarbonImmutable::parse('2000-01-01 00:00:00')->toDateTimeString();
        $scheduledShippingDate = CarbonImmutable::parse('2000-01-01 00:00:00')->toDateString();

        $orderNumber = 'whatever-order-number-1';
        $shipmentNumber = Str::uuid()->toString();
        $shipmentCourier = 'whatever-courier-1';
        $shipmentTrackingNumber = 'whatever-tracking-number-1';
        $productId = 1;
        $productName = 'whatever-product-name';
        $productPrice = 10.99;
        $sku = 'whatever-sku';

        $orderNumber2 = 'whatever-order-number-2';
        $shipmentNumber2 = Str::uuid()->toString();
        $shipmentCourier2 = 'whatever-courier-2';
        $shipmentTrackingNumber2 = 'whatever-tracking-number-2';
        $productId2 = 100;
        $productName2 = 'whatever-product-name-ii';
        $productPrice2 = 0.99;
        $sku2 = 'whatever-sku-ii';

        $shipmentNumber3 = Str::uuid()->toString();
        $shipmentCourier3 = 'whatever-courier-3';
        $shipmentTrackingNumber3 = 'whatever-tracking-number-3';
        $productId3 = 2;
        $productName3 = 'whatever-product-name-3';
        $productPrice3 = 1000;
        $sku3 = 'whatever-sku-3';

        $payload = [
            'channel' => $channel,
            'data' => [
                [
                    'order_number' => $orderNumber,
                    'recipient_name' => $recipientName,
                    'shipping_address' => $shippingAddress,
                    'scheduled_shipping_date' => $scheduledShippingDate,
                    'ordered_at' => $orderedAt,
                    'shipments' => [[
                        'shipment_number' => $shipmentNumber,
                        'courier' => $shipmentCourier,
                        'tracking_number' => $shipmentTrackingNumber,
                        'items' => [
                            [
                                'product_id' => $productId,
                                'sku' => $sku,
                                'quantity' => 2,
                            ],
                            [
                                'product_id' => $productId,
                                'sku' => $sku,
                                'quantity' => 20,
                            ],
                        ],
                    ]],
                ],
                [
                    'order_number' => $orderNumber2,
                    'recipient_name' => $recipientName,
                    'shipping_address' => $shippingAddress,
                    'ordered_at' => $orderedAt,
                    'shipments' => [
                        [
                            'shipment_number' => $shipmentNumber2,
                            'courier' => $shipmentCourier2,
                            'tracking_number' => $shipmentTrackingNumber2,
                            'items' => [
                                [
                                    'product_id' => $productId,
                                    'sku' => $sku,
                                    'quantity' => 1,
                                ],
                                [
                                    'product_id' => $productId2,
                                    'sku' => $sku2,
                                    'quantity' => 50,
                                ],
                            ],
                        ],
                        [
                            'shipment_number' => $shipmentNumber3,
                            'courier' => $shipmentCourier3,
                            'tracking_number' => $shipmentTrackingNumber3,
                            'items' => [
                                [
                                    'product_id' => $productId3,
                                    'sku' => $sku3,
                                    'quantity' => 5,
                                ],
                                [
                                    'product_id' => $productId2,
                                    'sku' => $sku2,
                                    'quantity' => 10,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        Product::factory(3)->sequence(
            [
                'id' => $productId,
                'name' => $productName,
                'price' => $productPrice,
                'sku' => $sku,
            ],
            [
                'id' => $productId2,
                'name' => $productName2,
                'price' => $productPrice2,
                'sku' => $sku2,
            ],
            [
                'id' => $productId3,
                'name' => $productName3,
                'price' => $productPrice3,
                'sku' => $sku3,
            ]
        )->create();

        $this->postJson(route('order.create'), $payload)
            ->assertCreated();
        $this->assertDatabaseHas(Order::class, [
            'channel' => $channel,
            'order_number' => $orderNumber,
            'user_id' => null,
            'recipient_name' => $recipientName,
            'shipping_address' => $shippingAddress,
            'scheduled_shipping_date' => $scheduledShippingDate,
            'ordered_at' => $orderedAt,
            'status' => 0,
            'total_amount' => $productPrice * 22,
        ]);
        $this->assertDatabaseHas(Order::class, [
            'channel' => $channel,
            'order_number' => $orderNumber2,
            'user_id' => null,
            'recipient_name' => $recipientName,
            'shipping_address' => $shippingAddress,
            'scheduled_shipping_date' => null,
            'ordered_at' => $orderedAt,
            'status' => 0,
            'total_amount' => $productPrice * 1 + $productPrice2 * 60 + $productPrice3 * 5,
        ]);
        $this->assertDatabaseCount(Shipment::class, 3);
        $this->assertDatabaseHas(Shipment::class, [
            'shipment_number' => $shipmentNumber,
            'courier' => $shipmentCourier,
            'tracking_number' => $shipmentTrackingNumber,
            'status' => 0,
        ]);
        $this->assertDatabaseHas(Shipment::class, [
            'shipment_number' => $shipmentNumber2,
            'courier' => $shipmentCourier2,
            'tracking_number' => $shipmentTrackingNumber2,
            'status' => 0,
        ]);
        $this->assertDatabaseHas(Shipment::class, [
            'shipment_number' => $shipmentNumber3,
            'courier' => $shipmentCourier3,
            'tracking_number' => $shipmentTrackingNumber3,
            'status' => 0,
        ]);
        $this->assertDatabaseCount(OrderItem::class, 6);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId,
            'product_name' => $productName,
            'sku' => $sku,
            'quantity' => 2,
            'price' => $productPrice,
            'total' => $productPrice * 2,
        ]);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId,
            'product_name' => $productName,
            'sku' => $sku,
            'quantity' => 20,
            'price' => $productPrice,
            'total' => $productPrice * 20,
        ]);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId,
            'product_name' => $productName,
            'sku' => $sku,
            'quantity' => 1,
            'price' => $productPrice,
            'total' => $productPrice * 1,
        ]);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId2,
            'product_name' => $productName2,
            'sku' => $sku2,
            'quantity' => 50,
            'price' => $productPrice2,
            'total' => $productPrice2 * 50,
        ]);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId3,
            'product_name' => $productName3,
            'sku' => $sku3,
            'quantity' => 5,
            'price' => $productPrice3,
            'total' => $productPrice3 * 5,
        ]);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId2,
            'product_name' => $productName2,
            'sku' => $sku2,
            'quantity' => 10,
            'price' => $productPrice2,
            'total' => $productPrice2 * 10,
        ]);
        $this->assertDatabaseCount(ShipmentItem::class, 6);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 20,
        ]);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 1,
        ]);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 50,
        ]);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 10,
        ]);
    }

    public static function provideChannels(): iterable
    {
        yield ['amazon'];
        yield ['momo'];
        yield ['hktvmall'];
    }

    #[Test]
    #[TestDox('測試官網建立訂單成功，回傳 201')]
    public function shouldReturn201WhenCreateOrdersFromOfficialSucceed(): void
    {
        $channel = 'official';
        $userId = 1;
        $recipientName = 'whatever-recipient-name';
        $recipientEmail = $this->faker->email;
        $recipientPhone = '+886912345678';
        $shippingAddress = 'whatever-shipping-address';
        $orderedAt = CarbonImmutable::parse('2000-01-01 00:00:00')->toDateTimeString();
        CarbonImmutable::setTestNow('2000-01-01 00:00:00');
        $scheduledShippingDate = CarbonImmutable::parse('2000-01-01 00:00:00')->toDateString();
        $orderRemark = $this->faker->sentence;

        $shipmentNumber = Str::uuid()->toString();
        $shipmentCourier = 'whatever-courier-1';
        $shipmentTrackingNumber = 'whatever-tracking-number-1';
        $shipmentRemark = $this->faker->sentence;
        $productId = 1;
        $productName = 'whatever-product-name';
        $productPrice = 10.99;
        $sku = 'whatever-sku';

        $shipmentNumber2 = Str::uuid()->toString();
        $shipmentCourier2 = 'whatever-courier-2';
        $shipmentTrackingNumber2 = 'whatever-tracking-number-2';
        $productId2 = 100;
        $productName2 = 'whatever-product-name-ii';
        $productPrice2 = 0.99;
        $sku2 = 'whatever-sku-ii';

        $shipmentNumber3 = Str::uuid()->toString();
        $shipmentCourier3 = 'whatever-courier-3';
        $shipmentTrackingNumber3 = 'whatever-tracking-number-3';
        $productId3 = 2;
        $productName3 = 'whatever-product-name-3';
        $productPrice3 = 1000;
        $sku3 = 'whatever-sku-3';
        $remark3 = $this->faker->sentence;

        $payload = [
            'channel' => $channel,
            'data' => [
                [
                    'recipient_name' => $recipientName,
                    'recipient_email' => $recipientEmail,
                    'recipient_phone' => $recipientPhone,
                    'shipping_address' => $shippingAddress,
                    'scheduled_shipping_date' => $scheduledShippingDate,
                    'remark' => $orderRemark,
                    'shipments' => [[
                        'shipment_number' => $shipmentNumber,
                        'courier' => $shipmentCourier,
                        'tracking_number' => $shipmentTrackingNumber,
                        'items' => [
                            [
                                'product_id' => $productId,
                                'sku' => $sku,
                                'quantity' => 2,
                            ],
                            [
                                'product_id' => $productId,
                                'sku' => $sku,
                                'quantity' => 20,
                            ],
                        ],
                        'remark' => $shipmentRemark,
                    ]],
                ],
                [
                    'recipient_name' => $recipientName,
                    'shipping_address' => $shippingAddress,
                    'ordered_at' => $orderedAt,
                    'shipments' => [
                        [
                            'shipment_number' => $shipmentNumber2,
                            'courier' => $shipmentCourier2,
                            'tracking_number' => $shipmentTrackingNumber2,
                            'items' => [
                                [
                                    'product_id' => $productId,
                                    'sku' => $sku,
                                    'quantity' => 1,
                                ],
                                [
                                    'product_id' => $productId2,
                                    'sku' => $sku2,
                                    'quantity' => 50,
                                ],
                            ],
                        ],
                        [
                            'shipment_number' => $shipmentNumber3,
                            'courier' => $shipmentCourier3,
                            'tracking_number' => $shipmentTrackingNumber3,
                            'items' => [
                                [
                                    'product_id' => $productId3,
                                    'sku' => $sku3,
                                    'quantity' => 5,
                                ],
                                [
                                    'product_id' => $productId2,
                                    'sku' => $sku2,
                                    'quantity' => 10,
                                ],
                            ],
                            'remark' => $remark3,
                        ],
                    ],
                ],
            ],
        ];

        Product::factory(3)->sequence(
            [
                'id' => $productId,
                'name' => $productName,
                'price' => $productPrice,
                'sku' => $sku,
            ],
            [
                'id' => $productId2,
                'name' => $productName2,
                'price' => $productPrice2,
                'sku' => $sku2,
            ],
            [
                'id' => $productId3,
                'name' => $productName3,
                'price' => $productPrice3,
                'sku' => $sku3,
            ]
        )->create();

        $this->postJson(route('order.create'), $payload)
            ->assertCreated();
        $this->assertDatabaseHas(Order::class, [
            'channel' => $channel,
            'user_id' => $userId,
            'recipient_name' => $recipientName,
            'recipient_email' => $recipientEmail,
            'recipient_phone' => $recipientPhone,
            'shipping_address' => $shippingAddress,
            'scheduled_shipping_date' => $scheduledShippingDate,
            'ordered_at' => $orderedAt,
            'status' => 0,
            'total_amount' => $productPrice * 22,
            'remark' => $orderRemark,
        ]);
        $this->assertDatabaseHas(Order::class, [
            'channel' => $channel,
            'user_id' => $userId,
            'recipient_name' => $recipientName,
            'recipient_email' => null,
            'recipient_phone' => null,
            'shipping_address' => $shippingAddress,
            'scheduled_shipping_date' => null,
            'ordered_at' => $orderedAt,
            'status' => 0,
            'total_amount' => $productPrice * 1 + $productPrice2 * 60 + $productPrice3 * 5,
            'remark' => null,
        ]);
        $this->assertDatabaseCount(Shipment::class, 3);
        $this->assertDatabaseHas(Shipment::class, [
            'shipment_number' => $shipmentNumber,
            'courier' => $shipmentCourier,
            'tracking_number' => $shipmentTrackingNumber,
            'status' => 0,
            'remark' => $shipmentRemark,
        ]);
        $this->assertDatabaseHas(Shipment::class, [
            'shipment_number' => $shipmentNumber2,
            'courier' => $shipmentCourier2,
            'tracking_number' => $shipmentTrackingNumber2,
            'status' => 0,
            'remark' => null,
        ]);
        $this->assertDatabaseHas(Shipment::class, [
            'shipment_number' => $shipmentNumber3,
            'courier' => $shipmentCourier3,
            'tracking_number' => $shipmentTrackingNumber3,
            'status' => 0,
            'remark' => $remark3,
        ]);
        $this->assertDatabaseCount(OrderItem::class, 6);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId,
            'product_name' => $productName,
            'sku' => $sku,
            'quantity' => 2,
            'price' => $productPrice,
            'total' => $productPrice * 2,
        ]);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId,
            'product_name' => $productName,
            'sku' => $sku,
            'quantity' => 20,
            'price' => $productPrice,
            'total' => $productPrice * 20,
        ]);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId,
            'product_name' => $productName,
            'sku' => $sku,
            'quantity' => 1,
            'price' => $productPrice,
            'total' => $productPrice * 1,
        ]);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId2,
            'product_name' => $productName2,
            'sku' => $sku2,
            'quantity' => 50,
            'price' => $productPrice2,
            'total' => $productPrice2 * 50,
        ]);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId3,
            'product_name' => $productName3,
            'sku' => $sku3,
            'quantity' => 5,
            'price' => $productPrice3,
            'total' => $productPrice3 * 5,
        ]);
        $this->assertDatabaseHas(OrderItem::class, [
            'product_id' => $productId2,
            'product_name' => $productName2,
            'sku' => $sku2,
            'quantity' => 10,
            'price' => $productPrice2,
            'total' => $productPrice2 * 10,
        ]);
        $this->assertDatabaseCount(ShipmentItem::class, 6);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 20,
        ]);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 1,
        ]);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 50,
        ]);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas(ShipmentItem::class, [
            'quantity' => 10,
        ]);
    }
}

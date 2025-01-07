<?php

namespace Tests\Feature\Order;

use App\Http\Controllers\Order\Create;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Order\Generator\Manager;
use App\Order\Generator\MomoGenerator;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
        yield 'channel 不合法' => [[
            'channel' => 'whatever',
            'data' => [
                [
                    'order_number' => Str::uuid()->toString(),
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString(),
                    'items' => [[
                        'product_id' => 1,
                        'sku' => 'whatever-sku',
                        'quantity' => 1,
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
                    'ordered_at' => CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString(),
                    'items' => [[
                        'product_id' => 1,
                        'sku' => 'whatever-sku',
                        'quantity' => 1,
                    ]],
                ],
            ],
        ]];
        yield '沒有收件人' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => Str::uuid()->toString(),
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString(),
                    'items' => [[
                        'product_id' => 1,
                        'sku' => 'whatever-sku',
                        'quantity' => 1,
                    ]],
                ],
            ],
        ]];
        yield '不是官網，但沒有收件地址' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => Str::uuid()->toString(),
                    'recipient_name' => 'whatever-name',
                    'ordered_at' => CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString(),
                    'items' => [[
                        'product_id' => 1,
                        'sku' => 'whatever-sku',
                        'quantity' => 1,
                    ]],
                ],
            ],
        ]];
        yield '沒有訂單商品' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => Str::uuid()->toString(),
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString(),
                ],
            ],
        ]];
        yield '訂單商品沒有商品 ID' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => Str::uuid()->toString(),
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString(),
                    'items' => [[
                        'sku' => 'whatever-sku',
                        'quantity' => 1,
                    ]],
                ],
            ],
        ]];
        yield '訂單商品沒有 sku' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => Str::uuid()->toString(),
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString(),
                    'items' => [[
                        'product_id' => 1,
                        'quantity' => 1,
                    ]],
                ],
            ],
        ]];
        yield '訂單商品沒有數量' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => Str::uuid()->toString(),
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString(),
                    'items' => [[
                        'product_id' => 1,
                        'sku' => 'whatever-sku',
                    ]],
                ],
            ],
        ]];
        yield '訂單商品數量小於 0' => [[
            'channel' => 'momo',
            'data' => [
                [
                    'order_number' => Str::uuid()->toString(),
                    'recipient_name' => 'whatever-name',
                    'shipping_address' => 'whatever-address',
                    'ordered_at' => CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString(),
                    'items' => [[
                        'product_id' => 1,
                        'sku' => 'whatever-sku',
                        'quantity' => -1,
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
                    'ordered_at' => CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString(),
                    'items' => [[
                        'product_id' => 1,
                        'sku' => 'whatever-sku',
                        'quantity' => 1,
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
        $orderNumber = Str::uuid()->toString();
        $recipientName = 'whatever-recipient-name';
        $shippingAddress = 'whatever-shipping-address';
        $orderedAt = CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString();
        $productId = 1;
        $productName = 'whatever-product-name';
        $productPrice = 10.99;
        $sku = 'whatever-sku';

        $orderNumber2 = Str::uuid()->toString();
        $productId2 = 100;
        $productName2 = 'whatever-product-name-ii';
        $productPrice2 = 0.99;
        $sku2 = 'whatever-sku-ii';

        $payload = [
            'channel' => $channel,
            'data' => [
                [
                    'order_number' => $orderNumber,
                    'recipient_name' => $recipientName,
                    'shipping_address' => $shippingAddress,
                    'ordered_at' => $orderedAt,
                    'items' => [
                        [
                            'product_id' => $productId,
                            'sku' => $sku,
                            'quantity' => 2,
                        ],
                    ],
                ],
                [
                    'order_number' => $orderNumber2,
                    'recipient_name' => $recipientName,
                    'shipping_address' => $shippingAddress,
                    'ordered_at' => $orderedAt,
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
            ],
        ];

        Product::factory(2)->sequence(
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
            'ordered_at' => $orderedAt,
            'status' => 0,
        ]);
        $this->assertDatabaseHas(Order::class, [
            'channel' => $channel,
            'order_number' => $orderNumber2,
            'user_id' => null,
            'recipient_name' => $recipientName,
            'shipping_address' => $shippingAddress,
            'ordered_at' => $orderedAt,
            'status' => 0,
        ]);
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
        $shippingAddress = 'whatever-shipping-address';
        $orderedAt = CarbonImmutable::parse('2000-01-01 00:00:00')->toAtomString();
        CarbonImmutable::setTestNow('2000-01-01 00:00:00');

        $productId = 1;
        $productName = 'whatever-product-name';
        $productPrice = 10.99;
        $sku = 'whatever-sku';

        $productId2 = 100;
        $productName2 = 'whatever-product-name-ii';
        $productPrice2 = 0.99;
        $sku2 = 'whatever-sku-ii';

        $payload = [
            'channel' => $channel,
            'data' => [
                [
                    'recipient_name' => $recipientName,
                    'shipping_address' => $shippingAddress,
                    'items' => [
                        [
                            'product_id' => $productId,
                            'sku' => $sku,
                            'quantity' => 2,
                        ],
                    ],
                ],
                [
                    'recipient_name' => $recipientName,
                    'shipping_address' => $shippingAddress,
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
            ],
        ];

        Product::factory(2)->sequence(
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
            ]
        )->create();

        $this->postJson(route('order.create'), $payload)
            ->assertCreated();
        $this->assertDatabaseHas(Order::class, [
            'channel' => $channel,
            'user_id' => $userId,
            'recipient_name' => $recipientName,
            'shipping_address' => $shippingAddress,
            'ordered_at' => $orderedAt,
            'status' => 0,
        ]);
        $this->assertDatabaseHas(Order::class, [
            'channel' => $channel,
            'user_id' => $userId,
            'recipient_name' => $recipientName,
            'shipping_address' => $shippingAddress,
            'ordered_at' => $orderedAt,
            'status' => 0,
        ]);
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
    }
}

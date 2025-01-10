<?php

namespace Tests\Feature\Order;

use App\Http\Controllers\Order\GetList;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[CoversClass(GetList::class)]
#[Group('order')]
class GetListTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    #[TestDox('測試輸入參數不合法，回傳 E001003')]
    #[DataProvider('provideInvalidArguments')]
    public function shouldReturnE001003WhenArgumentsAreInvalid(array $parameters): void
    {
        $this->getJson(route('order.list', $parameters))
            ->assertUnprocessable()
            ->assertJsonFragment([
                'error_code' => 'E001003',
            ]);
    }

    public static function provideInvalidArguments(): iterable
    {
        yield 'page 是空值' => [
            ['page' => ''],
        ];
        yield 'page 不是整數' => [
            ['page' => '1.1'],
        ];
        yield 'page 小於 1' => [
            ['page' => '0'],
        ];
        yield 'page 是字串' => [
            ['page' => 'whatever'],
        ];
        yield 'limit 是空值' => [
            ['limit' => ''],
        ];
        yield 'limit 不是整數' => [
            ['limit' => '1.1'],
        ];
        yield 'limit 小於 1' => [
            ['limit' => '0'],
        ];
        yield 'limit 是字串' => [
            ['limit' => 'whatever'],
        ];
        yield 'start_time 是空值' => [
            ['start_time' => ''],
        ];
        yield 'start_time 不是時間' => [
            ['start_time' => 'whatever'],
        ];
        yield 'start_time 時間格式不對' => [
            ['start_time' => '2021-01-01T00:00:00'],
        ];
        yield 'start_time 大於 end_time' => [
            [
                'start_time' => '2021-01-01 00:00:01',
                'end_time' => '2021-01-01 00:00:00',
            ],
        ];
        yield 'start_time 等於 end_time' => [
            [
                'start_time' => '2021-01-01 00:00:00',
                'end_time' => '2021-01-01 00:00:00',
            ],
        ];
        yield 'end_time 是空值' => [
            ['end_time' => ''],
        ];
        yield 'end_time 不是時間' => [
            ['end_time' => 'whatever'],
        ];
        yield 'end_time 時間格式不對' => [
            ['end_time' => '2021-01-01T00:00:00'],
        ];
        yield 'status 是空值' => [
            ['status' => ''],
        ];
        yield 'status 是字串' => [
            ['status' => 'whatever'],
        ];
        yield 'status 不是整數' => [
            ['status' => '1.1'],
        ];
        yield 'status 小於 0' => [
            ['status' => '-1'],
        ];
        yield 'status 大於 10' => [
            ['status' => '11'],
        ];
        yield 'channel 是空值' => [
            ['channel' => ''],
        ];
        yield 'channel 不合法' => [
            ['channel' => 'whatever'],
        ];
        yield 'order_number 是空值' => [
            ['order_number' => ''],
        ];
    }

    #[Test]
    #[TestDox('測試空清單')]
    public function shouldReturnEmptyDataWhenNoOrder(): void
    {
        $this->getJson(route('order.list'))
            ->assertOk()
            ->assertJson([
                'data' => [],
                'page' => 1,
                'limit' => 50,
                'count' => 0,
            ]);
    }

    #[Test]
    #[TestDox('測試過濾選項不匹配，回傳空清單')]
    public function shouldReturnEmptyDataWhenFilterIsNotMatch(): void
    {
        Order::factory()->create([
            'status' => 1,
        ]);

        $this->getJson(route('order.list', ['status' => 2]))
            ->assertOk()
            ->assertJson([
                'data' => [],
                'page' => 1,
                'limit' => 50,
                'count' => 0,
            ]);
    }

    #[Test]
    #[TestDox('測試沒有輸入參數，回傳所有清單')]
    public function shouldReturnAllOrderListWhenNoArgument(): void
    {
        Order::factory(3)
            ->sequence(
                [
                    'id' => 1,
                    'order_number' => 'whatever-1',
                    'status' => 1,
                ],
                [
                    'id' => 2,
                    'order_number' => 'whatever-2',
                    'status' => 2,
                ],
                [
                    'id' => 3,
                    'order_number' => 'whatever-3',
                    'status' => 3,
                ],
            )
            ->create();

        $response = $this->getJson(route('order.list'))
            ->assertOk()
            ->assertJsonFragment([
                'page' => 1,
                'limit' => 50,
                'count' => 3,
            ]);

        $actualData = collect($response->json('data'))
            ->sortBy('id')
            ->values()
            ->toArray();

        self::assertCount(3, $actualData);
        self::assertSame('whatever-1', $actualData[0]['order_number']);
        self::assertSame(1, $actualData[0]['status']);
        self::assertSame('whatever-2', $actualData[1]['order_number']);
        self::assertSame(2, $actualData[1]['status']);
        self::assertSame('whatever-3', $actualData[2]['order_number']);
        self::assertSame(3, $actualData[2]['status']);
    }

    #[Test]
    #[TestDox('測試有輸入參數，回傳指定訂單')]
    public function shouldReturnFilteredOrderWhenOrdersFound(): void
    {
        $parameters = [
            'channel' => 'amazon',
            'status' => 2,
        ];

        Order::factory(5)
            ->sequence(
                [
                    'id' => 1,
                    'channel' => 'amazon',
                    'order_number' => 'whatever-1',
                    'status' => 1,
                ],
                [
                    'id' => 2,
                    'channel' => 'amazon',
                    'order_number' => 'whatever-2',
                    'status' => 2,
                ],
                [
                    'id' => 3,
                    'channel' => 'amazon',
                    'order_number' => 'whatever-3',
                    'status' => 3,
                ],
                [
                    'id' => 4,
                    'channel' => 'momo',
                    'order_number' => 'whatever-4',
                    'status' => 2,
                ],
                [
                    'id' => 5,
                    'channel' => 'amazon',
                    'order_number' => 'whatever-5',
                    'status' => 2,
                ],
            )
            ->create();

        $response = $this->getJson(route('order.list', $parameters))
            ->assertOk()
            ->assertJsonFragment([
                'page' => 1,
                'limit' => 50,
                'count' => 2,
            ]);

        $actualData = collect($response->json('data'))
            ->sortBy('id')
            ->values()
            ->toArray();

        self::assertCount(2, $actualData);

        self::assertSame(2, $actualData[0]['id']);
        self::assertSame('amazon', $actualData[0]['channel']);
        self::assertSame('whatever-2', $actualData[0]['order_number']);
        self::assertSame(2, $actualData[0]['status']);

        self::assertSame(5, $actualData[1]['id']);
        self::assertSame('amazon', $actualData[1]['channel']);
        self::assertSame('whatever-5', $actualData[1]['order_number']);
        self::assertSame(2, $actualData[1]['status']);
    }
}

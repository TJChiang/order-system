<?php

namespace Tests\Feature\Order;

use App\Http\Controllers\Order\GetById;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[CoversClass(GetById::class)]
#[Group('order')]
class GetByIdTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    #[TestDox('測試無訂單資料，回傳 E001004')]
    public function shouldReturnE001004WhenOrderNotFound(): void
    {
        $orderId = 1;

        $this->getJson(route('order.get_by_id', ['id' => $orderId]))
            ->assertNotFound()
            ->assertJsonFragment([
                'error_code' => 'E001004',
            ]);
    }

    #[Test]
    #[TestDox('測試回傳訂單資料')]
    public function shouldReturnOrderDataWhenOrderFoundById(): void
    {
        $orderId = 1;
        $orderNumber = 'whatever-order-number';

        Order::factory()->create([
            'id' => $orderId,
            'order_number' => $orderNumber,
        ]);

        $response = $this->getJson(route('order.get_by_id', ['id' => $orderId]))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'order_number',
                    'status',
                    'channel',
                    'ordered_at',
                    'created_at',
                    'updated_at',
                ],
            ]);

        self::assertSame($orderId, $response->json('data.id'));
        self::assertSame($orderNumber, $response->json('data.order_number'));
    }
}

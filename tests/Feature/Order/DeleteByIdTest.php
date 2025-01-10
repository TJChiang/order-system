<?php

namespace Tests\Feature\Order;

use App\Http\Controllers\Order\DeleteById;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[CoversClass(DeleteById::class)]
#[Group('order')]
class DeleteByIdTest extends TestCase
{
    use DatabaseTransactions;

    #[TestDox('測試刪除訂單')]
    public function testDeleteById(): void
    {
        $orderId = 1;

        Order::factory()->create(['id' => $orderId]);

        Shipment::factory(3)
            ->sequence(
                [
                    'id' => 1,
                    'order_id' => $orderId,
                ],
                [
                    'id' => 2,
                    'order_id' => $orderId,
                ],
                [
                    'id' => 3,
                    'order_id' => $orderId,
                ],
            )
            ->create();

        OrderItem::factory(4)
            ->sequence(
                [
                    'id' => 1,
                    'order_id' => $orderId,
                ],
                [
                    'id' => 2,
                    'order_id' => $orderId,
                ],
                [
                    'id' => 3,
                    'order_id' => $orderId,
                ],
                [
                    'id' => 4,
                    'order_id' => $orderId,
                ],
            )
            ->create();

        ShipmentItem::factory(4)
            ->sequence(
                [
                    'id' => 1,
                    'shipment_id' => 1,
                    'order_item_id' => 1,
                ],
                [
                    'id' => 2,
                    'shipment_id' => 2,
                    'order_item_id' => 2,
                ],
                [
                    'id' => 3,
                    'shipment_id' => 2,
                    'order_item_id' => 3,
                ],
                [
                    'id' => 4,
                    'shipment_id' => 2,
                    'order_item_id' => 4,
                ],
            )
            ->create();

        $this->deleteJson(route('order.delete_by_id', ['id' => $orderId]))
            ->assertNoContent();

        $this->assertDatabaseMissing(Order::class, ['id' => $orderId]);
        $this->assertDatabaseMissing(Shipment::class, ['id' => 1]);
        $this->assertDatabaseMissing(Shipment::class, ['id' => 2]);
        $this->assertDatabaseMissing(Shipment::class, ['id' => 3]);
        $this->assertDatabaseMissing(OrderItem::class, ['id' => 1]);
        $this->assertDatabaseMissing(OrderItem::class, ['id' => 2]);
        $this->assertDatabaseMissing(OrderItem::class, ['id' => 3]);
        $this->assertDatabaseMissing(OrderItem::class, ['id' => 4]);
        $this->assertDatabaseMissing(ShipmentItem::class, ['shipment_id' => 1]);
        $this->assertDatabaseMissing(ShipmentItem::class, ['shipment_id' => 2]);
        $this->assertDatabaseMissing(ShipmentItem::class, ['shipment_id' => 3]);
    }

    #[TestDox('測試刪除無訂單')]
    public function testDeleteEmptyOrderById(): void
    {
        $orderId = 1;

        Order::factory()->create(['id' => $orderId]);

        $this->deleteJson(route('order.delete_by_id', ['id' => $orderId]))
            ->assertNoContent();
    }
}

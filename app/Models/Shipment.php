<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int id
 * @property int order_id order 外鍵
 * @property string shipment_number 出貨單號
 * @property string courier 配送公司
 * @property string tracking_number 配送追蹤編號
 * @property int status 出貨狀態
 * @property null|\Carbon\Carbon shipped_at 出貨時間
 * @property null|\Carbon\Carbon delivered_at 送達時間
 * @property null|string remark 備註
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 */
class Shipment extends Model
{
    use HasFactory;

    protected $table = 'shipments';
    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function orderItems(): BelongsToMany
    {
        return $this->belongsToMany(OrderItem::class, ShipmentItem::class, 'shipment_id', 'order_item_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}

<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int id
 * @property string channel 銷售渠道
 * @property string order_number 訂單編號
 * @property null|int user_id 使用者 ID
 * @property string recipient_name 收件人姓名
 * @property null|string recipient_email 收件人 Email
 * @property null|string recipient_phone 收件人電話
 * @property string shipping_address 送貨地址
 * @property null|\Carbon\Carbon scheduled_shipping_date 指定配送日期
 * @property int status 訂單狀態
 * @property float total_amount 訂單總金額
 * @property float shipping_fee 運費
 * @property float discount 折扣金額
 * @property float discount_rate 折扣比率
 * @property null|string remark 備註
 * @property \Carbon\Carbon ordered_at 訂單日期
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 */
class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'scheduled_shipping_date' => 'date:Y-m-d',
            'ordered_at' => 'datetime:Y-m-d H:i:s',
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'order_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function shipmentItems(): HasManyThrough
    {
        return $this->hasManyThrough(
            ShipmentItem::class,
            Shipment::class,
            'order_id',
            'shipment_id',
            'id',
            'id'
        );
    }

    public function scopeDefaultSelect(Builder $query, array $columns = []): Builder
    {
        return $query->select(array_merge(
            [
                'id',
                'channel',
                'order_number',
                'user_id',
                'recipient_name',
                'recipient_email',
                'recipient_phone',
                'shipping_address',
                'scheduled_shipping_date',
                'status',
                'total_amount',
                'ordered_at'
            ],
            $columns,
        ));
    }
}

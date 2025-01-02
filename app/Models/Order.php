<?php

namespace App\Models;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $channel 銷售渠道
 * @property string $order_number 訂單編號
 * @property string $customer_name 客戶姓名
 * @property null|string $customer_email 客戶 Email
 * @property null|string $customer_phone 客戶電話
 * @property string $shipping_address 送貨地址
 * @property int $status 訂單狀態
 * @property float $total_amount 訂單總金額
 * @property float $shipping_fee 運費
 * @property float $discount 折扣金額
 * @property float $discount_rate 折扣比率
 * @property CarbonInterface $order_date 訂單日期
 * @property CarbonInterface $created_at
 * @property CarbonInterface $updated_at
 */
class Order extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        'order_date' => 'datetime:' . DateTimeInterface::ATOM,
        'created_at' => 'datetime:' . DateTimeInterface::ATOM,
        'updated_at' => 'datetime:' . DateTimeInterface::ATOM,
    ];
}

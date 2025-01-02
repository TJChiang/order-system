<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $order_id order 外鍵
 * @property string $shipment_number 出貨單號
 * @property string $courier 配送公司
 * @property string $tracking_number 配送追蹤編號
 * @property int $status 出貨狀態
 * @property null|\Carbon\Carbon $shipped_at 出貨時間
 * @property null|\Carbon\Carbon $delivered_at 送達時間
 * @property null|string $remark 備註
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Shipment extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'shipments';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        'shipped_at' => 'datetime:' . DateTimeInterface::ATOM,
        'delivered_at' => 'datetime:' . DateTimeInterface::ATOM,
        'created_at' => 'datetime:' . DateTimeInterface::ATOM,
        'updated_at' => 'datetime:' . DateTimeInterface::ATOM,
    ];
}

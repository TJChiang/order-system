<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int id
 * @property int shipment_id 出貨單外鍵
 * @property int order_item_id 訂單項目外鍵
 * @property int quantity 數量
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 */
class ShipmentItem extends Pivot
{
    protected $table = 'shipment_items';
    public $incrementing = true;
    protected $guarded = [];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int id
 * @property int order_id 訂單 ID
 * @property int product_id 產品 ID
 * @property string product_name 商品名稱
 * @property string sku 商品編號 SKU
 * @property float price 單價
 * @property int quantity 數量
 * @property float total 總價
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(Shipment::class, ShipmentItem::class, 'order_item_id', 'shipment_id')
            ->using(ShipmentItem::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }
}

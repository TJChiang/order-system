<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string sku
 * @property string name 商品名稱
 * @property string|null description 商品描述
 * @property float price 價格
 * @property int stock 庫存
 * @property int status 商品狀態
 * @property int version 商品版本號
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 */
class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime:' . DateTimeInterface::ATOM,
        'updated_at' => 'datetime:' . DateTimeInterface::ATOM,
    ];
}

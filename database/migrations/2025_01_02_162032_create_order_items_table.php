<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->comment('出貨單 ID');
            $table->string('product_name', 255)->comment('商品名稱');
            $table->string('product_sku', 100)->index('index_product_sku')->comment('商品編號 SKU');
            $table->integer('quantity')->unsigned()->default(0)->comment('數量');
            $table->decimal('price', 10, 2)->unsigned()->default(0)->comment('單價');
            $table->decimal('total', 10, 2)->unsigned()->default(0)->comment('總價');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

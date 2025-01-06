<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('shipment_id')->comment('出貨單外鍵');
            $table->foreignId('order_item_id')->comment('訂單項目外鍵');
            $table->unsignedInteger('quantity')->comment('數量');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};

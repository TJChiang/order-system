<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('sku', 50)->unique()->comment('Stock Keeping Unit');
            $table->string('name', 255)->comment('商品名稱');
            $table->text('description')->nullable()->comment('商品描述');
            $table->decimal('price', 10, 2)->unsigned()->default(0)->comment('價格');
            $table->unsignedInteger('stock')->default(0)->comment('庫存');
            $table->unsignedTinyInteger('status')->default(0)->comment('商品狀態');
            $table->unsignedMediumInteger('version')->default(1)->comment('商品版本號');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

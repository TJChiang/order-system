<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('channel', 50)->comment('銷售渠道');
            $table->string('order_number', 100)->comment('訂單編號');
            $table->string('customer_name', 255)->comment('客戶姓名');
            $table->string('customer_email', 255)->nullable()->comment('客戶 Email');
            $table->string('customer_phone', 50)->nullable()->comment('客戶電話');
            $table->text('shipping_address')->comment('送貨地址');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('訂單狀態');
            $table->decimal('total_amount', 10, 2)->comment('訂單總金額');
            $table->decimal('shipping_fee', 10, 2)->default(0)->comment('運費');
            $table->decimal('discount', 10, 2)->default(0)->comment('折扣金額');
            $table->decimal('discount_rate', 10, 2)->default(0)->comment('折扣比率');
            $table->timestamp('order_date')->comment('訂單日期');
            $table->timestamps();

            $table->unique(['order_number', 'channel'], 'unique_order_number_channel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

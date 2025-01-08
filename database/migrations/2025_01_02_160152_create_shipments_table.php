<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('order_id')->comment('訂單 ID');
            $table->string('courier', 100)->comment('配送公司');
            $table->string('shipment_number', 100)->unique('unique_shipment_number')->comment('出貨單號');
            $table->string('tracking_number', 100)->default('')->index('index_tracking_number')->comment('配送追蹤編號');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('出貨狀態');
            $table->timestamp('shipped_at')->nullable()->comment('出貨時間');
            $table->timestamp('delivered_at')->nullable()->comment('送達時間');
            $table->text('remark')->nullable()->comment('備註');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

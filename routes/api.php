<?php

use App\Http\Controllers\Order\Create as CreateOrder;
use App\Http\Controllers\Order\DeleteById as DeleteOrderById;
use App\Http\Controllers\Order\GetById as GetOrderById;
use App\Http\Controllers\Order\GetList as GetOrderList;
use App\Http\Controllers\Order\UpdateById as UpdateOrderById;
use Illuminate\Support\Facades\Route;

Route::get('/hi', function () {
    return response()->json(['message' => 'Hello World!']);
});

Route::prefix('/order')->name('order.')->group(function () {
    Route::get('/', GetOrderList::class)->name('list');
    Route::get('/{id}', GetOrderById::class)->name('get_by_id');
    Route::post('/', CreateOrder::class)->name('create');
    Route::put('/{id}', UpdateOrderById::class)->name('update_by_id');
    Route::delete('/{id}', DeleteOrderById::class)->name('delete_by_id');
});

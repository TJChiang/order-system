<?php

use App\Http\Controllers\Order\Create as CreateOrder;
use App\Http\Controllers\Order\GetList as GetOrderList;
use Illuminate\Support\Facades\Route;

Route::get('/hi', function () {
    return response()->json(['message' => 'Hello World!']);
});

Route::prefix('/order')->name('order.')->group(function () {
    Route::get('/', GetOrderList::class)->name('list');
    Route::post('/', CreateOrder::class)->name('create');
    // Route::get('/{id}', 'Order\Show');
    // Route::put('/{id}', 'Order\Update');
    // Route::delete('/{id}', 'Order\Delete');
});

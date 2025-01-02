<?php

use Illuminate\Support\Facades\Route;

Route::get('/hi', function () {
    return response()->json(['message' => 'Hello World!']);
});

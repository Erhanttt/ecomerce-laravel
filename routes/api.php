<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('messages', MessageController::class)->only(['index', 'store', 'show', 'destroy']);
Route::apiResource('orders', OrderController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);

require __DIR__.'/auth.php';
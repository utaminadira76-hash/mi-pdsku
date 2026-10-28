<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [OrderController::class, 'health']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/user/{userId}', [OrderController::class, 'indexByUser']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::patch('/orders/{id}/pay', [OrderController::class, 'pay']);
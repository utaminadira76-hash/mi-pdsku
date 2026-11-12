<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;

Route::get('/carts/{userId}', [CartController::class, 'show']);
Route::post('/carts', [CartController::class, 'store']);
Route::put('/carts/items/{itemId}', [CartController::class, 'update']);
Route::delete('/carts/items/{itemId}', [CartController::class, 'removeItem']);
Route::delete('/carts/{userId}', [CartController::class, 'destroy']);
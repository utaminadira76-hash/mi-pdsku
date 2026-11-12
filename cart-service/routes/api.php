<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;

Route::get('/carts/{userId}', [CartController::class, 'show']);
Route::delete('/carts/{userId}', [CartController::class, 'destroy']);
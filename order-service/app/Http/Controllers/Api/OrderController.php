<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\EmptyCartException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\ServiceUnavailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
    ) {}

    public function health(): JsonResponse
    {
        return response()->json([
            'status'  => 'ok',
            'service' => 'order-service',
        ]);
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->checkout((int) $request->validated()['user_id']);

            return response()->json([
                'message' => 'Pesanan berhasil dibuat',
                'data'    => $order,
            ], 201);
        } catch (EmptyCartException|ProductNotFoundException|InsufficientStockException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        } catch (ServiceUnavailableException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 503);
        } catch (\Throwable $e) {
            Log::error('Unexpected error saat checkout', [
                'user_id' => $request->input('user_id'),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Terjadi kesalahan pada server',
            ], 500);
        }
    }
}
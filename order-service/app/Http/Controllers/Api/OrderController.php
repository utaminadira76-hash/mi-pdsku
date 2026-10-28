<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\EmptyCartException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\ServiceUnavailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function show($id): JsonResponse
    {
        if (!ctype_digit((string) $id)) {
            return response()->json([
                'message' => "Pesanan dengan id {$id} tidak ditemukan",
            ], 404);
        }

        $order = Order::with('items')->find((int) $id);

        if (!$order) {
            return response()->json([
                'message' => "Pesanan dengan id {$id} tidak ditemukan",
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil mengambil pesanan',
            'data'    => $order,
        ], 200);
    }

    public function indexByUser($userId, Request $request): JsonResponse
    {
        if (!ctype_digit((string) $userId)) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        $status = $request->query('status');

        if ($status !== null && !in_array($status, ['PENDING', 'PAID'], true)) {
            return response()->json([
                'message' => 'Data yang dikirim tidak valid',
                'errors'  => [
                    'status' => ['Status harus PENDING atau PAID'],
                ],
            ], 422);
        }

        $query = Order::with('items')
            ->where('user_id', (int) $userId)
            ->orderByDesc('created_at');

        if ($status !== null) {
            $query->where('status', $status);
        }

        $orders = $query->get();

        return response()->json([
            'message' => 'Berhasil mengambil daftar pesanan',
            'data'    => $orders,
        ], 200);
    }
}
<?php

namespace App\Services;

use App\Clients\CartServiceClient;
use App\Clients\ProductServiceClient;
use App\Exceptions\EmptyCartException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotFoundException;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected CartServiceClient $cartClient,
        protected ProductServiceClient $productClient,
    ) {}

    /**
     * Checkout: ubah isi keranjang menjadi pesanan.
     *
     * @throws EmptyCartException
     * @throws ProductNotFoundException
     * @throws InsufficientStockException
     */
    public function checkout(int $userId): Order
    {
        // 1. Ambil isi keranjang dari cart-service
        $cart = $this->cartClient->getCart($userId);
        $items = $cart['items'] ?? [];

        if (empty($items)) {
            throw new EmptyCartException();
        }

        // 2. Ambil detail produk & validasi stok
        $lineItems = [];
        $totalAmount = 0.0;

        foreach ($items as $item) {
            $productId = (int) $item['product_id'];
            $quantity  = (int) $item['quantity'];

            $product = $this->productClient->find($productId);

            if ($product === null) {
                throw new ProductNotFoundException($productId);
            }

            $stock = (int) ($product['stock'] ?? 0);
            if ($quantity > $stock) {
                throw new InsufficientStockException(
                    $product['name'] ?? "Produk {$productId}",
                    $quantity,
                    $stock
                );
            }

            $unitPrice = (float) $product['price'];
            $subtotal  = round($unitPrice * $quantity, 2);

            $lineItems[] = [
                'product_id'   => $productId,
                'product_name' => $product['name'],
                'unit_price'   => $unitPrice,
                'quantity'     => $quantity,
                'subtotal'     => $subtotal,
            ];

            $totalAmount += $subtotal;
        }

        // 3. Simpan pesanan + item dalam satu transaksi
        $order = DB::transaction(function () use ($userId, $lineItems, $totalAmount) {
            $order = Order::create([
                'user_id'      => $userId,
                'status'       => 'PENDING',
                'total_amount' => round($totalAmount, 2),
            ]);

            $order->items()->createMany($lineItems);

            return $order;
        });

        // 4. Kosongkan keranjang; jika gagal, jangan batalkan pesanan
        try {
            $this->cartClient->clearCart($userId);
        } catch (\Throwable $e) {
            Log::warning('Gagal mengosongkan keranjang setelah pesanan dibuat', [
                'order_id' => $order->id,
                'user_id'  => $userId,
                'error'    => $e->getMessage(),
            ]);
        }

        return $order->load('items');
    }
}
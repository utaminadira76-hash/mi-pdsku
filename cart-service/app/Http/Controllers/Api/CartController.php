<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    public function show($userId)
    {
        $items = DB::table('cart_items')->where('user_id', $userId)->get();
        $totalQuantity = $items->sum('quantity');

        return response()->json([
            'message' => 'Berhasil mengambil keranjang',
            'data' => [
                'user_id' => (int) $userId,
                'items' => $items,
                'total_quantity' => $totalQuantity,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        // Opsional: Cek validasi produk ke product-service (sesuai panduan)
        try {
            $response = Http::timeout(3)->get('http://product-service:3000/products/' . $validated['product_id']);
            if ($response->failed()) {
                return response()->json(['message' => 'Produk tidak ditemukan'], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Product service tidak dapat dihubungi'], 503);
        }

        $existing = DB::table('cart_items')
            ->where('user_id', $validated['user_id'])
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existing) {
            DB::table('cart_items')->where('id', $existing->id)->increment('quantity', $validated['quantity']);
        } else {
            DB::table('cart_items')->insert([
                'user_id' => $validated['user_id'],
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Berhasil menambahkan ke keranjang'], 201);
    }

    public function update(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        DB::table('cart_items')->where('id', $itemId)->update([
            'quantity' => $validated['quantity'],
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Jumlah berhasil diubah']);
    }

    public function removeItem($itemId)
    {
        DB::table('cart_items')->where('id', $itemId)->delete();
        return response()->json(['message' => 'Item berhasil dihapus']);
    }

    public function destroy($userId)
    {
        $deleted = DB::table('cart_items')->where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'Keranjang berhasil dikosongkan',
            'data' => [
                'deleted_items' => $deleted,
            ],
        ]);
    }
}
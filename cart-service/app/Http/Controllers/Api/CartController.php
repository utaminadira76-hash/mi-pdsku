<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
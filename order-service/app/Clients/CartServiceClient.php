<?php

namespace App\Clients;

use App\Exceptions\ServiceUnavailableException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CartServiceClient
{
    public function getCart(int $userId): array
    {
        $url = rtrim(config('services.cart.url'), '/') . "/carts/{$userId}";

        try {
            $response = Http::timeout(config('services.cart.timeout'))
                ->acceptJson()
                ->get($url);
        } catch (ConnectionException $e) {
            Log::error('Cart service connection failed', [
                'user_id' => $userId,
                'url'     => $url,
                'error'   => $e->getMessage(),
            ]);
            throw new ServiceUnavailableException('Cart service');
        }

        if ($response->failed()) {
            Log::error('Cart service returned error', [
                'user_id' => $userId,
                'status'  => $response->status(),
                'body'    => $response->body(),
            ]);
            throw new ServiceUnavailableException('Cart service');
        }

        return $response->json('data') ?? [];
    }

    public function clearCart(int $userId): void
    {
        $url = rtrim(config('services.cart.url'), '/') . "/carts/{$userId}";

        try {
            $response = Http::timeout(config('services.cart.timeout'))
                ->acceptJson()
                ->delete($url);
        } catch (ConnectionException $e) {
            Log::error('Cart service connection failed when clearing cart', [
                'user_id' => $userId,
                'url'     => $url,
                'error'   => $e->getMessage(),
            ]);
            throw new ServiceUnavailableException('Cart service');
        }

        if ($response->failed()) {
            Log::error('Cart service failed to clear cart', [
                'user_id' => $userId,
                'status'  => $response->status(),
                'body'    => $response->body(),
            ]);
            throw new ServiceUnavailableException('Cart service');
        }
    }
}
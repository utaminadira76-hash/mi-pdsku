<?php

namespace App\Clients;

use App\Exceptions\ServiceUnavailableException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductServiceClient
{
    public function find(int $productId): ?array
    {
        $url = rtrim(config('services.product.url'), '/') . "/products/{$productId}";

        try {
            $response = Http::timeout(config('services.product.timeout'))
                ->acceptJson()
                ->get($url);
        } catch (ConnectionException $e) {
            Log::error('Product service connection failed', [
                'product_id' => $productId,
                'url'        => $url,
                'error'      => $e->getMessage(),
            ]);
            throw new ServiceUnavailableException('Product service');
        }

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            Log::error('Product service returned error', [
                'product_id' => $productId,
                'status'     => $response->status(),
                'body'       => $response->body(),
            ]);
            throw new ServiceUnavailableException('Product service');
        }

        return $response->json('data');
    }
}
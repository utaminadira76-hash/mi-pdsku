<?php

namespace App\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    public function __construct(int $productId)
    {
        parent::__construct("Produk dengan id {$productId} tidak ditemukan di product-service");
    }
}
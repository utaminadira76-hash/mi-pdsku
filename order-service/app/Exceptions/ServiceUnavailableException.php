<?php

namespace App\Exceptions;

use Exception;

class ServiceUnavailableException extends Exception
{
    protected string $serviceName;

    public function __construct(string $serviceName, string $message = '')
    {
        $this->serviceName = $serviceName;
        $message = $message ?: "{$serviceName} tidak dapat dihubungi, coba lagi nanti";
        parent::__construct($message);
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }
}
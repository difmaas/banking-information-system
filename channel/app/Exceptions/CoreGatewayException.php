<?php

namespace App\Exceptions;

use RuntimeException;

class CoreGatewayException extends RuntimeException
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        string $message,
        public readonly int $status = 502,
        public readonly array $payload = [],
    ) {
        parent::__construct($message);
    }
}

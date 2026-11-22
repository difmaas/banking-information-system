<?php

namespace App\Support;

class HmacSigner
{
    public static function sign(string $method, string $path, string $timestamp, string $body, string $secret): string
    {
        $canonical = $method.'|'.$path.'|'.$timestamp.'|'.$body;

        return hash_hmac('sha256', $canonical, $secret);
    }

    /**
     * @return array<string, string>
     */
    public static function headers(string $method, string $path, string $body, string $token, string $secret): array
    {
        $timestamp = (string) now()->timestamp;

        return [
            'X-Service-Token' => $token,
            'X-Timestamp' => $timestamp,
            'X-Signature' => self::sign($method, $path, $timestamp, $body, $secret),
        ];
    }
}

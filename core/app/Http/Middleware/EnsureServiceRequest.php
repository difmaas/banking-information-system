<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureServiceRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Service-Token');
        $timestamp = $request->header('X-Timestamp');
        $signature = $request->header('X-Signature');

        $expectedToken = config('services.core.service_token');
        $secret = config('services.core.hmac_secret');
        $tolerance = (int) config('services.core.timestamp_tolerance', 300);

        if (! is_string($token) || ! is_string($timestamp) || ! is_string($signature) || ! is_string($expectedToken) || ! is_string($secret)) {
            abort(401, 'Missing service authentication headers.');
        }

        if (! hash_equals($expectedToken, $token)) {
            abort(401, 'Invalid service token.');
        }

        if (! is_numeric($timestamp) || abs(now()->timestamp - (int) $timestamp) > $tolerance) {
            abort(401, 'Invalid or expired timestamp.');
        }

        $canonical = $request->method().'|'.$request->getPathInfo().'|'.$timestamp.'|'.$request->getContent();
        $expectedSignature = hash_hmac('sha256', $canonical, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            abort(401, 'Invalid signature.');
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authenticates service-to-service requests from the channel middleware.
 *
 * The middleware verifies the service token, timestamp freshness, and
 * HMAC-SHA256 signature before allowing the request to reach core routes.
 */
class EnsureServiceRequest
{
    /**
     * Validate the incoming service request and continue the request lifecycle.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Service authentication headers sent by the channel middleware.
        $token = $request->header('X-Service-Token');
        $timestamp = $request->header('X-Timestamp');
        $signature = $request->header('X-Signature');

        // Expected credentials are configured per environment.
        $expectedToken = config('services.core.service_token');
        $secret = config('services.core.hmac_secret');
        $tolerance = (int) config('services.core.timestamp_tolerance', 300);

        // Reject requests that do not contain all required authentication data.
        if (! is_string($token) || ! is_string($timestamp) || ! is_string($signature) || ! is_string($expectedToken) || ! is_string($secret)) {
            abort(401, 'Missing service authentication headers.');
        }

        // Compare tokens in constant time to avoid timing attacks.
        if (! hash_equals($expectedToken, $token)) {
            abort(401, 'Invalid service token.');
        }

        // Reject stale requests to prevent replay attacks.
        if (! is_numeric($timestamp) || abs(now()->timestamp - (int) $timestamp) > $tolerance) {
            abort(401, 'Invalid or expired timestamp.');
        }

        // Canonical string: METHOD|PATH|TIMESTAMP|BODY.
        $canonical = $request->method().'|'.$request->getPathInfo().'|'.$timestamp.'|'.$request->getContent();
        $expectedSignature = hash_hmac('sha256', $canonical, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            abort(401, 'Invalid signature.');
        }

        return $next($request);
    }
}

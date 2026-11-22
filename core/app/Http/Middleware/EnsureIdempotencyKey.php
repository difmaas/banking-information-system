<?php

namespace App\Http\Middleware;

use App\Models\IdempotencyKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotencyKey
{
    private const WRITE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->method(), self::WRITE_METHODS, true)) {
            return $next($request);
        }

        $key = $request->header('Idempotency-Key');
        $service = $request->header('X-Service-Token');

        if (! is_string($key) || $key === '' || ! is_string($service)) {
            return $next($request);
        }

        $existing = IdempotencyKey::query()
            ->where('service', $service)
            ->where('key', $key)
            ->first();

        if ($existing !== null) {
            if ($existing->method !== $request->method() || $existing->path !== $request->getPathInfo()) {
                return response()->json([
                    'message' => 'Idempotency key already used for a different request.',
                ], 409);
            }

            return response()->json($existing->response, $existing->status_code)
                ->header('Idempotent-Replay', 'true');
        }

        $response = $next($request);
        $decoded = json_decode($response->getContent(), true);
        $payload = is_array($decoded) ? $decoded : ['data' => $response->getContent()];

        IdempotencyKey::query()->create([
            'service' => $service,
            'key' => $key,
            'method' => $request->method(),
            'path' => $request->getPathInfo(),
            'status_code' => $response->getStatusCode(),
            'response' => $payload,
        ]);

        return $response;
    }
}

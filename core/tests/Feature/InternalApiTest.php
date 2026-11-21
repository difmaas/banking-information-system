<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Feature tests for the internal API and service authentication middleware.
 */
class InternalApiTest extends TestCase
{
    /**
     * The health endpoint must be reachable without service authentication.
     */
    public function test_health_endpoint_is_public(): void
    {
        $this->getJson('/api/internal/v1/health')
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'service' => 'core',
            ]);
    }

    /**
     * The modules endpoint must reject requests without service authentication.
     */
    public function test_modules_endpoint_requires_service_authentication(): void
    {
        $this->getJson('/api/internal/v1/modules')
            ->assertUnauthorized();
    }

    /**
     * A correctly signed request must be accepted and return module summaries.
     */
    public function test_modules_endpoint_accepts_valid_signature(): void
    {
        config([
            'services.core.service_token' => 'test-token',
            'services.core.hmac_secret' => 'test-secret',
        ]);

        $timestamp = (string) now()->timestamp;
        $canonical = 'GET|/api/internal/v1/modules|'.$timestamp.'|';
        $signature = hash_hmac('sha256', $canonical, 'test-secret');

        $this->get('/api/internal/v1/modules', [
            'Accept' => 'application/json',
            'X-Service-Token' => 'test-token',
            'X-Timestamp' => $timestamp,
            'X-Signature' => $signature,
        ])
            ->assertOk()
            ->assertJsonPath('data.cis.module', 'CIS')
            ->assertJsonPath('data.lns.module', 'LNS')
            ->assertJsonPath('data.gls.module', 'GLS');
    }

    /**
     * A request with an invalid signature must be rejected.
     */
    public function test_modules_endpoint_rejects_invalid_signature(): void
    {
        config([
            'services.core.service_token' => 'test-token',
            'services.core.hmac_secret' => 'test-secret',
        ]);

        $this->getJson('/api/internal/v1/modules', [
            'X-Service-Token' => 'test-token',
            'X-Timestamp' => (string) now()->timestamp,
            'X-Signature' => 'invalid',
        ])->assertUnauthorized();
    }
}

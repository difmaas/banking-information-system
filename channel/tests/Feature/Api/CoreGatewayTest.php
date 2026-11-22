<?php

use App\Contracts\CoreGateway;
use App\Exceptions\CoreGatewayException;
use App\Models\CommunicationLog;
use App\Models\User;
use App\Services\CoreGateway\HttpCoreGateway;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'services.core.driver' => 'http',
        'services.core.base_url' => 'http://localhost:8001',
        'services.core.service_token' => 'test-token',
        'services.core.hmac_secret' => 'test-secret',
    ]);

    app()->bind(CoreGateway::class, HttpCoreGateway::class);
});

test('http gateway sends signed request and forwards idempotency key', function () {
    Http::fake([
        'http://localhost:8001/api/internal/v1/customers/1' => Http::response([
            'id' => 1,
            'name' => 'Core User',
        ]),
    ]);

    request()->headers->set('Idempotency-Key', 'key-1');

    $profile = (new HttpCoreGateway)->getCustomerProfile(1);

    expect($profile['name'])->toBe('Core User');

    Http::assertSent(fn (HttpRequest $request): bool => $request->hasHeader('X-Service-Token', 'test-token')
        && $request->hasHeader('X-Timestamp')
        && $request->hasHeader('X-Signature')
        && $request->hasHeader('Idempotency-Key', 'key-1')
        && str_contains($request->url(), '/api/internal/v1/customers/1'));
});

test('http gateway maps core errors to CoreGatewayException', function () {
    Http::fake([
        'http://localhost:8001/api/internal/v1/customers/1' => Http::response([
            'message' => 'Not found',
        ], 404),
    ]);

    try {
        (new HttpCoreGateway)->getCustomerProfile(1);
        $this->fail('Expected CoreGatewayException was not thrown.');
    } catch (CoreGatewayException $exception) {
        expect($exception->status)->toBe(404)
            ->and($exception->payload['message'])->toBe('Not found');
    }
});

test('create customer payload contains channel user id and provided nik', function () {
    Http::fake([
        'http://localhost:8001/api/internal/v1/customers' => Http::response(['id' => 7], 201),
    ]);

    (new HttpCoreGateway)->createCustomer(7, [
        'nik' => '3204012345678903',
        'phone' => '081234567892',
        'name' => 'Nasabah Tujuh',
        'email' => 'tujuh@example.com',
    ]);

    Http::assertSent(fn (HttpRequest $request): bool => $request->method() === 'POST'
        && $request['channel_user_id'] === 7
        && $request['nik'] === '3204012345678903');

    $log = CommunicationLog::query()->firstOrFail();

    expect($log->request['nik'])->toBe('[redacted]')
        ->and($log->request['phone'])->toBe('[redacted]');
});

test('api login provisions the core customer', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    Http::fake([
        'http://localhost:8001/api/internal/v1/customers' => Http::response(['id' => $user->id], 201),
    ]);

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk();

    Http::assertSent(fn (HttpRequest $request): bool => $request['channel_user_id'] === $user->id);
});

test('provision command backfills all existing users', function () {
    User::factory()->count(2)->create();

    Http::fake([
        'http://localhost:8001/api/internal/v1/customers' => Http::response(['id' => 1], 201),
    ]);

    $this->artisan('bis:provision-customers')->assertExitCode(0);

    Http::assertSentCount(2);
});

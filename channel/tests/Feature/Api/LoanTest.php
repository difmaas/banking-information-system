<?php

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

test('idempotency key replays the original response', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'product_id' => 1,
        'amount' => 5000000,
        'tenor_months' => 12,
    ];
    $headers = ['Idempotency-Key' => (string) Str::uuid()];

    $first = $this->postJson('/api/v1/loans', $payload, $headers);
    $second = $this->postJson('/api/v1/loans', $payload, $headers);

    $first->assertCreated();
    $second->assertCreated()
        ->assertHeader('Idempotent-Replay', 'true');

    expect($second->json('data.id'))->toBe($first->json('data.id'));
});

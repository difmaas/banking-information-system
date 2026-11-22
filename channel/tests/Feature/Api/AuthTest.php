<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

test('api login returns token', function () {
    $user = User::factory()->create([
        'email' => 'nasabah@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => ['user', 'token'],
        ]);
});

test('api me requires authentication', function () {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});

test('api me returns authenticated user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

test('api me returns masked nik only', function () {
    $user = User::factory()->create(['nik' => '3204012345678904']);
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.nik_masked', '3204••••••••8904')
        ->assertJsonMissingPath('data.nik');
});

test('api register creates nasabah and token', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Nasabah Baru',
        'nik' => '3204012345678902',
        'phone' => '081234567891',
        'email' => 'baru@example.com',
        'occupation' => 'Karyawan swasta',
        'monthly_income' => 7500000,
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => '1',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['data' => ['user', 'token']]);

    $user = User::query()->where('email', 'baru@example.com')->firstOrFail();

    expect($user->hasRole('Nasabah'))->toBeTrue()
        ->and($user->nik)->toBe('3204012345678902');
});

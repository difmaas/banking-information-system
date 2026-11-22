<?php

use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'nik' => '3204012345678901',
        'phone' => '081234567890',
        'email' => 'test@example.com',
        'occupation' => 'Karyawan swasta',
        'monthly_income' => 7500000,
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => '1',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $user = auth()->user();

    expect($user->nik)->toBe('3204012345678901')
        ->and($user->phone)->toBe('081234567890');
});

test('registration requires a valid nik and accepted terms', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'nik' => '123',
        'phone' => '12345',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['nik', 'phone', 'occupation', 'monthly_income', 'terms']);
    $this->assertGuest();
});

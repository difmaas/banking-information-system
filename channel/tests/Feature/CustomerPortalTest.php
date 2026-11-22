<?php

use App\Models\User;

test('nasabah can open customer portal pages', function (string $route) {
    $this->actingAs(User::factory()->create());

    $this->get(route($route))->assertOk();
})->with([
    'dashboard',
    'customer.profile',
    'customer.documents',
    'customer.credit-history',
    'customer.loans',
    'customer.loans.create',
    'customer.installments',
    'customer.payments',
]);

test('beranda does not show dense quick actions or profile summary', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Segera hadir')
        ->assertDontSee('Ringkasan profil');
});

test('sidebar does not show account status', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Status akun');
});

test('beranda memuat stylesheet dan class BlatUI', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('stylesheet', false)
        ->assertSee('bg-card', false);
});

test('nasabah can open coming soon feature pages', function (string $feature) {
    $this->actingAs(User::factory()->create());

    $this->get(route('customer.coming-soon', ['feature' => $feature]))
        ->assertOk()
        ->assertSee(__('Segera Hadir'));
})->with([
    'transfer',
    'qris',
    'top-up',
    'tagihan',
    'kartu',
    'promo',
    'bantuan',
]);

test('profile page masks the nik', function () {
    $user = User::factory()->create(['nik' => '3204012345678901']);

    $this->actingAs($user);

    $this->get(route('customer.profile'))
        ->assertOk()
        ->assertDontSee('3204012345678901')
        ->assertSee($user->maskedNik());
});

test('payment receipt page shows the reference', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('customer.payments.receipt', ['ref' => 'PAY-000001']))
        ->assertOk()
        ->assertSee('PAY-000001');
});

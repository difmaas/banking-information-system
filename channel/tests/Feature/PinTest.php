<?php

use App\Livewire\PinLock;
use App\Livewire\PinSetup;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('user tanpa pin wajib mengatur pin terlebih dahulu', function () {
    $user = User::factory()->withoutPin()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('pin.setup'));
});

test('user can set pin saat sudah login', function () {
    $user = User::factory()->withoutPin()->create();

    $this->actingAs($user);

    Livewire::test(PinSetup::class)
        ->set('current_password', 'password')
        ->set('pin', '246810')
        ->set('pin_confirmation', '246810')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user->refresh();

    expect($user->pin_set_at)->not->toBeNull()
        ->and(Hash::check('246810', (string) $user->pin_hash))->toBeTrue()
        ->and($user->pin_hash)->not->toBe('246810');
});

test('pin setup menolak kata sandi salah dan pin lemah', function () {
    $user = User::factory()->withoutPin()->create();

    $this->actingAs($user);

    Livewire::test(PinSetup::class)
        ->set('current_password', 'wrong-password')
        ->set('pin', '123456')
        ->set('pin_confirmation', '123456')
        ->call('save')
        ->assertHasErrors(['current_password', 'pin']);

    expect($user->refresh()->pin_hash)->toBeNull();
});

test('pin lock can lock and unlock session', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Livewire::test(PinLock::class)
        ->call('lockSession')
        ->assertSet('locked', true);

    expect(session('pin_locked'))->toBeTrue();

    $component->set('pin', '000000')
        ->call('unlock')
        ->assertHasErrors(['pin']);

    $component->set('pin', '654321')
        ->call('unlock')
        ->assertHasNoErrors()
        ->assertSet('locked', false);

    expect(session('pin_locked'))->toBeNull();
});

test('pin lockout setelah lima percobaan gagal', function () {
    $user = User::factory()->create(['pin_failed_attempts' => 4]);

    $this->actingAs($user);
    $this->withSession(['pin_locked' => true]);

    Livewire::test(PinLock::class)
        ->set('pin', '000000')
        ->call('unlock')
        ->assertHasErrors(['pin']);

    expect($user->refresh()->isPinLocked())->toBeTrue();
});

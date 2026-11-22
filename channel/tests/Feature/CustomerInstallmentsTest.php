<?php

use App\Contracts\CoreGateway;
use App\Models\User;
use Livewire\Livewire;
use Mockery;

test('installments page uses the customer loan id instead of a hardcoded id', function () {
    $user = User::factory()->create();

    $gateway = Mockery::mock(CoreGateway::class);
    $gateway->shouldReceive('getCustomerLoans')
        ->once()
        ->with($user->id)
        ->andReturn([['id' => 42, 'status' => 'Aktif']]);
    $gateway->shouldReceive('getCustomerLoanInstallments')
        ->once()
        ->with($user->id, 42)
        ->andReturn([['number' => 1, 'due_date' => '2026-12-01', 'amount' => 100000, 'status' => 'Belum Lunas']]);

    app()->instance(CoreGateway::class, $gateway);

    $this->actingAs($user);

    Livewire::test('pages::customer.installments')
        ->assertSee('2026-12-01');
});

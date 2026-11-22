<?php

use App\Models\User;
use Livewire\Livewire;

test('nasabah can simulate and submit a loan application', function () {
    $this->withSession(['pin_verified_at' => now()->timestamp]);
    $this->actingAs(User::factory()->create());

    $component = Livewire::test('pages::customer.loan-application')
        ->set('amount', '5000000')
        ->set('tenor_months', '6')
        ->call('simulate')
        ->assertHasNoErrors()
        ->assertSet('simulation.monthly_installment', 895833.33);

    $component->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('customer.loans'));
});

test('loan submission is blocked when required documents are missing', function () {
    $this->actingAs(User::factory()->create());

    $component = Livewire::test('pages::customer.loan-application')
        ->set('amount', '5000000')
        ->set('tenor_months', '6')
        ->set('documents', [
            ['type' => 'ktp', 'name' => 'ktp.jpg', 'size' => 100],
        ]);

    expect($component->instance()->missingDocuments())->toBe(['kk', 'slip_gaji']);

    $component->call('submit')->assertHasNoErrors();
});

test('loan submission requires pin confirmation', function () {
    $this->actingAs(User::factory()->create());

    $component = Livewire::test('pages::customer.loan-application')
        ->set('amount', '5000000')
        ->set('tenor_months', '6')
        ->call('submit')
        ->assertSet('needsPin', true);

    $component->set('pin', '654321')
        ->call('confirmAndSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('customer.loans'));
});

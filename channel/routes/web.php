<?php

use App\Livewire\PinSetup;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('pin/setup', PinSetup::class)->name('pin.setup');
});

Route::middleware(['auth', 'verified', 'pin.setup'])->group(function () {
    Route::livewire('dashboard', 'pages::customer.dashboard')->name('dashboard');

    Route::livewire('customer/profile', 'pages::customer.profile')->name('customer.profile');
    Route::livewire('customer/documents', 'pages::customer.documents')->name('customer.documents');
    Route::livewire('customer/credit-history', 'pages::customer.credit-history')->name('customer.credit-history');
    Route::livewire('customer/loans', 'pages::customer.loans')->name('customer.loans');
    Route::livewire('customer/loans/create', 'pages::customer.loan-application')->name('customer.loans.create');
    Route::livewire('customer/installments', 'pages::customer.installments')->name('customer.installments');
    Route::livewire('customer/payments', 'pages::customer.payments')->name('customer.payments');
    Route::livewire('customer/payments/receipt', 'pages::customer.payment-receipt')->name('customer.payments.receipt');
    Route::livewire('customer/fitur/{feature}', 'pages::customer.coming-soon')
        ->where('feature', 'transfer|qris|top-up|tagihan|kartu|promo|bantuan')
        ->name('customer.coming-soon');
});

require __DIR__.'/settings.php';

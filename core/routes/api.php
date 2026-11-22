<?php

/**
 * Internal API routes consumed by the channel middleware.
 *
 * The health endpoint is public for monitoring. All other endpoints require
 * the service.auth middleware (HMAC signature and service token).
 */

use App\Http\Controllers\Api\Internal\V1\CreditHistoryController;
use App\Http\Controllers\Api\Internal\V1\CustomerController;
use App\Http\Controllers\Api\Internal\V1\DocumentController;
use App\Http\Controllers\Api\Internal\V1\LoanController;
use App\Http\Controllers\Api\Internal\V1\LoanProductController;
use App\Http\Controllers\Api\Internal\V1\LoanScheduleController;
use App\Http\Controllers\Api\Internal\V1\LoanSimulationController;
use App\Http\Controllers\Api\Internal\V1\ModuleController;
use App\Http\Controllers\Api\Internal\V1\ParameterController;
use Illuminate\Support\Facades\Route;

Route::prefix('internal/v1')->group(function () {
    // Public health check used by the middleware and monitoring tools.
    Route::get('/health', fn () => response()->json([
        'status' => 'ok',
        'service' => 'core',
    ]))->name('internal.health');

    Route::middleware('service.auth')->group(function () {
        Route::get('/modules', [ModuleController::class, 'index'])->name('internal.modules');

        // Customer Information System.
        Route::post('/customers', [CustomerController::class, 'store'])->middleware('idempotency');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->whereNumber('customer');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])
            ->whereNumber('customer')
            ->middleware('idempotency');

        Route::get('/customers/{customer}/documents', [DocumentController::class, 'index'])->whereNumber('customer');
        Route::post('/customers/{customer}/documents', [DocumentController::class, 'store'])
            ->whereNumber('customer')
            ->middleware('idempotency');

        Route::get('/customers/{customer}/credit-history', [CreditHistoryController::class, 'index'])->whereNumber('customer');

        // Loan Management System - customer-facing endpoints.
        Route::get('/loan-products', [LoanProductController::class, 'index']);
        Route::post('/loan-simulations', [LoanSimulationController::class, 'simulate']);
        Route::get('/parameters/required-documents', [ParameterController::class, 'requiredDocuments']);
        Route::get('/customers/{customer}/loans', [LoanController::class, 'index'])->whereNumber('customer');
        Route::post('/customers/{customer}/loans', [LoanController::class, 'store'])
            ->whereNumber('customer')
            ->middleware('idempotency');
        Route::get('/customers/{customer}/loans/{loan}', [LoanController::class, 'show'])
            ->whereNumber(['customer', 'loan']);
        Route::get('/customers/{customer}/loans/{loan}/installments', [LoanScheduleController::class, 'installments'])
            ->whereNumber(['customer', 'loan']);
        Route::get('/customers/{customer}/loans/{loan}/payments', [LoanScheduleController::class, 'payments'])
            ->whereNumber(['customer', 'loan']);
    });
});

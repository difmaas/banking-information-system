<?php

/**
 * Internal API routes consumed by the channel middleware.
 *
 * The health endpoint is public for monitoring. All other endpoints require
 * the service.auth middleware (HMAC signature and service token).
 */

use App\Http\Controllers\Api\Internal\V1\ModuleController;
use Illuminate\Support\Facades\Route;

Route::prefix('internal/v1')->group(function () {
    // Public health check used by the middleware and monitoring tools.
    Route::get('/health', fn () => response()->json([
        'status' => 'ok',
        'service' => 'core',
    ]))->name('internal.health');

    // Module discovery and future internal service endpoints.
    Route::middleware('service.auth')->group(function () {
        Route::get('/modules', [ModuleController::class, 'index'])->name('internal.modules');
    });
});

<?php

use App\Http\Controllers\Api\Internal\V1\ModuleController;
use Illuminate\Support\Facades\Route;

Route::prefix('internal/v1')->group(function () {
    Route::get('/health', fn () => response()->json([
        'status' => 'ok',
        'service' => 'core',
    ]))->name('internal.health');

    Route::middleware('service.auth')->group(function () {
        Route::get('/modules', [ModuleController::class, 'index'])->name('internal.modules');
    });
});
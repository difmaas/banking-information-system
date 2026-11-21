<?php

namespace App\Modules\Gls\Providers;

use App\Modules\Gls\Services\GlsService;
use Illuminate\Support\ServiceProvider;

/**
 * Registers GLS module services in the Laravel container.
 */
class GlsServiceProvider extends ServiceProvider
{
    /**
     * Bind the GLS service as a singleton.
     */
    public function register(): void
    {
        $this->app->singleton(GlsService::class);
    }
}

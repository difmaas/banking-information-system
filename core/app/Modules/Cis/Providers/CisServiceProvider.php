<?php

namespace App\Modules\Cis\Providers;

use App\Modules\Cis\Services\CisService;
use Illuminate\Support\ServiceProvider;

/**
 * Registers CIS module services in the Laravel container.
 */
class CisServiceProvider extends ServiceProvider
{
    /**
     * Bind the CIS service as a singleton.
     */
    public function register(): void
    {
        $this->app->singleton(CisService::class);
    }
}

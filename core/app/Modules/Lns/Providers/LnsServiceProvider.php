<?php

namespace App\Modules\Lns\Providers;

use App\Modules\Lns\Services\LnsService;
use Illuminate\Support\ServiceProvider;

/**
 * Registers LNS module services in the Laravel container.
 */
class LnsServiceProvider extends ServiceProvider
{
    /**
     * Bind the LNS service as a singleton.
     */
    public function register(): void
    {
        $this->app->singleton(LnsService::class);
    }
}

<?php

namespace App\Modules\Gls\Providers;

use App\Modules\Gls\Services\GlsService;
use Illuminate\Support\ServiceProvider;

class GlsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GlsService::class);
    }
}
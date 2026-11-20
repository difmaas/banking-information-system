<?php

namespace App\Modules\Cis\Providers;

use App\Modules\Cis\Services\CisService;
use Illuminate\Support\ServiceProvider;

class CisServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CisService::class);
    }
}
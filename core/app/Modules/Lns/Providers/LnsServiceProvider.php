<?php

namespace App\Modules\Lns\Providers;

use App\Modules\Lns\Services\LnsService;
use Illuminate\Support\ServiceProvider;

class LnsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LnsService::class);
    }
}
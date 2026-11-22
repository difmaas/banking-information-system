<?php

namespace App\Providers;

use App\Models\SystemParameter;
use App\Models\User;
use App\Modules\Cis\Models\Customer;
use App\Modules\Cis\Models\CustomerDocument;
use App\Observers\CustomerDocumentObserver;
use App\Observers\CustomerObserver;
use App\Observers\RoleObserver;
use App\Observers\SystemParameterObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SystemParameter::observe(SystemParameterObserver::class);
        User::observe(UserObserver::class);
        Role::observe(RoleObserver::class);
        Customer::observe(CustomerObserver::class);
        CustomerDocument::observe(CustomerDocumentObserver::class);
    }
}

<?php

namespace App\Providers;

use App\Actions\EnsureCoreCustomer;
use App\Contracts\CoreGateway;
use App\Models\User;
use App\Services\CoreGateway\HttpCoreGateway;
use App\Services\CoreGateway\StubCoreGateway;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $driver = config('services.core.driver', 'stub');

        $this->app->bind(
            CoreGateway::class,
            $driver === 'http' ? HttpCoreGateway::class : StubCoreGateway::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureCoreCustomerProvisioning();
    }

    /**
     * Provision the matching core customer whenever a user logs in.
     */
    protected function configureCoreCustomerProvisioning(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            if ($event->guard === 'web' && $event->user instanceof User) {
                app(EnsureCoreCustomer::class)->handle($event->user);
            }
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}

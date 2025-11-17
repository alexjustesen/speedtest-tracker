<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\AppriseChannel;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Http\Request;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->defineCustomIfStatements();
        $this->defineGates();
        $this->forceHttps();
        $this->setApiRateLimit();
        $this->registerNotificationChannels();

        AboutCommand::add('Speedtest Tracker', fn () => [
            'Version' => config('speedtest.build_version'),
        ]);
    }

    /**
     * Register custom notification channels.
     */
    protected function registerNotificationChannels(): void
    {
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('apprise', function ($app) {
                return new AppriseChannel;
            });
        });
    }

    /**
     * Define custom if statements, these were added to make the blade templates more readable.
     *
     * Ref: https://github.com/laravel/framework/pull/51561
     */
    protected function defineCustomIfStatements(): void
    {
        /**
         * Adds blank() custom if statement.
         */
        Blade::if('blank', function (mixed $value) {
            return blank($value);
        });

        /**
         * Adds filled() custom if statement.
         */
        Blade::if('filled', function (mixed $value) {
            return filled($value);
        });
    }

    /**
     * Define any application gates.
     */
    protected function defineGates(): void
    {
        Gate::define('access-admin-panel', function (User $user) {
            return in_array($user->role, [UserRole::Admin, UserRole::User]);
        });

        Gate::define('view-dashboard', function (?User $user) {
            if (config('speedtest.public_dashboard')) {
                return true;
            }

            if ($user === null) {
                return false;
            }

            return in_array($user->role, [UserRole::Admin, UserRole::User]);
        });
    }

    /**
     * Force https scheme in non-local environments.
     */
    protected function forceHttps(): void
    {
        if (! app()->environment('local') && config('app.force_https')) {
            URL::forceScheme('https');
        }
    }

    protected function setApiRateLimit(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(config('api.rate_limit'));
        });
    }
}

<?php

namespace App\Providers;

use App\Services\SystemChecker;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);

            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        $system = new SystemChecker;

        AboutCommand::add('Speedtest Tracker', fn () => [
            'Version' => $system->getLocalVersion(),
            'Out of date' => $system->isOutOfDate() ? 'Yes' : 'No',
        ]);
    }
}

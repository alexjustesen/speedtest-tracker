<?php

namespace App\Providers;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;
use RyanChandler\FilamentLog\Logs;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Logs::can(function (User $user) {
            return config('app.debug');
        });

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                'Settings',
                'Links',
            ]);

            Filament::registerNavigationItems([
                NavigationItem::make('Documentation')
                    ->url('https://docs.speedtest-tracker.dev/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-external-link')
                    ->group('Links')
                    ->sort(0),
                NavigationItem::make('Donate')
                    ->url('https://github.com/sponsors/alexjustesen', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-external-link')
                    ->group('Links')
                    ->sort(1),
                NavigationItem::make('Source Code')
                    ->url('https://github.com/alexjustesen/speedtest-tracker', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-external-link')
                    ->group('Links')
                    ->sort(2),
            ]);
        });
    }
}

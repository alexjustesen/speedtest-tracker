<?php

namespace App\Providers;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use FilamentVersions\Facades\FilamentVersions;
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
            return true;
        });

        FilamentVersions::addItem('Speedtest Tracker', 'v0.9.1');

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                'Settings',
                'System',
                'Links',
            ]);

            Filament::registerNavigationItems([
                NavigationItem::make('Documentation')
                    ->url('https://docs.speedtest-tracker.dev/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-book-open')
                    ->group('Links')
                    ->sort(0),
                NavigationItem::make('Donate')
                    ->url('https://github.com/sponsors/alexjustesen', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-cash')
                    ->group('Links')
                    ->sort(1),
                NavigationItem::make('Source Code')
                    ->url('https://github.com/alexjustesen/speedtest-tracker', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-code')
                    ->group('Links')
                    ->sort(2),
            ]);
        });
    }
}

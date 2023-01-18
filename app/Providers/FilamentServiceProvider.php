<?php

namespace App\Providers;

use App\Settings\GeneralSettings;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use FilamentVersions\Facades\FilamentVersions;
use Illuminate\Support\ServiceProvider;

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
        try {
            config(['filament.brand' => app(GeneralSettings::class)->site_name ?? env('APP_NAME')]);
        } catch (\Throwable $th) {
            // if this fails it's because the migration doesn't exist so it can be skipped
        }

        FilamentVersions::addItem('Speedtest Tracker', 'v'.config('speedtest.build_version'));

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

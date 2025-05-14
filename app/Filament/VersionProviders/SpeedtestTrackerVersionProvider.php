<?php

namespace App\Filament\VersionProviders;

use Awcodes\FilamentVersions\Providers\Contracts\VersionProvider;

class SpeedtestTrackerVersionProvider implements VersionProvider
{
    public function getName(): string
    {
        return 'App';
    }

    public function getVersion(): string
    {
        return app()->isProduction()
            ? (config('speedtest.build_version'))
            : config('app.env');
    }
}

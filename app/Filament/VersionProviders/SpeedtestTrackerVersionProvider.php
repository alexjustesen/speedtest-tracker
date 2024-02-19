<?php

namespace App\Filament\VersionProviders;

use App\Services\SystemChecker;
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
            ? (new SystemChecker)->getLocalVersion()
            : config('app.env');
    }
}

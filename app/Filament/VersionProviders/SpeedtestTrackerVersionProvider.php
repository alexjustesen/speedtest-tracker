<?php

use Awcodes\FilamentVersions\Providers\Contracts\VersionProvider;

class SpeedtestTrackerVersionProvider implements VersionProvider
{
    public function getName(): string
    {
        return 'Speedtest Tracker';
    }

    public function getVersion(): string
    {
        return 'v'.config('speedtest.build_version');
    }
}

<?php

use Awcodes\FilamentVersions\Providers\Contracts\VersionProvider;

class SpeedtestTrackerVersionProvider implements VersionProvider
{
    public function getName(): string
    {
        return 'App';
    }

    public function getVersion(): string
    {
        return config('speedtest.build_version');
    }
}

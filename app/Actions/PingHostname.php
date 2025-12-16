<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Ping\Ping;
use Spatie\Ping\PingResult;

class PingHostname
{
    use AsAction;

    public function handle(?string $hostname = null, int $count = 1): PingResult
    {
        $hostname = $hostname ?? config('speedtest.preflight.check_internet_connectivity_url');

        // Remove protocol if present
        $hostname = preg_replace('#^https?://#', '', $hostname);

        $ping = (new Ping(
            hostname: $hostname,
            count: $count,
        ))->run();

        return $ping;
    }
}

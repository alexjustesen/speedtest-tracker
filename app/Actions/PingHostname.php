<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Ping\Ping;
use Spatie\Ping\PingResult;

class PingHostname
{
    use AsAction;

    public function handle(?string $hostname = null, int $count = 1): PingResult
    {
        $hostname = $hostname ?? config('speedtest.preflight.internet_check_hostname');

        // Remove protocol if present
        $hostname = preg_replace('#^https?://#', '', $hostname);

        $ping = (new Ping(
            hostname: $hostname,
            count: $count,
        ))->run();

        $data = $ping->toArray();
        unset($data['raw_output'], $data['lines']);

        Log::debug('Pinged hostname', [
            'host' => $hostname,
            'data' => $data,
        ]);

        return $ping;
    }
}

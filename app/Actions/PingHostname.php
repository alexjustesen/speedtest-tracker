<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Ping\Ping;
use Spatie\Ping\PingResult;
use Throwable;

class PingHostname
{
    use AsAction;

    /**
     * Attempt to ping the given hostname. Returns null when the ping binary
     * is unavailable or another OS-level error prevents execution.
     */
    public function handle(?string $hostname = null, int $count = 1): ?PingResult
    {
        $hostname = $hostname ?? config('speedtest.preflight.internet_check_hostname');

        // Remove protocol if present
        $hostname = preg_replace('#^https?://#', '', $hostname);

        try {
            $ping = (new Ping(
                hostname: $hostname,
                count: $count,
            ))->run();
        } catch (Throwable $e) {
            Log::debug('Ping command unavailable', [
                'host' => $hostname,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        $data = $ping->toArray();
        unset($data['raw_output'], $data['lines']);

        Log::debug('Pinged hostname', [
            'host' => $hostname,
            'data' => $data,
        ]);

        return $ping;
    }
}

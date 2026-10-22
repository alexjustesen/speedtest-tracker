<?php

namespace App\Services\Speedtests;

use RuntimeException;

/**
 * Downloads a file from any HTTP(S) URL, measuring download speed and latency only.
 */
class FileDownloadSpeedtest extends HttpSpeedtest
{
    protected function plan(?string $target): HttpPlan
    {
        $url = trim((string) $target);

        if (! filter_var($url, FILTER_VALIDATE_URL) || ! in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
            throw new RuntimeException(blank($url)
                ? 'No file URL was provided, add one under Settings → Speedtests or enter a URL when starting the speedtest.'
                : sprintf('"%s" is not a valid HTTP(S) URL.', $url));
        }

        $host = parse_url($url, PHP_URL_HOST);

        return new HttpPlan(
            latencyUrl: $url,
            downloadUrls: [$url],
            rangedLatency: true,
            server: [
                'name' => $host,
                'host' => $host,
                'url' => $url,
            ],
        );
    }
}

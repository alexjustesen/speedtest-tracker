<?php

namespace App\Services\Speedtests;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Measures against the Cloudflare edge, using the endpoints behind speed.cloudflare.com.
 */
class CloudflareSpeedtest extends HttpSpeedtest
{
    public const BASE_URL = 'https://speed.cloudflare.com';

    /**
     * Bytes requested per download, finished requests are re-issued until the test window closes.
     */
    public const DOWNLOAD_BYTES = 25_000_000;

    protected function plan(?string $target): HttpPlan
    {
        $meta = $this->meta();

        return new HttpPlan(
            latencyUrl: self::BASE_URL.'/__down?bytes=0',
            downloadUrls: [self::BASE_URL.'/__down?bytes='.self::DOWNLOAD_BYTES],
            uploadUrls: [self::BASE_URL.'/__up'],
            uploadChunk: 4_000_000,
            server: [
                'name' => 'Cloudflare',
                'location' => Arr::get($meta, 'colo.city'),
                'country' => Arr::get($meta, 'colo.cca2'),
                'host' => parse_url(self::BASE_URL, PHP_URL_HOST),
                'colo' => Arr::get($meta, 'colo.iata'),
            ],
            isp: Arr::get($meta, 'asOrganization'),
            externalIp: Arr::get($meta, 'clientIp'),
            headers: ['Referer' => self::BASE_URL.'/'],
        );
    }

    /**
     * Connection details as seen by Cloudflare: client IP, ISP and the serving data center.
     */
    protected function meta(): array
    {
        try {
            return Http::withHeaders(['Referer' => self::BASE_URL.'/'])
                ->timeout(10)
                ->get(self::BASE_URL.'/meta')
                ->throw()
                ->json() ?? [];
        } catch (Throwable $e) {
            Log::warning('Unable to retrieve Cloudflare connection metadata.', [$e->getMessage()]);

            return [];
        }
    }
}

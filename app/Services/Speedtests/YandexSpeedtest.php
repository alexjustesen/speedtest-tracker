<?php

namespace App\Services\Speedtests;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

/**
 * Measures against the Yandex CDN nodes picked by Yandex Internetometer (yandex.ru/internet).
 */
class YandexSpeedtest extends HttpSpeedtest
{
    public const PROBES_URL = 'https://yandex.ru/internet/api/v0/get-probes';

    protected function plan(?string $target): HttpPlan
    {
        return self::planFromProbes($this->probes());
    }

    /**
     * Turn the probes returned by Yandex into endpoints. Probes with a "timeout" are the short
     * warm-up requests the web client sends first, the others are the measurement ones.
     */
    public static function planFromProbes(array $probes): HttpPlan
    {
        $latencyUrl = Arr::get($probes, 'latency.probes.0.url');

        if (blank($latencyUrl)) {
            throw new RuntimeException('Yandex Internetometer did not return any latency probes.');
        }

        $downloads = collect(Arr::get($probes, 'download.probes', []))
            ->filter(fn (array $probe): bool => ! isset($probe['timeout']) && filled($probe['url'] ?? null))
            ->pluck('url')
            ->values()
            ->toArray();

        $uploads = collect(Arr::get($probes, 'upload.probes', []))
            ->filter(fn (array $probe): bool => ! isset($probe['timeout']) && filled($probe['postUrl'] ?? null))
            ->pluck('postUrl')
            ->values()
            ->toArray();

        $host = parse_url($downloads[0] ?? $latencyUrl, PHP_URL_HOST);

        return new HttpPlan(
            latencyUrl: $latencyUrl,
            downloadUrls: $downloads,
            uploadUrls: $uploads,
            uploadChunk: 4_000_000,
            server: [
                'name' => 'Yandex',
                'location' => self::location($host),
                'host' => $host,
            ],
        );
    }

    /**
     * Yandex CDN hosts are named after the data center, e.g. "cloudcdn-fra-02.cdn.yandex.net".
     */
    protected static function location(?string $host): ?string
    {
        if (preg_match('/^[a-z]+-([a-z]{3})-\d+\./i', (string) $host, $matches)) {
            return strtoupper($matches[1]);
        }

        return null;
    }

    protected function probes(): array
    {
        try {
            $probes = Http::timeout(10)
                ->retry(2, 250)
                ->get(self::PROBES_URL, [
                    'flag_ws-conn-timeout' => 2000,
                    'flag_speedtest-worker' => 0,
                ])
                ->throw()
                ->json();
        } catch (Throwable $e) {
            throw new RuntimeException('Unable to retrieve Yandex Internetometer probes: '.$e->getMessage(), previous: $e);
        }

        if (! is_array($probes)) {
            throw new RuntimeException('Yandex Internetometer returned an unexpected response.');
        }

        return $probes;
    }
}

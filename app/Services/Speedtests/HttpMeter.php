<?php

namespace App\Services\Speedtests;

use App\Settings\SpeedtestSettings;
use CurlHandle;
use RuntimeException;

/**
 * Measures latency and throughput over HTTP with parallel streams, the same way
 * browser based speedtests do: several connections transfer data for a fixed
 * window and the bytes moved after a short warm-up are counted.
 */
class HttpMeter
{
    /**
     * Pause between latency probes sent while a transfer is running.
     */
    private const PROBE_GAP = 0.25;

    /**
     * Ranged latency requests are cut off after this many bytes when the server ignores the range.
     */
    private const PROBE_MAX_BYTES = 65536;

    public function __construct(
        protected int $streams = 4,
        protected float $duration = 10,
        protected float $warmup = 1,
        protected array $headers = [],
    ) {}

    public static function fromSettings(array $headers = []): static
    {
        $settings = app(SpeedtestSettings::class);

        return new static(
            streams: max($settings->http_streams, 1),
            duration: max($settings->http_duration, 1),
            headers: $headers,
        );
    }

    /**
     * Measure the idle round trip time in milliseconds with sequential requests over one connection.
     *
     * A ranged request asks for the first byte only, for files on servers that are not
     * a speedtest endpoint (some of them do not answer HEAD requests).
     */
    public function latency(string $url, int $samples = 10, bool $ranged = false): array
    {
        $serverTime = 0.0;
        $handle = $this->probeHandle($url, $ranged, $serverTime);
        curl_setopt($handle, CURLOPT_TIMEOUT, 10);

        $times = [];

        // The first request opens the connection (DNS, TCP and TLS) and is not counted.
        for ($i = 0; $i <= $samples; $i++) {
            $serverTime = 0.0;

            curl_exec($handle);
            $result = curl_errno($handle);

            if (! self::probeSucceeded($handle, $result)) {
                throw new RuntimeException($this->failure($handle, $result) ?? sprintf('Latency request to "%s" failed.', self::host($url)));
            }

            if ($i > 0) {
                $times[] = self::roundTrip($handle, $serverTime);
            }
        }

        return $times;
    }

    /**
     * Download from the given URLs in parallel streams.
     *
     * @return array{bytes: int, elapsed: int, bandwidth: int, latency: array}
     */
    public function download(array $urls, ?string $probeUrl = null, bool $rangedProbe = false): array
    {
        return $this->transfer(
            factory: fn (int $stream, callable $count): CurlHandle => $this->downloadHandle($urls[$stream % count($urls)], $count),
            probeUrl: $probeUrl,
            rangedProbe: $rangedProbe,
        );
    }

    /**
     * Upload chunks of random data to the given URLs in parallel streams.
     *
     * @return array{bytes: int, elapsed: int, bandwidth: int, latency: array}
     */
    public function upload(array $urls, int $chunkSize, ?string $probeUrl = null, bool $rangedProbe = false): array
    {
        $payload = random_bytes($chunkSize);

        return $this->transfer(
            factory: fn (int $stream, callable $count): CurlHandle => $this->uploadHandle($urls[$stream % count($urls)], $payload, $count),
            probeUrl: $probeUrl,
            rangedProbe: $rangedProbe,
        );
    }

    /**
     * Keep the streams busy until the window closes, re-issuing requests that finish early,
     * and sample the latency under load alongside.
     */
    protected function transfer(callable $factory, ?string $probeUrl, bool $rangedProbe): array
    {
        $multi = curl_multi_init();

        // One TCP connection per stream, like the browser based tests.
        curl_multi_setopt($multi, CURLMOPT_PIPELINING, CURLPIPE_NOTHING);

        $warmupEnd = microtime(true) + $this->warmup;
        $deadline = $warmupEnd + $this->duration;

        $bytes = 0;
        $lastAt = $warmupEnd;
        $count = function (int $length) use (&$bytes, &$lastAt, $warmupEnd, $deadline): void {
            $now = microtime(true);

            if ($now >= $warmupEnd && $now < $deadline) {
                $bytes += $length;
                $lastAt = $now;
            }
        };

        $streams = [];

        for ($stream = 0; $stream < $this->streams; $stream++) {
            $handle = $factory($stream, $count);
            curl_multi_add_handle($multi, $handle);
            $streams[spl_object_id($handle)] = [$handle, $stream];
        }

        $error = null;
        $probe = null;
        $probeServerTime = 0.0;
        $probeSamples = [];
        $nextProbeAt = $warmupEnd;

        while (microtime(true) < $deadline && ! empty($streams)) {
            curl_multi_exec($multi, $running);

            while ($info = curl_multi_info_read($multi)) {
                $handle = $info['handle'];
                curl_multi_remove_handle($multi, $handle);

                if ($handle === $probe) {
                    if (self::probeSucceeded($handle, $info['result'])) {
                        $probeSamples[] = self::roundTrip($handle, $probeServerTime);
                    }

                    $probe = null;
                    $nextProbeAt = microtime(true) + self::PROBE_GAP;

                    continue;
                }

                [, $stream] = $streams[spl_object_id($handle)];
                unset($streams[spl_object_id($handle)]);

                $failure = $this->failure($handle, $info['result']);

                if ($failure !== null) {
                    // Keep the remaining streams running, a single failed request should not void the test.
                    $error ??= $failure;

                    continue;
                }

                if (microtime(true) < $deadline) {
                    $next = $factory($stream, $count);
                    curl_multi_add_handle($multi, $next);
                    $streams[spl_object_id($next)] = [$next, $stream];
                }
            }

            if ($probeUrl !== null && $probe === null && microtime(true) >= $nextProbeAt) {
                $probeServerTime = 0.0;
                $probe = $this->probeHandle($probeUrl, $rangedProbe, $probeServerTime);
                curl_multi_add_handle($multi, $probe);
            }

            curl_multi_select($multi, 0.05);
        }

        foreach ($streams as [$handle]) {
            curl_multi_remove_handle($multi, $handle);
        }

        if ($probe !== null) {
            curl_multi_remove_handle($multi, $probe);
        }

        curl_multi_close($multi);

        if ($bytes === 0) {
            throw new RuntimeException($error ?? 'No data was transferred during the test window.');
        }

        $elapsed = max($lastAt - $warmupEnd, 0.001);

        return [
            'bytes' => $bytes,
            'elapsed' => (int) round($elapsed * 1000),
            'bandwidth' => (int) round($bytes / $elapsed),
            'latency' => $probeSamples,
        ];
    }

    protected function downloadHandle(string $url, callable $count): CurlHandle
    {
        $handle = $this->handle($url);

        curl_setopt($handle, CURLOPT_WRITEFUNCTION, function (CurlHandle $handle, string $data) use ($count): int {
            // Returning less than the received length aborts the request, see failure().
            if (curl_getinfo($handle, CURLINFO_RESPONSE_CODE) >= 400 || self::isWebPage($handle)) {
                return 0;
            }

            $count(strlen($data));

            return strlen($data);
        });

        return $handle;
    }

    protected function uploadHandle(string $url, string $payload, callable $count): CurlHandle
    {
        $handle = $this->handle($url);
        $sent = 0;

        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [...$this->headerLines(), 'Content-Type: application/octet-stream', 'Expect:'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOPROGRESS => false,
            CURLOPT_XFERINFOFUNCTION => function (CurlHandle $handle, int $downloadTotal, int $downloaded, int $uploadTotal, int $uploaded) use (&$sent, $count): int {
                if ($uploaded > $sent) {
                    $count($uploaded - $sent);
                    $sent = $uploaded;
                }

                return 0;
            },
        ]);

        return $handle;
    }

    protected function probeHandle(string $url, bool $ranged, float &$serverTime): CurlHandle
    {
        $handle = $this->handle($url);
        $received = 0;

        curl_setopt_array($handle, array_filter([
            CURLOPT_RANGE => $ranged ? '0-0' : null,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_WRITEFUNCTION => function (CurlHandle $handle, string $data) use (&$received): int {
                $received += strlen($data);

                // Stop servers that ignore the range from sending the whole file.
                return $received > self::PROBE_MAX_BYTES ? 0 : strlen($data);
            },
            CURLOPT_HEADERFUNCTION => function (CurlHandle $handle, string $header) use (&$serverTime, &$received): int {
                if (str_starts_with($header, 'HTTP/')) {
                    $received = 0;
                }

                $serverTime += self::serverTiming($header);

                return strlen($header);
            },
        ], fn ($value): bool => $value !== null));

        return $handle;
    }

    /**
     * A probe counts when the server answered, even if the body was cut off on purpose.
     */
    protected static function probeSucceeded(CurlHandle $handle, int $result): bool
    {
        return in_array($result, [CURLE_OK, CURLE_WRITE_ERROR], true)
            && curl_getinfo($handle, CURLINFO_RESPONSE_CODE) > 0
            && curl_getinfo($handle, CURLINFO_RESPONSE_CODE) < 400;
    }

    protected function handle(string $url): CurlHandle
    {
        $handle = curl_init($url);

        curl_setopt_array($handle, array_filter([
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => (int) ceil($this->warmup + $this->duration) + 30,
            CURLOPT_USERAGENT => 'speedtest-tracker/'.config('speedtest.build_version'),
            CURLOPT_HTTPHEADER => $this->headerLines(),
            CURLOPT_ENCODING => 'identity',
            CURLOPT_INTERFACE => config('speedtest.interface') ?: null,
        ], fn ($value): bool => $value !== null));

        return $handle;
    }

    protected function headerLines(): array
    {
        return collect($this->headers)
            ->map(fn (string $value, string $name): string => $name.': '.$value)
            ->values()
            ->toArray();
    }

    /**
     * Describe why a finished request failed, or null when it succeeded.
     */
    protected function failure(CurlHandle $handle, int $result): ?string
    {
        $url = (string) curl_getinfo($handle, CURLINFO_EFFECTIVE_URL);
        $status = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);

        if ($status >= 400) {
            return sprintf('"%s" responded with HTTP %d.', self::host($url), $status);
        }

        if (self::isWebPage($handle)) {
            return sprintf('"%s" returned a web page instead of a file.', $url);
        }

        if ($result !== CURLE_OK) {
            return sprintf('Request to "%s" failed: %s', self::host($url), curl_strerror($result));
        }

        return null;
    }

    /**
     * Time from sending the request to the first response byte, minus the time
     * the server reports it spent on the request.
     */
    protected static function roundTrip(CurlHandle $handle, float $serverTime): float
    {
        $ttfb = (curl_getinfo($handle, CURLINFO_STARTTRANSFER_TIME_T) - curl_getinfo($handle, CURLINFO_PRETRANSFER_TIME_T)) / 1000;
        $rtt = $ttfb - $serverTime;

        return round($rtt > 0 ? $rtt : $ttfb, 3);
    }

    /**
     * Sum the durations of a "Server-Timing" header in milliseconds.
     */
    protected static function serverTiming(string $header): float
    {
        if (! preg_match('/^server-timing:(.*)$/i', trim($header), $matches)) {
            return 0;
        }

        preg_match_all('/dur=([\d.]+)/', $matches[1], $durations);

        return array_sum(array_map('floatval', $durations[1]));
    }

    protected static function isWebPage(CurlHandle $handle): bool
    {
        return str_starts_with(strtolower((string) curl_getinfo($handle, CURLINFO_CONTENT_TYPE)), 'text/html');
    }

    protected static function host(string $url): string
    {
        return parse_url($url, PHP_URL_HOST) ?: $url;
    }
}

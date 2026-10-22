<?php

namespace App\Services\Speedtests;

use App\Actions\PingHostname;
use App\Services\Speedtests\Contracts\Speedtest;
use App\Settings\SpeedtestSettings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Measures against an iPerf3 server with the iperf3 CLI: a reverse run for the
 * download, a normal run for the upload and an ICMP ping for the latency.
 */
class Iperf3Speedtest implements Speedtest
{
    public const DEFAULT_PORT = 5201;

    /**
     * Public iPerf3 servers run one test at a time, a busy server is retried this many times.
     */
    private const ATTEMPTS = 3;

    public function run(?string $target = null): array
    {
        [$host, $port] = self::parseTarget($target);

        $ping = PingHostname::run(hostname: $host, count: 5);

        $download = $this->measure($host, $port, reverse: true);

        $upload = $this->measure($host, $port, reverse: false);

        $pingStats = $ping?->isSuccess()
            ? [
                'latency' => $ping->averageTimeInMs(),
                'jitter' => $ping->standardDeviationTimeInMs(),
                'low' => $ping->minimumTimeInMs(),
                'high' => $ping->maximumTimeInMs(),
            ]
            : self::pingFromTcp($upload);

        return SpeedtestOutput::make(
            ping: $pingStats,
            download: $download,
            upload: $upload,
            server: [
                'name' => $host,
                'host' => $host,
                'port' => $port,
                'ip' => $upload['ip'] ?? $download['ip'] ?? null,
            ],
            packetLoss: $ping?->isSuccess() ? $ping->packetLossPercentage() : null,
        );
    }

    /**
     * Split "host", "host:port" or "[ipv6]:port" into host and port.
     *
     * @return array{0: string, 1: int}
     */
    public static function parseTarget(?string $target): array
    {
        $target = trim((string) $target);

        if (blank($target)) {
            throw new RuntimeException('No iPerf3 server was provided, add one under Settings → Speedtests or enter a server when starting the speedtest.');
        }

        $host = $target;
        $port = self::DEFAULT_PORT;

        if (preg_match('/^\[(.+)\](?::(\d+))?$/', $target, $matches)) {
            $host = $matches[1];
            $port = isset($matches[2]) ? (int) $matches[2] : $port;
        } elseif (substr_count($target, ':') === 1) {
            [$host, $port] = explode(':', $target);
            $port = ctype_digit($port) ? (int) $port : 0;
        }

        $validHost = filter_var($host, FILTER_VALIDATE_IP)
            || filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);

        if (! $validHost || $port < 1 || $port > 65535) {
            throw new RuntimeException(sprintf('"%s" is not a valid iPerf3 server, use "host" or "host:port".', $target));
        }

        return [$host, $port];
    }

    /**
     * Reduce the iperf3 JSON output to the transferred bytes, duration and TCP round trip times.
     */
    public static function parseTransfer(array $output): array
    {
        $received = Arr::get($output, 'end.sum_received');

        if (blank($received) || ! isset($received['bytes'], $received['seconds'], $received['bits_per_second'])) {
            throw new RuntimeException('iPerf3 did not report a transfer summary.');
        }

        // TCP round trip times are only known on the sending side, so iperf3 reports them for the upload
        // and as zero for the download, where the server sends.
        $senders = collect(Arr::get($output, 'end.streams', []))
            ->pluck('sender')
            ->filter(fn ($sender): bool => ($sender['mean_rtt'] ?? 0) > 0);

        $latency = $senders->isEmpty() ? null : array_filter([
            'iqm' => round($senders->avg('mean_rtt') / 1000, 3),
            'low' => round($senders->min('min_rtt') / 1000, 3),
            'high' => round($senders->max('max_rtt') / 1000, 3),
        ], fn ($value): bool => $value > 0);

        return [
            'bytes' => (int) $received['bytes'],
            'elapsed' => (int) round($received['seconds'] * 1000),
            'bandwidth' => (int) round($received['bits_per_second'] / 8),
            'latency_stats' => $latency ?: null,
            'ip' => Arr::get($output, 'start.connected.0.remote_host'),
        ];
    }

    protected function measure(string $host, int $port, bool $reverse): array
    {
        $settings = app(SpeedtestSettings::class);
        $duration = max($settings->iperf3_duration, 1);

        $command = array_values(array_filter([
            'iperf3',
            '--client', $host,
            '--port', (string) $port,
            '--json',
            '--time', (string) $duration,
            '--parallel', (string) max($settings->iperf3_streams, 1),
            '--omit', '1',
            '--connect-timeout', '5000',
            $reverse ? '--reverse' : null,
        ]));

        for ($attempt = 1; ; $attempt++) {
            $process = Process::timeout($duration + 30)->run($command);

            $output = json_decode($process->output(), true);

            if (! is_array($output)) {
                $error = trim($process->errorOutput()) ?: 'iperf3 returned no output, is the iperf3 CLI installed?';

                throw new RuntimeException('iPerf3 failed: '.$error);
            }

            if (filled($output['error'] ?? null)) {
                if (Str::contains($output['error'], 'busy') && $attempt < self::ATTEMPTS) {
                    Sleep::for(5)->seconds();

                    continue;
                }

                throw new RuntimeException('iPerf3 failed: '.$output['error']);
            }

            return self::parseTransfer($output);
        }
    }

    /**
     * Fall back to the TCP round trip times when ICMP is not available.
     */
    protected static function pingFromTcp(array $upload): ?array
    {
        $low = Arr::get($upload, 'latency_stats.low');

        return $low === null ? null : ['latency' => $low, 'low' => $low];
    }
}

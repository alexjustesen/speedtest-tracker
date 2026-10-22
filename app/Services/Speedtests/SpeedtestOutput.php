<?php

namespace App\Services\Speedtests;

use App\Actions\GetExternalIpAddress;
use Illuminate\Support\Arr;

class SpeedtestOutput
{
    /**
     * Build result data in the shape the Ookla CLI reports it, the rest of the
     * application reads the metrics from these keys.
     *
     * @param  array|null  $ping  output of LatencyStats::summarize()
     * @param  array|null  $download  bytes, elapsed (ms), bandwidth (bytes/s) and latency samples (ms) under load
     * @param  array|null  $upload  same as download
     */
    public static function make(
        ?array $ping,
        ?array $download,
        ?array $upload,
        array $server,
        ?string $isp = null,
        ?string $externalIp = null,
        null|float|int $packetLoss = null,
    ): array {
        $externalIp ??= self::externalIp();

        return array_filter([
            'type' => 'result',
            'timestamp' => now()->toIso8601ZuluString(),
            'ping' => $ping ? Arr::only($ping, ['latency', 'jitter', 'low', 'high']) : null,
            'download' => self::transfer($download),
            'upload' => self::transfer($upload),
            'packetLoss' => $packetLoss,
            'isp' => $isp,
            'interface' => array_filter(['externalIp' => $externalIp]),
            'server' => array_filter($server, fn ($value): bool => ! blank($value)),
        ], fn ($value): bool => ! is_null($value));
    }

    private static function transfer(?array $transfer): ?array
    {
        if ($transfer === null) {
            return null;
        }

        // iPerf3 reports TCP round trip statistics instead of individual samples.
        $latency = $transfer['latency_stats'] ?? LatencyStats::summarize($transfer['latency'] ?? []);

        return array_filter([
            'bandwidth' => $transfer['bandwidth'],
            'bytes' => $transfer['bytes'],
            'elapsed' => $transfer['elapsed'],
            'latency' => $latency ? Arr::except($latency, ['latency']) : null,
        ], fn ($value): bool => ! is_null($value));
    }

    private static function externalIp(): ?string
    {
        $response = GetExternalIpAddress::run();

        return $response['ok'] ? $response['body'] : null;
    }
}

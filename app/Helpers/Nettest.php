<?php

namespace App\Helpers;

use Symfony\Component\Process\Exception\ProcessFailedException;

class Nettest
{
    /**
     * Builds the nettest CLI command.
     *
     * Only options that are configured are passed, so nettest keeps applying its
     * own defaults for everything else.
     *
     * @return array<int, string>
     */
    public static function getCommand(): array
    {
        return array_values(array_filter([
            'nettest',
            '-c',
            (string) config('speedtest.nettest.server'),
            config('speedtest.nettest.port') ? '-p' : null,
            config('speedtest.nettest.port') ? (string) config('speedtest.nettest.port') : null,
            config('speedtest.nettest.threads') ? '-t' : null,
            config('speedtest.nettest.threads') ? (string) config('speedtest.nettest.threads') : null,
            config('speedtest.nettest.tls') ? '-tls' : null,
            config('speedtest.nettest.websocket') ? '-ws' : null,
            '-json',
        ]));
    }

    /**
     * Gets the error message from a failed CLI process exception.
     *
     * Nettest reports its diagnostics on stderr as plain text, so the last
     * non-empty line carries the reason the run failed.
     */
    public static function getErrorMessage(ProcessFailedException $exception): string
    {
        $output = trim($exception->getProcess()->getErrorOutput());

        $lines = array_filter(
            array_map('trim', explode(PHP_EOL, $output)),
            fn (string $line): bool => $line !== ''
        );

        if (empty($lines)) {
            return 'An unexpected error occurred while running the Nettest CLI.';
        }

        return implode(' | ', array_unique($lines));
    }

    /**
     * Maps a nettest measurement onto the columns and the data of a result.
     *
     * Nettest reports speed in bits per second while the result columns hold
     * bytes per second, so both speeds are converted and rounded because the
     * columns are integers. Values that nettest did not measure stay absent
     * instead of being stored as zero.
     *
     * @param  array<string, mixed>  $output
     * @return array<string, mixed>
     */
    public static function mapResult(array $output): array
    {
        $downloadBits = data_get($output, 'download.speed_bps');
        $uploadBits = data_get($output, 'upload.speed_bps');
        $downloadBytes = data_get($output, 'download.bytes_transferred');
        $uploadBytes = data_get($output, 'upload.bytes_transferred');

        return [
            'ping' => data_get($output, 'ping.latency_ms'),
            'download' => $downloadBits !== null ? (int) round($downloadBits / 8) : null,
            'upload' => $uploadBits !== null ? (int) round($uploadBits / 8) : null,
            'download_bytes' => $downloadBytes,
            'upload_bytes' => $uploadBytes,
            'data' => [
                'ping' => [
                    'latency' => data_get($output, 'ping.latency_ms'),
                    'jitter' => data_get($output, 'ping.jitter_ms'),
                ],
                'download' => [
                    'bytes' => $downloadBytes,
                ],
                'upload' => [
                    'bytes' => $uploadBytes,
                ],
                'packetLoss' => data_get($output, 'packet_loss_percent'),
                'server' => [
                    'name' => data_get($output, 'server.host'),
                    'host' => data_get($output, 'server.host'),
                    'port' => data_get($output, 'server.port'),
                ],
                'nettest' => $output,
            ],
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Helpers\Benchmark;
use App\Helpers\Number;
use App\Settings\ThresholdSettings;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResultSeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, name: string, host: string, ip: string, location: string, country: string, download_mbps: float, upload_mbps: float, ping_ms: float}>
     */
    private array $servers = [
        [
            'id' => 1,
            'name' => 'Speedtest New York',
            'host' => 'nyc.speedtest.example.com',
            'ip' => '198.51.100.10',
            'location' => 'New York City, NY',
            'country' => 'United States',
            'download_mbps' => 480,
            'upload_mbps' => 45,
            'ping_ms' => 8,
        ],
        [
            'id' => 2,
            'name' => 'Speedtest Frankfurt',
            'host' => 'fra.speedtest.example.com',
            'ip' => '198.51.100.20',
            'location' => 'Frankfurt',
            'country' => 'Germany',
            'download_mbps' => 240,
            'upload_mbps' => 90,
            'ping_ms' => 25,
        ],
        [
            'id' => 3,
            'name' => 'Speedtest Singapore',
            'host' => 'sgp.speedtest.example.com',
            'ip' => '198.51.100.30',
            'location' => 'Singapore',
            'country' => 'Singapore',
            'download_mbps' => 90,
            'upload_mbps' => 35,
            'ping_ms' => 180,
        ],
    ];

    /**
     * Seed two months of results, spread across three servers, including some failed runs.
     */
    public function run(): void
    {
        DB::table('results')->truncate();

        $thresholds = app(ThresholdSettings::class);

        $now = CarbonImmutable::now();
        $rows = [];

        for ($tick = $now->subMonths(2); $tick->lessThanOrEqualTo($now); $tick = $tick->addHours(3)) {
            foreach ($this->servers as $index => $server) {
                $timestamp = $tick->addMinutes($index * 2);

                $rows[] = fake()->boolean(8)
                    ? $this->failedResult($server, $timestamp)
                    : $this->completedResult($server, $timestamp, $thresholds);

                if (count($rows) >= 500) {
                    DB::table('results')->insert($rows);
                    $rows = [];
                }
            }
        }

        if ($rows !== []) {
            DB::table('results')->insert($rows);
        }
    }

    /**
     * @param  array{id: int, name: string, host: string, ip: string, location: string, country: string, download_mbps: float, upload_mbps: float, ping_ms: float}  $server
     * @return array<string, mixed>
     */
    private function completedResult(array $server, CarbonImmutable $timestamp, ThresholdSettings $thresholds): array
    {
        $downloadMbps = max(1, $server['download_mbps'] + fake()->randomFloat(2, -$server['download_mbps'] * 0.05, $server['download_mbps'] * 0.05));
        $uploadMbps = max(1, $server['upload_mbps'] + fake()->randomFloat(2, -$server['upload_mbps'] * 0.05, $server['upload_mbps'] * 0.05));
        $ping = max(1, $server['ping_ms'] + fake()->randomFloat(3, -$server['ping_ms'] * 0.05, $server['ping_ms'] * 0.05));

        $downloadBandwidth = (int) round($downloadMbps * 125000);
        $uploadBandwidth = (int) round($uploadMbps * 125000);

        $downloadElapsed = fake()->numberBetween(10000, 15000);
        $uploadElapsed = fake()->numberBetween(6000, 12000);

        $downloadBytes = (int) round($downloadBandwidth * $downloadElapsed / 1000);
        $uploadBytes = (int) round($uploadBandwidth * $uploadElapsed / 1000);

        $data = [
            'isp' => 'Speedtest Communications',
            'ping' => [
                'low' => round($ping * 0.8, 3),
                'high' => round($ping * 1.2, 3),
                'jitter' => round(fake()->randomFloat(3, 0.2, 3), 3),
                'latency' => round($ping, 3),
            ],
            'type' => 'result',
            'result' => [
                'id' => (string) Str::uuid(),
                'url' => "https://www.speedtest.net/result/{$timestamp->timestamp}",
                'persisted' => true,
            ],
            'server' => [
                'id' => $server['id'],
                'ip' => $server['ip'],
                'host' => $server['host'],
                'name' => $server['name'],
                'port' => 8080,
                'country' => $server['country'],
                'location' => $server['location'],
            ],
            'upload' => [
                'bytes' => $uploadBytes,
                'elapsed' => $uploadElapsed,
                'latency' => [
                    'iqm' => round($ping * fake()->randomFloat(2, 1.5, 3), 3),
                    'low' => round($ping * 0.9, 3),
                    'high' => round($ping * fake()->randomFloat(2, 2, 4), 3),
                    'jitter' => round(fake()->randomFloat(3, 1, 8), 3),
                ],
                'bandwidth' => $uploadBandwidth,
            ],
            'download' => [
                'bytes' => $downloadBytes,
                'elapsed' => $downloadElapsed,
                'latency' => [
                    'iqm' => round($ping * fake()->randomFloat(2, 1.2, 2), 3),
                    'low' => round($ping * 0.9, 3),
                    'high' => round($ping * fake()->randomFloat(2, 1.5, 3), 3),
                    'jitter' => round(fake()->randomFloat(3, 0.5, 5), 3),
                ],
                'bandwidth' => $downloadBandwidth,
            ],
            'interface' => [
                'name' => 'eth0',
                'isVpn' => false,
                'macAddr' => '00:00:00:00:00:00',
                'externalIp' => '203.0.113.1',
                'internalIp' => '192.168.1.10',
            ],
            'timestamp' => $timestamp->toIso8601String(),
            'packetLoss' => fake()->randomFloat(2, 0, 1),
        ];

        [$benchmarks, $healthy] = $this->benchmark($downloadBandwidth, $uploadBandwidth, $ping, $thresholds);

        return [
            'service' => ResultService::Ookla->value,
            'ping' => $data['ping']['latency'],
            'download' => $downloadBandwidth,
            'upload' => $uploadBandwidth,
            'download_bytes' => $downloadBytes,
            'upload_bytes' => $uploadBytes,
            'comments' => null,
            'data' => json_encode($data),
            'benchmarks' => $benchmarks !== [] ? json_encode($benchmarks) : null,
            'healthy' => $healthy,
            'status' => ResultStatus::Completed->value,
            'scheduled' => true,
            'dispatched_by' => null,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }

    /**
     * Mirrors App\Jobs\Ookla\BenchmarkSpeedtestJob's benchmarking logic.
     *
     * @return array{0: array<string, mixed>, 1: ?bool}
     */
    private function benchmark(int $downloadBandwidth, int $uploadBandwidth, float $ping, ThresholdSettings $thresholds): array
    {
        if (! $thresholds->absolute_enabled) {
            return [[], null];
        }

        $benchmarks = [];
        $healthy = true;

        if (! blank($thresholds->absolute_download) && $thresholds->absolute_download > 0) {
            $passed = Benchmark::bitrate($downloadBandwidth, ['value' => $thresholds->absolute_download, 'unit' => 'mbps']);

            $benchmarks['download'] = [
                'bar' => 'min',
                'passed' => $passed,
                'type' => 'absolute',
                'test_value' => Number::bitsToMagnitude(bits: $downloadBandwidth * 8, precision: 0, magnitude: 'mbit'),
                'benchmark_value' => $thresholds->absolute_download,
                'unit' => 'mbps',
            ];

            if (! $passed) {
                $healthy = false;
            }
        }

        if (! blank($thresholds->absolute_upload) && $thresholds->absolute_upload > 0) {
            $passed = Benchmark::bitrate($uploadBandwidth, ['value' => $thresholds->absolute_upload, 'unit' => 'mbps']);

            $benchmarks['upload'] = [
                'bar' => 'min',
                'passed' => $passed,
                'type' => 'absolute',
                'test_value' => Number::bitsToMagnitude(bits: $uploadBandwidth * 8, precision: 0, magnitude: 'mbit'),
                'benchmark_value' => $thresholds->absolute_upload,
                'unit' => 'mbps',
            ];

            if (! $passed) {
                $healthy = false;
            }
        }

        if (! blank($thresholds->absolute_ping) && $thresholds->absolute_ping > 0) {
            $passed = Benchmark::ping($ping, ['value' => $thresholds->absolute_ping]);

            $benchmarks['ping'] = [
                'bar' => 'max',
                'passed' => $passed,
                'type' => 'absolute',
                'test_value' => round($ping),
                'benchmark_value' => $thresholds->absolute_ping,
                'unit' => 'ms',
            ];

            if (! $passed) {
                $healthy = false;
            }
        }

        return [$benchmarks, $benchmarks !== [] ? $healthy : null];
    }

    /**
     * @param  array{id: int, name: string, host: string, ip: string, location: string, country: string, download_mbps: float, upload_mbps: float, ping_ms: float}  $server
     * @return array<string, mixed>
     */
    private function failedResult(array $server, CarbonImmutable $timestamp): array
    {
        $data = [
            'type' => 'log',
            'level' => 'error',
            'message' => fake()->randomElement([
                "Unable to connect to server '{$server['name']}'",
                'Connection timed out',
                'No internet connection detected',
                'Server refused the connection',
            ]),
            'timestamp' => $timestamp->toIso8601String(),
        ];

        return [
            'service' => ResultService::Ookla->value,
            'ping' => null,
            'download' => null,
            'upload' => null,
            'download_bytes' => null,
            'upload_bytes' => null,
            'comments' => null,
            'data' => json_encode($data),
            'benchmarks' => null,
            'healthy' => false,
            'status' => ResultStatus::Failed->value,
            'scheduled' => true,
            'dispatched_by' => null,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }
}

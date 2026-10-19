<?php

namespace Database\Seeders;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Models\Result;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ResultSeeder extends Seeder
{
    /**
     * Number of hourly results to generate, working backwards from now.
     */
    private const int HOURS = 24 * 30;

    /**
     * Percentage chance that a generated result is a failed test.
     */
    private const int FAILURE_PERCENTAGE = 5;

    /**
     * Periods (in hours ago) where the app was "turned off" and no results exist,
     * these show up as gaps in the dashboard charts.
     */
    private const array OUTAGES = [
        [300, 294], // 6 hour outage
        [130, 124], // 6 hour outage
        [80, 44],   // 1.5 day outage
        [10, 7],    // 3 hour outage
    ];

    /**
     * Error messages used for failed results.
     */
    private const array FAILURE_MESSAGES = [
        'Ping command is unavailable and HTTP fallback also failed.',
        'Configuration - Could not retrieve or read configuration (ConfigurationError)',
        'Cannot open socket: Timeout occurred in connect.',
        'Failed to fetch external IP address from "https://icanhazip.com". See the logs for more details.',
    ];

    /**
     * Seed the results table with one result per hour, some of which have failed.
     */
    public function run(): void
    {
        $now = Carbon::now();

        collect(range(0, self::HOURS - 1))
            ->reject(fn (int $hoursAgo): bool => $this->inOutage($hoursAgo))
            ->map(function (int $hoursAgo) use ($now): array {
                $timestamp = $now->copy()->subHours($hoursAgo);

                return fake()->boolean(self::FAILURE_PERCENTAGE)
                    ? $this->makeFailedResult($timestamp)
                    : $this->makeResult($timestamp);
            })
            // A burst of manual tests a few minutes apart, these shouldn't skew the gap detection.
            ->merge(collect([3, 6, 9, 12])->map(
                fn (int $minutes): array => [
                    ...$this->makeResult($now->copy()->subHours(30)->addMinutes($minutes)),
                    'scheduled' => false,
                ],
            ))
            ->chunk(100)
            ->each(fn ($chunk) => Result::insert($chunk->values()->all()));
    }

    /**
     * Whether the given hour falls inside one of the outage periods.
     */
    private function inOutage(int $hoursAgo): bool
    {
        foreach (self::OUTAGES as [$from, $to]) {
            if ($hoursAgo <= $from && $hoursAgo > $to) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build a single failed result row.
     *
     * @return array<string, mixed>
     */
    private function makeFailedResult(Carbon $timestamp): array
    {
        return [
            'service' => ResultService::Faker->value,
            'ping' => null,
            'download' => null,
            'upload' => null,
            'download_bytes' => null,
            'upload_bytes' => null,
            'data' => json_encode([
                'type' => 'log',
                'level' => 'error',
                'message' => fake()->randomElement(self::FAILURE_MESSAGES),
                'timestamp' => $timestamp->toIso8601String(),
            ]),
            'status' => ResultStatus::Failed->value,
            'scheduled' => true,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }

    /**
     * Build a single result row.
     *
     * @return array<string, mixed>
     */
    private function makeResult(Carbon $timestamp): array
    {
        $ping = fake()->randomFloat(3, 10, 100);
        $pingJitter = fake()->randomFloat(3, 2, 10);
        [$pingLow, $pingHigh] = $this->spread($ping, 10, 100);

        $downloadMbps = fake()->randomFloat(2, 800, 950);
        $uploadMbps = fake()->randomFloat(2, 800, 950);
        $downloadBandwidth = (int) round($downloadMbps * 1_000_000 / 8);
        $uploadBandwidth = (int) round($uploadMbps * 1_000_000 / 8);
        $downloadBytes = $downloadBandwidth * 10;
        $uploadBytes = $uploadBandwidth * 10;

        $downloadLatencyIqm = fake()->randomFloat(3, 1, 100);
        [$downloadLatencyLow, $downloadLatencyHigh] = $this->spread($downloadLatencyIqm, 1, 100);
        $downloadJitter = fake()->randomFloat(3, 2, 10);

        $uploadLatencyIqm = fake()->randomFloat(3, 1, 100);
        [$uploadLatencyLow, $uploadLatencyHigh] = $this->spread($uploadLatencyIqm, 1, 100);
        $uploadJitter = fake()->randomFloat(3, 2, 10);

        $data = [
            'isp' => 'Speedtest Communications',
            'ping' => [
                'low' => $pingLow,
                'high' => $pingHigh,
                'jitter' => $pingJitter,
                'latency' => $ping,
            ],
            'type' => 'result',
            'result' => [
                'id' => (string) Str::uuid(),
                'url' => 'https://docs.speedtest-tracker.dev',
                'persisted' => true,
            ],
            'server' => [
                'id' => 0,
                'ip' => '127.0.0.1',
                'host' => 'docs.speedtest-tracker.dev',
                'name' => 'Speedtest',
                'port' => 8080,
                'country' => 'United States',
                'location' => 'New York City, NY',
            ],
            'upload' => [
                'bytes' => $uploadBytes,
                'elapsed' => 10000,
                'latency' => [
                    'iqm' => $uploadLatencyIqm,
                    'low' => $uploadLatencyLow,
                    'high' => $uploadLatencyHigh,
                    'jitter' => $uploadJitter,
                ],
                'bandwidth' => $uploadBandwidth,
            ],
            'download' => [
                'bytes' => $downloadBytes,
                'elapsed' => 10000,
                'latency' => [
                    'iqm' => $downloadLatencyIqm,
                    'low' => $downloadLatencyLow,
                    'high' => $downloadLatencyHigh,
                    'jitter' => $downloadJitter,
                ],
                'bandwidth' => $downloadBandwidth,
            ],
            'interface' => [
                'name' => 'eth0',
                'isVpn' => false,
                'macAddr' => '00:00:00:00:00:00',
                'externalIp' => '127.0.0.1',
                'internalIp' => '127.0.0.1',
            ],
            'timestamp' => $timestamp->toIso8601String(),
            'packetLoss' => 0,
        ];

        return [
            'service' => ResultService::Faker->value,
            'ping' => $ping,
            'download' => $downloadBandwidth,
            'upload' => $uploadBandwidth,
            'download_bytes' => $downloadBytes,
            'upload_bytes' => $uploadBytes,
            'data' => json_encode($data),
            'status' => ResultStatus::Completed->value,
            'scheduled' => true,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }

    /**
     * Spread a low/high pair around a center value, clamped to the given bounds.
     *
     * @return array{0: float, 1: float}
     */
    private function spread(float $center, float $min, float $max): array
    {
        $low = max($min, $center - fake()->randomFloat(2, 0, 20));
        $high = min($max, $center + fake()->randomFloat(2, 0, 20));

        return [round($low, 3), round($high, 3)];
    }
}

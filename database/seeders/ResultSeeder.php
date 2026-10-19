<?php

namespace Database\Seeders;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Models\Result;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * Seeds a week of hourly results with a few outages, failed tests and a burst of
 * manual tests, so gaps in the dashboard charts can be checked visually.
 *
 * php artisan db:seed --class=ResultSeeder
 */
class ResultSeeder extends Seeder
{
    /**
     * Periods (in hours ago) where the app was "turned off" and no results exist.
     *
     * @var array<int, array{0: int, 1: int}>
     */
    protected array $outages = [
        [130, 124], // 6 hour outage
        [80, 44],   // 1.5 day outage
        [10, 7],    // 3 hour outage
    ];

    public function run(): void
    {
        Result::query()->where('service', ResultService::Faker)->delete();

        $now = CarbonImmutable::now()->startOfHour();

        for ($hoursAgo = 7 * 24; $hoursAgo >= 0; $hoursAgo--) {
            if ($this->inOutage($hoursAgo)) {
                continue;
            }

            $createdAt = $now->subHours($hoursAgo)->addSeconds(random_int(0, 30));

            // Roughly 1 in 25 scheduled tests fails, failed results aren't charted.
            random_int(1, 25) === 1
                ? $this->createFailed($createdAt)
                : $this->createCompleted($createdAt, scheduled: true);
        }

        // A burst of manual tests a few minutes apart, these shouldn't skew the gap detection.
        foreach ([3, 6, 9, 12] as $minutes) {
            $this->createCompleted($now->subHours(30)->addMinutes($minutes), scheduled: false);
        }
    }

    protected function inOutage(int $hoursAgo): bool
    {
        foreach ($this->outages as [$from, $to]) {
            if ($hoursAgo <= $from && $hoursAgo > $to) {
                return true;
            }
        }

        return false;
    }

    protected function createCompleted(CarbonImmutable $createdAt, bool $scheduled): void
    {
        // Slower in the evening to give the charts some shape.
        $evening = in_array($createdAt->hour, [19, 20, 21, 22]);

        $download = (int) (random_int(850, 950) * ($evening ? 0.7 : 1) * 125_000); // bytes/sec
        $upload = (int) (random_int(85, 100) * ($evening ? 0.8 : 1) * 125_000);
        $ping = $this->float(8, 14) + ($evening ? 6 : 0);

        $data = [
            'type' => 'result',
            'timestamp' => $createdAt->toIso8601ZuluString(),
            'isp' => 'Speedtest Communications',
            'packetLoss' => 0,
            'ping' => [
                'latency' => $ping,
                'jitter' => $this->float(0.5, 3),
                'low' => $ping - $this->float(1, 3),
                'high' => $ping + $this->float(2, 8),
            ],
            'download' => [
                'bandwidth' => $download,
                'bytes' => $download * 10,
                'elapsed' => 10000,
                'latency' => [
                    'iqm' => $this->float(20, 60),
                    'low' => $this->float(10, 20),
                    'high' => $this->float(80, 250),
                    'jitter' => $this->float(2, 15),
                ],
            ],
            'upload' => [
                'bandwidth' => $upload,
                'bytes' => $upload * 10,
                'elapsed' => 10000,
                'latency' => [
                    'iqm' => $this->float(30, 120),
                    'low' => $this->float(10, 25),
                    'high' => $this->float(150, 500),
                    'jitter' => $this->float(5, 40),
                ],
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
            'interface' => [
                'name' => 'eth0',
                'isVpn' => false,
                'macAddr' => '00:00:00:00:00:00',
                'externalIp' => '127.0.0.1',
                'internalIp' => '127.0.0.1',
            ],
        ];

        $this->persist([
            'ping' => $ping,
            'download' => $download,
            'upload' => $upload,
            'download_bytes' => $data['download']['bytes'],
            'upload_bytes' => $data['upload']['bytes'],
            'data' => $data,
            'status' => ResultStatus::Completed,
            'scheduled' => $scheduled,
        ], $createdAt);
    }

    protected function createFailed(CarbonImmutable $createdAt): void
    {
        $this->persist([
            'data' => [
                'type' => 'log',
                'level' => 'error',
                'message' => 'A faked error message.',
                'timestamp' => $createdAt->toIso8601ZuluString(),
            ],
            'status' => ResultStatus::Failed,
            'scheduled' => true,
        ], $createdAt);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function persist(array $attributes, CarbonImmutable $createdAt): void
    {
        $result = new Result([
            'service' => ResultService::Faker,
            ...$attributes,
        ]);

        $result->created_at = $createdAt;
        $result->updated_at = $createdAt;
        $result->saveQuietly();
    }

    protected function float(float $min, float $max): float
    {
        return round($min + lcg_value() * ($max - $min), 3);
    }
}

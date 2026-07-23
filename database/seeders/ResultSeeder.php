<?php

namespace Database\Seeders;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Models\Result;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ResultSeeder extends Seeder
{
    /**
     * Backfill completed results across a range of time spans so every
     * chart time filter (1h through month) has data to display.
     */
    public function run(): void
    {
        $now = now();

        $this->seedRange($now->copy()->subDays(3), $now, intervalMinutes: 15);
        $this->seedRange($now->copy()->subDays(30), $now->copy()->subDays(3), intervalMinutes: 120);
        $this->seedRange($now->copy()->subMonths(6), $now->copy()->subDays(30), intervalMinutes: 480);
    }

    private function seedRange(Carbon $start, Carbon $end, int $intervalMinutes): void
    {
        $rows = [];
        $timestamp = $start->copy();

        while ($timestamp->lessThan($end)) {
            $rows[] = $this->buildResult($timestamp->copy());
            $timestamp->addMinutes($intervalMinutes);
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            Result::insert($chunk);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildResult(Carbon $timestamp): array
    {
        $downloadBandwidth = random_int(70_000_000, 140_000_000);
        $uploadBandwidth = random_int(40_000_000, 120_000_000);
        $ping = round(random_int(80, 350) / 10, 3);

        $data = [
            'isp' => 'Speedtest Communications',
            'ping' => [
                'low' => max(0, round($ping - (random_int(10, 50) / 10), 3)),
                'high' => round($ping + (random_int(10, 100) / 10), 3),
                'jitter' => round(random_int(5, 40) / 10, 3),
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
                'bytes' => $uploadBandwidth * 8,
                'elapsed' => random_int(8000, 11000),
                'latency' => [
                    'iqm' => round(random_int(500, 5000) / 10, 3),
                    'low' => round(random_int(100, 500) / 10, 3),
                    'high' => round(random_int(3000, 8000) / 10, 3),
                    'jitter' => round(random_int(50, 600) / 10, 3),
                ],
                'bandwidth' => $uploadBandwidth,
            ],
            'download' => [
                'bytes' => $downloadBandwidth * 8,
                'elapsed' => random_int(12000, 16000),
                'latency' => [
                    'iqm' => round(random_int(500, 3000) / 10, 3),
                    'low' => round(random_int(100, 400) / 10, 3),
                    'high' => round(random_int(2000, 5000) / 10, 3),
                    'jitter' => round(random_int(50, 300) / 10, 3),
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
            'download_bytes' => $data['download']['bytes'],
            'upload_bytes' => $data['upload']['bytes'],
            'data' => json_encode($data),
            'status' => ResultStatus::Completed->value,
            'scheduled' => true,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\ResultStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Lottery;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Result>
 */
class ResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $response = Lottery::odds(1, 100)
            ->winner(function () {
                return '{"type": "log", "level": "error", "message": "A faked error message.", "timestamp": "2024-03-01T01:00:00Z"}';
            })
            ->loser(function () {
                return '{"isp": "Speedtest Communications", "ping": {"low": 17.841, "high": 24.077, "jitter": 1.878, "latency": 19.133}, "type": "result", "result": {"id": "d6fe2fb3-f4f8-4cc5-b898-7b42109e67c2", "url": "https://docs.speedtest-tracker.dev", "persisted": true}, "server": {"id": 0, "ip": "127.0.0.1", "host": "docs.speedtest-tracker.dev", "name": "Speedtest", "port": 8080, "country": "United States", "location": "New York City, NY"}, "upload": {"bytes": 124297377, "elapsed": 9628, "latency": {"iqm": 341.111, "low": 16.663, "high": 529.86, "jitter": 37.587}, "bandwidth": 113750000}, "download": {"bytes": 230789788, "elapsed": 14301, "latency": {"iqm": 104.125, "low": 23.72, "high": 269.563, "jitter": 13.447}, "bandwidth": 115625000}, "interface": {"name": "eth0", "isVpn": false, "macAddr": "00:00:00:00:00:00", "externalIp": "127.0.0.1", "internalIp": "127.0.0.1"}, "timestamp": "2024-03-01T01:00:00Z", "packetLoss": 0}';
            })
            ->choose();

        $output = json_decode($response, true);

        if (Arr::exists($output, 'level')) {
            return [
                'service' => 'faker',
                'data' => $output,
                'status' => ResultStatus::Failed,
                'scheduled' => false,
            ];
        }

        return [
            'service' => 'faker',
            'ping' => Arr::get($output, 'ping.latency'),
            'download' => Arr::get($output, 'download.bandwidth'),
            'upload' => Arr::get($output, 'upload.bandwidth'),
            'data' => $output,
            'status' => ResultStatus::Completed,
            'scheduled' => false,
        ];
    }
}

<?php

namespace App\Services;

use App\Data\OoklaResult;
use App\Models\Result;
use Illuminate\Support\Str;

class SpeedtestFakeResultGenerator
{
    public static function completed(): Result
    {
        $data = [
            'isp' => 'Speedtest Communications',
            'ping' => [
                'low' => 17.841,
                'high' => 24.077,
                'jitter' => 1.878,
                'latency' => 19.133,
            ],
            'type' => 'result',
            'result' => [
                'id' => (string) Str::uuid(),
                'url' => 'https://docs.speedtest-tracker.dev',
                'persisted' => true,
            ],
            'server' => [
                'id' => 1234,
                'ip' => '127.0.0.1',
                'host' => 'docs.speedtest-tracker.dev',
                'name' => 'Speedtest',
                'country' => 'United States',
                'location' => 'New York City, NY',
            ],
            'upload' => [
                'bytes' => 124297377,
                'elapsed' => 9628,
                'latency' => [
                    'iqm' => 341.111,
                    'low' => 16.663,
                    'high' => 529.86,
                    'jitter' => 37.587,
                ],
                'bandwidth' => 113750000,
            ],
            'download' => [
                'bytes' => 230789788,
                'elapsed' => 14301,
                'latency' => [
                    'iqm' => 104.125,
                    'low' => 23.72,
                    'high' => 269.563,
                    'jitter' => 13.447,
                ],
                'bandwidth' => 115625000,
            ],
            'interface' => [
                'name' => 'eth0',
                'isVpn' => false,
                'macAddr' => '00:00:00:00:00:00',
                'externalIp' => '127.0.0.1',
                'internalIp' => '127.0.0.1',
            ],
            'timestamp' => now()->toIso8601String(),
            'packetLoss' => 11,
        ];

        return new Result([
            ...OoklaResult::fromArray($data)->toModelAttributes(),
            'status' => 'completed',
            'service' => 'faker',
            'scheduled' => false,
        ]);
    }

    public static function failed(): Result
    {
        return new Result([
            'error_message' => 'A faked error message.',
            'status' => 'failed',
            'service' => 'faker',
            'scheduled' => false,
        ]);
    }
}

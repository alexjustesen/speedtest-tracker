<?php

namespace App\Services;

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
                'port' => 8080,
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
            'ping' => $data['ping']['latency'],
            'ping_low' => $data['ping']['low'],
            'ping_high' => $data['ping']['high'],
            'ping_jitter' => $data['ping']['jitter'],
            'packet_loss' => $data['packetLoss'],
            'download' => $data['download']['bandwidth'],
            'download_bits' => $data['download']['bandwidth'],
            'download_bytes' => $data['download']['bytes'],
            'download_elapsed' => $data['download']['elapsed'],
            'download_latency_iqm' => $data['download']['latency']['iqm'],
            'download_latency_low' => $data['download']['latency']['low'],
            'download_latency_high' => $data['download']['latency']['high'],
            'download_latency_jitter' => $data['download']['latency']['jitter'],
            'upload' => $data['upload']['bandwidth'],
            'upload_bits' => $data['upload']['bandwidth'],
            'upload_bytes' => $data['upload']['bytes'],
            'upload_elapsed' => $data['upload']['elapsed'],
            'upload_latency_iqm' => $data['upload']['latency']['iqm'],
            'upload_latency_low' => $data['upload']['latency']['low'],
            'upload_latency_high' => $data['upload']['latency']['high'],
            'upload_latency_jitter' => $data['upload']['latency']['jitter'],
            'server_id' => $data['server']['id'],
            'server_ip' => $data['server']['ip'],
            'server_host' => $data['server']['host'],
            'server_name' => $data['server']['name'],
            'server_port' => $data['server']['port'],
            'server_country' => $data['server']['country'],
            'server_location' => $data['server']['location'],
            'interface_name' => $data['interface']['name'],
            'interface_is_vpn' => $data['interface']['isVpn'],
            'interface_mac_addr' => $data['interface']['macAddr'],
            'interface_internal_ip' => $data['interface']['internalIp'],
            'ip_address' => $data['interface']['externalIp'],
            'uuid' => $data['result']['id'],
            'result_url' => $data['result']['url'],
            'data' => $data,
            'status' => 'completed',
            'service' => 'faker',
            'scheduled' => false,
        ]);
    }

    public static function failed(): Result
    {
        $data = [
            'type' => 'log',
            'level' => 'error',
            'message' => 'A faked error message.',
            'timestamp' => now()->toIso8601String(),
        ];

        return new Result([
            'data' => $data,
            'status' => 'failed',
            'service' => 'faker',
            'scheduled' => false,
        ]);
    }
}

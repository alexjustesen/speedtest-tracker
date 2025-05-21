<?php

namespace App\OpenApi\Support;

class ResultExamples
{
    public const SINGLE = [
        'data' => [
            'id' => 1,
            'service' => 'faker',
            'ping' => 19.133,
            'download' => 113750000,
            'upload' => 113750000,
            'download_bits' => 113750000,
            'upload_bits' => 113750000,
            'download_bits_human' => '113.75 Mbps',
            'upload_bits_human' => '113.75 Mbps',
            'benchmarks' => null,
            'healthy' => null,
            'status' => 'completed',
            'scheduled' => false,
            'comments' => null,
            'data' => [
                'isp' => 'Speedtest Communications',
                'ping' => [
                    'low' => 17.841,
                    'high' => 24.077,
                    'jitter' => 1.878,
                    'latency' => 19.133,
                ],
                'type' => 'result',
                'result' => [
                    'id' => 'abc123-uuid',
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
                'timestamp' => '2025-05-21T12:00:00Z',
                'packetLoss' => 11,
            ],
            'created_at' => '2025-05-21T12:00:00Z',
            'updated_at' => '2025-05-21T12:00:00Z',
        ],
        'message' => 'ok',
    ];

    public const COLLECTION = [
        'data' => [
            self::SINGLE['data'],
            self::SINGLE['data'],
        ],
        'meta' => [
            'current_page' => 1,
            'per_page' => 25,
            'total' => 2,
        ],
        'links' => [
            'first' => '/?page=1',
            'last' => '/?page=1',
            'prev' => null,
            'next' => null,
        ],
    ];

    public const NOT_FOUND = [
        'data' => null,
        'message' => 'No result found.',
    ];
}

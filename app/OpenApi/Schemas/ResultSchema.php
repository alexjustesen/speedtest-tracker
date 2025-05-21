<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Result',
    type: 'object',
    description: 'A single Speedtest result entry',
    required: [
        'id',
        'service',
        'ping',
        'download',
        'upload',
        'download_bits',
        'upload_bits',
        'download_bits_human',
        'upload_bits_human',
        'benchmarks',
        'healthy',
        'status',
        'scheduled',
        'comments',
        'data',
        'created_at',
        'updated_at',
    ],
    properties: [
        new OA\Property(
            property: 'id',
            type: 'integer',
            format: 'int64',
            example: 3
        ),
        new OA\Property(
            property: 'service',
            type: 'string',
            example: 'ookla'
        ),
        new OA\Property(
            property: 'ping',
            type: 'number',
            format: 'float',
            nullable: true,
            example: 6.246
        ),
        new OA\Property(
            property: 'download',
            type: 'integer',
            example: 11784522
        ),
        new OA\Property(
            property: 'upload',
            type: 'integer',
            example: 3596514
        ),
        new OA\Property(
            property: 'download_bits',
            type: 'integer',
            example: 94276176
        ),
        new OA\Property(
            property: 'upload_bits',
            type: 'integer',
            example: 28772112
        ),
        new OA\Property(
            property: 'download_bits_human',
            type: 'string',
            example: '94.28 Mbps'
        ),
        new OA\Property(
            property: 'upload_bits_human',
            type: 'string',
            example: '28.77 Mbps'
        ),
        new OA\Property(
            property: 'benchmarks',
            type: 'object',
            nullable: true,
            example: null
        ),
        new OA\Property(
            property: 'healthy',
            type: 'boolean',
            nullable: true,
            example: null
        ),
        new OA\Property(
            property: 'status',
            type: 'string',
            example: 'completed'
        ),
        new OA\Property(
            property: 'scheduled',
            type: 'boolean',
            example: false
        ),
        new OA\Property(
            property: 'comments',
            type: 'string',
            nullable: true,
            example: null
        ),
        new OA\Property(
            property: 'data',
            type: 'object',
            description: 'Raw speedtest details',
            example: [
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
            ]
        ),
        new OA\Property(
            property: 'created_at',
            type: 'string',
            format: 'date-time',
            example: '2025-05-21 17:44:00'
        ),
        new OA\Property(
            property: 'updated_at',
            type: 'string',
            format: 'date-time',
            example: '2025-05-21 17:44:16'
        ),
    ]
)]
class ResultSchema {}

<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Result',
    type: 'object',
    description: 'Speedtest result entry',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'service', type: 'string'),
        new OA\Property(property: 'ping', type: 'number'),
        new OA\Property(property: 'download', type: 'integer'),
        new OA\Property(property: 'upload', type: 'integer'),
        new OA\Property(property: 'download_bits', type: 'integer'),
        new OA\Property(property: 'upload_bits', type: 'integer'),
        new OA\Property(property: 'download_bits_human', type: 'string'),
        new OA\Property(property: 'upload_bits_human', type: 'string'),
        new OA\Property(property: 'benchmarks', type: 'array', nullable: true, items: new OA\Items(type: 'object')),
        new OA\Property(property: 'healthy', type: 'boolean', nullable: true),
        new OA\Property(property: 'status', type: 'string'),
        new OA\Property(property: 'scheduled', type: 'boolean'),
        new OA\Property(property: 'comments', type: 'string', nullable: true),
        new OA\Property(
            property: 'data',
            type: 'object',
            description: 'Nested speedtest data payload',
            properties: [
                new OA\Property(property: 'isp', type: 'string'),
                new OA\Property(
                    property: 'ping',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'low', type: 'number', format: 'float'),
                        new OA\Property(property: 'high', type: 'number', format: 'float'),
                        new OA\Property(property: 'jitter', type: 'number', format: 'float'),
                        new OA\Property(property: 'latency', type: 'number', format: 'float'),
                    ]
                ),
                new OA\Property(property: 'type', type: 'string'),
                new OA\Property(
                    property: 'result',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string'),
                        new OA\Property(property: 'url', type: 'string', format: 'uri'),
                        new OA\Property(property: 'persisted', type: 'boolean'),
                    ]
                ),
                new OA\Property(
                    property: 'server',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'ip', type: 'string', format: 'ipv4'),
                        new OA\Property(property: 'host', type: 'string'),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'port', type: 'integer'),
                        new OA\Property(property: 'country', type: 'string'),
                        new OA\Property(property: 'location', type: 'string'),
                    ]
                ),
                new OA\Property(
                    property: 'upload',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'bytes', type: 'integer'),
                        new OA\Property(property: 'elapsed', type: 'integer'),
                        new OA\Property(
                            property: 'latency',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'iqm', type: 'number', format: 'float'),
                                new OA\Property(property: 'low', type: 'number', format: 'float'),
                                new OA\Property(property: 'high', type: 'number', format: 'float'),
                                new OA\Property(property: 'jitter', type: 'number', format: 'float'),
                            ]
                        ),
                        new OA\Property(property: 'bandwidth', type: 'integer'),
                    ]
                ),
                new OA\Property(
                    property: 'download',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'bytes', type: 'integer'),
                        new OA\Property(property: 'elapsed', type: 'integer'),
                        new OA\Property(
                            property: 'latency',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'iqm', type: 'number', format: 'float'),
                                new OA\Property(property: 'low', type: 'number', format: 'float'),
                                new OA\Property(property: 'high', type: 'number', format: 'float'),
                                new OA\Property(property: 'jitter', type: 'number', format: 'float'),
                            ]
                        ),
                        new OA\Property(property: 'bandwidth', type: 'integer'),
                    ]
                ),
                new OA\Property(
                    property: 'interface',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'isVpn', type: 'boolean'),
                        new OA\Property(property: 'macAddr', type: 'string', pattern: '^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$'),
                        new OA\Property(property: 'externalIp', type: 'string', format: 'ipv4'),
                        new OA\Property(property: 'internalIp', type: 'string', format: 'ipv4'),
                    ]
                ),
                new OA\Property(property: 'timestamp', type: 'string', format: 'date-time'),
                new OA\Property(property: 'packetLoss', type: 'number'),
            ]
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    additionalProperties: false
)]
class ResultSchema {}

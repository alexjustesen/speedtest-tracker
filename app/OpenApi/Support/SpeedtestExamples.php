<?php

namespace App\OpenApi\Support;

class SpeedtestExamples
{
    public const CREATED = [
        'data' => [
            'id' => 99,
            'service' => 'ookla',
            'status' => 'queued',
            'scheduled' => false,
            'uuid' => 'queued-uuid-1234',
            'comments' => null,
            'created_at' => '2025-05-21T12:34:00Z',
            'updated_at' => '2025-05-21T12:34:00Z',
        ],
        'message' => 'Speedtest added to the queue.',
    ];

    public const FORBIDDEN = [
        'data' => null,
        'message' => 'You do not have permission to run speedtests.',
    ];

    public const VALIDATION = [
        'data' => [
            'server_id' => ['The server id must be an integer.'],
        ],
        'message' => 'Validation failed.',
    ];
}

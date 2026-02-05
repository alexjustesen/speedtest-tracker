<?php

return [
    // Status enum values
    'status' => [
        'benchmarking' => 'Benchmarking',
        'checking' => 'Checking',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'running' => 'Running',
        'started' => 'Started',
        'skipped' => 'Skipped',
        'waiting' => 'Waiting',
    ],

    // Service enum values
    'service' => [
        'faker' => 'Faker',
        'ookla' => 'Ookla',
    ],

    // Schedule status enum values
    'schedule_status' => [
        'healthy' => 'Healthy',
        'unhealthy' => 'Unhealthy',
        'failed' => 'Failed',
        'not_tested' => 'Not Tested',
    ],
];

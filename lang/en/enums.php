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

    // Webhook event values
    'webhook_event' => [
        'started' => 'Started',
        'waiting' => 'Waiting',
        'checking' => 'Checking',
        'running' => 'Running',
        'benchmarking' => 'Benchmarking',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'skipped' => 'Skipped',
        'benchmark_healthy' => 'Healthy',
        'benchmark_unhealthy' => 'Unhealthy',
    ],
];

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

    // Notification event values
    'notification_event' => [
        'completed' => 'Completed',
        'failed' => 'Failed',
        'recovered' => 'Recovered',
        'benchmark_unhealthy' => 'Benchmark unhealthy',
        'benchmark_recovered' => 'Benchmark recovered',
    ],

    // Notification channel type values
    'notification_channel_type' => [
        'mail' => 'Mail',
        'apprise' => 'Apprise',
        'webhook' => 'Webhook',
    ],

    // Benchmark metric values
    'benchmark_metric' => [
        'download' => 'Download',
        'upload' => 'Upload',
        'ping' => 'Ping',
        'packet_loss' => 'Packet Loss',
    ],

    // Benchmark type values
    'benchmark_type' => [
        'absolute' => 'Absolute',
        'relative' => 'Relative',
    ],
];

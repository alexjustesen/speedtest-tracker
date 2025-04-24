<?php

use Carbon\Carbon;

return [

    'build_date' => Carbon::parse('2025-04-16'),

    'build_version' => 'v1.4.2',

    /**
     * General settings.
     */
    'content_width' => env('CONTENT_WIDTH', '7xl'),

    'prune_results_older_than' => (int) env('PRUNE_RESULTS_OLDER_THAN', 0),

    'public_dashboard' => env('PUBLIC_DASHBOARD', false),

    /**
     * Speedtest settings.
     */
    'schedule' => env('SPEEDTEST_SCHEDULE', false),

    'servers' => env('SPEEDTEST_SERVERS'),

    'blocked_servers' => env('SPEEDTEST_BLOCKED_SERVERS'),

    'interface' => env('SPEEDTEST_INTERFACE'),

    'checkinternet_url' => env('SPEEDTEST_CHECKINTERNET_URL', 'https://icanhazip.com'),

    /**
     * IP filtering settings.
     */
    'skip_ips' => env('SPEEDTEST_SKIP_IPS', ''),

    /**
     * Threshold settings.
     */
    'threshold_enabled' => env('THRESHOLD_ENABLED', false),

    'threshold_download' => env('THRESHOLD_DOWNLOAD', 0),

    'threshold_upload' => env('THRESHOLD_UPLOAD', 0),

    'threshold_ping' => env('THRESHOLD_PING', 0) ,

    /**
     * Spedtest Retry settings.
     */
    'retries_enabled' => env('RETRIES_ENABLED', false),

    'retries_speedtest_enabled' => env('RETRIES_SPEEDTEST_ENABLED', false),

    'retries_benchmark_enabled' => env('RETRIES_BENCHMARK_ENABLED', false),

    'max_retries' => env('MAX_RETRIES', 5),
];

<?php

use Carbon\Carbon;

return [
    /**
     * General settings.
     */
    'build_date' => Carbon::parse('2025-12-08'),

    'build_version' => 'v1.12.3',

    'content_width' => env('CONTENT_WIDTH', '7xl'),

    'prune_results_older_than' => (int) env('PRUNE_RESULTS_OLDER_THAN', 0),

    'public_dashboard' => env('PUBLIC_DASHBOARD', false),

    'default_chart_range' => strtolower(env('DEFAULT_CHART_RANGE', '24h')),

    /**
     * Speedtest settings.
     */
    'schedule' => env('SPEEDTEST_SCHEDULE', false),

    'servers' => env('SPEEDTEST_SERVERS'),

    'blocked_servers' => env('SPEEDTEST_BLOCKED_SERVERS'),

    'interface' => env('SPEEDTEST_INTERFACE'),

    'preflight' => [
        'check_internet_connectivity_url' => env('SPEEDTEST_CHECKINTERNET_URL') ?? env('SPEEDTEST_CHECK_INTERNET_CONNECTIVITY_URL', 'speedtest-tracker.dev'),
        'get_external_ip_url' => env('SPEEDTEST_CHECKINTERNET_URL') ?? env('SPEEDTEST_GET_EXTERNAL_IP_URL', 'https://icanhazip.com'),
        'allowed_ips' => env('ALLOWED_IPS') ?? env('SPEEDTEST_ALLOWED_IPS'),
        'skip_ips' => env('SPEEDTEST_SKIP_IPS') ?? env('SPEEDTEST_SKIP_IPS'),
    ],

    'checkinternet_url' => env('SPEEDTEST_CHECKINTERNET_URL', 'https://icanhazip.com'), // ! DEPRECATED, use 'preflight.check_internet_connectivity' instead

    /**
     * IP filtering settings.
     */
    'allowed_ips' => env('ALLOWED_IPS'), // ! DEPRECATED, use 'preflight.allowed_ips' instead

    'skip_ips' => env('SPEEDTEST_SKIP_IPS', ''), // ! DEPRECATED, use 'preflight.skip_ips' instead

    /**
     * Threshold settings.
     */
    'threshold_enabled' => env('THRESHOLD_ENABLED', false),

    'threshold_download' => env('THRESHOLD_DOWNLOAD', 0),

    'threshold_upload' => env('THRESHOLD_UPLOAD', 0),

    'threshold_ping' => env('THRESHOLD_PING', 0),
];

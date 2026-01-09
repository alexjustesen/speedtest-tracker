<?php

use Carbon\Carbon;

return [
    /**
     * General settings.
     */
    'build_date' => Carbon::parse('2026-01-08'),

    'build_version' => 'v1.13.5',

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
        'external_ip_url' => env('SPEEDTEST_CHECKINTERNET_URL') ?? env('SPEEDTEST_EXTERNAL_IP_URL', 'https://icanhazip.com'),
        'internet_check_hostname' => env('SPEEDTEST_CHECKINTERNET_URL') ?? env('SPEEDTEST_INTERNET_CHECK_HOSTNAME', 'icanhazip.com'),
        'skip_ips' => env('SPEEDTEST_SKIP_IPS'),
    ],

    /**
     * IP filtering settings.
     */
    'allowed_ips' => env('ALLOWED_IPS'),

    /**
     * Threshold settings.
     */
    'threshold_enabled' => env('THRESHOLD_ENABLED', false),

    'threshold_download' => env('THRESHOLD_DOWNLOAD', 0),

    'threshold_upload' => env('THRESHOLD_UPLOAD', 0),

    'threshold_ping' => env('THRESHOLD_PING', 0),
];

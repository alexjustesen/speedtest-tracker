<?php

use Carbon\Carbon;

return [
    /**
     * General settings.
     */
    'build_date' => Carbon::parse('2026-08-21'),

    'build_version' => 'v1.15.0',

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

    /**
     * Service for the SPEEDTEST_SCHEDULE schedule: ookla, cloudflare, yandex, iperf3 or file.
     * More schedules, sources and measurement settings are managed under Settings → Speedtests,
     * the variables below only provide their initial values.
     */
    'service' => env('SPEEDTEST_SERVICE', 'ookla'),

    'http' => [
        'duration' => (int) env('SPEEDTEST_HTTP_DURATION', 10),
        'streams' => (int) env('SPEEDTEST_HTTP_STREAMS', 4),
    ],

    'iperf3' => [
        'servers' => env('SPEEDTEST_IPERF3_SERVERS'),
        'duration' => (int) env('SPEEDTEST_IPERF3_DURATION', 10),
        'streams' => (int) env('SPEEDTEST_IPERF3_STREAMS', 4),
    ],

    'file' => [
        'urls' => env('SPEEDTEST_FILE_URLS'),
    ],

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

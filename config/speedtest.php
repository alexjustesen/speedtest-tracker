<?php

use Carbon\Carbon;

return [
    /**
     * General settings.
     */
    'build_date' => Carbon::parse('2025-11-24'),

    'build_version' => 'v1.9.0',

    'content_width' => env('CONTENT_WIDTH', '7xl'),

    'prune_results_older_than' => (int) env('PRUNE_RESULTS_OLDER_THAN', 0),

    'public_dashboard' => env('PUBLIC_DASHBOARD', false),

    'default_chart_range' => strtolower(env('DEFAULT_CHART_RANGE', '24h')),

    /**
     * Dashboard V2 settings.
     */
    'dashboard_v2' => [
        'enabled' => env('ENABLE_DASHBOARD_V2', false),
    ],

    /**
     * Public API cache settings (Dashboard V2).
     */
    'public_api' => [
        'stats_cache_ttl' => (int) env('STATS_CACHE_TTL', 60), // 1 minute
        'servers_cache_ttl' => (int) env('SERVERS_CACHE_TTL', 600), // 10 minutes
        'health_cache_ttl' => (int) env('HEALTH_CACHE_TTL', 60), // 1 minute
        'statistics_cache_ttl' => (int) env('STATISTICS_CACHE_TTL', 300), // 5 minutes
        'chart_cache_ttl' => (int) env('CHART_CACHE_TTL', 60), // 1 minute
    ],

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
    'allowed_ips' => env('ALLOWED_IPS'),

    'skip_ips' => env('SPEEDTEST_SKIP_IPS', ''),

    /**
     * Threshold settings.
     */
    'threshold_enabled' => env('THRESHOLD_ENABLED', false),

    'threshold_download' => env('THRESHOLD_DOWNLOAD', 0),

    'threshold_upload' => env('THRESHOLD_UPLOAD', 0),

    'threshold_ping' => env('THRESHOLD_PING', 0),
];

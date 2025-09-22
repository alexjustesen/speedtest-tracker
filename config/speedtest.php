<?php

use Carbon\Carbon;

return [
    /**
     * General settings.
     */

    'build_date' => Carbon::parse('2025-09-22'),

    'build_version' => 'v1.6.7',

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

    'allowed_ips' => env('ALLOWED_IPS'),

    'skip_ips' => env('SPEEDTEST_SKIP_IPS', ''),


    /**
     * Threshold settings.
     */

     'threshold_enabled' => env('THRESHOLD_ENABLED', false),

     'threshold_download' => env('THRESHOLD_DOWNLOAD', 0),

     'threshold_upload' => env('THRESHOLD_UPLOAD', 0),

     'threshold_ping' => env('THRESHOLD_PING', 0) ,
];

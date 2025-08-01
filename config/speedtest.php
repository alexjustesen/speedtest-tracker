<?php

use Carbon\Carbon;

return [
    /**
     * General settings.
     */

    'build_date' => Carbon::parse('2025-07-31'),

    'build_version' => 'v1.6.6',

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
     * Quota settings.
     */

    'quota_enabled' => (bool) env('SPEEDTEST_QUOTA_ENABLED', false), // enable quota tracking

    'quota_size' => (string) env('SPEEDTEST_QUOTA_SIZE', '500G'), // like 500G or 1T

    'quota_period' => (string) env('SPEEDTEST_QUOTA_PERIOD', 'month'), // like month or week

    'quota_reset_day' => (int) env('SPEEDTEST_QUOTA_RESET_DAY', 0), // day of the month or week


    /**
     * Threshold settings.
     */

     'threshold_enabled' => env('THRESHOLD_ENABLED', false),

     'threshold_download' => env('THRESHOLD_DOWNLOAD', 0),

     'threshold_upload' => env('THRESHOLD_UPLOAD', 0),

     'threshold_ping' => env('THRESHOLD_PING', 0) ,
];

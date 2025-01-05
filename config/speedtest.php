<?php

use Carbon\Carbon;

return [

    'build_date' => Carbon::parse('2025-01-05'),

    'build_version' => 'v1.0.3',

    /**
     * General settings.
     */
    'content_width' => env('CONTENT_WIDTH', '7xl'),

    'prune_results_older_than' => env('PRUNE_RESULTS_OLDER_THAN', 0),

    'public_dashboard' => env('PUBLIC_DASHBOARD', false),

    /**
     * Polling settings.
     */
    'dashboard_polling' => env('DASHBOARD_POLLING', '60s'),

    'notification_polling' => env('NOTIFICATION_POLLING', '60s'),

    'results_polling' => env('RESULTS_POLLING', null),

    /**
     * Speedtest settings.
     */
    'schedule' => env('SPEEDTEST_SCHEDULE', false),

    'servers' => env('SPEEDTEST_SERVERS'),

    'blocked_servers' => env('SPEEDTEST_BLOCKED_SERVERS'),

    'interface' => env('SPEEDTEST_INTERFACE'),

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
];

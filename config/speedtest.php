<?php

use Carbon\Carbon;

return [

    'build_date' => Carbon::parse('2024-12-11'),

    'build_version' => 'v0.26.0',

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

    /**
     * IP filtering settings.
     */
    'skip_ips' => env('SPEEDTEST_SKIP_IPS', ''),

];

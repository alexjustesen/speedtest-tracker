<?php

use Carbon\Carbon;

return [

    'build_date' => Carbon::parse('2024-10-09'),

    'build_version' => 'v0.21.4',

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
    'ping_url' => env('SPEEDTEST_PING_URL'),

    'schedule' => env('SPEEDTEST_SCHEDULE'),

    'servers' => env('SPEEDTEST_SERVERS', ''),

];

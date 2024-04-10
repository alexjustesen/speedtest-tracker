<?php

use Carbon\Carbon;

return [
    /**
     * Build information
     */
    'build_date' => Carbon::parse('2024-04-09'),

    'build_version' => 'v0.18.6',

    /**
     * General
     */
    'content_width' => env('CONTENT_WIDTH', '7xl'),

    'public_dashboard' => env('PUBLIC_DASHBOARD', false),

    /**
     * Polling
     */
    'dashboard_polling' => env('DASHBOARD_POLLING', '60s'),

    'notification_polling' => env('NOTIFICATION_POLLING', '60s'),

    'results_polling' => env('RESULTS_POLLING', null),
];

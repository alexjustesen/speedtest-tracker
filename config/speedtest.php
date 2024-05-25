<?php

use Carbon\Carbon;

return [

    'build_date' => Carbon::parse('2024-04-16'),

    'build_version' => 'v0.19.0',

    /**
     * General settings.
     */
    'content_width' => env('CONTENT_WIDTH', '7xl'),

    'public_dashboard' => env('PUBLIC_DASHBOARD', false),

    /**
     * Polling settings.
     */
    'dashboard_polling' => env('DASHBOARD_POLLING', '60s'),

    'notification_polling' => env('NOTIFICATION_POLLING', '60s'),

    'results_polling' => env('RESULTS_POLLING', null),

];

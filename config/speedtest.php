<?php

use Carbon\Carbon;

return [
    /**
     * Build information
     */
    'build_date' => Carbon::parse('2023-12-12'),

    'build_version' => '0.14.0-beta8',

    /**
     * General
     */
    'content_width' => env('CONTENT_WIDTH', '7xl'),

    /**
     * Polling
     */
    'dashboard_polling' => env('DASHBOARD_POLLING', '60s'),

    'notification_polling' => env('NOTIFICATION_POLLING', '60s'),

    'results_polling' => env('RESULTS_POLLING', null),

    /**
     * Security
     */
    'allow_embeds' => env('ALLOW_EMBEDS', null),
];

<?php

use Carbon\Carbon;

return [
    /**
     * Build information
     */
    'build_date' => Carbon::parse('2023-06-09'),

    'build_version' => '0.11.17',

    /**
     * Polling
     */
    'dashboard_polling' => env('DASHBOARD_POLLING', '60s'),

    'results_polling' => env('RESULTS_POLLING', null),
];

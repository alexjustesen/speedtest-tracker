<?php

use Carbon\Carbon;

return [
    /**
     * Build information
     */
    'build_date' => Carbon::parse('2023-09-05'),

    'build_version' => '0.12.0-beta.1',

    /**
     * Polling
     */
    'dashboard_polling' => env('DASHBOARD_POLLING', '60s'),

    'results_polling' => env('RESULTS_POLLING', null),
];

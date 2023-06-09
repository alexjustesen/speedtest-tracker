<?php

use Carbon\Carbon;

return [
    /**
     * Build information
     */
    'build_date' => Carbon::parse('2023-05-12'),

    'build_version' => '0.11.16',

    /**
     * Polling
     */
    'dashboard_polling' => env('DASHBOARD_POLLING', '5s'),

    'results_polling' => env('RESULTS_POLLING', '5s'),
];

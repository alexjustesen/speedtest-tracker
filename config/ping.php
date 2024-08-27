<?php

use Carbon\Carbon;

return [

    'schedule' => env('PING_SCHEDULE'),

    'urls' => explode(',', env('PING_URLS',)),

    'timeout' => env('PING_TIMEOUT', 3),

    'ping_count' => env('PING_COUNT', 10),
];


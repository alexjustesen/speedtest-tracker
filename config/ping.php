<?php

use Carbon\Carbon;

return [

    'schedule' => env('PING_SCHEDULE'),

    'urls' => explode(',', env('PING_URLS',)),

    'ping_count' => env('PING_COUNT', 10),
];


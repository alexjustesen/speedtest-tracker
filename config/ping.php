<?php

use Carbon\Carbon;

return [

    'schedule' => env('PING_SCHEDULE', '*/5 * * * *'),

    'urls' => explode(',', env('PING_URLS', 'http://example.com')),

    'timeout' => env('PING_TIMEOUT', 3),

    'ping_count' => env('PING_COUNT', 10),
];


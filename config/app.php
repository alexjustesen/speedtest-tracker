<?php

use Illuminate\Support\Facades\Facade;

return [

    'name' => env('APP_NAME', 'Speedtest Tracker'),

    'env' => env('APP_ENV', 'production'),

    'force_https' => env('FORCE_HTTPS', false),

    'aliases' => Facade::defaultAliases()->merge([
        'TimeZoneHelper' => App\Helpers\TimeZoneHelper::class,
    ])->toArray(),

];

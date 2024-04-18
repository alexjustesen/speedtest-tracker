<?php

use Illuminate\Support\Facades\Facade;

return [

    'name' => env('APP_NAME', 'Speedtest Tracker'),

    'force_https' => env('FORCE_HTTPS', false),

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
        'TimeZoneHelper' => App\Helpers\TimeZoneHelper::class,
    ])->toArray(),

];

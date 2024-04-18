<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    'force_https' => env('FORCE_HTTPS', false),


    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
        'TimeZoneHelper' => App\Helpers\TimeZoneHelper::class,
    ])->toArray(),

];

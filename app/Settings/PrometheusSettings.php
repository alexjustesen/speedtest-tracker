<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PrometheusSettings extends Settings
{
    public bool $enabled;

    public static function group(): string
    {
        return 'prometheus';
    }
}

<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $default_chart_range;

    public static function group(): string
    {
        return 'general';
    }
}

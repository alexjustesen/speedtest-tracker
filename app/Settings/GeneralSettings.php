<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public int $default_chart_range;

    public static function group(): string
    {
        return 'general';
    }
}

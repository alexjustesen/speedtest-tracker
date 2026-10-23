<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public int $chart_default_range;

    public bool $chart_begin_at_zero;

    public string $chart_datetime_format;

    public bool $chart_only_show_avg_latency;

    public static function group(): string
    {
        return 'general';
    }
}

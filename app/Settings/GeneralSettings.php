<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public int $default_chart_range;

    public string $external_ip_url;

    public string $internet_check_hostname;

    public static function group(): string
    {
        return 'general';
    }
}

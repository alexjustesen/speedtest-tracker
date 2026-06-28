<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $default_chart_range;

    public int $prune_results_older_than;

    public string $datetime_format;

    public string $chart_datetime_format;

    public string $display_timezone;

    public bool $chart_begin_at_zero;

    public string $speedtest_external_ip_url;

    public string $speedtest_internet_check_hostname;

    public static function group(): string
    {
        return 'general';
    }
}

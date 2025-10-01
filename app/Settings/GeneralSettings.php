<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $app_name;

    public ?string $asset_url;

    public ?string $app_timezone;

    public bool $chart_begin_at_zero;

    public ?string $chart_datetime_format;

    public ?string $datetime_format;

    public ?string $display_timezone;

    public bool $public_dashboard;

    public ?string $speedtest_skip_ips;

    public ?string $speedtest_schedule;

    public ?string $speedtest_servers;

    public ?string $speedtest_blocked_servers;

    public ?string $speedtest_interface;

    public ?string $speedtest_checkinternet_url;

    public bool $threshold_enabled;

    public ?string $threshold_download;

    public ?string $threshold_upload;

    public ?string $threshold_ping;

    public ?string $prune_results_older_than;

    public ?string $api_rate_limit;

    public static function group(): string
    {
        return 'general';
    }
}

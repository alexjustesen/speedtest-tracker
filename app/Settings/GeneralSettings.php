<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $auth_enabled;

    public string $content_width;

    public ?string $speedtest_schedule;

    /** @var string[] */
    public $speedtest_server;

    public string $site_name;

    public string $time_format;

    public string $timezone;

    public static function group(): string
    {
        return 'general';
    }
}

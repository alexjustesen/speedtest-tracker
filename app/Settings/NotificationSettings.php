<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NotificationSettings extends Settings
{
    public bool $database_enabled;

    public bool $database_on_speedtest_run;

    public bool $database_on_threshold_failure;

    public bool $mail_enabled;

    public bool $mail_on_speedtest_run;

    public bool $mail_on_threshold_failure;

    public ?array $mail_recipients;

    public bool $apprise_enabled;

    public ?string $apprise_server_url;

    public bool $apprise_on_speedtest_run;

    public bool $apprise_on_threshold_failure;

    public bool $apprise_verify_ssl;

    public ?array $apprise_channel_urls;

    public static function group(): string
    {
        return 'notification';
    }
}

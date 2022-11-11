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

    public ?string $mail_recipients;

    public static function group(): string
    {
        return 'notification';
    }
}

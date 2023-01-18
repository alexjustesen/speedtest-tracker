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

    public bool $telegram_enabled;

    public bool $telegram_on_speedtest_run;

    public bool $telegram_on_threshold_failure;

    public ?array $telegram_recipients;

    public static function group(): string
    {
        return 'notification';
    }
}

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

    public bool $telegram_disable_notification;

    public bool $telegram_on_speedtest_run;

    public bool $telegram_on_threshold_failure;

    public ?array $telegram_recipients;

    public bool $webhook_enabled;

    public bool $webhook_on_speedtest_run;

    public bool $webhook_on_threshold_failure;

    public ?array $webhook_urls;

    public bool $discord_enabled;

    public bool $discord_on_speedtest_run;

    public bool $discord_on_threshold_failure;

    public ?array $discord_webhooks;

    public static function group(): string
    {
        return 'notification';
    }
}

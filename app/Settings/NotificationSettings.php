<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NotificationSettings extends Settings
{
    public bool $database_enabled;

    public bool $database_on_speedtest_run;

    public bool $database_on_threshold_failure;

    public static function group(): string
    {
        return 'notification';
    }
}

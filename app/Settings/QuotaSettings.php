<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class QuotaSettings extends Settings
{
    public bool $enabled; // Enable quota tracking

    public string $size; // Quota size, e.g., '500 GB' or '1 TB'

    public string $period; // Options: day, week, month

    public int $reset_day; // Day of the month or week for quota reset

    public int $used; // Track used quota, default to 0

    public static function group(): string
    {
        return 'quota';
    }
}
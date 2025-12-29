<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $data_usage_enabled = false; // Enable or disable data cap

    public string $data_usage_limit = ''; // Example: '500GB' or '1TB'

    public string $data_usage_period = 'month'; // Options: 'day', 'week', 'month' or 'manual'

    public int $data_usage_reset_day = 1; // Day of the period to reset data usage

    public string $data_usage_action = 'notify'; // Options: 'notify', 'restrict'

    public int $data_usage_used = 0; // Example: amount of data used in bytes

    public static function group(): string
    {
        return 'general';
    }
}

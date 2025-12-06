<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $data_cap_data_limit = ''; // Example: '500GB' or '1TB'

    public string $data_cap_period = 'monthly'; // Options: 'daily', 'monthly', 'weekly'

    public int $data_cap_reset_day = 1; // Day of the month to reset data cap

    public int $data_cap_warning_percentage = 80; // Percentage to notify user

    public string $data_cap_action = 'notify'; // Options: 'notify', 'restrict'

    public static function group(): string
    {
        return 'general';
    }
}

<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.data_usage_enabled', false); // Enable or disable data cap
        $this->migrator->add('general.data_usage_limit', ''); // Example: '500GB' or '1TB'
        $this->migrator->add('general.data_usage_period', 'month'); // Options: 'day', 'week', 'month' or 'manual'
        $this->migrator->add('general.data_usage_reset_day', 1); // Day of the period to reset data usage
        $this->migrator->add('general.data_usage_action', 'notify'); // Options: 'notify', 'restrict'
        $this->migrator->add('general.data_usage_used', 0); // Example: amount of data used in bytes
    }
};

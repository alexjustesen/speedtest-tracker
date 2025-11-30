<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.data_cap_enabled', false);
        $this->migrator->add('general.data_cap_data_limit', null); // Example: '500GB' or '1TB'
        $this->migrator->add('general.data_cap_period', 'monthly'); // Options: 'daily', 'monthly', 'weekly'
        $this->migrator->add('general.data_cap_reset_day', 1); // Day of the month to reset data cap
        $this->migrator->add('general.data_cap_warning_percentage', 80); // Percentage to notify user
        $this->migrator->add('general.data_cap_action', 'notify'); // Options: 'notify', 'restrict'
    }
};

<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('quota.enabled', config('speedtest.quota_enabled')); // Enable quota tracking
        $this->migrator->add('quota.size', config('speedtest.quota_size')); // Quota size, e.g., '500 GB' or '1 TB'
        $this->migrator->add('quota.period', config('speedtest.quota_period')); // Options: day, week, month
        $this->migrator->add('quota.reset_day', config('speedtest.quota_reset_day')); // Day of the month or week for quota reset
        $this->migrator->add('quota.used', 0); // Track used quota, default to 0
    }
};

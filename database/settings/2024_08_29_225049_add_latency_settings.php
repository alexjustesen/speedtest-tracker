<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddLatencySettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('latency.ping_count', 10);  // Default ping count
        $this->migrator->add('latency.target_url', []);  // Default empty array for ping URLs
        $this->migrator->add('latency.latency_schedule', '');  // Default cron expression
        $this->migrator->add('latency.enabled', false);  // Default state for the enable/disable toggle
    }

    public function down(): void
    {
        $this->migrator->delete('latency.ping_count');
        $this->migrator->delete('latency.target_url');
        $this->migrator->delete('latency.latency_schedule');  // Remove the cron expression setting
        $this->migrator->delete('latency.enabled');  // Remove the enable/disable toggle setting
    }
}

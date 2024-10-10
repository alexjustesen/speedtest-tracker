<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddLatencySettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('latency.ping_count', 10);  // Default ping count
        $this->migrator->add('latency.target_url', []);  // Default empty array for ping URLs
        $this->migrator->add('latency.latency_schedule', '');  // Default cron expression
        $this->migrator->add('latency.latency_enabled', false);  // Default state for the enable/disable toggle
        $this->migrator->add('latency.latency_column_span', 'full'); // Add column_span with default value

    }
}

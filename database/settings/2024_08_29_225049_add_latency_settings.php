<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddLatencySettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('latency.ping_count', 10);  // Default ping count
        $this->migrator->add('latency.ping_urls', []);  // Default empty array for ping URLs
        $this->migrator->add('latency.cron_expression', '0 0 * * *');  // Default cron expression
    }

    public function down(): void
    {
        $this->migrator->delete('latency.ping_count');
        $this->migrator->delete('latency.ping_urls');
        $this->migrator->delete('latency.cron_expression');  // Remove the cron expression setting
    }
}

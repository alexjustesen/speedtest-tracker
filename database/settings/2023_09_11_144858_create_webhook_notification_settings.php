<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->migrator->add('notification.webhook_enabled', false);
        $this->migrator->add('notification.webhook_on_speedtest_run', false);
        $this->migrator->add('notification.webhook_on_threshold_failure', false);
        $this->migrator->add('notification.webhook_urls', null);
    }
};

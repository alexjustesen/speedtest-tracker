<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.discord_enabled', false);
        $this->migrator->add('notification.discord_on_speedtest_run', false);
        $this->migrator->add('notification.discord_on_threshold_failure', false);
        $this->migrator->add('notification.discord_webhooks', null);
    }
};

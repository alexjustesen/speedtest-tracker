<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.teams_enabled', false);
        $this->migrator->add('notification.teams_on_speedtest_run', false);
        $this->migrator->add('notification.teams_on_threshold_failure', false);
        $this->migrator->add('notification.teams_webhooks', null);
    }
};

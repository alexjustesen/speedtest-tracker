<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.wecom_enabled', false);
        $this->migrator->add('notification.wecom_on_speedtest_run', false);
        $this->migrator->add('notification.wecom_on_threshold_failure', false);
        $this->migrator->add('notification.wecom_webhooks', null);
    }
};

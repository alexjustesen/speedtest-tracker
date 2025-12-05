<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.apprise_enabled', false);
        $this->migrator->add('notification.apprise_server_url', null);
        $this->migrator->add('notification.apprise_on_speedtest_run', false);
        $this->migrator->add('notification.apprise_on_threshold_failure', false);
        $this->migrator->add('notification.apprise_verify_ssl', true);
        $this->migrator->add('notification.apprise_channel_urls', null);
    }
};

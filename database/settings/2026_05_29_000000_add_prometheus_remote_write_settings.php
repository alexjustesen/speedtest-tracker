<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddPrometheusRemoteWriteSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('dataintegration.prometheus_remote_write_enabled', false);
        $this->migrator->add('dataintegration.prometheus_remote_write_url', null);
        $this->migrator->add('dataintegration.prometheus_remote_write_username', null);
        $this->migrator->add('dataintegration.prometheus_remote_write_password', null);
    }
}

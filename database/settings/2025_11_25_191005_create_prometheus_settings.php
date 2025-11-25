<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreatePrometheusSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('dataintegration.prometheus_enabled', false);
        $this->migrator->add('dataintegration.prometheus_basic_auth_enabled', false);
        $this->migrator->add('dataintegration.prometheus_basic_auth_username', null);
        $this->migrator->add('dataintegration.prometheus_basic_auth_password', null);
    }
}

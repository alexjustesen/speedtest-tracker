<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreatePrometheusSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('dataintegration.prometheus_enabled', config('data-integrations.prometheus_enabled'));
        $this->migrator->add('dataintegration.prometheus_allowed_ips', config('data-integrations.prometheus_allowed_ips') ? explode(',', config('data-integrations.prometheus_allowed_ips')) : []);
    }
}

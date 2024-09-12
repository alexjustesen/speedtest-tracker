<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateMetricsSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('metrics.prometheus_enabled', false);
        $this->migrator->add('metrics.influxdb_v2_enabled', false);
        $this->migrator->add('metrics.influxdb_v2_url', null);
        $this->migrator->add('metrics.influxdb_v2_org', null);
        $this->migrator->add('metrics.influxdb_v2_bucket', 'speedtest-tracker');
        $this->migrator->add('metrics.influxdb_v2_token', null);
        $this->migrator->add('metrics.influxdb_v2_verify_ssl', true);
    }
}

<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateMetricsSettings extends SettingsMigration
{
    public function up(): void
    {
        // Add Prometheus setting
        $this->migrator->add('metrics.prometheus_enabled', false);

        // Migrate InfluxDB v2 settings from the old structure
        $this->migrator->add('metrics.influxdb_v2_enabled', false);
        $this->migrator->add('metrics.influxdb_v2_url');
        $this->migrator->add('metrics.influxdb_v2_org');
        $this->migrator->add('metrics.influxdb_v2_bucket', 'speedtest-tracker');
        $this->migrator->add('metrics.influxdb_v2_token');
        $this->migrator->add('metrics.influxdb_v2_verify_ssl', true);
    }
}

<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateDataIntegrationSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('dataintegration.influxdb_v2_enabled', false);
        $this->migrator->add('dataintegration.influxdb_v2_url');
        $this->migrator->add('dataintegration.influxdb_v2_org');
        $this->migrator->add('dataintegration.influxdb_v2_bucket', 'speedtest-tracker');
        $this->migrator->add('dataintegration.influxdb_v2_token');
        $this->migrator->add('dataintegration.influxdb_v2_verify_ssl', true);
    }
}

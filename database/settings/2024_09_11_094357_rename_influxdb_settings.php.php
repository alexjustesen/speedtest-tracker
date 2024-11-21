<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class RenameInfluxdbSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->rename('influxdb.v2_enabled', 'dataintegration.influxdb_v2_enabled');
        $this->migrator->rename('influxdb.v2_url', 'dataintegration.influxdb_v2_url');
        $this->migrator->rename('influxdb.v2_org', 'dataintegration.influxdb_v2_org');
        $this->migrator->rename('influxdb.v2_bucket', 'dataintegration.influxdb_v2_bucket');
        $this->migrator->rename('influxdb.v2_token', 'dataintegration.influxdb_v2_token');
        $this->migrator->rename('influxdb.v2_verify_ssl', 'dataintegration.influxdb_v2_verify_ssl');
    }
}

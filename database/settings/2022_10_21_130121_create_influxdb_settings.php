<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateInfluxDbSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('influxdb.v2_enabled', config('data-integrations.influxdb_v2_enabled'));
        $this->migrator->add('influxdb.v2_url', config('data-integrations.influxdb_v2_url'));
        $this->migrator->add('influxdb.v2_org', config('data-integrations.influxdb_v2_org'));
        $this->migrator->add('influxdb.v2_bucket', config('data-integrations.influxdb_v2_bucket'));
        $this->migrator->add('influxdb.v2_token', config('data-integrations.influxdb_v2_token'));
    }
}

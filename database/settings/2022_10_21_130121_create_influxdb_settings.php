<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateInfluxDbSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('influxdb.v2_enabled', false);
        $this->migrator->add('influxdb.v2_url', null);
        $this->migrator->add('influxdb.v2_org', null);
        $this->migrator->add('influxdb.v2_bucket', 'speedtest-tracker');
        $this->migrator->add('influxdb.v2_token', null);
    }
}

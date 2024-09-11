<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreatePrometheusSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('prometheus.enabled', false);
    }
}

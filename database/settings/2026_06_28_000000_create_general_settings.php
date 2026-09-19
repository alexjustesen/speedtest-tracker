<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.chart_default_range', config('speedtest.chart_default_range_days'));
        $this->migrator->add('general.chart_begin_at_zero', config('speedtest.chart_begin_at_zero'));
        $this->migrator->add('general.chart_datetime_format', config('speedtest.chart_datetime_format'));
        $this->migrator->add('general.chart_only_show_avg_latency', config('speedtest.chart_only_show_avg_latency'));
    }
};

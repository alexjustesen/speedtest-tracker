<?php

namespace App\Listeners\Data;

use App\Events\SpeedtestCompleted;
use App\Jobs\InfluxDBv2\WriteCompletedSpeedtest;
use App\Settings\MetricsSettings; // Update to use MetricsSettings

class InfluxDb2Listener
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $metricsSettings = new MetricsSettings();

        if ($metricsSettings->influxdb_v2_enabled) {
            WriteCompletedSpeedtest::dispatch($event->result, $metricsSettings);
        }
    }
}

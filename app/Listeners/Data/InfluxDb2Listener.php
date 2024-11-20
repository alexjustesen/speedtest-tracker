<?php

namespace App\Listeners\Data;

use App\Events\SpeedtestCompleted;
use App\Jobs\InfluxDBv2\WriteCompletedSpeedtest;
use App\Settings\DataIntegrationSettings;

class InfluxDb2Listener
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $DataIntegrationSettings = new DataIntegrationSettings;

        if ($DataIntegrationSettings->influxdb_v2_enabled) {
            WriteCompletedSpeedtest::dispatch($event->result, $DataIntegrationSettings);
        }
    }
}

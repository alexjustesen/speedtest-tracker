<?php

namespace App\Listeners\Data;

use App\Events\SpeedtestCompleted;
use App\Jobs\InfluxDBv2\WriteCompletedSpeedtest;
use App\Settings\InfluxDbSettings;

class InfluxDb2Listener
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $influxSettings = new InfluxDbSettings;

        if ($influxSettings->v2_enabled) {
            WriteCompletedSpeedtest::dispatch($event->result, $influxSettings);
        }
    }
}

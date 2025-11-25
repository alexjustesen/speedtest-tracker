<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Influxdb\v2\WriteResult;
use App\Settings\DataIntegrationSettings;

class ProcessSpeedtestDataIntegrations
{
    /**
     * Create the event listener.
     */
    public function __construct(
        public DataIntegrationSettings $settings,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted|SpeedtestFailed $event): void
    {
        if ($this->settings->influxdb_v2_enabled) {
            WriteResult::dispatch($event->result);
        }
    }
}

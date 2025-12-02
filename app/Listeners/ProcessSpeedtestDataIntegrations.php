<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Influxdb\v2\WriteResult;
use App\Settings\DataIntegrationSettings;
use Illuminate\Support\Facades\Cache;

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

        if ($this->settings->prometheus_enabled) {
            Cache::forever('prometheus:latest_result', $event->result->id);
        }
    }
}

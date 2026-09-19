<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Influxdb\v2\WriteResult as InfluxdbWriteResult;
use App\Jobs\Prometheus\WriteResult as PrometheusWriteResult;
use App\Services\Prometheus\PrometheusMetricsService;
use App\Settings\DataIntegrationSettings;

class ProcessSpeedtestDataIntegrations
{
    /**
     * Create the event listener.
     */
    public function __construct(
        public DataIntegrationSettings $settings,
        public PrometheusMetricsService $prometheusService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted|SpeedtestFailed $event): void
    {
        if ($this->settings->influxdb_v2_enabled) {
            InfluxdbWriteResult::dispatch($event->result);
        }

        if ($this->settings->prometheus_enabled) {
            $this->prometheusService->updateMetrics($event->result);
        }

        if ($this->settings->prometheus_remote_write_enabled) {
            PrometheusWriteResult::dispatch($event->result);
        }
    }
}

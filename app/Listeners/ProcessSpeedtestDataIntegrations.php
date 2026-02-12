<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Influxdb\v2\WriteResult;
use App\Services\PrometheusMetricsService;
use App\Settings\DataIntegrationSettings;

class ProcessSpeedtestDataIntegrations
{
    /**
     * Create the event listener.
     */
    public function __construct(
        public DataIntegrationSettings $settings,
        public PrometheusMetricsService $prometheusService,
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
            // Update Prometheus metrics cache when speedtest completes/fails
            // This prevents rebuilding metrics on every scrape
            $this->prometheusService->updateMetrics($event->result);
        }
    }
}

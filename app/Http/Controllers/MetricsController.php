<?php

namespace App\Http\Controllers;

use App\Services\PrometheusMetricsService;
use App\Settings\DataIntegrationSettings;
use Illuminate\Http\Response;

class MetricsController extends Controller
{
    public function __construct(
        protected PrometheusMetricsService $metricsService,
        protected DataIntegrationSettings $settings
    ) {}

    public function __invoke(): Response
    {
        if (! $this->settings->prometheus_enabled) {
            abort(404);
        }

        $metrics = $this->metricsService->generateMetrics();

        return response($metrics, 200, [
            'Content-Type' => 'text/plain; version=0.0.4; charset=utf-8',
        ]);
    }
}

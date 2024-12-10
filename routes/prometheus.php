<?php

use App\Services\PrometheusService;
use App\Settings\DataIntegrationSettings;

Route::get('/metrics', function (DataIntegrationSettings $settings, PrometheusService $prometheusService) {
    // Check if metrics are enabled
    if (! $settings->prometheus_enabled) {
        return response('Metrics endpoint is disabled.', 403);
    }

    // Set up and collect metrics
    $prometheusService->setupMetrics();
    $prometheusService->collectMetrics();

    // Render and return metrics
    return response($prometheusService->renderMetrics(), 200)
        ->header('Content-Type', \Prometheus\RenderTextFormat::MIME_TYPE);
});

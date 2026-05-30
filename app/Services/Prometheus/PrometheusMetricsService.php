<?php

namespace App\Services\Prometheus;

use App\Models\Result;
use Illuminate\Support\Facades\Cache;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;

class PrometheusMetricsService extends PrometheusService
{
    public function generateMetrics(): string
    {
        // Return cached metrics if available
        // This avoids rebuilding the registry and querying the DB on every scrape
        return Cache::get('prometheus:metrics', fn () => $this->emptyMetrics());
    }

    public function updateMetrics(Result $result): void
    {
        // Build metrics only when data changes (speedtest completes/fails)
        $registry = new CollectorRegistry(new InMemory);

        $this->registerMetrics($registry, $result);

        $renderer = new RenderTextFormat;
        $metrics = $renderer->render($registry->getMetricFamilySamples());

        // Cache the rendered metrics so scrapes don't rebuild every time
        Cache::forever('prometheus:metrics', $metrics);
    }

    protected function registerMetrics(CollectorRegistry $registry, Result $result): void
    {
        $labels = $this->buildLabels($result);
        $labelNames = array_keys($labels);
        $labelValues = array_values($labels);
        $timestamp = $result->updated_at?->timestamp;

        $this->registerStaticMetrics($registry);

        // Info metric — always 1, metadata in labels
        // Exported for both completed and failed tests so Prometheus can track all test attempts
        $infoGauge = $registry->getOrRegisterGauge('speedtest_tracker', 'info', 'Speedtest metadata and status', $labelNames);
        $infoGauge->set(1, $labelValues, $timestamp);

        foreach ($this->collectMetrics($result) as $name => $metric) {
            $this->registerGaugeIfNotNull($registry, $name, $metric['help'], $labelNames, $labelValues, $metric['value'], $timestamp);
        }
    }

    protected function registerGaugeIfNotNull(
        CollectorRegistry $registry,
        string $name,
        string $help,
        array $labelNames,
        array $labelValues,
        mixed $value,
        ?int $timestamp = null
    ): void {
        if ($value !== null) {
            $gauge = $registry->getOrRegisterGauge('speedtest_tracker', $name, $help, $labelNames);
            $gauge->set($value, $labelValues, $timestamp);
        }
    }

    protected function registerStaticMetrics(CollectorRegistry $registry): void
    {
        $up = $registry->getOrRegisterGauge('speedtest_tracker', 'up', 'Exporter is responding');
        $up->set(1, []);

        $buildInfo = $registry->getOrRegisterGauge('speedtest_tracker', 'build_info', 'Application version information', ['version']);
        $buildInfo->set(1, [config('speedtest.build_version')]);
    }

    protected function emptyMetrics(): string
    {
        $registry = new CollectorRegistry(new InMemory);

        $this->registerStaticMetrics($registry);

        return (new RenderTextFormat)->render($registry->getMetricFamilySamples());
    }
}

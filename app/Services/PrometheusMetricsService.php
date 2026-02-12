<?php

namespace App\Services;

use App\Models\Result;
use App\Settings\DataIntegrationSettings;
use Illuminate\Support\Facades\Cache;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;

class PrometheusMetricsService
{
    public function __construct(
        protected DataIntegrationSettings $settings
    ) {}

    public function generateMetrics(): string
    {
        // Return cached metrics if available
        // This avoids rebuilding the registry and querying the DB on every scrape
        return Cache::get('prometheus:metrics', $this->emptyMetrics());
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

        // Standard 'up' metric - exporter is responding
        $up = $registry->getOrRegisterGauge('speedtest_tracker', 'up', 'Exporter is responding');
        $up->set(1, []);

        // Build info metric - application version
        $buildInfo = $registry->getOrRegisterGauge('speedtest_tracker', 'build_info', 'Application version information', ['version']);
        $buildInfo->set(1, [config('speedtest.build_version')]);

        // Info metric - always set to 1, metadata in labels
        // Exported for both completed and failed tests so Prometheus can track all test attempts
        $infoGauge = $registry->getOrRegisterGauge('speedtest_tracker', 'info', 'Speedtest metadata and status', $labelNames);
        $infoGauge->set(1, $labelValues, $timestamp);

        // Register all speed/latency metrics
        // Failed tests will have null values, which registerGaugeIfNotNull automatically skips

        // Speed metrics (rates)
        $this->registerGaugeIfNotNull($registry, 'download_bytes_per_second', 'Download speed in bytes per second', $labelNames, $labelValues, $result->download, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'upload_bytes_per_second', 'Upload speed in bytes per second', $labelNames, $labelValues, $result->upload, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'download_bits_per_second', 'Download speed in bits per second', $labelNames, $labelValues, $result->download ? toBits($result->download) : null, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'upload_bits_per_second', 'Upload speed in bits per second', $labelNames, $labelValues, $result->upload ? toBits($result->upload) : null, $timestamp);

        // Ping metrics
        $this->registerGaugeIfNotNull($registry, 'ping_ms', 'Ping latency in milliseconds', $labelNames, $labelValues, $result->ping, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'ping_low_ms', 'Ping low latency in milliseconds', $labelNames, $labelValues, $result->ping_low, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'ping_high_ms', 'Ping high latency in milliseconds', $labelNames, $labelValues, $result->ping_high, $timestamp);

        // Jitter metrics
        $this->registerGaugeIfNotNull($registry, 'ping_jitter_ms', 'Ping jitter in milliseconds', $labelNames, $labelValues, $result->ping_jitter, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'download_jitter_ms', 'Download jitter in milliseconds', $labelNames, $labelValues, $result->download_jitter, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'upload_jitter_ms', 'Upload jitter in milliseconds', $labelNames, $labelValues, $result->upload_jitter, $timestamp);

        // Packet loss
        $this->registerGaugeIfNotNull($registry, 'packet_loss_percent', 'Packet loss percentage', $labelNames, $labelValues, $result->packet_loss, $timestamp);

        // Download latency metrics (IQM = Interquartile Mean - more reliable than average)
        $this->registerGaugeIfNotNull($registry, 'download_latency_iqm_ms', 'Download latency interquartile mean in milliseconds', $labelNames, $labelValues, $result->downloadlatencyiqm, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'download_latency_low_ms', 'Download latency low in milliseconds', $labelNames, $labelValues, $result->downloadlatency_low, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'download_latency_high_ms', 'Download latency high in milliseconds', $labelNames, $labelValues, $result->downloadlatency_high, $timestamp);

        // Upload latency metrics
        $this->registerGaugeIfNotNull($registry, 'upload_latency_iqm_ms', 'Upload latency interquartile mean in milliseconds', $labelNames, $labelValues, $result->uploadlatencyiqm, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'upload_latency_low_ms', 'Upload latency low in milliseconds', $labelNames, $labelValues, $result->uploadlatency_low, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'upload_latency_high_ms', 'Upload latency high in milliseconds', $labelNames, $labelValues, $result->uploadlatency_high, $timestamp);

        // Bytes transferred during test (cumulative totals)
        $this->registerGaugeIfNotNull($registry, 'test_downloaded_bytes_total', 'Total bytes downloaded during test', $labelNames, $labelValues, $result->downloaded_bytes, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'test_uploaded_bytes_total', 'Total bytes uploaded during test', $labelNames, $labelValues, $result->uploaded_bytes, $timestamp);

        // Test duration
        $this->registerGaugeIfNotNull($registry, 'download_elapsed_ms', 'Download test duration in milliseconds', $labelNames, $labelValues, $result->download_elapsed, $timestamp);
        $this->registerGaugeIfNotNull($registry, 'upload_elapsed_ms', 'Upload test duration in milliseconds', $labelNames, $labelValues, $result->upload_elapsed, $timestamp);
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
            $gauge = $registry->getOrRegisterGauge(
                'speedtest_tracker',
                $name,
                $help,
                $labelNames
            );
            $gauge->set($value, $labelValues, $timestamp);
        }
    }

    protected function buildLabels(Result $result): array
    {
        return [
            'server_name' => $result->server_name ?? '',
            'server_location' => $result->server_location ?? '',
            'isp' => $result->isp ?? '',
            'scheduled' => $result->scheduled ? 'true' : 'false',
            'healthy' => $result->healthy ? 'true' : 'false',
            'status' => $result->status->value,
            'app_name' => config('app.name', 'Speedtest Tracker'),
        ];
    }

    protected function emptyMetrics(): string
    {
        return "# no data available\n";
    }
}

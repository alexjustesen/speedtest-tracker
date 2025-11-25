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
        $registry = new CollectorRegistry(new InMemory);

        $resultId = Cache::get('prometheus:latest_result');

        if (! $resultId) {
            return $this->emptyMetrics();
        }

        $lastResult = Result::find($resultId);

        if (! $lastResult) {
            return $this->emptyMetrics();
        }

        $this->registerMetrics($registry, $lastResult);

        $renderer = new RenderTextFormat;

        return $renderer->render($registry->getMetricFamilySamples());
    }

    protected function registerMetrics(CollectorRegistry $registry, Result $result): void
    {
        $labels = $this->buildLabels($result);
        $labelNames = array_keys($labels);
        $labelValues = array_values($labels);

        // Download speed in bytes
        $downloadBytesGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'download_bytes',
            'Download speed in bytes per second',
            $labelNames
        );
        $downloadBytesGauge->set($result->download, $labelValues);

        // Upload speed in bytes
        $uploadBytesGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'upload_bytes',
            'Upload speed in bytes per second',
            $labelNames
        );
        $uploadBytesGauge->set($result->upload, $labelValues);

        // Download speed in bits per second
        $downloadBitsGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'download_bits',
            'Download speed in bits per second',
            $labelNames
        );
        $downloadBitsGauge->set(toBits($result->download), $labelValues);

        // Upload speed in bits per second
        $uploadBitsGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'upload_bits',
            'Upload speed in bits per second',
            $labelNames
        );
        $uploadBitsGauge->set(toBits($result->upload), $labelValues);

        // Ping latency in milliseconds
        $pingGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'ping_ms',
            'Ping latency in milliseconds',
            $labelNames
        );
        $pingGauge->set($result->ping, $labelValues);

        // Ping jitter
        $pingJitterGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'ping_jitter_ms',
            'Ping jitter in milliseconds',
            $labelNames
        );
        $pingJitterGauge->set($result->ping_jitter ?? 0, $labelValues);

        // Download jitter
        $downloadJitterGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'download_jitter_ms',
            'Download jitter in milliseconds',
            $labelNames
        );
        $downloadJitterGauge->set($result->download_jitter ?? 0, $labelValues);

        // Upload jitter
        $uploadJitterGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'upload_jitter_ms',
            'Upload jitter in milliseconds',
            $labelNames
        );
        $uploadJitterGauge->set($result->upload_jitter ?? 0, $labelValues);

        // Packet loss
        $packetLossGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'packet_loss_percent',
            'Packet loss percentage',
            $labelNames
        );
        $packetLossGauge->set($result->packet_loss ?? 0, $labelValues);

        // Ping latency low/high
        $pingLowGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'ping_low_ms',
            'Ping low latency in milliseconds',
            $labelNames
        );
        $pingLowGauge->set($result->ping_low ?? 0, $labelValues);

        $pingHighGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'ping_high_ms',
            'Ping high latency in milliseconds',
            $labelNames
        );
        $pingHighGauge->set($result->ping_high ?? 0, $labelValues);

        // Download latency metrics (IQM = Interquartile Mean - more reliable than average)
        $downloadLatencyIqmGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'download_latency_iqm_ms',
            'Download latency interquartile mean in milliseconds',
            $labelNames
        );
        $downloadLatencyIqmGauge->set($result->downloadlatencyiqm ?? 0, $labelValues);

        $downloadLatencyLowGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'download_latency_low_ms',
            'Download latency low in milliseconds',
            $labelNames
        );
        $downloadLatencyLowGauge->set($result->downloadlatency_low ?? 0, $labelValues);

        $downloadLatencyHighGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'download_latency_high_ms',
            'Download latency high in milliseconds',
            $labelNames
        );
        $downloadLatencyHighGauge->set($result->downloadlatency_high ?? 0, $labelValues);

        // Upload latency metrics
        $uploadLatencyIqmGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'upload_latency_iqm_ms',
            'Upload latency interquartile mean in milliseconds',
            $labelNames
        );
        $uploadLatencyIqmGauge->set($result->uploadlatencyiqm ?? 0, $labelValues);

        $uploadLatencyLowGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'upload_latency_low_ms',
            'Upload latency low in milliseconds',
            $labelNames
        );
        $uploadLatencyLowGauge->set($result->uploadlatency_low ?? 0, $labelValues);

        $uploadLatencyHighGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'upload_latency_high_ms',
            'Upload latency high in milliseconds',
            $labelNames
        );
        $uploadLatencyHighGauge->set($result->uploadlatency_high ?? 0, $labelValues);

        // Bytes transferred during test
        $downloadedBytesGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'downloaded_bytes',
            'Total bytes downloaded during test',
            $labelNames
        );
        $downloadedBytesGauge->set($result->downloaded_bytes ?? 0, $labelValues);

        $uploadedBytesGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'uploaded_bytes',
            'Total bytes uploaded during test',
            $labelNames
        );
        $uploadedBytesGauge->set($result->uploaded_bytes ?? 0, $labelValues);

        // Test duration
        $downloadElapsedGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'download_elapsed_ms',
            'Download test duration in milliseconds',
            $labelNames
        );
        $downloadElapsedGauge->set($result->download_elapsed ?? 0, $labelValues);

        $uploadElapsedGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'upload_elapsed_ms',
            'Upload test duration in milliseconds',
            $labelNames
        );
        $uploadElapsedGauge->set($result->upload_elapsed ?? 0, $labelValues);
    }

    protected function buildLabels(Result $result): array
    {
        return [
            'server_id' => (string) ($result->server_id ?? ''),
            'server_name' => $result->server_name ?? '',
            'server_country' => $result->server_country ?? '',
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

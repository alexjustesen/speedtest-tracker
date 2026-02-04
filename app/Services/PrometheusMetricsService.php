<?php

namespace App\Services;

use App\Enums\ResultStatus;
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

        // Info metric - always exported so users can see test status (including failures)
        $infoGauge = $registry->getOrRegisterGauge(
            'speedtest_tracker',
            'result_id',
            'Speedtest result id',
            $labelNames
        );
        $infoGauge->set($result->id, $labelValues);

        // Only export numeric metrics for completed tests
        // Failed/incomplete tests won't have valid measurements
        if ($result->status !== ResultStatus::Completed) {
            return;
        }

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

        // Jitter metrics - optional, may not be present in all test results
        $this->registerGaugeIfNotNull($registry, 'ping_jitter_ms', 'Ping jitter in milliseconds', $labelNames, $labelValues, $result->ping_jitter);
        $this->registerGaugeIfNotNull($registry, 'download_jitter_ms', 'Download jitter in milliseconds', $labelNames, $labelValues, $result->download_jitter);
        $this->registerGaugeIfNotNull($registry, 'upload_jitter_ms', 'Upload jitter in milliseconds', $labelNames, $labelValues, $result->upload_jitter);

        // Packet loss - optional
        $this->registerGaugeIfNotNull($registry, 'packet_loss_percent', 'Packet loss percentage', $labelNames, $labelValues, $result->packet_loss);

        // Ping latency metrics - optional
        $this->registerGaugeIfNotNull($registry, 'ping_low_ms', 'Ping low latency in milliseconds', $labelNames, $labelValues, $result->ping_low);
        $this->registerGaugeIfNotNull($registry, 'ping_high_ms', 'Ping high latency in milliseconds', $labelNames, $labelValues, $result->ping_high);

        // Download latency metrics - optional (IQM = Interquartile Mean - more reliable than average)
        $this->registerGaugeIfNotNull($registry, 'download_latency_iqm_ms', 'Download latency interquartile mean in milliseconds', $labelNames, $labelValues, $result->downloadlatencyiqm);
        $this->registerGaugeIfNotNull($registry, 'download_latency_low_ms', 'Download latency low in milliseconds', $labelNames, $labelValues, $result->downloadlatency_low);
        $this->registerGaugeIfNotNull($registry, 'download_latency_high_ms', 'Download latency high in milliseconds', $labelNames, $labelValues, $result->downloadlatency_high);

        // Upload latency metrics - optional
        $this->registerGaugeIfNotNull($registry, 'upload_latency_iqm_ms', 'Upload latency interquartile mean in milliseconds', $labelNames, $labelValues, $result->uploadlatencyiqm);
        $this->registerGaugeIfNotNull($registry, 'upload_latency_low_ms', 'Upload latency low in milliseconds', $labelNames, $labelValues, $result->uploadlatency_low);
        $this->registerGaugeIfNotNull($registry, 'upload_latency_high_ms', 'Upload latency high in milliseconds', $labelNames, $labelValues, $result->uploadlatency_high);

        // Bytes transferred during test - optional
        $this->registerGaugeIfNotNull($registry, 'downloaded_bytes', 'Total bytes downloaded during test', $labelNames, $labelValues, $result->downloaded_bytes);
        $this->registerGaugeIfNotNull($registry, 'uploaded_bytes', 'Total bytes uploaded during test', $labelNames, $labelValues, $result->uploaded_bytes);

        // Test duration - optional
        $this->registerGaugeIfNotNull($registry, 'download_elapsed_ms', 'Download test duration in milliseconds', $labelNames, $labelValues, $result->download_elapsed);
        $this->registerGaugeIfNotNull($registry, 'upload_elapsed_ms', 'Upload test duration in milliseconds', $labelNames, $labelValues, $result->upload_elapsed);
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

    /**
     * Register a gauge metric only if the value is not null.
     * Follows Prometheus best practice of not exporting missing metrics.
     */
    protected function registerGaugeIfNotNull(
        CollectorRegistry $registry,
        string $name,
        string $help,
        array $labelNames,
        array $labelValues,
        mixed $value
    ): void {
        if ($value !== null) {
            $gauge = $registry->getOrRegisterGauge(
                'speedtest_tracker',
                $name,
                $help,
                $labelNames
            );
            $gauge->set($value, $labelValues);
        }
    }
}

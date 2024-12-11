<?php

namespace App\Services;

use App\Models\Result;
use Illuminate\Support\Facades\DB;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;

class PrometheusService
{
    protected $registry;

    public function __construct()
    {
        $this->registry = new CollectorRegistry(new InMemory);
    }

    public function setupMetrics(): void
    {
        $metrics = [
            'ping_jitter' => 'Ping jitter',
            'ping_latency' => 'Ping latency',
            'ping_low' => 'Ping low value',
            'ping_high' => 'Ping high value',
            'download_bandwidth' => 'Download bandwidth',
            'download_bytes' => 'Download bytes',
            'download_elapsed' => 'Download elapsed time',
            'download_latency_iqm' => 'Download latency IQM',
            'download_latency_low' => 'Download latency low value',
            'download_latency_high' => 'Download latency high value',
            'download_latency_jitter' => 'Download latency jitter',
            'upload_bandwidth' => 'Upload bandwidth',
            'upload_bytes' => 'Upload bytes',
            'upload_elapsed' => 'Upload elapsed time',
            'upload_latency_iqm' => 'Upload latency IQM',
            'upload_latency_low' => 'Upload latency low value',
            'upload_latency_high' => 'Upload latency high value',
            'upload_latency_jitter' => 'Upload latency jitter',
            'packet_loss' => 'Packet loss percentage',
            'result_id' => 'Result ID',
        ];

        foreach ($metrics as $key => $description) {
            $this->registry->registerGauge(
                'speedtest_tracker',
                $key,
                $description,
                ['server_id', 'server_name', 'isp', 'server_location', 'scheduled', 'healthy', 'status', 'app_name']
            );
        }
    }

    public function collectMetrics(): void
    {
        $latestResult = DB::table('results')->orderBy('created_at', 'desc')->first();

        if ($latestResult && $latestResult->data) {
            $data = json_decode($latestResult->data);
            $result = Result::find($latestResult->id);

            $labels = [
                $data->server->id ?? 'unknown',
                $data->server->name ?? 'unknown',
                $data->isp ?? 'unknown',
                $data->server->location ?? 'unknown',
                $result->scheduled ? 'true' : 'false',
                $result->status ?? 'unknown',
                $result->healthy ? 'true' : 'false',
                config('app.name'),
            ];

            $this->setGauge('ping_jitter', $data->ping->jitter ?? 0.0, $labels);
            $this->setGauge('ping_latency', $data->ping->latency ?? 0.0, $labels);
            $this->setGauge('ping_low', $data->ping->low ?? 0.0, $labels);
            $this->setGauge('ping_high', $data->ping->high ?? 0.0, $labels);

            $this->setGauge('download_bandwidth', $data->download->bandwidth ?? 0.0, $labels);
            $this->setGauge('download_bytes', $data->download->bytes ?? 0.0, $labels);
            $this->setGauge('download_elapsed', $data->download->elapsed ?? 0.0, $labels);
            $this->setGauge('download_latency_iqm', $data->download->latency->iqm ?? 0.0, $labels);
            $this->setGauge('download_latency_low', $data->download->latency->low ?? 0.0, $labels);
            $this->setGauge('download_latency_high', $data->download->latency->high ?? 0.0, $labels);
            $this->setGauge('download_latency_jitter', $data->download->latency->jitter ?? 0.0, $labels);

            $this->setGauge('upload_bandwidth', $data->upload->bandwidth ?? 0.0, $labels);
            $this->setGauge('upload_bytes', $data->upload->bytes ?? 0.0, $labels);
            $this->setGauge('upload_elapsed', $data->upload->elapsed ?? 0.0, $labels);
            $this->setGauge('upload_latency_iqm', $data->upload->latency->iqm ?? 0.0, $labels);
            $this->setGauge('upload_latency_low', $data->upload->latency->low ?? 0.0, $labels);
            $this->setGauge('upload_latency_high', $data->upload->latency->high ?? 0.0, $labels);
            $this->setGauge('upload_latency_jitter', $data->upload->latency->jitter ?? 0.0, $labels);

            $this->setGauge('packet_loss', $data->packetLoss ?? 0.0, $labels);
            $this->setGauge('result_id', $latestResult->id, $labels);
        } else {
            $this->setDefaultMetrics();
        }
    }

    protected function setGauge(string $metric, float $value, array $labels): void
    {
        $gauge = $this->registry->getGauge('speedtest_tracker', $metric);
        $gauge->set($value, $labels);
    }

    protected function setDefaultMetrics(): void
    {
        $defaultLabels = [
            'unknown', 'unknown', 'unknown', 'unknown',
            'false', 'unknown', 'false', config('app.name'),
        ];

        // Register and set default value for all metrics
        $metrics = [
            'ping_jitter', 'ping_latency', 'ping_low', 'ping_high',
            'download_bandwidth', 'download_bytes', 'download_elapsed',
            'download_latency_iqm', 'download_latency_low', 'download_latency_high',
            'download_latency_jitter', 'upload_bandwidth', 'upload_bytes',
            'upload_elapsed', 'upload_latency_iqm', 'upload_latency_low',
            'upload_latency_high', 'upload_latency_jitter', 'packet_loss',
            'result_id',
        ];

        foreach ($metrics as $metric) {
            $gauge = $this->registry->getGauge('speedtest_tracker', $metric);

            // If the metric doesn't exist, register it
            if (! $gauge) {
                $gauge = $this->registry->registerGauge(
                    'speedtest_tracker',
                    $metric,
                    'Default metric for '.$metric,
                    ['server_id', 'server_name', 'isp', 'server_location', 'scheduled', 'healthy', 'status', 'app_name']
                );
            }

            // Set the default value for the metric
            $gauge->set(0.0, $defaultLabels);
        }
    }

    public function renderMetrics()
    {
        $renderer = new RenderTextFormat;

        return $renderer->render($this->registry->getMetricFamilySamples());
    }
}

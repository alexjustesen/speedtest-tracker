<?php

use App\Settings\PrometheusSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;

/*
|--------------------------------------------------------------------------
| Metrics Route
|--------------------------------------------------------------------------
|
| This route exposes metrics for Prometheus.
|
*/

Route::get('/metrics', function (PrometheusSettings $settings) {
    // Check if metrics are enabled
    if (! $settings->enabled) {
        return response('Metrics endpoint is disabled.', 403);
    }

    // Create a new Prometheus registry
    $registry = new CollectorRegistry(new InMemory());

    // Register gauges for numeric metrics with labels
    $pingJitterGauge = $registry->registerGauge('speedtest_tracker', 'ping_jitter', 'Ping jitter', ['server_id', 'server_name', 'isp']);
    $pingLatencyGauge = $registry->registerGauge('speedtest_tracker', 'ping_latency', 'Ping latency', ['server_id', 'server_name', 'isp']);
    $pingLowGauge = $registry->registerGauge('speedtest_tracker', 'ping_low', 'Ping low value', ['server_id', 'server_name', 'isp']);
    $pingHighGauge = $registry->registerGauge('speedtest_tracker', 'ping_high', 'Ping high value', ['server_id', 'server_name', 'isp']);

    $downloadBandwidthGauge = $registry->registerGauge('speedtest_tracker', 'download_bandwidth', 'Download bandwidth', ['server_id', 'server_name', 'isp']);
    $downloadBytesGauge = $registry->registerGauge('speedtest_tracker', 'download_bytes', 'Download bytes', ['server_id', 'server_name', 'isp']);
    $downloadElapsedGauge = $registry->registerGauge('speedtest_tracker', 'download_elapsed', 'Download elapsed time', ['server_id', 'server_name', 'isp']);
    $downloadLatencyIqmGauge = $registry->registerGauge('speedtest_tracker', 'download_latency_iqm', 'Download latency IQM', ['server_id', 'server_name', 'isp']);
    $downloadLatencyLowGauge = $registry->registerGauge('speedtest_tracker', 'download_latency_low', 'Download latency low value', ['server_id', 'server_name', 'isp']);
    $downloadLatencyHighGauge = $registry->registerGauge('speedtest_tracker', 'download_latency_high', 'Download latency high value', ['server_id', 'server_name', 'isp']);
    $downloadLatencyJitterGauge = $registry->registerGauge('speedtest_tracker', 'download_latency_jitter', 'Download latency jitter', ['server_id', 'server_name', 'isp']);

    $uploadBandwidthGauge = $registry->registerGauge('speedtest_tracker', 'upload_bandwidth', 'Upload bandwidth', ['server_id', 'server_name', 'isp']);
    $uploadBytesGauge = $registry->registerGauge('speedtest_tracker', 'upload_bytes', 'Upload bytes', ['server_id', 'server_name', 'isp']);
    $uploadElapsedGauge = $registry->registerGauge('speedtest_tracker', 'upload_elapsed', 'Upload elapsed time', ['server_id', 'server_name', 'isp']);
    $uploadLatencyIqmGauge = $registry->registerGauge('speedtest_tracker', 'upload_latency_iqm', 'Upload latency IQM', ['server_id', 'server_name', 'isp']);
    $uploadLatencyLowGauge = $registry->registerGauge('speedtest_tracker', 'upload_latency_low', 'Upload latency low value', ['server_id', 'server_name', 'isp']);
    $uploadLatencyHighGauge = $registry->registerGauge('speedtest_tracker', 'upload_latency_high', 'Upload latency high value', ['server_id', 'server_name', 'isp']);
    $uploadLatencyJitterGauge = $registry->registerGauge('speedtest_tracker', 'upload_latency_jitter', 'Upload latency jitter', ['server_id', 'server_name', 'isp']);

    $packetLossGauge = $registry->registerGauge('speedtest_tracker', 'packet_loss', 'Packet loss percentage', ['server_id', 'server_name', 'isp']);
    $resultIdGauge = $registry->registerGauge('speedtest_tracker', 'result_id', 'Result ID', ['server_id', 'server_name', 'isp']);

    // Fetch the latest result from the database
    $latestResult = DB::table('results')
        ->orderBy('created_at', 'desc')
        ->first();

    if ($latestResult && $latestResult->data) {
        $data = json_decode($latestResult->data);

        // Set gauge values for numeric metrics with labels
        $serverId = $data->server->id ?? 'unknown';
        $serverName = $data->server->name ?? 'unknown';
        $isp = $data->isp ?? 'unknown';

        $pingJitterGauge->set((float) ($data->ping->jitter ?? 0.0), [$serverId, $serverName, $isp]);
        $pingLatencyGauge->set((float) ($data->ping->latency ?? 0.0), [$serverId, $serverName, $isp]);
        $pingLowGauge->set((float) ($data->ping->low ?? 0.0), [$serverId, $serverName, $isp]);
        $pingHighGauge->set((float) ($data->ping->high ?? 0.0), [$serverId, $serverName, $isp]);

        $downloadBandwidthGauge->set((float) ($data->download->bandwidth ?? 0.0), [$serverId, $serverName, $isp]);
        $downloadBytesGauge->set((float) ($data->download->bytes ?? 0.0), [$serverId, $serverName, $isp]);
        $downloadElapsedGauge->set((float) ($data->download->elapsed ?? 0.0), [$serverId, $serverName, $isp]);
        $downloadLatencyIqmGauge->set((float) ($data->download->latency->iqm ?? 0.0), [$serverId, $serverName, $isp]);
        $downloadLatencyLowGauge->set((float) ($data->download->latency->low ?? 0.0), [$serverId, $serverName, $isp]);
        $downloadLatencyHighGauge->set((float) ($data->download->latency->high ?? 0.0), [$serverId, $serverName, $isp]);
        $downloadLatencyJitterGauge->set((float) ($data->download->latency->jitter ?? 0.0), [$serverId, $serverName, $isp]);

        $uploadBandwidthGauge->set((float) ($data->upload->bandwidth ?? 0.0), [$serverId, $serverName, $isp]);
        $uploadBytesGauge->set((float) ($data->upload->bytes ?? 0.0), [$serverId, $serverName, $isp]);
        $uploadElapsedGauge->set((float) ($data->upload->elapsed ?? 0.0), [$serverId, $serverName, $isp]);
        $uploadLatencyIqmGauge->set((float) ($data->upload->latency->iqm ?? 0.0), [$serverId, $serverName, $isp]);
        $uploadLatencyLowGauge->set((float) ($data->upload->latency->low ?? 0.0), [$serverId, $serverName, $isp]);
        $uploadLatencyHighGauge->set((float) ($data->upload->latency->high ?? 0.0), [$serverId, $serverName, $isp]);
        $uploadLatencyJitterGauge->set((float) ($data->upload->latency->jitter ?? 0.0), [$serverId, $serverName, $isp]);

        $packetLossGauge->set((float) ($data->packetLoss ?? 0.0), [$serverId, $serverName, $isp]);
        $resultIdGauge->set((float) $latestResult->id ?? 0, [$serverId, $serverName, $isp]);

    } else {
        // Set default values for numeric metrics with labels
        $defaultLabels = ['unknown', 'unknown', 'unknown'];

        $pingJitterGauge->set(0.0, $defaultLabels);
        $pingLatencyGauge->set(0.0, $defaultLabels);
        $pingLowGauge->set(0.0, $defaultLabels);
        $pingHighGauge->set(0.0, $defaultLabels);

        $downloadBandwidthGauge->set(0.0, $defaultLabels);
        $downloadBytesGauge->set(0.0, $defaultLabels);
        $downloadElapsedGauge->set(0.0, $defaultLabels);
        $downloadLatencyIqmGauge->set(0.0, $defaultLabels);
        $downloadLatencyLowGauge->set(0.0, $defaultLabels);
        $downloadLatencyHighGauge->set(0.0, $defaultLabels);
        $downloadLatencyJitterGauge->set(0.0, $defaultLabels);

        $uploadBandwidthGauge->set(0.0, $defaultLabels);
        $uploadBytesGauge->set(0.0, $defaultLabels);
        $uploadElapsedGauge->set(0.0, $defaultLabels);
        $uploadLatencyIqmGauge->set(0.0, $defaultLabels);
        $uploadLatencyLowGauge->set(0.0, $defaultLabels);
        $uploadLatencyHighGauge->set(0.0, $defaultLabels);
        $uploadLatencyJitterGauge->set(0.0, $defaultLabels);

        $packetLossGauge->set(0.0, $defaultLabels);
        $resultIdGauge->set(0.0, $defaultLabels);
    }

    // Render metrics
    $renderer = new RenderTextFormat();
    $result = $renderer->render($registry->getMetricFamilySamples());

    return response($result, 200)->header('Content-Type', RenderTextFormat::MIME_TYPE);
});

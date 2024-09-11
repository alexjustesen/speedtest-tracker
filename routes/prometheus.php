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

    // Register gauges for numeric metrics
    $pingJitterGauge = $registry->registerGauge('speedtest_tracker', 'ping_jitter', 'Ping jitter');
    $pingLatencyGauge = $registry->registerGauge('speedtest_tracker', 'ping_latency', 'Ping latency');
    $pingLowGauge = $registry->registerGauge('speedtest_tracker', 'ping_low', 'Ping low value');
    $pingHighGauge = $registry->registerGauge('speedtest_tracker', 'ping_high', 'Ping high value');

    $downloadBandwidthGauge = $registry->registerGauge('speedtest_tracker', 'download_bandwidth', 'Download bandwidth');
    $downloadBytesGauge = $registry->registerGauge('speedtest_tracker', 'download_bytes', 'Download bytes');
    $downloadElapsedGauge = $registry->registerGauge('speedtest_tracker', 'download_elapsed', 'Download elapsed time');
    $downloadLatencyIqmGauge = $registry->registerGauge('speedtest_tracker', 'download_latency_iqm', 'Download latency IQM');
    $downloadLatencyLowGauge = $registry->registerGauge('speedtest_tracker', 'download_latency_low', 'Download latency low value');
    $downloadLatencyHighGauge = $registry->registerGauge('speedtest_tracker', 'download_latency_high', 'Download latency high value');
    $downloadLatencyJitterGauge = $registry->registerGauge('speedtest_tracker', 'download_latency_jitter', 'Download latency jitter');

    $uploadBandwidthGauge = $registry->registerGauge('speedtest_tracker', 'upload_bandwidth', 'Upload bandwidth');
    $uploadBytesGauge = $registry->registerGauge('speedtest_tracker', 'upload_bytes', 'Upload bytes');
    $uploadElapsedGauge = $registry->registerGauge('speedtest_tracker', 'upload_elapsed', 'Upload elapsed time');
    $uploadLatencyIqmGauge = $registry->registerGauge('speedtest_tracker', 'upload_latency_iqm', 'Upload latency IQM');
    $uploadLatencyLowGauge = $registry->registerGauge('speedtest_tracker', 'upload_latency_low', 'Upload latency low value');
    $uploadLatencyHighGauge = $registry->registerGauge('speedtest_tracker', 'upload_latency_high', 'Upload latency high value');
    $uploadLatencyJitterGauge = $registry->registerGauge('speedtest_tracker', 'upload_latency_jitter', 'Upload latency jitter');

    $packetLossGauge = $registry->registerGauge('speedtest_tracker', 'packet_loss', 'Packet loss percentage');
    $resultIdGauge = $registry->registerGauge('speedtest_tracker', 'result_id', 'Result ID');

    // Register gauges with labels for server information
    $serverGauge = $registry->registerGauge('speedtest_tracker', 'server_info', 'Server information', ['type']);

    // Fetch the latest result from the database
    $latestResult = DB::table('results')
        ->orderBy('created_at', 'desc')
        ->first();

    if ($latestResult && $latestResult->data) {
        $data = json_decode($latestResult->data);

        // Set gauge values for numeric metrics
        $resultIdGauge->set($latestResult->id ?? 0);
        $pingJitterGauge->set((float) ($data->ping->jitter ?? 0.0));
        $pingLatencyGauge->set((float) ($data->ping->latency ?? 0.0));
        $pingLowGauge->set((float) ($data->ping->low ?? 0.0));
        $pingHighGauge->set((float) ($data->ping->high ?? 0.0));

        $downloadBandwidthGauge->set((float) ($data->download->bandwidth ?? 0.0));
        $downloadBytesGauge->set((float) ($data->download->bytes ?? 0.0));
        $downloadElapsedGauge->set((float) ($data->download->elapsed ?? 0.0));
        $downloadLatencyIqmGauge->set((float) ($data->download->latency->iqm ?? 0.0));
        $downloadLatencyLowGauge->set((float) ($data->download->latency->low ?? 0.0));
        $downloadLatencyHighGauge->set((float) ($data->download->latency->high ?? 0.0));
        $downloadLatencyJitterGauge->set((float) ($data->download->latency->jitter ?? 0.0));

        $uploadBandwidthGauge->set((float) ($data->upload->bandwidth ?? 0.0));
        $uploadBytesGauge->set((float) ($data->upload->bytes ?? 0.0));
        $uploadElapsedGauge->set((float) ($data->upload->elapsed ?? 0.0));
        $uploadLatencyIqmGauge->set((float) ($data->upload->latency->iqm ?? 0.0));
        $uploadLatencyLowGauge->set((float) ($data->upload->latency->low ?? 0.0));
        $uploadLatencyHighGauge->set((float) ($data->upload->latency->high ?? 0.0));
        $uploadLatencyJitterGauge->set((float) ($data->upload->latency->jitter ?? 0.0));

        $packetLossGauge->set((float) ($data->packetLoss ?? 0.0));

        // Set gauge values for server information using labels
        $serverGauge->set(1, ['type' => 'server_name:'.($data->server->name ?? 'unknown')]);
        $serverGauge->set(1, ['type' => 'server_location:'.($data->server->location ?? 'unknown')]);
        $serverGauge->set(1, ['type' => 'server_country:'.($data->server->country ?? 'unknown')]);
        $serverGauge->set(1, ['type' => 'serverid:'.($data->server->id ?? 'unknown')]);
        $serverGauge->set(1, ['type' => 'isp:'.($data->isp ?? 'unknown')]);
    } else {
        // Set default values for numeric metrics
        $resultIdGauge->set(0);
        $pingJitterGauge->set(0.0);
        $pingLatencyGauge->set(0.0);
        $pingLowGauge->set(0.0);
        $pingHighGauge->set(0.0);

        $downloadBandwidthGauge->set(0.0);
        $downloadBytesGauge->set(0.0);
        $downloadElapsedGauge->set(0.0);
        $downloadLatencyIqmGauge->set(0.0);
        $downloadLatencyLowGauge->set(0.0);
        $downloadLatencyHighGauge->set(0.0);
        $downloadLatencyJitterGauge->set(0.0);

        $uploadBandwidthGauge->set(0.0);
        $uploadBytesGauge->set(0.0);
        $uploadElapsedGauge->set(0.0);
        $uploadLatencyIqmGauge->set(0.0);
        $uploadLatencyLowGauge->set(0.0);
        $uploadLatencyHighGauge->set(0.0);
        $uploadLatencyJitterGauge->set(0.0);

        $packetLossGauge->set(0.0);

        // Set default values for server information with labels
        $serverGauge->set(1, ['type' => 'server_name:unknown']);
        $serverGauge->set(1, ['type' => 'server_location:unknown']);
        $serverGauge->set(1, ['type' => 'server_country:unknown']);
        $serverGauge->set(1, ['type' => 'serverid:unknown']);
        $serverGauge->set(1, ['type' => 'isp:unknown']);
    }

    // Render metrics
    $renderer = new RenderTextFormat();
    $result = $renderer->render($registry->getMetricFamilySamples());

    return response($result, 200)->header('Content-Type', RenderTextFormat::MIME_TYPE);
});

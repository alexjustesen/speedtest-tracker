<?php

namespace App\Services\Prometheus;

use App\Models\Result;

abstract class PrometheusService
{
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

    /**
     * @return array<string, array{help: string, value: float|null}>
     */
    protected function collectMetrics(Result $result): array
    {
        return [
            'download_bytes_per_second' => ['help' => 'Download speed in bytes per second', 'value' => $result->download !== null ? (float) $result->download : null],
            'upload_bytes_per_second' => ['help' => 'Upload speed in bytes per second', 'value' => $result->upload !== null ? (float) $result->upload : null],
            'download_bits_per_second' => ['help' => 'Download speed in bits per second', 'value' => $result->download_bits !== null ? (float) $result->download_bits : null],
            'upload_bits_per_second' => ['help' => 'Upload speed in bits per second', 'value' => $result->upload_bits !== null ? (float) $result->upload_bits : null],
            'ping_ms' => ['help' => 'Ping latency in milliseconds', 'value' => $result->ping !== null ? (float) $result->ping : null],
            'ping_low_ms' => ['help' => 'Ping low latency in milliseconds', 'value' => $result->ping_low !== null ? (float) $result->ping_low : null],
            'ping_high_ms' => ['help' => 'Ping high latency in milliseconds', 'value' => $result->ping_high !== null ? (float) $result->ping_high : null],
            'ping_jitter_ms' => ['help' => 'Ping jitter in milliseconds', 'value' => $result->ping_jitter !== null ? (float) $result->ping_jitter : null],
            'download_jitter_ms' => ['help' => 'Download jitter in milliseconds', 'value' => $result->download_jitter !== null ? (float) $result->download_jitter : null],
            'upload_jitter_ms' => ['help' => 'Upload jitter in milliseconds', 'value' => $result->upload_jitter !== null ? (float) $result->upload_jitter : null],
            'packet_loss_percent' => ['help' => 'Packet loss percentage', 'value' => $result->packet_loss !== null ? (float) $result->packet_loss : null],
            'download_latency_iqm_ms' => ['help' => 'Download latency interquartile mean in milliseconds', 'value' => $result->downloadlatencyiqm !== null ? (float) $result->downloadlatencyiqm : null],
            'download_latency_low_ms' => ['help' => 'Download latency low in milliseconds', 'value' => $result->downloadlatency_low !== null ? (float) $result->downloadlatency_low : null],
            'download_latency_high_ms' => ['help' => 'Download latency high in milliseconds', 'value' => $result->downloadlatency_high !== null ? (float) $result->downloadlatency_high : null],
            'upload_latency_iqm_ms' => ['help' => 'Upload latency interquartile mean in milliseconds', 'value' => $result->uploadlatencyiqm !== null ? (float) $result->uploadlatencyiqm : null],
            'upload_latency_low_ms' => ['help' => 'Upload latency low in milliseconds', 'value' => $result->uploadlatency_low !== null ? (float) $result->uploadlatency_low : null],
            'upload_latency_high_ms' => ['help' => 'Upload latency high in milliseconds', 'value' => $result->uploadlatency_high !== null ? (float) $result->uploadlatency_high : null],
            'test_downloaded_bytes_total' => ['help' => 'Total bytes downloaded during test', 'value' => $result->downloaded_bytes !== null ? (float) $result->downloaded_bytes : null],
            'test_uploaded_bytes_total' => ['help' => 'Total bytes uploaded during test', 'value' => $result->uploaded_bytes !== null ? (float) $result->uploaded_bytes : null],
            'download_elapsed_ms' => ['help' => 'Download test duration in milliseconds', 'value' => $result->download_elapsed !== null ? (float) $result->download_elapsed : null],
            'upload_elapsed_ms' => ['help' => 'Upload test duration in milliseconds', 'value' => $result->upload_elapsed !== null ? (float) $result->upload_elapsed : null],
        ];
    }
}

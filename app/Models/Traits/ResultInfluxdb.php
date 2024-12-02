<?php

namespace App\Models\Traits;

trait ResultInfluxdb
{
/**
     * The tag attributes to be passed to influxdb
     */
    public function formatTagsForInfluxDB2(): array
    {
        return [
            'app_name' => (string) config('app.name'),
            'isp' => $this->isp,
            'server_id' => (int) $this->server_id,
            'server_host' => $this->server_host,
            'server_name' => $this->server_name,
            'healthy' => (bool) $this->healthy,
            'status' => $this->status->value,
            'scheduled' => (bool) $this->scheduled,
        ];
    }

    /**
     * The attributes to be passed to influxdb
     */
    public function formatForInfluxDB2(): array
    {
        return [
            'id' => $this->id,
            'ping' => $this->ping ?? null,
            'download' => $this->download ?? null,
            'upload' => $this->upload ?? null,
            'download_bits' => (float) $this->download_bits,
            'upload_bits' => (float) $this->upload_bits,
            'ping_jitter' => (float) $this->ping_jitter,
            'download_jitter' => (float) $this->download_jitter,
            'upload_jitter' => (float) $this->upload_jitter,
            'download_latency_avg' => (float) $this->download_latency_iqm,
            'download_latency_low' => (float) $this->download_latency_low,
            'download_latency_high' => (float) $this->download_latency_high,
            'upload_latency_avg' => (float) $this->upload_latency_iqm,
            'upload_latency_low' => (float) $this->upload_latency_low,
            'upload_latency_high' => (float) $this->upload_latency_high,
            'packet_loss' => (float) $this->packet_loss,
        ];
    }
}

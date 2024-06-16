<?php

namespace App\Models;

use App\Enums\ResultStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Arr;

class Result extends Model
{
    use HasFactory, Prunable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'status' => ResultStatus::class,
            'scheduled' => 'boolean',
        ];
    }

    /**
     * The tag attributes to be passed to influxdb
     */
    public function formatTagsForInfluxDB2(): array
    {
        return [
            'server_id' => (int) $this->server_id,
            'server_host' => $this->server_host,
            'server_name' => $this->server_name,
        ];
    }

    /**
     * The attributes to be passed to influxdb
     */
    public function formatForInfluxDB2()
    {
        return [
            'id' => $this->id,
            'ping' => $this?->ping,
            'download' => $this?->download,
            'upload' => $this?->upload,
            'download_bits' => $this->download_bits,
            'upload_bits' => $this->upload_bits,
            'ping_jitter' => $this->ping_jitter,
            'download_jitter' => $this->download_jitter,
            'upload_jitter' => $this->upload_jitter,
            'download_latency_avg' => $this->download_latency_iqm,
            'download_latency_low' => $this->download_latency_low,
            'download_latency_high' => $this->download_latency_high,
            'upload_latency_avg' => $this->upload_latency_iqm,
            'upload_latency_low' => $this->upload_latency_low,
            'upload_latency_high' => $this->upload_latency_high,
            'server_id' => $this?->server_id,
            'isp' => $this?->isp,
            'server_host' => $this?->server_host,
            'server_name' => $this?->server_name,
            'server_location' => $this?->server_location,
            'scheduled' => $this->scheduled,
            'successful' => $this->status === ResultStatus::Completed,
            'packet_loss' => (float) $this->packet_loss,
        ];
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(config('speedtest.prune_results_older_than')));
    }

    /**
     * Get the result's download in bits.
     */
    protected function downloadBits(): Attribute
    {
        return Attribute::make(
            get: fn (): ?int => ! blank($this->download) && is_numeric($this->download)
                ? number_format(num: $this->download * 8, decimals: 0, thousands_separator: '')
                : null,
        );
    }

    /**
     * Get the result's download jitter in milliseconds.
     */
    protected function downloadJitter(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'download.latency.jitter'),
        );
    }

    /**
     * Get the result's download latency high in milliseconds.
     */
    protected function downloadlatencyHigh(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'download.latency.high'),
        );
    }

    /**
     * Get the result's download latency low in milliseconds.
     */
    protected function downloadlatencyLow(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'download.latency.low'),
        );
    }

    /**
     * Get the result's download latency iqm in milliseconds.
     */
    protected function downloadlatencyiqm(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'download.latency.iqm'),
        );
    }

    /**
     * Get the result's download jitter in milliseconds.
     */
    protected function errorMessage(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'message', ''),
        );
    }

    /**
     * Get the result's external ip address (yours).
     */
    protected function ipAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'interface.externalIp'),
        );
    }

    /**
     * Get the result's isp tied to the external (yours) ip address.
     */
    protected function isp(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'isp'),
        );
    }

    /**
     * Get the result's packet loss as a percentage.
     */
    protected function packetLoss(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'packetLoss'),
        );
    }

    /**
     * Get the result's ping jitter in milliseconds.
     */
    protected function pingJitter(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'ping.jitter'),
        );
    }

    /**
     * Get the result's server ID.
     */
    protected function resultUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'result.url'),
        );
    }

    /**
     * Get the result's server host.
     */
    protected function serverHost(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'server.host'),
        );
    }

    /**
     * Get the result's server ID.
     */
    protected function serverId(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'server.id'),
        );
    }

    /**
     * Get the result's server name.
     */
    protected function serverName(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'server.name'),
        );
    }

    /**
     * Get the result's server location.
     */
    protected function serverLocation(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'server.location'),
        );
    }

    /**
     * Get the result's upload in bits.
     */
    protected function uploadBits(): Attribute
    {
        return Attribute::make(
            get: fn (): ?int => ! blank($this->upload) && is_numeric($this->upload)
                ? number_format(num: $this->upload * 8, decimals: 0, thousands_separator: '')
                : null,
        );
    }

    /**
     * Get the result's upload jitter in milliseconds.
     */
    protected function uploadJitter(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'upload.latency.jitter'),
        );
    }

    /**
     * Get the result's upload latency high in milliseconds.
     */
    protected function uploadlatencyHigh(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'upload.latency.high'),
        );
    }

    /**
     * Get the result's upload latency low in milliseconds.
     */
    protected function uploadlatencyLow(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'upload.latency.low'),
        );
    }

    /**
     * Get the result's upload latency iqm in milliseconds.
     */
    protected function uploadlatencyiqm(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'upload.latency.iqm'),
        );
    }
}

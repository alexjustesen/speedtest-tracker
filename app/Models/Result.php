<?php

namespace App\Models;

use App\Enums\ResultStatus;
use App\Events\ResultCreated;
use App\Settings\GeneralSettings;
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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'status' => ResultStatus::class,
        'scheduled' => 'boolean',
    ];

    /**
     * Event mapping for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ResultCreated::class,
    ];

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
            'server_id' => $this?->server_id,
            'server_host' => $this?->server_host,
            'server_name' => $this?->server_name,
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
        $settings = new GeneralSettings();

        return static::where('created_at', '<=', now()->subDays($settings->prune_results_older_than));
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
}

<?php

namespace App\Models;

use App\Events\ResultCreated;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Result extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ping',
        'download',
        'upload',
        'server_id',
        'server_host',
        'server_name',
        'url',
        'comments',
        'scheduled',
        'successful',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'scheduled' => 'boolean',
        'successful' => 'boolean',
        'data' => 'array',
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
            'download_bits' => $this->download ? $this->download * 8 : null,
            'upload_bits' => $this->upload ? $this->upload * 8 : null,
            'ping_jitter' => $this->ping_jitter,
            'download_jitter' => $this->download_jitter,
            'upload_jitter' => $this->upload_jitter,
            'server_id' => $this?->server_id,
            'server_host' => $this?->server_host,
            'server_name' => $this?->server_name,
            'scheduled' => $this->scheduled,
            'successful' => $this->successful,
            'packet_loss' => (float) $this->packet_loss,
        ];
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
     * Get the result's ping jitter in milliseconds.
     */
    protected function pingJitter(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'ping.jitter'),
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
     * Get the result's upload jitter in milliseconds.
     */
    protected function uploadJitter(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'upload.latency.jitter'),
        );
    }
}

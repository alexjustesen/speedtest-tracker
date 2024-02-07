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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
        'created_at' => 'datetime',
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
        $data = json_decode($this->data, true);

        return [
            'id' => $this->id,
            'ping' => $this?->ping,
            'download' => $this?->download,
            'upload' => $this?->upload,
            'download_bits' => $this->download ? $this->download * 8 : null,
            'upload_bits' => $this->upload ? $this->upload * 8 : null,
            'ping_jitter' => Arr::get($data, 'ping.jitter'),
            'download_jitter' => Arr::get($data, 'download.latency.jitter'),
            'upload_jitter' => Arr::get($data, 'upload.latency.jitter'),
            'server_id' => $this?->server_id,
            'server_host' => $this?->server_host,
            'server_name' => $this?->server_name,
            'scheduled' => $this->scheduled,
            'successful' => $this->successful,
            'packet_loss' => (float) Arr::get($data, 'packetLoss', 0),
        ];
    }

    public function getJitterData(): array
    {
        $data = json_decode($this->data, true);

        return [
            'download' => Arr::get($data, 'download.latency.jitter'),
            'upload' => Arr::get($data, 'upload.latency.jitter'),
            'ping' => Arr::get($data, 'ping.jitter'),
        ];
    }

    /**
     * Get the result's download in bits.
     */
    protected function downloadBits(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value): ?string => ! blank($this->download) && is_numeric($this->download)
                ? number_format(num: $this->download * 8, decimals: 0, thousands_separator: '')
                : null,
        );
    }

    /**
     * Get the result's upload in bits.
     */
    protected function uploadBits(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value): ?string => ! blank($this->upload) && is_numeric($this->upload)
                ? number_format(num: $this->upload * 8, decimals: 0, thousands_separator: '')
                : null,
        );
    }
}

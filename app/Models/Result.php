<?php

namespace App\Models;

use App\Events\ResultCreated;
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

        // New hotness
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
            'packet_loss'=> Arr::get($data, 'packetLoss'),
        ];
    }

    public function getJitterData(): array
    {
        $data = json_decode($this->data, true);

        return [
            'download' => $data['download']['latency']['jitter'] ?? null,
            'upload' => $data['upload']['latency']['jitter'] ?? null,
            'ping' => $data['ping']['jitter'] ?? null,
        ];
    }

    /**
     * Return the previous test result.
     */
    public function previous(): ?self
    {
        return static::orderByDesc('id')
            ->where('id', '<', $this->id)
            ->first();
    }
}

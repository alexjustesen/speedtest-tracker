<?php

namespace App\Models;

use App\Events\ResultCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            'id' => (int) $this->id,
            'ping' => (float) $this->ping,
            'download' => (int) $this->download,
            'upload' => (int) $this->upload,
            'download_bits' => (int) $this->download * 8,
            'upload_bits' => (int) $this->upload * 8,
            'ping_jitter' => (float) $data['ping']['jitter'] ?? null,
            'download_jitter' => (float) $data['download']['latency']['jitter'] ?? null,
            'upload_jitter' => (float) $data['upload']['latency']['jitter'] ?? null,
            'server_id' => (int) $this->server_id,
            'server_host' => $this->server_host,
            'server_name' => $this->server_name,
            'scheduled' => $this->scheduled,
            'packet_loss' => (float) $data['packetLoss'] ?? null, // optional, because apparently the cli doesn't always have this metric
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
     *
     * @return  self|null
     */
    public function previous()
    {
        return static::orderBy('id', 'desc')
            ->where('id', '<', $this->id)
            ->first();
    }
}

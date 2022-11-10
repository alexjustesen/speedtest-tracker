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
        'scheduled',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'scheduled' => 'boolean',
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
     * The attributes to be passed to influxdb
     */
    public function formatForInfluxDB2()
    {
        return [
            'id' => (int) $this->id,
            'ping' => (float) $this->ping,
            'download' => (int) $this->download,
            'upload' => (int) $this->upload,
            'download_bits' => (int) $this->download * 8,
            'upload_bits' => (int) $this->upload * 8,
            'ping_jitter' => (float) $this->data['ping']['jitter'],
            'download_jitter' => (float) $this->data['download']['latency']['jitter'],
            'upload_jitter' => (float) $this->data['upload']['latency']['jitter'],
            'server_id' => (int) $this->server_id,
            'server_host' => $this->server_host,
            'server_name' => $this->server_name,
            'scheduled' => $this->scheduled,
            'packet_loss' => $this->data['packetLoss'],
        ];
    }
}

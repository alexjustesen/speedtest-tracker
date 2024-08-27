<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PingResult extends Model
{
    protected $fillable = [
        'url',
        'min_latency',
        'avg_latency',
        'max_latency',
        'packet_loss',
        'ping_count',
    ];
}

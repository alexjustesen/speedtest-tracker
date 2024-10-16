<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LatencyResult extends Model
{
    protected $fillable = [
        'target_url',
        'target_name',
        'min_latency',
        'avg_latency',
        'max_latency',
        'packet_loss',
        'ping_count',
    ];
}

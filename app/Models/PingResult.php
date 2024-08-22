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

    // You can also specify the table if it does not follow Laravel's naming conventions
    // protected $table = 'ping_results';
}

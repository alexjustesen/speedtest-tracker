<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ping extends Model
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
        'http_code',
        'total_time',
        'data',
        'is_successful',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_scheduled' => 'boolean',
        'data' => 'array',
        'created_at' => 'datetime',
    ];

    public function address()
    {
        return $this->belongsTo(PingAddress::class, 'address_id');
    }
}

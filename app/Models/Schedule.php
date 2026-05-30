<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'enabled',
        'schedule',
        'servers',
        'blocked_servers',
        'interface',
        'skip_ips',
    ];

    public function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'servers' => 'array',
            'blocked_servers' => 'array',
            'skip_ips' => 'array',
        ];
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }
}

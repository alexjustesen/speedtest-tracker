<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataIntegrationSetting extends Model
{
    protected $fillable = [
        'name',
        'type',
        'config',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'config' => 'array',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataIntegration extends Model
{
    protected $fillable = [
        'name',
        'enabled',
        'type',
        'config',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'config' => 'array',
    ];
}

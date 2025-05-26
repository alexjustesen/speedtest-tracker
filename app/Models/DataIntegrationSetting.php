<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataIntegrationSetting extends Model
{
    protected $fillable = [
        'name',
        'type',
        'enabled',
        'url',
        'org',
        'bucket',
        'token',
        'verify_ssl',
    ];
}

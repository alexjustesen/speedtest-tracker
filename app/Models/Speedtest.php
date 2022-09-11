<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speedtest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'schedule',
        'ookla_server_id',
        'next_run_at',
    ];

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}

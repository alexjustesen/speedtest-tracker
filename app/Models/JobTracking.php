<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobTracking extends Model
{

    protected $fillable = ['tracking_key', 'status', 'result_id'];

}

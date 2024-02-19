<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class JobTracking extends Model
{
    use Prunable;

    protected $fillable = ['tracking_key', 'status', 'result_id'];

    public function prunable(): Builder
    {
        return static::where('created_at', '<', Carbon::now()->subDays(30))
            ->orWhere('status', JobTrackingStatusEnum::Failed);
    }
}

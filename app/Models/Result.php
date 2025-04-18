<?php

namespace App\Models;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Models\Traits\ResultDataAttributes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class Result extends Model
{
    use HasFactory, Prunable, ResultDataAttributes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'benchmarks' => 'array',
            'data' => 'array',
            'healthy' => 'boolean',
            'scheduled' => 'boolean',
            'service' => ResultService::class,
            'status' => ResultStatus::class,
        ];
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(config('speedtest.prune_results_older_than')));
    }
}

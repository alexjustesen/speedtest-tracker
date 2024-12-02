<?php

namespace App\Models;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Models\Traits\ResultDataAttributes;
use App\Models\Traits\ResultInfluxdb;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class Result extends Model
{
    use HasFactory, Prunable, ResultDataAttributes, ResultInfluxdb;

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
            'service' => ResultService::class,
            'status' => ResultStatus::class,
            'scheduled' => 'boolean',
        ];
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(config('speedtest.prune_results_older_than')));
    }
}

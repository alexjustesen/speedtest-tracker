<?php

namespace App\Models;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Models\Traits\ResultDataAttributes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(config('speedtest.prune_results_older_than')));
    }

    /**
     * Get the user who dispatched this speedtest.
     */
    public function dispatchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    /**
     * Determine if the result was unscheduled.
     */
    protected function unscheduled(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => ! $this->scheduled,
        );
    }
}

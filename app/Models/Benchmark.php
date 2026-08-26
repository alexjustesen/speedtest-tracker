<?php

namespace App\Models;

use App\Enums\BenchmarkMetric;
use App\Enums\BenchmarkState;
use App\Enums\BenchmarkType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benchmark extends Model
{
    use HasFactory;

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
            'metric' => BenchmarkMetric::class,
            'type' => BenchmarkType::class,
            'state' => BenchmarkState::class,
            'absolute_value' => 'float',
            'baseline_value' => 'float',
            'relative_percentage' => 'float',
            'consecutive_breaches' => 'integer',
            'repeat_while_in_alarm' => 'boolean',
            'enabled' => 'boolean',
            'state_changed_at' => 'datetime',
        ];
    }

    /**
     * Clear whichever value columns don't apply to the current type, so
     * switching between absolute and relative doesn't leave stale values
     * behind that could resurface if the type is switched back later.
     */
    protected static function booted(): void
    {
        static::saving(function (self $benchmark): void {
            if ($benchmark->type === BenchmarkType::Absolute) {
                $benchmark->baseline_value = null;
                $benchmark->relative_percentage = null;
            } else {
                $benchmark->absolute_value = null;
            }
        });
    }

    /**
     * Scope a query to only include enabled benchmarks.
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    /**
     * Determine if the benchmark is currently in an alarm state.
     */
    public function isInAlarm(): bool
    {
        return $this->state === BenchmarkState::Alarm;
    }

    /**
     * Get the result's current value for this benchmark's metric.
     */
    public function currentValue(Result $result): float|int|null
    {
        return $this->metric->valueFor($result);
    }

    /**
     * Get the effective value this benchmark's metric is compared against.
     */
    public function benchmarkValue(): ?float
    {
        if ($this->type === BenchmarkType::Absolute) {
            return $this->absolute_value;
        }

        if (blank($this->baseline_value) || blank($this->relative_percentage)) {
            return null;
        }

        return round($this->baseline_value * $this->relative_percentage / 100, 2);
    }

    /**
     * Validate if the result passes this benchmark.
     */
    public function passes(Result $result): bool
    {
        $value = $this->currentValue($result);

        $benchmark = $this->benchmarkValue();

        // Pass the benchmark if there's no data to compare.
        if (blank($value) || blank($benchmark)) {
            return true;
        }

        return $this->metric->direction() === 'min'
            ? $value >= $benchmark
            : $value < $benchmark;
    }
}

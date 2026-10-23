<?php

namespace App\Models;

use App\Actions\Schedules\ResolveScheduleServerLabels;
use App\Enums\ServerMode;
use App\Schedules\CronSchedule;
use DirectoryTree\Cadence\HasSchedules;
use DirectoryTree\Cadence\Schedulable;
use DirectoryTree\Cadence\Schedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Speedtest extends Model implements Schedulable
{
    /** @use HasFactory<\Database\Factories\SpeedtestFactory> */
    use HasFactory, HasSchedules;

    protected $fillable = [
        'name',
        'server_mode',
        'servers',
        'server_labels',
    ];

    protected $attributes = [
        'server_mode' => 'auto',
    ];

    protected static function booted(): void
    {
        static::saving(function (Speedtest $speedtest) {
            if ($speedtest->isDirty('servers')) {
                $speedtest->server_labels = ResolveScheduleServerLabels::run(
                    servers: $speedtest->servers ?? [],
                    existingLabels: $speedtest->getOriginal('server_labels') ?? [],
                );
            }
        });

        static::deleting(function (Speedtest $speedtest) {
            $speedtest->schedules()->delete();
        });
    }

    public function casts(): array
    {
        return [
            'server_mode' => ServerMode::class,
            'servers' => 'array',
            'server_labels' => 'array',
        ];
    }

    /**
     * Get the cron schedule that triggers this speedtest.
     */
    public function schedule(): MorphOne
    {
        return $this->morphOne(Schedule::class, 'schedulable');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->whereRelation('schedule', 'disabled_at', null);
    }

    public function scopeDisabled(Builder $query): Builder
    {
        return $query->whereRelation('schedule', 'disabled_at', '!=', null);
    }

    /**
     * Get the configured servers keyed by ID with their stored label, falling back to the ID.
     *
     * @return array<string, string>
     */
    public function serverLabels(): array
    {
        $labels = $this->server_labels ?? [];

        return collect($this->servers ?? [])
            ->mapWithKeys(fn ($id) => [$id => $labels[$id] ?? (string) $id])
            ->all();
    }

    /**
     * Build the cron schedule attributes for the given expression and enabled state.
     *
     * @return array<string, mixed>
     */
    public static function cronScheduleAttributes(string $expression, bool $enabled): array
    {
        $timezone = config('app.display_timezone');

        return [
            'type' => 'cron',
            'expression' => $expression,
            'timezone' => $timezone,
            'disabled_at' => $enabled ? null : now(),
            'next_run_at' => $enabled ? (new CronSchedule($expression, $timezone))->getNextOccurrence(now()) : null,
        ];
    }
}

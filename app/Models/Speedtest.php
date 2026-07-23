<?php

namespace App\Models;

use DirectoryTree\Cadence\Drivers\CronSchedule;
use DirectoryTree\Cadence\HasSchedules;
use DirectoryTree\Cadence\Schedulable;
use DirectoryTree\Cadence\Schedule as CadenceSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speedtest extends Model implements Schedulable
{
    /** @use HasFactory<\Database\Factories\SpeedtestFactory> */
    use HasFactory, HasSchedules;

    protected $fillable = [
        'name',
        'servers',
        'blocked_servers',
        'server_labels',
        'interface',
        'skip_ips',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Speedtest $speedtest) {
            $speedtest->schedules()->delete();
        });
    }

    public function casts(): array
    {
        return [
            'servers' => 'array',
            'blocked_servers' => 'array',
            'server_labels' => 'array',
            'skip_ips' => 'array',
        ];
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->whereHas('schedules', fn (Builder $q) => $q->whereNull('disabled_at'));
    }

    public function scopeDisabled(Builder $query): Builder
    {
        return $query->whereHas('schedules', fn (Builder $q) => $q->whereNotNull('disabled_at'));
    }

    /**
     * Get the single cron schedule attached to this speedtest.
     */
    public function cronSchedule(): ?CadenceSchedule
    {
        return $this->schedules->first();
    }

    protected function cronExpression(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->cronSchedule()?->expression,
        );
    }

    protected function isEnabled(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->cronSchedule()?->isEnabled() ?? false,
        );
    }

    protected function nextRunAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cronSchedule()?->next_run_at,
        );
    }

    protected function lastRunAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cronSchedule()?->last_run_at,
        );
    }

    /**
     * Create or update the attached cron schedule and set its enabled state.
     */
    public function syncCronSchedule(string $expression, bool $enabled, ?string $timezone = null): void
    {
        $schedule = $this->cronSchedule();

        if ($schedule) {
            $schedule->fill([
                'expression' => $expression,
                'timezone' => $timezone,
            ])->save();
        } else {
            $schedule = $this->addSchedule(new CronSchedule($expression, $timezone));
        }

        $enabled ? $schedule->enable() : $schedule->disable();
    }
}

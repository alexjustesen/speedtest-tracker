<?php

namespace App\Models;

use App\Enums\ScheduleStatus;
use App\Observers\ScheduleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([ScheduleObserver::class])]

class Schedule extends Model
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
            'options' => 'array',
            'is_active' => 'boolean',
            'status' => ScheduleStatus::class,
            'next_run_at' => 'datetime',
            'last_run_at' => 'datetime',
        ];
    }

    /**
     * Get the user who created this schedule.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getServerTooltip(): ?string
    {
        $preference = $this->options['server_preference'] ?? 'auto';

        if ($preference === 'auto') {
            return null;
        }

        $servers = collect($this->options['servers'] ?? [])
            ->pluck('server_id');

        return $servers
            ->map(fn ($id) => self::getServerLabel($id))
            ->implode(', ');
    }
}

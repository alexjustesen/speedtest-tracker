<?php

namespace App\Models;

use App\Actions\GetOoklaSpeedtestServers;
use App\Observers\ScheduleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            'next_run_at' => 'datetime',
            'thresholds' => 'array',
        ];
    }

    public function getServerTooltip(): ?string
    {
        $preference = $this->options['server_preference'] ?? 'auto';

        if ($preference === 'auto') {
            return null;
        }

        $servers = collect($this->options['servers'] ?? [])
            ->pluck('server_id');

        $lookup = GetOoklaSpeedtestServers::run();

        return $servers
            ->map(fn ($id) => $lookup[$id] ?? "Unknown ($id)")
            ->implode(', ');
    }

    public function getThresholdTooltip(): ?string
    {
        $thresholds = $this->thresholds;

        if (! ($thresholds['enabled'] ?? false)) {
            return null;
        }

        return sprintf(
            "Download: %s Mbps\nUpload: %s Mbps\nPing: %s ms",
            $thresholds['download'] ?? '—',
            $thresholds['upload'] ?? '—',
            $thresholds['ping'] ?? '—',
        );
    }
}

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
        ];
    }

    public static function getServerLabel(string|int $serverId): string
    {
        $lookup = GetOoklaSpeedtestServers::run();

        return $lookup[$serverId] ?? "Server ID: {$serverId}";
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

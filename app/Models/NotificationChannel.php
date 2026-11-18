<?php

namespace App\Models;

use App\Enums\NotificationChannelType;
use App\Enums\NotificationEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class NotificationChannel extends Model
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
            'type' => NotificationChannelType::class,
            'enabled' => 'boolean',
            'config' => 'encrypted:array',
            'events' => 'array',
            'include_unscheduled' => 'boolean',
            'last_sent_at' => 'datetime',
            'last_failed_at' => 'datetime',
        ];
    }

    /**
     * Scope a query to only include enabled channels.
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope a query to only include channels subscribed to the given event.
     */
    public function scopeSubscribedTo(Builder $query, NotificationEvent $event): Builder
    {
        return $query->whereJsonContains('events', $event->value);
    }

    /**
     * Determine if the channel subscribes to the given event.
     */
    public function subscribesTo(NotificationEvent $event): bool
    {
        return in_array($event->value, $this->events ?? [], strict: true);
    }

    /**
     * Get a config value using "dot" notation.
     */
    public function config(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->config ?? [], $key, $default);
    }

    /**
     * Determine if the most recent delivery attempt failed.
     */
    public function isFailing(): bool
    {
        return $this->last_failed_at !== null
            && ($this->last_sent_at === null || $this->last_failed_at->isAfter($this->last_sent_at));
    }

    /**
     * Record a successful delivery.
     */
    public function markAsSent(): void
    {
        $this->forceFill([
            'last_sent_at' => now(),
            'last_error' => null,
        ])->saveQuietly();
    }

    /**
     * Record a failed delivery.
     */
    public function markAsFailed(string $error): void
    {
        $this->forceFill([
            'last_failed_at' => now(),
            'last_error' => str($error)->limit(1000)->toString(),
        ])->saveQuietly();
    }
}

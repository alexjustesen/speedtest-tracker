<?php

/**
 * This trait is inspired by a simplified version of https://github.com/cybercog/laravel-ownership
 * which also avoid the errors seen when using Laravel strict models.
 *
 * This simplified trait also assumes only the `User` model is an owner.
 */

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasOwner
{
    /**
     * Owner of the model.
     */
    public function ownedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owned_by_id');
    }

    /**
     * Owner of the model, alias for `ownedBy()` method.
     */
    public function owner(): BelongsTo
    {
        return $this->ownedBy();
    }

    /**
     * Determines if the model has an owner.
     */
    public function hasOwner(): bool
    {
        return ! is_null($this->owned_by_id);
    }

    /**
     * Checks if the model is owned by the given user.
     */
    public function isOwnedBy(User $owner): bool
    {
        if (! $this->hasOwner()) {
            return false;
        }

        return $owner->id === $this->owned_by_id;
    }

    /**
     * Scope a query to only include models by the owner.
     */
    public function scopeWhereOwnedBy(Builder $query, User $owner): Builder
    {
        return $query->where('owned_by_id', '=', $owner->id);
    }

    /**
     * Scope a query to exclude models by owner.
     */
    public function scopeWhereNotOwnedBy(Builder $query, User $owner): Builder
    {
        return $query->where('owned_by_id', '!=', $owner->id);
    }
}

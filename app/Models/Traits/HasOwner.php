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
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Creator of the model.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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
        return ! is_null($this->created_by);
    }

    /**
     * Checks if the model is owned by the given user.
     */
    public function isOwnedBy(User $owner): bool
    {
        if (! $this->hasOwner()) {
            return false;
        }

        return $owner->id === $this->created_by;
    }

    /**
     * Scope a query to only include models by the owner.
     */
    public function scopeWhereOwnedBy(Builder $query, User $owner): Builder
    {
        return $query->where('created_by', '=', $owner->id);
    }

    /**
     * Scope a query to exclude models by owner.
     */
    public function scopeWhereNotOwnedBy(Builder $query, User $owner): Builder
    {
        return $query->where('created_by', '!=', $owner->id);
    }
}

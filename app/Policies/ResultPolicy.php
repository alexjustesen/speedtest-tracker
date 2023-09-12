<?php

namespace App\Policies;

use App\Models\Result;
use App\Models\User;

class ResultPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Result $result): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Result $result): bool
    {
        return $user->is_admin
            || $user->is_user;
    }

    /**
     * Determine whether the user can bulk delete any model.
     */
    public function deleteAny(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Result $result): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Result $result): bool
    {
        return false; // soft deletes not used
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Result $result): bool
    {
        return false; // soft deletes not used
    }
}

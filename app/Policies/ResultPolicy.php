<?php

namespace App\Policies;

use App\Models\Result;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ResultPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Result $result): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return Response::deny();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Result $result): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can bulk delete any model.
     */
    public function deleteAny(User $user): Response
    {
        return $user->is_admin
            ? Response::allow()
            : Response::deny('You do not have permission to delete results.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Result $result): Response
    {
        return $user->is_admin
            ? Response::allow()
            : Response::deny('You do not have permission to delete this result.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Result $result): Response
    {
        return Response::deny(); // soft deletes not used
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Result $result): Response
    {
        return Response::deny(); // soft deletes not used
    }
}

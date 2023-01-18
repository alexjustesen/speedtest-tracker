<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FailedJobPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return config('app.debug');
    }
}

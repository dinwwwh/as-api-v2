<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether user can read email of model
     *
     */
    public function readEmail(User $user, User $model): bool
    {
        if ($user->is($model)) return true;

        return false;
    }

    /**
     * Determine whether user can read email of model
     *
     */
    public function readBalance(User $user, User $model): bool
    {
        if ($user->is($model)) return true;

        return false;
    }
}

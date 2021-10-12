<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether user can manage users
     *
     */
    public function manage(User $user): bool
    {
        return $user->hasPermission('manage_user');
    }

    /**
     * Determine whether user can read email of model
     *
     */
    public function readEmail(User $user, User $model): bool
    {
        if ($user->is($model)) return true;
        if ($this->manage($user)) return true;
        if ($this->update($user, $model)) return true;

        return false;
    }

    /**
     * Determine whether user can read email of model
     *
     */
    public function readBalance(User $user, User $model): bool
    {
        if ($user->is($model)) return true;
        if ($this->manage($user)) return true;
        if ($this->update($user, $model)) return true;

        return false;
    }

    /**
     * Determine whether user can update $user
     *
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasPermission('update_user');
    }
}

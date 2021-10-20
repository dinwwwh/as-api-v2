<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Validator;
use Illuminate\Auth\Access\HandlesAuthorization;

class ValidatorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether user can manage validators
     *
     */
    public function manage(User $user): bool
    {
        return $user->hasPermission('manage_validator');
    }

    /**
     * Determine whether user can create validator
     *
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_validator');
    }

    /**
     * Determine whether user can update a validator
     *
     */
    public function update(User $user, Validator $validator): bool
    {
        if (!$user->hasPermission('update_validator')) return false;

        return $validator->creator_id == $user->getKey()
            || $this->manage($user);
    }
}

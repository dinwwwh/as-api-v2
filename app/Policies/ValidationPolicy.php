<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Validation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ValidationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether user can manage validation
     *
     */
    public function manage(User $user): bool
    {
        return $user->hasPermission('manage_validation');
    }

    /**
     * Determine whether user can start approving a validation
     *
     */
    public function startApproving(User $user, Validation $validation): bool
    {
        if ($validation->approver_id)
            return false;

        return $validation
            ->validatorable
            ->validator
            ->hasUser($user);
    }

    /**
     * Determine whether user can end approving a validation
     *
     */
    public function endApproving(User $user, Validation $validation): bool
    {
        if (
            !$validation
                ->validatorable
                ->validator
                ->hasUser($user)
            ||
            !$validation->is_approving
        )
            return false;

        return $validation->approver_id == $user->getKey()
            || $this->manage($user);
    }

    /**
     * Determine whether user can read validationable infos
     *
     */
    public function readValidationableInfos(User $user, Validation $validation): bool
    {
        return $this->endApproving($user, $validation);
    }
}

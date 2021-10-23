<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\User;
use App\Models\Validator;
use App\Models\Validatorable;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccountType  $accountType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, AccountType $accountType)
    {
        //
    }

    /**
     * Determine whether user can manage account types
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function manage(User $user)
    {
        return $user->hasPermission('manage_account_type');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_account_type');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccountType  $accountType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, AccountType $accountType)
    {
        if (!$user->hasPermission('update_account_type')) return false;

        return $user->getKey() === $accountType->creator_id
            || $this->manage($user);
    }

    /**
     * Determine whether create new validatorable relation
     *
     */
    public function createValidatorable(User $user, AccountType $accountType, Validator $validator)
    {
        return $this->update($user, $accountType);
    }

    /**
     * Determine whether delete validatorable relation
     *
     */
    public function deleteValidatorable(User $user, AccountType $accountType, Validatorable $validatorable)
    {
        return $validatorable->parent_id == $accountType->getKey() &&
            $validatorable->parent_type == $accountType->getMorphClass() &&
            $this->update($user, $accountType);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccountType  $accountType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, AccountType $accountType)
    {
        if (!$user->hasPermission('delete_account_type')) return false;

        return $user->getKey() === $accountType->creator_id
            || $this->manage($user);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccountType  $accountType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, AccountType $accountType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccountType  $accountType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, AccountType $accountType)
    {
        //
    }
}

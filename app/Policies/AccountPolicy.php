<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Account $account)
    {
        //
    }

    /**
     * Determine whether user can read cost property of model
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function readCost(User $user, Account $account)
    {
        return $user->getKey() == $account->creator_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, AccountType $accountType)
    {
        return $accountType->hasUser($user);
    }

    /**
     * Determine whether the user can buy account.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function buy(User $user, Account $account)
    {
        return is_null($account->bought_at) && $user->balance >= $account->price;
    }

    /**
     * Determine whether the user can confirm bought account is oke or not.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function confirm(User $user, Account $account)
    {
        return $user->getKey() == $account->buyer_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Account $account)
    {
        if (!is_null($account->bought_at)) return false;

        if ($account->creator_id == $user->getKey()) return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Account $account)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Account $account)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Account $account)
    {
        //
    }
}

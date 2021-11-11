<?php

namespace App\Policies\Pivot;

use App\Models\Account;
use App\Models\AccountInfo;
use App\Models\Pivot\AccountAccountInfo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Collection;

class AccountAccountInfoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether user can read value property
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function readValue(User $user, AccountAccountInfo $pivot)
    {
        $account = $pivot->account;
        $accountInfo = $pivot->accountInfo;

        if (
            $account->isSelling()
            && $user->getKey() == $account->creator_id
            && $accountInfo->can_creator
        ) return true;

        if (
            $account->isBought()
            && $user->getKey() == $account->buyer_id
            && $accountInfo->can_buyer
        ) return true;

        if (
            $account->isBoughtOke()
            && $user->getKey() == $account->buyer_id
            && $accountInfo->can_buyer_oke
        ) return true;

        if ($user->can('approve', $account)) return true;

        if (
            $user->getKey() == $account->creator_id
            && $account->refunded_at
        ) return true;

        return false;
    }
}

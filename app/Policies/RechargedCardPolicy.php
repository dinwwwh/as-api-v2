<?php

namespace App\Policies;

use App\Models\RechargedCard;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

use function PHPUnit\Framework\isNull;

class RechargedCardPolicy
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
     * @param  \App\Models\RechargedCard  $rechargedCard
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, RechargedCard $rechargedCard)
    {
        //
    }

    /**
     * Determine whether user can read code attribute of model
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function readCode(User $user, RechargedCard $rechargedCard)
    {
        if ($user->getKey() == $rechargedCard->creator_id) return true;

        if (
            $user->getKey() == $rechargedCard->approver_id
            && is_null($rechargedCard->received_value)
            && is_null($rechargedCard->real_face_value)
        ) return true;

        if ($user->hasPermission('manage_recharged_card')) return true;

        return false;
    }

    /**
     * Determine whether user can start approving
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function startApproving(User $user, RechargedCard $rechargedCard)
    {
        if (
            !is_null($rechargedCard->approver_id)
            || !is_null($rechargedCard->real_face_value)
            || !is_null($rechargedCard->received_value)
        ) return false;

        if ($user->hasPermission('approve_recharged_card')) return true;

        return false;
    }

    /**
     * Determine whether user can end approving
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function endApproving(User $user, RechargedCard $rechargedCard)
    {
        if (
            is_null($rechargedCard->approver_id)
            || !is_null($rechargedCard->real_face_value)
            || !is_null($rechargedCard->received_value)
            || !$user->hasPermission('approve_recharged_card')
        ) return false;

        if ($rechargedCard->approver_id == $user->getKey()) return true;

        if ($user->hasPermission('manage_recharged_card')) return true;

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RechargedCard  $rechargedCard
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, RechargedCard $rechargedCard)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RechargedCard  $rechargedCard
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, RechargedCard $rechargedCard)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RechargedCard  $rechargedCard
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, RechargedCard $rechargedCard)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RechargedCard  $rechargedCard
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, RechargedCard $rechargedCard)
    {
        //
    }
}

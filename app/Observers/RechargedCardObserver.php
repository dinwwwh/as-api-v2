<?php

namespace App\Observers;

use App\Models\RechargedCard;
use App\Services\Thesieure;

class RechargedCardObserver
{
    /**
     * Handle the RechargedCard "created" event.
     *
     * @param  \App\Models\RechargedCard  $rechargedCard
     * @return void
     */
    public function created(RechargedCard $rechargedCard)
    {
        $this->pay($rechargedCard);

        if ($rechargedCard->service == rechargedCard::THESIEURE_SERVICE) {
            Thesieure::rechargeCard($rechargedCard);
        }
    }

    /**
     * Handle the RechargedCard "updated" event.
     *
     * @param  \App\Models\RechargedCard  $rechargedCard
     * @return void
     */
    public function updated(RechargedCard $rechargedCard)
    {
        $this->pay($rechargedCard);
    }

    /**
     * Handle the RechargedCard "deleted" event.
     *
     * @param  \App\Models\RechargedCard  $rechargedCard
     * @return void
     */
    public function deleted(RechargedCard $rechargedCard)
    {
        //
    }

    /**
     * Handle the RechargedCard "restored" event.
     *
     * @param  \App\Models\RechargedCard  $rechargedCard
     * @return void
     */
    public function restored(RechargedCard $rechargedCard)
    {
        //
    }

    /**
     * Handle the RechargedCard "force deleted" event.
     *
     * @param  \App\Models\RechargedCard  $rechargedCard
     * @return void
     */
    public function forceDeleted(RechargedCard $rechargedCard)
    {
        //
    }

    /**
     * Auto pay money for user.
     *
     */
    public function pay(RechargedCard $rechargedCard): void
    {
        if (
            is_null($rechargedCard->real_face_value)
            || is_null($rechargedCard->approver_id)
            || is_null($rechargedCard->received_value)
            || !is_null($rechargedCard->paid_at)
        ) return;

        $rechargedCard->creator?->updateBalance($rechargedCard->received_value, "Nạp thẻ với serial {$rechargedCard->serial} thành công.");
        $rechargedCard->update([
            'paid_at' => now(),
        ]);
    }
}

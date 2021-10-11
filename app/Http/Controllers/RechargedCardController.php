<?php

namespace App\Http\Controllers;

use App\Http\Requests\RechargedCard\EndApprovingRequest;
use App\Http\Requests\RechargedCard\RechargeRequest;
use App\Http\Requests\RechargedCard\StartApprovingRequest;
use App\Http\Resources\RechargedCardResource;
use App\Models\RechargedCard;
use DB;
use Illuminate\Http\Request;

class RechargedCardController extends Controller
{

    /**
     * Get recharged cards
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->_perPage) {
            $rechargedCards = RechargedCard::orderBy('id', 'desc')
                ->paginate($request->_perPage);
        } else {
            $rechargedCards = RechargedCard::orderBy('id', 'desc')
                ->get();
        }

        return RechargedCardResource::withLoad($rechargedCards);
    }

    /**
     * Get pending approver recharged cards
     *
     * @return \Illuminate\Http\Response
     */
    public function getPending(Request $request)
    {
        if ($request->_perPage) {
            $rechargedCards = RechargedCard::whereNull('approver_id')
                ->whereNull('real_face_value')
                ->whereNull('received_value')
                ->paginate($request->_perPage);
        } else {
            $rechargedCards = RechargedCard::whereNull('approver_id')
                ->whereNull('real_face_value')
                ->whereNull('received_value')
                ->get();
        }

        return RechargedCardResource::withLoad($rechargedCards);
    }

    /**
     * Get approving recharged cards
     *
     * @return \Illuminate\Http\Response
     */
    public function getApproving(Request $request)
    {
        if ($request->_perPage) {
            $rechargedCards = RechargedCard::whereNotNull('approver_id')
                ->whereNull('real_face_value')
                ->whereNull('received_value')
                ->paginate($request->_perPage);
        } else {
            $rechargedCards = RechargedCard::whereNotNull('approver_id')
                ->whereNull('real_face_value')
                ->whereNull('received_value')
                ->get();
        }

        return RechargedCardResource::withLoad($rechargedCards);
    }

    /**
     * Get approving recharged cards by me
     *
     * @return \Illuminate\Http\Response
     */
    public function getApprovingByMe(Request $request)
    {
        if ($request->_perPage) {
            $rechargedCards = RechargedCard::where('approver_id', auth()->user()->getKey())
                ->whereNull('real_face_value')
                ->whereNull('received_value')
                ->paginate($request->_perPage);
        } else {
            $rechargedCards = RechargedCard::where('approver_id', auth()->user()->getKey())
                ->whereNull('real_face_value')
                ->whereNull('received_value')
                ->get();
        }

        return RechargedCardResource::withLoad($rechargedCards);
    }

    /**
     * Handle recharge card.
     *
     * @return \Illuminate\Http\Response
     */
    public function recharge(RechargeRequest $request)
    {
        try {
            DB::beginTransaction();

            $rechargedCard = RechargedCard::create([
                'telco' => $request->telco,
                'face_value' => $request->faceValue,
                'serial' => $request->serial,
                'code' => $request->code,
                'service' => $request->service,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return RechargedCardResource::withLoad($rechargedCard->refresh());
    }

    /**
     * Get recharged card
     *
     * @return \Illuminate\Http\Response
     */
    public function show(RechargedCard $rechargedCard)
    {
        return RechargedCardResource::withLoad($rechargedCard);
    }

    /**
     * Handle start approving.
     *
     * @return \Illuminate\Http\Response
     */
    public function startApproving(StartApprovingRequest $request, RechargedCard $rechargedCard)
    {
        try {
            DB::beginTransaction();
            $rechargedCard->update([
                'approver_id' => auth()->user()->getKey(),
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return RechargedCardResource::withLoad($rechargedCard);
    }

    /**
     * Handle End approving.
     *
     * @return \Illuminate\Http\Response
     */
    public function endApproving(EndApprovingRequest $request, RechargedCard $rechargedCard)
    {
        try {
            DB::beginTransaction();

            $rechargedCard->update([
                'real_face_value' => $request->realFaceValue,
                'received_value' => $request->receivedValue,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return RechargedCardResource::withLoad($rechargedCard);
    }
}

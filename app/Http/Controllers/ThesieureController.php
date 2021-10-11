<?php

namespace App\Http\Controllers;

use App\Http\Requests\CallbackThesieureRequest;
use App\Models\RechargedCard;
use App\Models\User;
use App\Services\Thesieure;
use DB;
use Http;

class ThesieureController extends Controller
{
    /**
     * Handle callback from thesieure service
     *
     */
    public function callback(CallbackThesieureRequest $request)
    {
        $card = RechargedCard::findOrFail($request->request_id);

        abort_if(
            !Thesieure::checkSign($request->callback_sign, $card),
            403,
            'Callback sign is invalid.'
        );

        abort_if(
            $card->approver_id != User::where('login', config('thesieure.user.login'))->first()->getKey(),
            403,
            'Recharged card is handled before.'
        );

        if (!$card->isApproving()) {
            return response()->json([
                'message' => 'Card is approved before.',
            ]);
        }

        try {
            DB::beginTransaction();

            $card->update([
                'real_face_value' => $request->value,
                'received_value' => $request->amount,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'message' => 'Approved card successfully.',
        ]);
    }

    public function getTelcos()
    {
        $resData =  Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->get('https://' . config('thesieure.domain') . '/chargingws/v2/getfee?partner_id=' . config('thesieure.id'))
            ->json();

        $telcos = [];
        foreach ($resData as $item) {
            if (array_filter($telcos, fn ($telco) => $telco['name'] == $item['telco'])) {
                $telcos = array_map(function ($telco) use ($item) {
                    if ($telco['name'] == $item['telco']) {
                        $telco['faceValues'][] = [
                            'value' => $item['value'],
                            'tax' => $item['fees'],
                            'taxForInvalidFaceValue' => $item['penalty'],
                        ];
                    }

                    return $telco;
                }, $telcos);
            } else {
                $telcos[] = [
                    'name' => $item['telco'],
                    'faceValues' => [
                        [
                            'value' => $item['value'],
                            'tax' => $item['fees'],
                            'taxForInvalidFaceValue' => $item['penalty'],
                        ]
                    ],
                ];
            }
        }

        return response()->json([
            'data' => $telcos,
        ]);
    }
}

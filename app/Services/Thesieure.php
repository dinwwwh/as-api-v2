<?php

namespace App\Services;

use App\Models\RechargedCard;
use App\Models\User;
use DB;
use Http;

class Thesieure
{
    public static function rechargeCard(RechargedCard $card)
    {
        try {
            DB::beginTransaction();

            $card->update([
                'approver_id' => User::where('login', config('thesieure.user.login'))
                    ->first()
                    ->getKey(),
            ]);

            $resData =  Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://' . config('thesieure.domain') . '/chargingws/v2', [
                'telco' => $card->telco,
                'amount' => $card->face_value,
                'serial' => $card->serial,
                'code' => $card->code,
                'request_id' => $card->getKey(),
                'partner_id' => config('thesieure.id'),
                'sign' => static::generateSign($card),
                'command' => 'charging',
            ])->json();

            if (in_array($resData['status'], [1, 2])) {
                $card->update([
                    'real_face_value' => $resData['value'],
                    'received_value' =>  $resData['amount'],
                ]);
            } elseif (in_array($resData['status'], [3, 4, 100])) {
                $card->update([
                    'real_face_value' => 0,
                    'received_value' =>  0,
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Generate a sign use to send to thesieure server
     *
     */
    public static function generateSign(RechargedCard $card): string
    {
        return md5(config('thesieure.key') . $card->code . $card->serial);
    }

    /**
     * Check a callback sign of thesieure whether valid
     *
     */
    public static function checkSign(string $sign,  RechargedCard $card): bool
    {
        return $sign == static::generateSign($card);
    }
}

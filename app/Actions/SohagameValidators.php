<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Validation;
use App\Models\Validator;
use App\Services\Sohagame;

class SohagameValidators
{
    public const USERNAME_FIELD = 'tài khoản';
    public const PASSWORD_FIELD = 'mật khẩu';

    /**
     * User behalf this class
     *
     */
    public static function user(): User
    {
        return User::firstOrCreate([
            'login' => 'sohag'
        ], [
            'name' => 'sohag',
            'gender' => 'other',
            'avatar_path' => "https://avatars.dicebear.com/api/male/sohag.svg",
            'email' => 'sohag@gmail.com',
            'password' => 'invalid password',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Validate whether sohagame account is correct
     *
     */
    public static function validateWhetherAccountIsCorrect(Validator $validator, Validation $validation): bool
    {
        $userId = static::user()->getKey();
        $readableValues = $validation->getReadableValues();

        $sohagame = Sohagame::login(
            $readableValues[static::USERNAME_FIELD],
            $readableValues[static::PASSWORD_FIELD],
        );

        if ($sohagame) {
            return $validation->update([
                'is_approving' => false,
                'is_error' => false,
                'approver_id' => $userId,
                'updater_id' =>  $userId,
            ]);
        }

        return $validation->update([
            'is_approving' => false,
            'is_error' => true,
            'description' => 'Tài khoản hoặc mật khẩu không chính xác',
            'approver_id' =>  $userId,
            'updater_id' =>  $userId,
        ]);
    }
}

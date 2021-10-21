<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Http;

class Sohagame
{
    public const APP_ID = '41c42365a47d43a5997335fe82ff6ca3';

    public function __construct(
        private string $username,
        private string $password,
        private string $accessToken,
        private ?Carbon $expiredAccessTokenTime = null,
    ) {
    }

    /**
     * Determine whether username is existed
     *
     */
    public static function checkUsername(string $username): bool
    {
        $resData = Http::asForm()->post(
            'https://soap.soha.vn/api/a/get/auth/checkaccount',
            [
                'email' => $username,
                'lang' => 'vi'
            ]
        )->json();

        return !!$resData['account_exist'];
    }

    /**
     * Login to sohagame
     *
     */
    public static function login(string $username, string $password): ?static
    {
        $resData = Http::asForm()->post(
            'https://soap.soha.vn/api/a/POST/Auth/Login',
            [
                'email' => $username,
                'password' => base64_encode($password),
                'app_id' => static::APP_ID,
                'lang_code' => 'vi',
                'lang' => 'vi'
            ]
        )->json();

        if ($resData['status'] != 'success') return null;

        $accessToken = $resData['access_token'];
        $expiredAccessTokenTime = new Carbon((int)$resData['access_token_expired'], 'Asia/Ho_Chi_Minh');

        return new static(
            username: $username,
            password: $password,
            accessToken: $accessToken,
            expiredAccessTokenTime: $expiredAccessTokenTime,
        );
    }
}

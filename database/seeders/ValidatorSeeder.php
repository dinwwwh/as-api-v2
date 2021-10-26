<?php

namespace Database\Seeders;

use App\Actions\SohagameValidators;
use App\Models\Validator;
use Illuminate\Database\Seeder;

class ValidatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Validator::firstOrCreate([
            'name' => 'Kiểm tra tài khoản, mật khẩu - sohagame'
        ], [
            'description' => 'Mục đích kiểm tra xem tài khoản, mật khẩu sohagame có chính xác không.',
            'approver_description' => 'Bạn vào https://auth.sohagame.vn/ và đăng nhập nếu vào được thì là thành công.',
            'readable_fields' => [SohagameValidators::USERNAME_FIELD, SohagameValidators::PASSWORD_FIELD],
            'updatable_fields' => [],
            'callback' => [SohagameValidators::class, 'validateWhetherAccountIsCorrect'],
        ]);
    }
}

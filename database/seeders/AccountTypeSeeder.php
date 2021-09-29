<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::firstOrCreate(['key' => 'create_account_type'], [
            'name' => 'tạo kiểu tài khoản',
            'description' => 'Quyết định xem người dùng có thể tạo kiểu tài khoản.',
        ]);

        Permission::firstOrCreate(['key' => 'update_account_type'], [
            'name' => 'cập nhật kiểu tài khoản',
            'description' => 'Quyết định xem người dùng có thể cập nhật kiểu tài khoản do chính họ tạo ra.',
        ]);

        Permission::firstOrCreate(['key' => 'delete_account_type'], [
            'name' => 'xoá kiểu tài khoản',
            'description' => 'Quyết định xem người dùng có thể xoá kiểu tài khoản do chính họ tạo ra.',
        ]);

        Permission::firstOrCreate(['key' => 'manage_account_type'], [
            'name' => 'quản lý các kiểu tài khoản',
            'description' => 'Quyết định xem người dùng có thể quản lý các kiểu tài khoản. Cập nhật bất cứ kiểu tài khoản nào (nếu có quyền cập nhật kiểu tài khoản). Xoá bất cứ kiểu tài khoản nào (nếu có quyền xoá kiểu tài khoản).',
        ]);
    }
}

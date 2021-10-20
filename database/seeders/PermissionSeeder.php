<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permission::firstOrCreate([], []);

        Permission::firstOrCreate(['key' => 'manage_user'], [
            'name' => 'quản lý người dùng',
            'description' => 'Quyết định xem người dùng có thể quản lý người dùng.'
        ]);

        Permission::firstOrCreate(['key' => 'update_user'], [
            'name' => 'cập nhật người dùng',
            'description' => 'Quyết định xem người dùng có thể cập nhật thông tin bất cứ người dùng nào.'
        ]);

        Permission::firstOrCreate(['key' => 'create_validator'], [
            'name' => 'tạo kiểm chứng',
            'description' => 'Quyết định xem người dùng có thể tạo kiểm chứng.'
        ]);

        Permission::firstOrCreate(['key' => 'update_validator'], [
            'name' => 'cập nhật kiểm chứng',
            'description' => 'Quyết định xem người dùng có thể cập nhật kiểm chứng.'
        ]);

        Permission::firstOrCreate(['key' => 'manage_validator'], [
            'name' => 'quản lý kiểm chứng',
            'description' => 'Quyết định xem người dùng có thể quản lý kiểm chứng.'
        ]);
    }
}

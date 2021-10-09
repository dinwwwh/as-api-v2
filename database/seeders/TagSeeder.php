<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tag::factory()->count(2)->create();

        Permission::firstOrCreate(['key' => 'create_tag'], [
            'name' => 'tạo nhãn',
            'description' => 'Quyết định xem người dùng có thể tạo nhãn cho hệ thống, với việc thể thết lập một số thuộc tính đặc biệt khác so với việc tạo thông thường của người dùng.'
        ]);

        Permission::firstOrCreate(['key' => 'update_tag'], [
            'name' => 'cập nhật nhãn',
            'description' => 'Quyết định xem người dùng có thể cập nhật các nhãn của hệ thống.'
        ]);

        Permission::firstOrCreate(['key' => 'manage_tag'], [
            'name' => 'quản lý nhãn',
            'description' => 'Quyết định xem người dùng có thể quản lý các nhãn của hệ thống.'
        ]);
    }
}

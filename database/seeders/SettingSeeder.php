<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::firstOrCreate(['key' => 'update_setting'], [
            'name' => 'cập nhật cài đặt hệ thống',
            'description' => 'Quyết định xem người dùng có thể cập nhật các cài đặt của hệ thống.'
        ]);

        Permission::firstOrCreate(['key' => 'manage_setting'], [
            'name' => 'quản lý cài đặt hệ thống',
            'description' => 'Quyết định xem người dùng có thể quản lý các cài đặt của hệ thống.'
        ]);

        Setting::firstOrCreate(['key' => 'app_logo_url'], [
            'description' => 'Đường dẫn logo, thường là hình tròn.',
            'assigned_config_key' => null,
            'value' => "https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg",
            'structure_description' => 'string',
            'public' => true,
            'rules' => ['string'],
        ]);
    }
}

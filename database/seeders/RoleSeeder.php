<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::firstOrCreate(['key' => 'admin'], [
            'name' => 'admin',
            'description' => 'Người quản lý website và nắm giữ tất cả các quyền.',
            'color' => 'red',
        ])
            ->permissions()
            ->sync(Permission::all());
    }
}

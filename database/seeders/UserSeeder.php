<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            ['login' => 'dinhdjj'],
            [
                'name' => 'Lê Định',
                'gender' => 'male',
                'avatar_path' => "https://avatars.dicebear.com/api/male/Lê Định.svg",
                'email' => 'dinhdjj@gmail.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'email_verified_at' => now(),
            ]
        )->roles()->sync(Role::find('admin'));

        User::firstOrCreate(
            ['login' => 'tester'],
            [
                'name' => 'Kiểm thử',
                'gender' => 'female',
                'avatar_path' => "https://avatars.dicebear.com/api/female/Kiểm thử.svg",
                'email' => 'tter@gmail.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'email_verified_at' => now(),
            ]
        );
    }
}

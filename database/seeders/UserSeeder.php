<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'user_name' => 'Super Admin',
            'phone' => '091234567',
            'password' => Hash::make('password'),
            'role' => 's_admin',
            'is_active' => true,
        ]);

        User::create([
            'user_name' => 'Admin',
            'phone' => '0987654321',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'user_name' => 'John Doe',
            'phone' => '091237890',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
        ]);

        User::factory(10)->create();
    }
}

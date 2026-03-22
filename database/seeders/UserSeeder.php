<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $staffRole = Role::where('name', 'staff')->first();
        $userRole = Role::where('name', 'user')->first();

        $hospitals = Hospital::orderBy('id')->take(3)->get();

        # Admin
        User::firstOrCreate(
            ['email' => 'admin@bdms.com'],
            [
                'role_id' => $adminRole->id,
                'user_name' => 'System Admin',
                'password' => 'password',
                'is_active' => true,
            ]
        );

        # Staff
        foreach ($hospitals as $index => $hospital) {
            $staffNumber = $index + 1;

            User::firstOrCreate(
                ['email' => "staff{$staffNumber}@gmail.com"],
                [
                    'role_id' => $staffRole->id,
                    'hospital_id' => $hospital->id,
                    'user_name' => "Staff {$staffNumber}",
                    'password' => 'password123',
                    'is_active' => true,
                ]
            );
        }

        # User
        for ($i = 0; $i < 10; $i++) {
            User::firstOrCreate(
                ['email' => fake()->unique()->safeEmail()],
                [
                    'role_id' => $userRole->id,
                    'hospital_id' => $hospitals->random()->id,
                    'user_name' => fake()->name(),
                    'password' => 'password123',
                    'is_active' => true,
                ]
            );
        }
    }
}

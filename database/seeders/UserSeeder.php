<?php

namespace Database\Seeders;

use App\Enums\BloodGroup;
use App\Enums\Gender;
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
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@bdms.com"],
                [
                    'role_id' => $userRole->id,
                    'hospital_id' => $hospitals->random()->id,
                    'user_name' => "Donor User {$i}",
                    'password' => bcrypt('password123'),
                    'is_active' => true,
                ]
            );

            if ($user->wasRecentlyCreated) {
                $user->donor()->create([
                    'nrc_no' => "12/YAKANA(N)" . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'date_of_birth' => '1995-01-01',
                    'gender' => Gender::values()[0],
                    'blood_group' => BloodGroup::values()[0],
                    'weight' => 60.50,
                    'emergency_contact' => 'Family Member',
                    'emergency_phone' => '0912345678',
                    'address' => 'Yangon, Myanmar',
                    'is_active' => true,
                ]);
            }
        }

        # without donor profile
        for ($i = 10; $i > 5; $i--) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@bdms.com"],
                [
                    'role_id' => $userRole->id,
                    'hospital_id' => $hospitals->random()->id,
                    'user_name' => "Donor User {$i}",
                    'password' => bcrypt('password123'),
                    'is_active' => true,
                ]
            );
        }
    }
}

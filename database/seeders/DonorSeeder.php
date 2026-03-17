<?php

namespace Database\Seeders;

use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class DonorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::find(2);

        if ($user) {
            Donor::create([
                'user_id'            => $user->id,
                'nrc_no'             => '1/BDM(S)123456',
                'date_of_birth'      => '1995-05-15',
                'gender'             => 'male',
                'blood_group'        => 'A+',
                'weight'             => 75.50,
                'last_donation_date' => '2025-10-01',
                'remarks'            => 'Regular donor, very healthy.',
                'emergency_contact'  => 'U Kyaw Kyaw',
                'emergency_phone'    => '09123456789',
                'address'            => 'No. 123, Pyay Road, Yangon',
                'is_active'          => true,
            ]);

            $this->command->info('Donor profile created for ' . $user->name);
        } else {
            $this->command->error('User not found. Check if you seeded Users first!');
        }
    }
}

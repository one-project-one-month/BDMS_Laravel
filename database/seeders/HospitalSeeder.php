<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainHospitals = [
            [
                'name' => 'Yangon General Hospital',
                'address' => 'Bogyoke Aung San Road, Latha Township, Yangon',
                'phone' => '01256112',
                'email' => 'ygh@health.gov.mm',
                'is_active' => true,
                'is_verified' => true,
            ],
            [
                'name' => 'Pun Hlaing Hospital',
                'address' => 'Pun Hlaing Golf Estate Avenue, Hlaing Tharyar, Yangon',
                'phone' => '013684323',
                'email' => 'info@punhlainghospitals.com',
                'is_active' => true,
                'is_verified' => true,
            ],
            [
                'name' => 'Mandalay General Hospital',
                'address' => '30th St, Between 74th & 77th St, Mandalay',
                'phone' => '0221041',
                'email' => 'mgh@health.gov.mm',
                'is_active' => true,
                'is_verified' => true,
            ],
        ];

        foreach ($mainHospitals as $hospital) {
            Hospital::create($hospital);
        }
    }
}

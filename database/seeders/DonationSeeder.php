<?php

namespace Database\Seeders;

use App\Enums\DonationStatus;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $donors = Donor::all();
        $hospitals = Hospital::all();
        $staff = User::whereHas('role', function ($q) {
            $q->where('name', 'staff');
        })->first();

        if ($donors->isEmpty() || $hospitals->isEmpty() || !$staff)
            return;

        foreach ($donors as $donor) {
            Donation::create([
                'donor_id' => $donor->id,
                'hospital_id' => $hospitals->random()->id,
                'created_by' => $staff->id,
                'blood_group' => $donor->blood_group,
                'units_donated' => 1,
                'donation_date' => now()->subDays(rand(1, 10))->toDateString(),
                'status' => DonationStatus::COMPLETED->value,
                'remarks' => 'Regular donation seed data.',
            ]);
        }
    }
}

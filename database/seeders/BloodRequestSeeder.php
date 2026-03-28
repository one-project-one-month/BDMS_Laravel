<?php

namespace Database\Seeders;

use App\Enums\BloodGroup;
use App\Enums\BloodRequestStatus;
use App\Enums\Urgency;
use App\Models\BloodRequest;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Seeder;

class BloodRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'user');
        })->take(5)->get();

        $hospitals = Hospital::all();

        if ($users->isEmpty() || $hospitals->isEmpty())
            return;

        foreach ($users as $index => $user) {
            BloodRequest::create([
                'user_id' => $user->id,
                'hospital_id' => $hospitals->random()->id,
                'patient_name' => "Patient of " . $user->user_name,
                'blood_group' => BloodGroup::values()[array_rand(BloodGroup::values())],
                'units_required' => rand(1, 4),
                'contact_phone' => '09' . rand(111111111, 999999999),
                'urgency' => Urgency::values()[array_rand(Urgency::values())],
                'required_date' => now()->addDays(rand(1, 7))->toDateString(),
                'status' => BloodRequestStatus::PENDING->value,
                'reason' => "Medical emergency - Request no. " . ($index + 1),
                'approved_by' => null,
                'approved_at' => null,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'user');
        })->get();

        $hospital = Hospital::first();

        if ($users->isEmpty() || !$hospital) {
            return;
        }

        foreach ($users as $index => $user) {
            if (!$user->donor)
                continue;

            $isDonation = rand(0, 1) == 1;

            Appointment::create([
                'user_id' => $user->id,
                'hospital_id' => $hospital->id,
                'donation_id' => $isDonation ? (Donation::inRandomOrder()->first()?->id) : null,
                'blood_request_id' => !$isDonation ? (BloodRequest::inRandomOrder()->first()?->id) : null,
                'appointment_date' => now()->addDays(rand(1, 30))->toDateString(),
                'appointment_time' => sprintf('%02d:00:00', rand(9, 16)),
                'status' => AppointmentStatus::SCHEDULED->value,
                'remarks' => $isDonation ? "Scheduled for blood donation." : "Scheduled for blood request pickup.",
            ]);

            if ($index >= 9)
                break;
        }
    }
}

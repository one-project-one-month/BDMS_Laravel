<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::inRandomOrder()->first();
        $hospital = Hospital::inRandomOrder()->first();

        for($i = 0; $i < 10; $i++){
            Appointment::create([
                'user_id' => $user->id,
                'hospital_id' => $hospital->id,
                'donation_id' => Donation::inRandomOrder()->first() ->id ?? null,
                'blood_request_id' => BloodRequest::inRandomOrder()->first()->id ?? null,
                'appointment_date' => now()->addDays(rand(1, 30))->toDateString(),
                'appointment_time' => now()->addMinutes(rand(1, 1440))->toTimeString(), 
                'status' => \App\Enums\AppointmentStatus::SCHEDULED->value,
                'remarks' => Str::random(20),
            ]);
        }
    }
}

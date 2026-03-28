<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Enums\BloodInventoryStatus;
use App\Enums\BloodGroup;

class BloodInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $donationIds = DB::table('donations')->pluck('id')->toArray();
        $bloodGroups = BloodGroup::values();
        $statuses = BloodInventoryStatus::values();
        $hospitalIds = range(1, 3);

        foreach ($donationIds as $donationId) {
            $daysAgo = rand(0, 30);
            $collectedAt = Carbon::now()->subDays($daysAgo);

            DB::table('blood_inventories')->insert([
                'donation_id' => $donationId,
                'hospital_id' => $hospitalIds[array_rand($hospitalIds)],
                'blood_group' => $bloodGroups[array_rand($bloodGroups)],
                'units' => rand(1, 5),
                'collected_at' => $collectedAt->format('Y-m-d'),
                'expired_at' => $collectedAt->copy()->addDays(35)->format('Y-m-d'),
                'status' => $statuses[array_rand($statuses)],
                'blood_request_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

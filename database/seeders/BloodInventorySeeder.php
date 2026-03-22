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
        $faker = Faker::create();

        $donationIds = DB::table('donations')->pluck('id')->toArray();
        $bloodGroups = BloodGroup::values();
        $statuses = BloodInventoryStatus::values();
        $hospitalIds = range(1, 3);

        foreach ($donationIds as $donationId) {
            $collectedAt = $faker->dateTimeBetween('-30 days', 'now');

            DB::table('blood_inventories')->insert([
                'donation_id' => $donationId,
                'hospital_id' => $faker->randomElement($hospitalIds),
                'blood_group' => $faker->randomElement($bloodGroups),
                'units' => $faker->numberBetween(1, 5),
                'collected_at' => $collectedAt->format('Y-m-d'),
                'expired_at' => Carbon::parse($collectedAt)->addDays(35)->format('Y-m-d'),
                'status' => $faker->randomElement($statuses),
                'blood_request_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

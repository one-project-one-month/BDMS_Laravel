<?php

namespace Database\Seeders;

use Database\Seeders\HospitalSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([HospitalSeeder::class]);
        $this->call([UserSeeder::class]);
    }
}

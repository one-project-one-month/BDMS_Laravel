<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Announcement::create([
            'title' => 'System Maintenance',
            'content' => 'System will be down at 10 PM tonight.',
            'is_active' => true,
            'expired_at' => Carbon::now()->addDays(3),
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (config('roles') as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }
}

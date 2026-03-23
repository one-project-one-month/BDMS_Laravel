<?php

use App\Models\User;
use Database\Seeders\HospitalSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case Configuration
|--------------------------------------------------------------------------
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature')->beforeEach(function () {
        $this->seed(HospitalSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
    })->in('Feature');
;

/*
|--------------------------------------------------------------------------
| Custom Helpers (Functions)
|--------------------------------------------------------------------------
*/

/**
 * Login
 */
function login($user = null)
{
    $user = $user ?? User::factory()->create();

    return test()->actingAs($user);
}

function loginAsAdmin()
{
    $admin = User::factory()->create(['role' => 'admin']);
    return test()->actingAs($admin);
}

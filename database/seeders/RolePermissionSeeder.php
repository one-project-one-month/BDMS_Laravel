<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = config('roles');
        $modules = config('permissions');

        $admin = Role::where('name', $roles['admin'])->firstOrFail();
        $staff = Role::where('name', $roles['staff'])->firstOrFail();
        $user  = Role::where('name', $roles['user'])->firstOrFail();

        /**
         * ------------------------------------------------------
         * Admin -> gets EVERYTHING
         * ------------------------------------------------------
         */
        $admin->permissions()->sync(Permission::pluck('id'));

        /**
         * ------------------------------------------------------
         * Staff -> exclude sensitive modules
         * ------------------------------------------------------
         */
        $excludedModulesForStaff = [
            'roles',
            'permissions',
            'announcements',
        ];

        $staffPermissionNames = collect($modules)
            ->except($excludedModulesForStaff)
            ->flatten()
            ->values();

        $staffPermissionIds = Permission::whereIn('name', $staffPermissionNames)->pluck('id');

        $staff->permissions()->sync($staffPermissionIds);

        /**
         * ------------------------------------------------------
         * User -> exclude sensitive modules
         * ------------------------------------------------------
         */
        $allowedUserModules = [
            'profile',
            'donations',
            'blood_requests',
            'appointments',
            'certificates',
        ];

        $userPermissionNames = collect($modules)
            ->only($allowedUserModules)
            ->flatten()
            ->values();

        $userPermissionIds = Permission::whereIn('name', $userPermissionNames)->pluck('id');

        $user->permissions()->sync($userPermissionIds);
    }
}

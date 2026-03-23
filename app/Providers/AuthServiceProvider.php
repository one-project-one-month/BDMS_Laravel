<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Gate::before(function ($user, $ability) {
        //     # admin - allows everything
        //     if ($user->role?->name === config('roles.admin')) {
        //         return true;
        //     }
        // });

        // # Dynamically register all permissions
        // Permission::pluck('name')->each(function ($permission) {
        //     Gate::define($permission, fn($user) => $user->hasPermission($permission));
        // });
    }
}

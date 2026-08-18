<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::define('superadmin-only', function ($user) {
            return $user->rol_id === 1; // Solo el rol con ID 1 (Super Admin)
        });

        \Illuminate\Support\Facades\Gate::define('admin-org', function ($user) {
            return $user->esAdminDeOrganizacion();
        });

        \Illuminate\Support\Facades\Gate::define('supervisor-org', function ($user) {
            return $user->esSupervisorDeOrganizacion();
        });
    }
}

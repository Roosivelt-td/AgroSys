<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\Labor;
use App\Models\MiembroOrganizacion;
use App\Models\Organizacion;
use App\Observers\AgroAuditObserver;

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
        // Registro de Observers para Trazabilidad Forense
        Terreno::observe(AgroAuditObserver::class);
        Cultivo::observe(AgroAuditObserver::class);
        Labor::observe(AgroAuditObserver::class);
        MiembroOrganizacion::observe(AgroAuditObserver::class);
        Organizacion::observe(AgroAuditObserver::class);

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

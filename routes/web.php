<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware('auth')->group(function () {
    // Dashboard y Perfil comunes
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // Historial Personal de Actividad
    Route::get('mi-actividad', \App\Livewire\Profile\ActividadPersonal::class)
        ->name('profile.actividad');

    // Mis Trámites (Invitaciones y Solicitudes Personales)
    Route::get('mis-tramites', \App\Livewire\Profile\TramitesRecibidos::class)
        ->name('profile.tramites');

    // Gestión de Mis Organizaciones (Multi-empresa)
    Route::get('mis-organizaciones', \App\Livewire\Profile\MisOrganizaciones::class)
        ->name('profile.organizaciones');

    // Gestión de Terrenos (React + Leaflet)
    Route::get('mis-terrenos', \App\Livewire\Admin\TerrenosManager::class)
        ->name('admin.terrenos');

    // Gestión de Cultivos
    Route::get('mis-cultivos', \App\Livewire\Admin\CultivosManager::class)
        ->name('admin.cultivos');

    // Gestión de Labores
    Route::get('mis-labores', \App\Livewire\Admin\LaboresManager::class)
        ->name('admin.labores');

    // Inteligencia y Clima
    Route::get('clima-ia', \App\Livewire\Admin\ClimaIA::class)
        ->name('admin.clima-ia');

    // Chat y Mensajería Técnica
    Route::get('mensajeria', \App\Livewire\Chat\ChatManager::class)
        ->name('chat.index');

    // Gestión Interna de Miembros (Miembros de Org)
    Route::get('organizacion/{id}/miembros', \App\Livewire\Admin\GestionMiembros::class)
        ->name('admin.organizacion.miembros');

    // Detalle de Supervisor y sus asignados
    Route::get('organizacion/{orgId}/supervisor/{miembroId}', \App\Livewire\Admin\SupervisorDetail::class)
        ->name('admin.organizacion.supervisor')
        ->middleware('can:admin-org');

    // Panel de Mis Agricultores (Supervisor)
    Route::get('supervision/mis-agricultores', \App\Livewire\Admin\MisAgricultores::class)
        ->name('supervisor.agricultores')
        ->middleware('can:supervisor-org');

    Route::get('supervision/agricultor/{id}', \App\Livewire\Admin\AgricultorSupervisionDetail::class)
        ->name('supervisor.agricultor.detalle')
        ->middleware('can:supervisor-org');

    // Solicitudes Internas (Admin de Org)
    Route::get('organizacion/solicitudes', \App\Livewire\Admin\SolicitudesInternas::class)
        ->name('admin.solicitudes.internas')
        ->middleware('can:admin-org');

    // Asignación de Supervisores (Contexto Org)
    Route::get('organizacion/{id}/asignar-supervisores', \App\Livewire\Admin\AsignarSupervisores::class)
        ->name('admin.organizacion.asignar-supervisores')
        ->middleware('can:admin-org');

    // Gestión de Organizaciones e Historial (Super Admin)
    Route::get('admin/solicitudes', \App\Livewire\Admin\SolicitudesManager::class)
        ->name('admin.solicitudes')
        ->middleware('can:superadmin-only');

    Route::get('admin/historial', \App\Livewire\Admin\HistorialManager::class)
        ->name('admin.historial')
        ->middleware('can:superadmin-only');

    Route::get('admin/usuarios', \App\Livewire\Admin\UsersManager::class)
        ->name('admin.usuarios')
        ->middleware('can:superadmin-only');

    Route::get('admin/organizaciones', \App\Livewire\Admin\OrganizacionesManager::class)
        ->name('admin.organizaciones')
        ->middleware('can:superadmin-only');

    Route::get('admin/catalogo-cultivos', \App\Livewire\Admin\CatalogoCultivosManager::class)
        ->name('admin.catalogo-cultivos')
        ->middleware('can:superadmin-only');
});

require __DIR__.'/auth.php';

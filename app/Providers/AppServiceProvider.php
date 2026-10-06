<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\Labor;
use App\Models\Cosecha;
use App\Models\Venta;
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
        Cosecha::observe(AgroAuditObserver::class);
        Venta::observe(AgroAuditObserver::class);
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

        // Registrar componente de layout de presentación
        \Illuminate\Support\Facades\Blade::component('presentacion.layouts.guest', 'presentacion-guest');

        // Registrar Auditoría de Inicio de Sesión de Usuarios con Dispositivo y Ubicación
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function (\Illuminate\Auth\Events\Login $event) {
            if ($event->user) {
                $request = request();
                $ip = $request->ip() ?: '127.0.0.1';
                $ua = $request->userAgent() ?: 'Navegador Web';

                // Dispositivo y OS
                $os = 'Sistema Desconocido';
                if (preg_match('/windows nt 10/i', $ua)) $os = 'Windows 10/11';
                elseif (preg_match('/windows/i', $ua)) $os = 'Windows';
                elseif (preg_match('/android/i', $ua)) $os = 'Android';
                elseif (preg_match('/iphone|ipad|ipod/i', $ua)) $os = 'iOS';
                elseif (preg_match('/macintosh|mac os x/i', $ua)) $os = 'macOS';
                elseif (preg_match('/linux/i', $ua)) $os = 'Linux';

                $browser = 'Navegador';
                if (preg_match('/edg/i', $ua)) $browser = 'Microsoft Edge';
                elseif (preg_match('/chrome/i', $ua)) $browser = 'Google Chrome';
                elseif (preg_match('/firefox/i', $ua)) $browser = 'Mozilla Firefox';
                elseif (preg_match('/safari/i', $ua)) $browser = 'Apple Safari';

                $deviceType = preg_match('/mobile|android|iphone|ipad/i', $ua) ? 'Móvil' : 'Escritorio';
                $dispositivo = "{$browser} en {$os} ({$deviceType})";

                // Ubicación estimada
                $ubicacion = ($ip === '127.0.0.1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.') || str_starts_with($ip, '172.'))
                    ? 'Red Local / Servidor AgroSys'
                    : 'Ubicación IP ' . $ip;

                \App\Models\HistorialProceso::create([
                    'usuario_id' => $event->user->id,
                    'organizacion_id' => null,
                    'tabla_afectada' => 'usuarios',
                    'registro_id' => $event->user->id,
                    'accion' => 'INICIO DE SESIÓN',
                    'descripcion' => 'El usuario ' . $event->user->email . ' inició sesión desde ' . $dispositivo,
                    'detalles_previos' => [
                        'ip' => $ip,
                        'dispositivo' => $dispositivo,
                        'navegador' => $browser,
                        'sistema_operativo' => $os,
                        'tipo_dispositivo' => $deviceType,
                        'ubicacion_ip' => $ubicacion,
                        'user_agent' => $ua
                    ]
                ]);
            }
        });
    }
}

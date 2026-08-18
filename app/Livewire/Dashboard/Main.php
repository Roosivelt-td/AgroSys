<?php

namespace App\Livewire\Dashboard;

use App\Models\Solicitud;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\Labor;
use App\Models\HistorialProceso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * LÓGICA (BACKEND) - Dashboard Principal.
 * Gestiona el estado del agricultor y sus métricas.
 */
class Main extends Component
{
    /**
     * Obtiene los datos necesarios para renderizar el Dashboard.
     */
    public function render()
    {
        $usuario = Auth::user();

        // Obtener historial personal de procesos
        $historial = HistorialProceso::where('usuario_id', $usuario->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Verificamos membresías activas
        $membresia = $usuario->membresias()->where('estado', 1)->first();

        // Verificamos solicitudes de creación pendientes (Flujo SaaS)
        $solicitudPendiente = Solicitud::where('solicitante_usuario_id', $usuario->id)
            ->where('tipo', 'creacion_organizacion')
            ->where('estado', 0)
            ->first();

        if (!$membresia) {
            return view('livewire.dashboard.main', [
                'hasOrg' => false,
                'isWaiting' => (bool)$solicitudPendiente,
                'solicitud' => $solicitudPendiente,
                'historial' => $historial,
                'stats' => []
            ]);
        }

        // Si tiene organización, calculamos métricas reales
        $stats = [
            'terrenos' => Terreno::where('organizacion_id', $membresia->organizacion_id)->count(),
            'cultivos' => Cultivo::whereHas('terreno', function($q) use ($membresia) {
                $q->where('organizacion_id', $membresia->organizacion_id);
            })->where('estado', 'En crecimiento')->count(),
            'labores' => Labor::whereHas('cultivo.terreno', function($q) use ($membresia) {
                $q->where('organizacion_id', $membresia->organizacion_id);
            })->where('estado', 'Pendiente')->count(),
        ];

        return view('livewire.dashboard.main', [
            'hasOrg' => true,
            'isWaiting' => false,
            'organizacion' => $membresia->organizacion,
            'historial' => $historial,
            'stats' => $stats
        ]);
    }
}

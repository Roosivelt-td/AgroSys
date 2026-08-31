<?php

namespace App\Livewire\Dashboard;

use App\Models\Solicitud;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\Labor;
use App\Models\HistorialProceso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;

/**
 * LÓGICA (BACKEND) - Dashboard Principal.
 * Gestiona el estado del agricultor y sus métricas.
 */
#[Title('Dashboard')]
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

        // Si tiene organización, calculamos métricas respetando la visibilidad por rol
        $orgId = $membresia->organizacion_id;
        $esAdmin = $membresia->roles()->whereHas('rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->where('estado', 1)->exists();
        $esSupervisor = $membresia->roles()->whereHas('rolDetalle', fn($q) => $q->where('nombre', 'Supervisor'))->where('estado', 1)->exists();

        $allowedUserIds = [$usuario->id];
        if ($esSupervisor) {
            $assignedIds = $usuario->getIdsAgricultoresAsignados($orgId);
            $allowedUserIds = array_merge($allowedUserIds, $assignedIds);
        }

        $queryTerrenos = Terreno::where('organizacion_id', $orgId);
        $queryCultivos = Cultivo::whereHas('terreno', fn($q) => $q->where('organizacion_id', $orgId));
        $queryLabores = Labor::whereHas('cultivo.terreno', fn($q) => $q->where('organizacion_id', $orgId));

        if (!$esAdmin) {
            $queryTerrenos->whereIn('usuario_id', $allowedUserIds);
            $queryCultivos->whereHas('terreno', fn($q) => $q->whereIn('usuario_id', $allowedUserIds));
            $queryLabores->whereHas('cultivo.terreno', fn($q) => $q->whereIn('usuario_id', $allowedUserIds));
        }

        $stats = [
            'terrenos' => $queryTerrenos->count(),
            'cultivos' => $queryCultivos->where('estado', 'En crecimiento')->count(),
            'labores' => $queryLabores->where('estado', 'Pendiente')->count(),
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

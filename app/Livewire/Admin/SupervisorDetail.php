<?php

namespace App\Livewire\Admin;

use App\Models\MiembroOrganizacion;
use App\Models\AsignacionSupervisor;
use App\Models\User;
use App\Http\Controllers\OrganizacionController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SupervisorDetail extends Component
{
    use WithPagination;

    public $orgId;
    public $miembroId;
    public $supervisor;
    public $search = '';
    public $searchNewMember = ''; // Para buscar agricultores libres

    public function mount($orgId, $miembroId)
    {
        $this->orgId = $orgId;
        $this->miembroId = $miembroId;

        // Verificar que el supervisor pertenezca a la org y sea supervisor activo
        $this->supervisor = MiembroOrganizacion::where('id', $miembroId)
            ->where('organizacion_id', $orgId)
            ->whereHas('roles', fn($q) => $q->where('rol_id', 2)->where('estado', 1)) // 2 = Supervisor
            ->with('usuario')
            ->firstOrFail();

        // SEGURIDAD DE ACCESO ESTRICTA
        $myId = Auth::id();
        $esAdmin = MiembroOrganizacion::where('usuario_id', $myId)->where('organizacion_id', $orgId)
            ->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->exists();

        $soyElSupervisor = ($this->supervisor->usuario_id === $myId);

        $soyAgricultorAsignado = AsignacionSupervisor::where('supervisor_miembro_id', $miembroId)
            ->where('agricultor_usuario_id', $myId)->exists();

        if (!$esAdmin && !$soyElSupervisor && !$soyAgricultorAsignado) {
            abort(403, 'No tienes permiso para ver este equipo de supervisión.');
        }
    }

    public function toggleAsignacion($agricultorId)
    {
        $controller = new OrganizacionController();
        $res = $controller->asignarAgricultorASupervisor($this->orgId, $this->miembroId, $agricultorId);
        session()->flash($res['success'] ? 'status' : 'error', $res['mensaje']);
    }

    public function quitarAsignacion($asignacionId)
    {
        $controller = new OrganizacionController();
        $res = $controller->eliminarAsignacionSupervisor($asignacionId);
        session()->flash($res['success'] ? 'status' : 'error', $res['mensaje']);
    }

    public function render()
    {
        // 1. Agricultores ya asignados a este supervisor en esta organización
        $query = AsignacionSupervisor::where('supervisor_miembro_id', $this->miembroId)
            ->where('organizacion_id', $this->orgId)
            ->with(['agricultor.rol']);

        if ($this->search) {
            $query->whereHas('agricultor', function($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                  ->orWhere('dni', 'like', '%' . $this->search . '%');
            });
        }

        // 2. Miembros de la organización que NO son supervisores y NO tienen supervisor aún
        $libres = [];
        if (strlen($this->searchNewMember) > 1) {
            // IDs de usuarios que YA son supervisores en esta organización
            $supervisoresIds = MiembroOrganizacion::where('organizacion_id', $this->orgId)
                ->whereHas('roles', fn($q) => $q->where('rol_id', 2)->where('estado', 1))
                ->pluck('usuario_id');

            // IDs de usuarios que YA tienen un supervisor asignado en esta organización
            $yaAsignadosIds = AsignacionSupervisor::where('organizacion_id', $this->orgId)
                ->pluck('agricultor_usuario_id');

            // IDs de todos los miembros de la organización
            $miembrosIds = MiembroOrganizacion::where('organizacion_id', $this->orgId)
                ->pluck('usuario_id');

            $libres = User::whereIn('id', $miembrosIds)
                ->whereNotIn('id', $yaAsignadosIds)
                ->whereNotIn('id', $supervisoresIds) // Regla: Un supervisor no puede ser supervisado
                ->where('id', '!=', $this->supervisor->usuario_id)
                ->where(function($q) {
                    $q->where('nombres', 'like', '%' . $this->searchNewMember . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->searchNewMember . '%')
                      ->orWhere('dni', 'like', '%' . $this->searchNewMember . '%');
                })
                ->take(5)
                ->get();
        }

        return view('livewire.admin.supervisor-detail', [
            'asignaciones' => $query->paginate(10),
            'agricultoresLibres' => $libres
        ]);
    }
}

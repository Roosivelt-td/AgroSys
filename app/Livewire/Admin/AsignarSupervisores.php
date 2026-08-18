<?php

namespace App\Livewire\Admin;

use App\Models\MiembroOrganizacion;
use App\Models\RolesOrganizacion;
use App\Models\MiembroRol;
use App\Models\Solicitud;
use App\Http\Controllers\OrganizacionController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AsignarSupervisores extends Component
{
    use WithPagination;

    public $orgId;
    public $search = '';

    public function mount($id)
    {
        $this->orgId = $id;
        // Verificar que sea admin de esta org
        $esAdmin = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->where('organizacion_id', $id)
            ->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))
            ->exists();

        if (!$esAdmin) abort(403);
    }

    public function invitar($miembroId)
    {
        $miembro = MiembroOrganizacion::with('usuario')->find($miembroId);
        $res = (new OrganizacionController())->invitarUsuario($this->orgId, $miembro->usuario->dni, 2); // 2 = Supervisor
        session()->flash($res['success'] ? 'status' : 'error', $res['mensaje']);
    }

    public function cancelarInvitacion($solicitudId)
    {
        $solicitud = Solicitud::find($solicitudId);
        if ($solicitud) {
            $solicitud->delete();
            session()->flash('status', 'Invitación cancelada.');
        }
    }

    public function bajarGrado($miembroId)
    {
        // Regla: No se puede bajar de grado al propietario
        $miembro = MiembroOrganizacion::find($miembroId);
        if ($miembro->es_propietario) {
            session()->flash('error', 'No se puede remover el cargo al propietario.');
            return;
        }

        // Desactivar físicamente el rol de supervisor (ID 2)
        MiembroRol::where('miembro_id', $miembroId)
            ->where('rol_id', 2)
            ->delete(); // Usamos delete para que deje de ser supervisor totalmente

        // Eliminar todas las asignaciones de agricultores que tenía este supervisor
        AsignacionSupervisor::where('supervisor_miembro_id', $miembroId)
            ->where('organizacion_id', $this->orgId)
            ->delete();

        session()->flash('status', 'El cargo de Supervisor ha sido revocado y sus asignaciones limpiadas.');
    }

    public function render()
    {
        $miembros = MiembroOrganizacion::where('organizacion_id', $this->orgId)
            ->where('usuario_id', '!=', Auth::id()) // Excluir al administrador actual
            ->with(['usuario', 'roles.rolDetalle'])
            ->whereHas('usuario', function($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                  ->orWhere('dni', 'like', '%' . $this->search . '%');
            })
            ->paginate(8);

        // Obtener solicitudes de invitación de supervisor pendientes para esta org
        $invitacionesPendientes = Solicitud::where('organizacion_id', $this->orgId)
            ->where('tipo', 'invitacion_organizacion')
            ->where('datos_extra->rol_propuesto_id', 2)
            ->where('estado', 0)
            ->pluck('id', 'destinatario_usuario_id')
            ->toArray();

        return view('livewire.admin.asignar-supervisores', [
            'miembros' => $miembros,
            'invitacionesPendientes' => $invitacionesPendientes,
            'organizacion' => \App\Models\Organizacion::find($this->orgId)
        ]);
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\MiembroOrganizacion;
use App\Models\RolesOrganizacion;
use App\Models\AsignacionSupervisor;
use App\Models\MiembroRol;
use App\Models\User;
use App\Http\Controllers\OrganizacionController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GestionMiembros extends Component
{
    use WithPagination;

    public $orgId;
    public $organizacion;
    public $dniInvitacion = '';
    public $rolInvitacion = 3;
    public $selectedMember = null;
    public $search = '';

    public $searchUserInvite = ''; // Para búsqueda interactiva de invitables
    public $usuariosEncontrados = [];

    public $miRolEnOrg;
    public $misAsignaciones = [];

    public function mount($id)
    {
        $this->orgId = $id;
        $this->organizacion = \App\Models\Organizacion::findOrFail($id);

        $miMembresia = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->where('organizacion_id', $this->orgId)
            ->where('estado', 1)
            ->with('roles.rolDetalle')
            ->first();

        if (!$miMembresia) {
            return redirect()->route('profile.organizaciones');
        }

        $this->miRolEnOrg = $miMembresia->roles->pluck('rolDetalle.nombre')->toArray();

        if (in_array('Supervisor', $this->miRolEnOrg)) {
            $this->misAsignaciones = AsignacionSupervisor::where('supervisor_miembro_id', $miMembresia->id)
                ->pluck('agricultor_usuario_id')
                ->toArray();
        }
    }

    public function updatedSearchUserInvite($value)
    {
        if (strlen($value) > 1) {
            $this->usuariosEncontrados = User::where('rol_id', 2)
                ->where(function($q) use ($value) {
                    $q->where('nombres', 'like', '%' . $value . '%')
                      ->orWhere('dni', 'like', '%' . $value . '%');
                })
                ->whereNotIn('id', MiembroOrganizacion::where('organizacion_id', $this->orgId)->where('estado', 1)->pluck('usuario_id'))
                ->take(5)
                ->get()
                ->toArray();
        } else {
            $this->usuariosEncontrados = [];
        }
    }

    public function seleccionarParaInvitar($dni, $nombre)
    {
        $this->dniInvitacion = $dni;
        $this->searchUserInvite = $nombre;
        $this->usuariosEncontrados = [];
    }

    public function showProfile($miembroId)
    {
        $this->selectedMember = null;
        $miembro = MiembroOrganizacion::with(['usuario.rol', 'roles.rolDetalle'])->find($miembroId);

        if (!$miembro) return;

        if (in_array('Administrador', $this->miRolEnOrg)) {
            $this->selectedMember = $miembro;
        } elseif (in_array('Supervisor', $this->miRolEnOrg) && in_array($miembro->usuario_id, $this->misAsignaciones)) {
            $this->selectedMember = $miembro;
        }
    }

    public function closeProfile()
    {
        $this->selectedMember = null;
    }

    public function toggleBloqueo($miembroId)
    {
        if (!in_array('Administrador', $this->miRolEnOrg)) return;
        $miembro = MiembroOrganizacion::find($miembroId);
        $miembro->estado = !$miembro->estado;
        $miembro->save();
    }

    public function eliminarMiembro($miembroId)
    {
        if (!in_array('Administrador', $this->miRolEnOrg)) return;
        $miembro = MiembroOrganizacion::find($miembroId);
        if (!$miembro->es_propietario) $miembro->delete();
    }

    public function enviarInvitacion()
    {
        $this->validate(['dniInvitacion' => 'required', 'rolInvitacion' => 'required']);
        $res = (new OrganizacionController())->invitarUsuario($this->orgId, $this->dniInvitacion, $this->rolInvitacion);
        session()->flash($res['success'] ? 'status' : 'error', $res['mensaje']);
        if ($res['success']) {
            $this->dniInvitacion = '';
            $this->searchUserInvite = '';
        }
    }

    public function solicitarCargo($tipo)
    {
        $controller = new OrganizacionController();
        $res = ($tipo === 'supervisor') ? $controller->solicitarAscenso($this->orgId) : $controller->solicitarRenunciaSupervisor($this->orgId);
        session()->flash($res['success'] ? 'status' : 'error', $res['mensaje']);
    }

    public function abandonarOrganizacion()
    {
        $res = (new OrganizacionController())->abandonarOrganizacion($this->orgId);
        if ($res['success']) return redirect()->route('profile.organizaciones');
        session()->flash('error', $res['mensaje']);
    }

    public function render()
    {
        $query = MiembroOrganizacion::where('organizacion_id', $this->orgId)
            ->with(['usuario.rol', 'roles' => function($q) {
                $q->where('estado', 1)->with('rolDetalle');
            }, 'usuario.misSupervisores' => function($q) {
                $q->where('organizacion_id', $this->orgId)->with('supervisor.usuario');
            }]);

        if ($this->search) {
            $query->whereHas('usuario', function($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                  ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                  ->orWhere('dni', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.gestion-miembros', [
            'miembros' => $query->paginate(10),
            'rolesCatalogo' => RolesOrganizacion::whereIn('id', [2, 3, 4])->get()
        ]);
    }
}

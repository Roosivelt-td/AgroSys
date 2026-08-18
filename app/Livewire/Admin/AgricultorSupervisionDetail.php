<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\AsignacionSupervisor;
use App\Models\MiembroOrganizacion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AgricultorSupervisionDetail extends Component
{
    public $agricultor;
    public $organizacion;

    public function mount($id)
    {
        // 1. Cargar datos del agricultor
        $this->agricultor = User::findOrFail($id);

        // 2. Verificar que el supervisor autenticado tenga asignado a este agricultor en ALGUNA organización
        $misMembresiasSup = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Supervisor'))
            ->pluck('id');

        $asignacion = AsignacionSupervisor::whereIn('supervisor_miembro_id', $misMembresiasSup)
            ->where('agricultor_usuario_id', $id)
            ->with('organizacion')
            ->first();

        if (!$asignacion) {
            abort(403, 'No tienes permisos para supervisar a este agricultor.');
        }

        $this->organizacion = $asignacion->organizacion;
    }

    public function render()
    {
        return view('livewire.admin.agricultor-supervision-detail');
    }
}

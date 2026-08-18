<?php

namespace App\Livewire\Admin;

use App\Models\AsignacionSupervisor;
use App\Models\MiembroOrganizacion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class MisAgricultores extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedOrgId = null; // Para el selector de organización

    public function mount()
    {
        $misOrgsSup = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Supervisor'))
            ->where('estado', 1)
            ->get();

        if ($misOrgsSup->count() === 1) {
            $this->selectedOrgId = $misOrgsSup->first()->organizacion_id;
        }
    }

    public function selectOrg($id)
    {
        $this->selectedOrgId = $id;
        $this->resetPage();
    }

    public function render()
    {
        // 1. Obtener organizaciones donde soy supervisor para el "Picker"
        $misOrgsSup = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Supervisor'))
            ->where('estado', 1)
            ->with('organizacion')
            ->get();

        $agricultores = collect();

        if ($this->selectedOrgId) {
            $miMembresiaId = MiembroOrganizacion::where('usuario_id', Auth::id())
                ->where('organizacion_id', $this->selectedOrgId)
                ->value('id');

            $query = AsignacionSupervisor::where('supervisor_miembro_id', $miMembresiaId)
                ->where('organizacion_id', $this->selectedOrgId)
                ->with(['agricultor.rol']);

            if ($this->search) {
                $query->whereHas('agricultor', function($q) {
                    $q->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('dni', 'like', '%' . $this->search . '%');
                });
            }
            $agricultores = $query->paginate(10);
        }

        return view('livewire.admin.mis-agricultores', [
            'misOrgsSup' => $misOrgsSup,
            'agricultores' => $agricultores,
            'currentOrg' => $this->selectedOrgId ? \App\Models\Organizacion::find($this->selectedOrgId) : null
        ]);
    }
}

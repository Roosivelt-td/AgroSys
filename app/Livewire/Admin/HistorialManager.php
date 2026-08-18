<?php

namespace App\Livewire\Admin;

use App\Models\HistorialProceso;
use App\Models\Organizacion;
use App\Models\RolesOrganizacion;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class HistorialManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedItem = null;

    // Filtros
    public $filterCategory = ''; // 'all', 'super_admin', 'admin_org', 'supervisor', 'agricultor'
    public $filterOrg = '';

    public function showDetails($id)
    {
        $this->selectedItem = HistorialProceso::with(['usuario.rol', 'organizacion'])->find($id);
    }

    public function closeDetails()
    {
        $this->selectedItem = null;
    }

    public function render()
    {
        $query = HistorialProceso::with(['usuario.rol', 'organizacion'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('tabla_afectada', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por Categoría de Usuario
        if ($this->filterCategory) {
            switch ($this->filterCategory) {
                case 'super_admin':
                    $query->whereHas('usuario', fn($q) => $q->where('rol_id', 1));
                    break;
                case 'admin_org':
                    $query->whereHas('usuario.membresias.roles.rolDetalle', fn($q) => $q->where('nombre', 'Administrador'));
                    break;
                case 'supervisor':
                    $query->whereHas('usuario.membresias.roles.rolDetalle', fn($q) => $q->where('nombre', 'Supervisor'));
                    break;
                case 'agricultor':
                    // Usuario que es Agricultor global y no tiene otros cargos superiores o simplemente agricultor interno
                    $query->whereHas('usuario', fn($q) => $q->where('rol_id', 2));
                    break;
            }
        }

        // Filtro por Organización
        if ($this->filterOrg) {
            $query->where('organizacion_id', $this->filterOrg);
        }

        return view('livewire.admin.historial-manager', [
            'logs' => $query->paginate(10),
            'organizaciones' => Organizacion::all()
        ]);
    }
}

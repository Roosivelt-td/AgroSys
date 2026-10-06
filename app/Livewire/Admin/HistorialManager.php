<?php

namespace App\Livewire\Admin;

use App\Models\HistorialProceso;
use App\Models\Organizacion;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\Labor;
use App\Models\Cosecha;
use App\Models\Venta;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class HistorialManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedItem = null;
    public $selectedUserLog = null;
    public $selectedTerreno = null;
    public $selectedCultivo = null;
    public $selectedLabor = null;
    public $selectedCosecha = null;
    public $selectedVenta = null;

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

    public function showUserDetails($id)
    {
        $this->selectedUserLog = HistorialProceso::with(['usuario.rol', 'usuario.membresias.organizacion', 'organizacion'])->find($id);
        $this->selectedTerreno = null;
        $this->selectedCultivo = null;
        $this->selectedLabor = null;
        $this->selectedCosecha = null;
        $this->selectedVenta = null;

        if ($this->selectedUserLog) {
            $tabla = $this->selectedUserLog->tabla_afectada;
            if ($tabla === 'terrenos') {
                $this->selectedTerreno = Terreno::withTrashed()->find($this->selectedUserLog->registro_id);
            } elseif ($tabla === 'cultivos') {
                $this->selectedCultivo = Cultivo::with(['detalleCatalogo', 'terreno'])->find($this->selectedUserLog->registro_id);
            } elseif ($tabla === 'labores') {
                $this->selectedLabor = Labor::with(['detalleCatalogo', 'cultivo.detalleCatalogo', 'cultivo.terreno'])->find($this->selectedUserLog->registro_id);
            } elseif ($tabla === 'cosechas') {
                $this->selectedCosecha = Cosecha::with(['labor.cultivo.detalleCatalogo', 'labor.cultivo.terreno'])->find($this->selectedUserLog->registro_id);
            } elseif ($tabla === 'ventas') {
                $this->selectedVenta = Venta::with(['cosecha.labor.cultivo.detalleCatalogo'])->find($this->selectedUserLog->registro_id);
            }
        }
    }

    public function closeUserDetails()
    {
        $this->selectedUserLog = null;
        $this->selectedTerreno = null;
        $this->selectedCultivo = null;
        $this->selectedLabor = null;
        $this->selectedCosecha = null;
        $this->selectedVenta = null;
    }

    public function render()
    {
        $query = HistorialProceso::with(['usuario.rol', 'organizacion'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('tabla_afectada', 'like', '%' . $this->search . '%')
                  ->orWhereHas('usuario', function($u) {
                      $u->where('nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('dni', 'like', '%' . $this->search . '%');
                  });
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

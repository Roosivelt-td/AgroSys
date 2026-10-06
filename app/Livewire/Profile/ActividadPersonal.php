<?php

namespace App\Livewire\Profile;

use App\Models\HistorialProceso;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\Labor;
use App\Models\Cosecha;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ActividadPersonal extends Component
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

    public function showDetails($id)
    {
        $this->selectedItem = HistorialProceso::where('usuario_id', Auth::id())
            ->with(['usuario', 'organizacion'])
            ->find($id);
    }

    public function closeDetails()
    {
        $this->selectedItem = null;
    }

    public function showUserDetails($id)
    {
        $this->selectedUserLog = HistorialProceso::where('usuario_id', Auth::id())
            ->with(['usuario.rol', 'usuario.membresias.organizacion', 'organizacion'])
            ->find($id);

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
        $query = HistorialProceso::where('usuario_id', Auth::id())
            ->with(['usuario.rol', 'organizacion'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('tabla_afectada', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.profile.actividad-personal', [
            'logs' => $query->paginate(10)
        ]);
    }
}

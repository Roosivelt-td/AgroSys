<?php

namespace App\Livewire\Profile;

use App\Models\HistorialProceso;
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

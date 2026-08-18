<?php

namespace App\Livewire\Admin;

use App\Models\Organizacion;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class OrganizacionesManager extends Component
{
    use WithPagination;

    public $search = '';

    public function toggleStatus($id)
    {
        $org = Organizacion::findOrFail($id);
        $org->estado = !$org->estado;
        $org->save();

        session()->flash('status', 'Estado de la organización actualizado.');
    }

    public function render()
    {
        $organizaciones = Organizacion::with(['miembros.usuario'])
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->orWhere('ruc', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.organizaciones-manager', [
            'organizaciones' => $organizaciones
        ]);
    }
}

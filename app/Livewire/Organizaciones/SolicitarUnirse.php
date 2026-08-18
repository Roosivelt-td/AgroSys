<?php

namespace App\Livewire\Organizaciones;

use App\Models\Organizacion;
use App\Http\Controllers\OrganizacionController;
use Livewire\Component;

class SolicitarUnirse extends Component
{
    public $search = '';
    public $confirmingJoin = null;

    public function selectOrg($id)
    {
        $this->confirmingJoin = Organizacion::find($id);
    }

    public function sendRequest()
    {
        if (!$this->confirmingJoin) return;

        $controller = new OrganizacionController();
        $resultado = $controller->solicitarUnirse($this->confirmingJoin->id);

        if ($resultado['success']) {
            session()->flash('status', $resultado['mensaje']);
            $this->dispatch('close-modal', 'join-organization');
            $this->confirmingJoin = null;
            return redirect()->route('dashboard');
        } else {
            session()->flash('error', $resultado['mensaje']);
        }
    }

    public function render()
    {
        $organizaciones = [];
        if (strlen($this->search) > 1) {
            $organizaciones = Organizacion::where('estado', 1)
                ->where(function($query) {
                    $query->where('nombre', 'like', '%' . $this->search . '%')
                          ->orWhere('ruc', 'like', '%' . $this->search . '%');
                })
                ->take(6)
                ->get();
        }

        return view('livewire.organizaciones.solicitar-unirse', [
            'organizaciones' => $organizaciones
        ]);
    }
}

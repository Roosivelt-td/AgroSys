<?php

namespace App\Livewire\Admin;

use App\Models\Terreno;
use App\Models\HistorialProceso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Gestión Global de Terrenos')]
class AdminTerrenosManager extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function delete($id)
    {
        $terreno = Terreno::findOrFail($id);
        $nombre = $terreno->nombre;
        $terreno->delete();

        HistorialProceso::create([
            'usuario_id' => Auth::id(),
            'tabla_afectada' => 'terrenos',
            'registro_id' => $id,
            'accion' => 'DELETE',
            'descripcion' => "Terreno global '{$nombre}' eliminado por Super Admin."
        ]);

        session()->flash('status', 'Terreno eliminado del sistema.');
    }

    public function render()
    {
        $terrenos = Terreno::with(['responsable', 'organizacion'])
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.admin-terrenos-manager', [
            'terrenos' => $terrenos
        ]);
    }
}

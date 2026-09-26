<?php

namespace App\Livewire\Admin;

use App\Models\CatalogoLabor;
use App\Models\HistorialProceso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Gestión del Catálogo de Labores')]
class CatalogoLaboresManager extends Component
{
    use WithPagination;

    public $search = '';

    // Formulario
    public $laborId = null;
    public $nombre = '';
    public $categoria = 'mantenimiento';
    public $descripcion = '';

    protected $rules = [
        'nombre' => 'required|string|max:100|unique:catalogo_labores,nombre',
        'categoria' => 'required|in:preparacion,siembra,mantenimiento,cosecha',
        'descripcion' => 'nullable|string',
    ];

    public function resetForm()
    {
        $this->reset(['laborId', 'nombre', 'categoria', 'descripcion']);
        $this->resetErrorBag();
    }

    public function save()
    {
        $validationRules = $this->rules;
        if ($this->laborId) {
            $validationRules['nombre'] = 'required|string|max:100|unique:catalogo_labores,nombre,' . $this->laborId;
        }

        $this->validate($validationRules);

        $data = [
            'nombre' => $this->nombre,
            'categoria' => $this->categoria,
            'descripcion' => $this->descripcion,
        ];

        if ($this->laborId) {
            $labor = CatalogoLabor::findOrFail($this->laborId);
            $labor->update($data);
            $accion = 'UPDATE';
            $msg = "Labor '{$this->nombre}' actualizada.";
        } else {
            CatalogoLabor::create($data);
            $accion = 'INSERT';
            $msg = "Nueva labor '{$this->nombre}' registrada en el catálogo.";
        }

        HistorialProceso::create([
            'usuario_id' => Auth::id(),
            'tabla_afectada' => 'catalogo_labores',
            'registro_id' => $this->laborId ?: 0,
            'accion' => $accion,
            'descripcion' => $msg
        ]);

        $this->dispatch('close-modal', 'modal-labor-form');
        $this->resetForm();
        session()->flash('status', $msg);
    }

    public function edit($id)
    {
        $labor = CatalogoLabor::findOrFail($id);
        $this->laborId = $labor->id;
        $this->nombre = $labor->nombre;
        $this->categoria = $labor->categoria;
        $this->descripcion = $labor->descripcion;

        $this->dispatch('open-modal', 'modal-labor-form');
    }

    public function delete($id)
    {
        $labor = CatalogoLabor::findOrFail($id);
        $nombre = $labor->nombre;
        $labor->delete();

        HistorialProceso::create([
            'usuario_id' => Auth::id(),
            'tabla_afectada' => 'catalogo_labores',
            'registro_id' => $id,
            'accion' => 'DELETE',
            'descripcion' => "Labor '{$nombre}' eliminada del catálogo."
        ]);

        session()->flash('status', 'Labor eliminada del catálogo maestro.');
    }

    public function render()
    {
        $labores = CatalogoLabor::where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('categoria')
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.admin.catalogo-labores-manager', [
            'labores' => $labores
        ]);
    }
}

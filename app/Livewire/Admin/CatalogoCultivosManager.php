<?php

namespace App\Livewire\Admin;

use App\Models\CatalogoCultivo;
use App\Models\ArchivoMultimedia;
use App\Services\AgroStorageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CatalogoCultivosManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';

    // Formulario
    public $catId = null;
    public $nombre = '';
    public $nombre_cientifico = '';
    public $tipo_ciclo = 'ciclo_corto';
    public $vida_util_estimada_meses = '';
    public $dias_a_cosecha_promedio = '';
    public $instrucciones_base_riego = '';
    public $instrucciones_base_plagas = '';
    public $es_personalizado = 0;
    public $photo;
    public $currentPhotoPath;

    protected $queryString = ['search'];

    public function resetForm()
    {
        $this->reset([
            'catId', 'nombre', 'nombre_cientifico', 'tipo_ciclo', 'vida_util_estimada_meses',
            'dias_a_cosecha_promedio', 'instrucciones_base_riego', 'instrucciones_base_plagas',
            'es_personalizado', 'photo', 'currentPhotoPath'
        ]);
        $this->tipo_ciclo = 'ciclo_corto';
    }

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:100|unique:catalogo_cultivos,nombre,' . $this->catId,
            'tipo_ciclo' => 'required|in:ciclo_corto,perenne',
            'photo' => 'nullable|image|max:2048',
        ]);

        $photoPath = $this->currentPhotoPath;
        if ($this->photo) {
            $fileData = AgroStorageService::storeUserFile($this->photo, Auth::user(), 'catalogo');
            $photoPath = $fileData['ruta_completa'];
            ArchivoMultimedia::create($fileData);
        }

        $data = [
            'nombre' => $this->nombre,
            'nombre_cientifico' => $this->nombre_cientifico,
            'tipo_ciclo' => $this->tipo_ciclo,
            'vida_util_estimada_meses' => $this->vida_util_estimada_meses ?: null,
            'dias_a_cosecha_promedio' => $this->dias_a_cosecha_promedio ?: null,
            'instrucciones_base_riego' => $this->instrucciones_base_riego,
            'instrucciones_base_plagas' => $this->instrucciones_base_plagas,
            'es_personalizado' => $this->es_personalizado,
            'foto_path' => $photoPath,
            'usuario_creador_id' => Auth::id(),
        ];

        if ($this->catId) {
            CatalogoCultivo::find($this->catId)->update($data);
            $msg = "Cultivo del catálogo actualizado.";
        } else {
            CatalogoCultivo::create($data);
            $msg = "Nuevo tipo de cultivo añadido al catálogo.";
        }

        $this->dispatch('close-modal', 'modal-cat-cultivo');
        $this->resetForm();
        session()->flash('status', $msg);
    }

    public function edit($id)
    {
        $c = CatalogoCultivo::findOrFail($id);
        $this->catId = $c->id;
        $this->nombre = $c->nombre;
        $this->nombre_cientifico = $c->nombre_cientifico;
        $this->tipo_ciclo = $c->tipo_ciclo;
        $this->vida_util_estimada_meses = $c->vida_util_estimada_meses;
        $this->dias_a_cosecha_promedio = $c->dias_a_cosecha_promedio;
        $this->instrucciones_base_riego = $c->instrucciones_base_riego;
        $this->instrucciones_base_plagas = $c->instrucciones_base_plagas;
        $this->es_personalizado = $c->es_personalizado;
        $this->currentPhotoPath = $c->foto_path;

        $this->dispatch('open-modal', 'modal-cat-cultivo');
    }

    public function delete($id)
    {
        CatalogoCultivo::findOrFail($id)->delete();
        session()->flash('status', 'Cultivo eliminado del catálogo.');
    }

    public function render()
    {
        $cultivos = CatalogoCultivo::where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('nombre')
            ->paginate(12);

        return view('livewire.admin.catalogo-cultivos-manager', [
            'cultivos' => $cultivos
        ]);
    }
}

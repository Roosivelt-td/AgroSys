<?php

namespace App\Livewire\Admin;

use App\Models\Cultivo;
use App\Models\Terreno;
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
class CultivosManager extends Component
{
    use WithPagination, WithFileUploads;

    // Filtros
    public $searchTerreno = '';
    public $searchCultivo = '';
    public $searchVariedad = '';
    public $filterStatus = '';
    public $filterTerrenoId = null; // Para el enlace desde la tarjeta

    // Formulario
    public $cropId = null;
    public $terreno_id = '';
    public $catalogo_cultivo_id = '';
    public $nombre_lote = '';
    public $variedad = '';
    public $fecha_planificada = '';
    public $fecha_siembra = '';
    public $fecha_cosecha_estimada = '';
    public $fecha_cosecha_finalizada = '';
    public $estado = 'En crecimiento';
    public $area_destinada = '';
    public $plantas_estimadas = '';
    public $rendimiento_esperado_tn_ha = '';
    public $observaciones = '';
    public $cropPhoto;
    public $currentPhotoPath;

    public $queryTerreno = '';
    public $queryCultivo = '';
    public $showTerrenos = false;
    public $showCultivos = false;
    public $areaDisponible = 0;
    public $terrenoNombreSelected = '';
    public $cultivoNombreSelected = '';

    protected $queryString = ['searchTerreno', 'searchCultivo', 'searchVariedad', 'filterStatus', 'filterTerrenoId'];

    public function mount()
    {
        $this->fecha_planificada = date('Y-m-d');
        $this->fecha_siembra = date('Y-m-d');
    }

    public function filterByTerreno($id)
    {
        $this->resetFilters();
        $this->filterTerrenoId = $id;
        $t = Terreno::find($id);
        $this->searchTerreno = $t->nombre;
    }

    public function selectTerreno($id, $nombre, $disponible)
    {
        $this->terreno_id = $id;
        $this->terrenoNombreSelected = strtoupper($nombre);
        $this->areaDisponible = $disponible;
        $this->queryTerreno = '';
        $this->generarNombreLote();
    }

    public function selectCultivo($id, $nombre)
    {
        $this->catalogo_cultivo_id = $id;
        $this->cultivoNombreSelected = strtoupper($nombre);
        $this->queryCultivo = '';
        $this->generarNombreLote();
    }

    protected function generarNombreLote()
    {
        if ($this->catalogo_cultivo_id && $this->terreno_id) {
            $cultivoNombre = CatalogoCultivo::find($this->catalogo_cultivo_id)?->nombre;
            $num = Cultivo::where('terreno_id', $this->terreno_id)->count() + 1;
            $fecha = $this->fecha_siembra ? date('d-m-Y', strtotime($this->fecha_siembra)) : date('d-m-Y');
            $this->nombre_lote = "{$num}-" . strtoupper($cultivoNombre) . "-" . strtoupper($this->variedad ?: 'GENERICA') . "-{$fecha}";
        }
    }

    public function resetForm()
    {
        $this->reset([
            'cropId', 'terreno_id', 'catalogo_cultivo_id', 'nombre_lote', 'variedad',
            'fecha_planificada', 'fecha_siembra', 'fecha_cosecha_estimada', 'fecha_cosecha_finalizada',
            'estado', 'area_destinada', 'plantas_estimadas', 'rendimiento_esperado_tn_ha',
            'observaciones', 'cropPhoto', 'currentPhotoPath', 'areaDisponible',
            'terrenoNombreSelected', 'cultivoNombreSelected', 'queryTerreno', 'queryCultivo'
        ]);
        $this->fecha_planificada = date('Y-m-d');
        $this->fecha_siembra = date('Y-m-d');
        $this->estado = 'En crecimiento';
    }

    public function save()
    {
        $this->validate([
            'terreno_id' => 'required',
            'catalogo_cultivo_id' => 'required',
            'nombre_lote' => 'required',
            'area_destinada' => "required|numeric|min:0.01|max:{$this->areaDisponible}",
            'fecha_siembra' => 'required|date',
        ]);

        $user = Auth::user();
        $photoPath = $this->currentPhotoPath;

        if ($this->cropPhoto) {
            $fileData = AgroStorageService::storeUserFile($this->cropPhoto, $user, 'cultivo');
            $photoPath = $fileData['ruta_completa'];
            ArchivoMultimedia::create($fileData);
        }

        $data = [
            'terreno_id' => $this->terreno_id,
            'catalogo_cultivo_id' => $this->catalogo_cultivo_id,
            'nombre_lote' => $this->nombre_lote,
            'variedad' => $this->variedad,
            'fecha_planificada' => $this->fecha_planificada,
            'fecha_siembra' => $this->fecha_siembra,
            'fecha_cosecha_estimada' => $this->fecha_cosecha_estimada ?: null,
            'fecha_cosecha_finalizada' => $this->fecha_cosecha_finalizada ?: null,
            'estado' => $this->estado,
            'area_destinada' => $this->area_destinada,
            'plantas_estimadas' => $this->plantas_estimadas ?: 0,
            'rendimiento_esperado_tn_ha' => $this->rendimiento_esperado_tn_ha ?: 0,
            'observaciones' => $this->observaciones,
            'foto_path' => $photoPath,
        ];

        if ($this->cropId) {
            Cultivo::find($this->cropId)->update($data);
            $msg = "Campaña actualizada.";
        } else {
            Cultivo::create($data);
            $msg = "Siembra registrada.";
        }

        $this->dispatch('close-modal', 'modal-crop-manager');
        $this->resetForm();
        session()->flash('status', $msg);
    }

    public function edit($id)
    {
        $c = Cultivo::findOrFail($id);
        $this->cropId = $c->id;
        $this->terreno_id = $c->terreno_id;
        $this->catalogo_cultivo_id = $c->catalogo_cultivo_id;
        $this->nombre_lote = $c->nombre_lote;
        $this->variedad = $c->variedad;
        $this->fecha_planificada = $c->fecha_planificada;
        $this->fecha_siembra = $c->fecha_siembra;
        $this->fecha_cosecha_estimada = $c->fecha_cosecha_estimada;
        $this->fecha_cosecha_finalizada = $c->fecha_cosecha_finalizada;
        $this->estado = $c->estado;
        $this->area_destinada = $c->area_destinada;
        $this->plantas_estimadas = $c->plantas_estimadas;
        $this->rendimiento_esperado_tn_ha = $c->rendimiento_esperado_tn_ha;
        $this->observaciones = $c->observaciones;
        $this->currentPhotoPath = $c->foto_path;

        $t = Terreno::find($c->terreno_id);
        $this->terrenoNombreSelected = strtoupper($t->nombre);
        $this->cultivoNombreSelected = strtoupper($c->detalleCatalogo->nombre);

        $areaOcupada = Cultivo::where('terreno_id', $c->terreno_id)->whereIn('estado', ['Planificado', 'En crecimiento'])->where('id', '!=', $c->id)->sum('area_destinada');
        $this->areaDisponible = $t->hectareas - $areaOcupada;

        $this->dispatch('open-modal', 'modal-crop-manager');
    }

    public function delete($id)
    {
        Cultivo::findOrFail($id)->delete();
        session()->flash('status', 'Cultivo eliminado.');
    }

    public function render()
    {
        $query = Cultivo::whereHas('terreno', function($q) {
            $q->where('usuario_id', Auth::id());
        })->with(['terreno', 'detalleCatalogo', 'labores']);

        if ($this->filterTerrenoId) {
            $query->where('terreno_id', $this->filterTerrenoId);
        } else {
            if ($this->searchTerreno) $query->whereHas('terreno', fn($q) => $q->where('nombre', 'like', "%{$this->searchTerreno}%"));
            if ($this->searchCultivo) $query->whereHas('detalleCatalogo', fn($q) => $q->where('nombre', 'like', "%{$this->searchCultivo}%"));
            if ($this->searchVariedad) $query->where('variedad', 'like', "%{$this->searchVariedad}%");
            if ($this->filterStatus) $query->where('estado', $this->filterStatus);
        }

        $cultivos = $query->orderBy('fecha_siembra', 'desc')->paginate(12);

        // Inyectar inversión total en cada modelo
        $cultivos->getCollection()->transform(function($c) {
            $c->total_inversion = $c->labores->sum('costo_total');
            return $c;
        });

        // Búsqueda dinámica para el Modal
        $resultsTerrenos = [];
        if (strlen($this->queryTerreno) > 0) {
            $resultsTerrenos = Terreno::where('usuario_id', Auth::id())
                ->where('nombre', 'like', "%{$this->queryTerreno}%")
                ->get()->map(function($t) {
                    $ocupado = Cultivo::where('terreno_id', $t->id)->whereIn('estado', ['Planificado', 'En crecimiento'])->where('id', '!=', $this->cropId)->sum('area_destinada');
                    $t->disponible = $t->hectareas - $ocupado;
                    return $t;
                });
        }

        $resultsCatalogo = [];
        if (strlen($this->queryCultivo) > 0) {
            $resultsCatalogo = CatalogoCultivo::where('nombre', 'like', "%{$this->queryCultivo}%")->get();
        }

        $selTerreno = $this->terreno_id ? Terreno::find($this->terreno_id) : null;
        $selCultivo = $this->catalogo_cultivo_id ? CatalogoCultivo::find($this->catalogo_cultivo_id) : null;

        return view('livewire.admin.cultivos-manager', [
            'cultivos' => $cultivos,
            'resultsTerrenos' => $resultsTerrenos,
            'resultsCatalogo' => $resultsCatalogo,
            'selectedTerrenoModel' => $selTerreno,
            'selectedCultivoModel' => $selCultivo,
            'totalArea' => $query->sum('area_destinada'),
            'totalCount' => $query->count(),
            'misTerrenos' => Terreno::where('usuario_id', Auth::id())->get(),
            'catalogo' => CatalogoCultivo::all()
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['searchTerreno', 'searchCultivo', 'searchVariedad', 'filterStatus', 'filterTerrenoId']);
    }
}

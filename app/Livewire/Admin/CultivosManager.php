<?php

namespace App\Livewire\Admin;

use App\Models\Cultivo;
use App\Models\Terreno;
use App\Models\CatalogoCultivo;
use App\Models\MiembroOrganizacion;
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
    public $filterDateStart = '';
    public $filterDateEnd = '';
    public $chartType = 'hist_tn';
    public $filterTerrenoId = null; // Para el enlace desde la tarjeta
    public $selectedOrgId = null;

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

    protected $queryString = [
        'searchTerreno', 'searchCultivo', 'searchVariedad',
        'filterStatus', 'filterTerrenoId', 'filterDateStart', 'filterDateEnd', 'chartType'
    ];

    public function mount()
    {
        $this->fecha_planificada = date('Y-m-d');
        $this->fecha_siembra = date('Y-m-d');

        // Detectar organización activa
        $membresia = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->where('estado', 1)
            ->first();

        if ($membresia) {
            $this->selectedOrgId = $membresia->organizacion_id;
        }
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

    public function updatedVariedad()
    {
        $this->generarNombreLote();
    }

    public function updatedFechaSiembra()
    {
        $this->generarNombreLote();
    }

    protected function generarNombreLote()
    {
        if ($this->catalogo_cultivo_id && $this->terreno_id) {
            $cultivoNombre = CatalogoCultivo::find($this->catalogo_cultivo_id)?->nombre;
            // Usamos el ID actual si existe (edición), o calculamos el próximo ID disponible (creación)
            $idLabel = $this->cropId ?: (Cultivo::max('id') + 1);
            $fecha = $this->fecha_siembra ? date('d-m-Y', strtotime($this->fecha_siembra)) : date('d-m-Y');
            $this->nombre_lote = "{$idLabel}-" . strtoupper($cultivoNombre) . "-" . strtoupper($this->variedad ?: 'GENERICA') . "-{$fecha}";
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
            'plantas_estimadas' => 'nullable|integer|min:0',
            'rendimiento_esperado_tn_ha' => 'nullable|numeric|min:0',
            'cropPhoto' => 'nullable|image|max:5120',
        ]);

        $user = Auth::user();
        $photoPath = $this->currentPhotoPath;

        if ($this->cropPhoto) {
            $fileData = AgroStorageService::storeUserFile($this->cropPhoto, $user, 'cultivo', $this->selectedOrgId);
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
        $user = Auth::user();
        $allowedUserIds = [$user->id];

        // Lógica de visibilidad
        if ($this->selectedOrgId) {
            $miembro = $user->membresias()->where('organizacion_id', $this->selectedOrgId)->where('estado', 1)->first();

            if ($user->rol_id === 1) {
                // Super Admin ve todo de la organización
                $query = Cultivo::whereHas('terreno', function($q) {
                    $q->where('organizacion_id', $this->selectedOrgId);
                });
            } elseif ($miembro) {
                $esAdmin = $miembro->roles()->whereHas('rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->where('estado', 1)->exists();
                $esSupervisor = $miembro->roles()->whereHas('rolDetalle', fn($q) => $q->where('nombre', 'Supervisor'))->where('estado', 1)->exists();

                if ($esAdmin) {
                    $query = Cultivo::whereHas('terreno', function($q) {
                        $q->where('organizacion_id', $this->selectedOrgId);
                    });
                } elseif ($esSupervisor) {
                    $assignedIds = $user->getIdsAgricultoresAsignados($this->selectedOrgId);
                    $allowedUserIds = array_merge($allowedUserIds, $assignedIds);
                    $query = Cultivo::whereHas('terreno', function($q) use ($allowedUserIds) {
                        $q->whereIn('usuario_id', $allowedUserIds)->where('organizacion_id', $this->selectedOrgId);
                    });
                } else {
                    $query = Cultivo::whereHas('terreno', function($q) use ($user) {
                        $q->where('usuario_id', $user->id)->where('organizacion_id', $this->selectedOrgId);
                    });
                }
            } else {
                $query = Cultivo::whereHas('terreno', function($q) use ($user) {
                    $q->where('usuario_id', $user->id);
                });
            }
        } else {
            $query = Cultivo::whereHas('terreno', function($q) use ($user) {
                $q->where('usuario_id', $user->id);
            });
        }

        $query->with(['terreno', 'detalleCatalogo', 'labores']);

        if ($this->filterTerrenoId) {
            $query->where('terreno_id', $this->filterTerrenoId);
        } else {
            if ($this->searchTerreno) $query->whereHas('terreno', fn($q) => $q->where('nombre', 'like', "%{$this->searchTerreno}%"));
            if ($this->searchCultivo) $query->whereHas('detalleCatalogo', fn($q) => $q->where('nombre', 'like', "%{$this->searchCultivo}%"));
            if ($this->searchVariedad) $query->where('variedad', 'like', "%{$this->searchVariedad}%");
            if ($this->filterStatus) $query->where('estado', $this->filterStatus);
            if ($this->filterDateStart) $query->where('fecha_siembra', '>=', $this->filterDateStart);
            if ($this->filterDateEnd) $query->where('fecha_siembra', '<=', $this->filterDateEnd);
        }

        // Clonamos para totales y Chart
        $totalQuery = clone $query;
        $cultivos = $query->orderBy('fecha_siembra', 'desc')->paginate(12);

        // --- Lógica del Chart Dinámico (agrosys_cultivos_v2) ---
        $chartData = [
            'labels' => [],
            'values' => [],
            'title' => '',
            'unit' => ($this->chartType === 'hist_inv' || $this->chartType === 'real_inv') ? 'S/' : 'Tn/ha'
        ];

        // Definir título exacto según selección
        $titulos = [
            'hist_tn' => 'Rendimiento histórico por cultivo (Tn/ha)',
            'hist_inv' => 'Rendimiento histórico por cultivo (Inversión/Ganancia)',
            'real_inv' => 'Rendimiento histórico real por cultivo (Inversión/Ganancia)',
            'real_tn' => 'Rendimiento histórico real por cultivo (Tn/ha)'
        ];
        $chartData['title'] = $titulos[$this->chartType] ?? $titulos['hist_tn'];

        // Filtrado adicional para tipos "Real" (solo cosechados, en proceso o perdidos)
        $chartQuery = clone $totalQuery;
        if (str_contains($this->chartType, 'real')) {
            $chartQuery->whereIn('estado', ['En crecimiento', 'Cosechado', 'Perdido']);
        }

        $groupedData = $chartQuery->get()->groupBy('detalleCatalogo.nombre');
        foreach ($groupedData as $nombre => $items) {
            $chartData['labels'][] = $nombre;

            if ($chartData['unit'] === 'S/') {
                // Cálculo de inversión vs ganancia
                $chartData['values'][] = $items->map(fn($c) => $c->labores->sum('costo_total'))->avg();
            } else {
                $chartData['values'][] = $items->avg('rendimiento_esperado_tn_ha') ?: 0;
            }
        }

        // Inyectar inversión total en cada modelo
        $cultivos->getCollection()->transform(function($c) {
            $c->total_inversion = $c->labores->sum('costo_total');
            return $c;
        });

        // Búsqueda dinámica para el Modal (debe respetar visibilidad)
        $terrenoQuery = Terreno::query();

        if ($user->rol_id === 1 && $this->selectedOrgId) {
            $terrenoQuery->where('organizacion_id', $this->selectedOrgId);
        } elseif ($this->selectedOrgId && isset($esAdmin) && $esAdmin) {
            $terrenoQuery->where('organizacion_id', $this->selectedOrgId);
        } elseif ($this->selectedOrgId && isset($esSupervisor) && $esSupervisor) {
            $terrenoQuery->whereIn('usuario_id', $allowedUserIds)->where('organizacion_id', $this->selectedOrgId);
        } else {
            $terrenoQuery->where('usuario_id', $user->id);
        }

        if (strlen(trim($this->queryTerreno)) > 0) {
            $terrenoQuery->where('nombre', 'like', "%" . trim($this->queryTerreno) . "%");
        }

        $resultsTerrenos = $terrenoQuery->take(10)->get()->map(function($t) {
            $ocupado = Cultivo::where('terreno_id', $t->id)->whereIn('estado', ['Planificado', 'En crecimiento'])->where('id', '!=', $this->cropId)->sum('area_destinada');
            $t->disponible = max(0, $t->hectareas - $ocupado);
            return $t;
        });

        $resultsCatalogo = [];
        if (strlen(trim($this->queryCultivo)) > 0) {
            $resultsCatalogo = CatalogoCultivo::where('nombre', 'like', '%' . trim($this->queryCultivo) . '%')
                ->orWhere('nombre_cientifico', 'like', '%' . trim($this->queryCultivo) . '%')
                ->take(10)
                ->get();
        } else {
            // Mostrar los primeros 10 si no hay búsqueda para que la lista no esté vacía al abrir
            $resultsCatalogo = CatalogoCultivo::take(10)->get();
        }

        $selTerreno = $this->terreno_id ? Terreno::find($this->terreno_id) : null;
        $selCultivo = $this->catalogo_cultivo_id ? CatalogoCultivo::find($this->catalogo_cultivo_id) : null;

        $filteredTerreno = $this->filterTerrenoId ? Terreno::find($this->filterTerrenoId) : null;

        // Obtener solo el catálogo de cultivos que el usuario ha utilizado realmente
        $misCultivosIds = Cultivo::whereHas('terreno', fn($q) => $q->where('usuario_id', Auth::id()))
            ->pluck('catalogo_cultivo_id')
            ->unique();

        return view('livewire.admin.cultivos-manager', [
            'cultivos' => $cultivos,
            'resultsTerrenos' => $resultsTerrenos,
            'resultsCatalogo' => $resultsCatalogo,
            'selectedTerrenoModel' => $selTerreno,
            'selectedCultivoModel' => $selCultivo,
            'filteredTerreno' => $filteredTerreno,
            'chartData' => $chartData,
            'totalArea' => $totalQuery->sum('area_destinada'),
            'totalCount' => $totalQuery->count(),
            'misTerrenos' => Terreno::where('usuario_id', Auth::id())->get(),
            'catalogo' => CatalogoCultivo::whereIn('id', $misCultivosIds)->get()
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['searchTerreno', 'searchCultivo', 'searchVariedad', 'filterStatus', 'filterTerrenoId', 'filterDateStart', 'filterDateEnd']);
    }
}

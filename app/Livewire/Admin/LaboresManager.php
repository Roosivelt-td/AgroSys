<?php

namespace App\Livewire\Admin;

use App\Models\Labor;
use App\Models\Cultivo;
use App\Models\CatalogoLabor;
use App\Models\CatalogoCultivo;
use App\Models\Terreno;
use App\Models\MiembroOrganizacion;
use App\Models\ArchivoMultimedia;
use App\Services\AgroStorageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class LaboresManager extends Component
{
    use WithPagination, WithFileUploads;

    // Filtros
    public $searchType = '';
    public $filterCropId = '';
    public $filterStatus = '';
    public $filterDateStart = '';
    public $filterDateEnd = '';
    public $selectedOrgId = null;

    // Formulario
    public $laborId = null;
    public $cultivo_id = '';
    public $catalogo_labor_id = '';
    public $fecha_realizacion = '';
    public $costo_mano_obra_total = 0;
    public $costo_maquinaria_total = 0;
    public $costo_total = 0;
    public $estado = 'Pendiente';
    public $observaciones = '';
    public $laborPhoto;
    public $currentPhotoPath;

    // Registros Dinámicos (agrosys_labores_v3)
    public $itemsInsumos = [];
    public $itemsManoObra = [];
    public $itemsMaquinaria = [];

    // Búsqueda Secuencial
    public $queryCat = '';
    public $queryVar = '';
    public $queryLand = '';
    public $queryCrop = '';
    public $queryIns = '';
    public $queryProv = '';
    public $activeIdx = null; // Para saber qué fila de insumo se busca

    public $selStatus = '';
    public $selCatId = null;
    public $selVarName = '';
    public $selLandId = null;
    public $catNombreSelected = '';
    public $landNombreSelected = '';
    public $cropNombreSelected = '';
    public $cropFechaPlanificada = null;
    public $cropEstado = '';

    public $showCats = false;
    public $showVars = false;
    public $showLands = false;
    public $showCrops = false;
    public $showIns = false;
    public $showProv = false;
    public $showStatus = false;

    // Registro Rápido Proveedor
    public $newProvNombre = '';
    public $newProvRuc = '';
    public $newProvTelf = '';
    public $newProvTipo = '';
    public $currentInsumoIdx = null;

    // Lógica de Pasos (agrosys_labores_v2)
    public $step = 1; // 1: Selección, 2: Formulario
    public $laborStatusMap = [];

    protected $queryString = ['searchType', 'filterCropId', 'filterStatus', 'filterDateStart', 'filterDateEnd'];

    public function mount()
    {
        $this->fecha_realizacion = date('Y-m-d');
        $membresia = MiembroOrganizacion::where('usuario_id', Auth::id())->where('estado', 1)->first();
        if ($membresia) $this->selectedOrgId = $membresia->organizacion_id;
        $this->checkLaborAvailability($this->cultivo_id);
    }

    public function openAddProvider($idx = null)
    {
        $this->currentInsumoIdx = $idx;
        $this->reset(['newProvNombre', 'newProvRuc', 'newProvTelf', 'newProvTipo']);
        $this->dispatch('open-modal', 'modal-add-provider');
    }

    public function saveQuickProvider()
    {
        $this->validate([
            'newProvNombre' => 'required|string|max:200',
            'newProvRuc' => 'nullable|string|max:20',
            'newProvTelf' => 'nullable|string|max:20',
        ]);

        $prov = \App\Models\Proveedor::create([
            'nombre_empresa' => $this->newProvNombre,
            'ruc' => $this->newProvRuc,
            'telefono' => $this->newProvTelf,
            'tipo_servicio' => $this->newProvTipo ?: 'Insumos',
        ]);

        if ($this->currentInsumoIdx !== null) {
            $this->itemsInsumos[$this->currentInsumoIdx]['proveedor_id'] = $prov->id;
            $this->itemsInsumos[$this->currentInsumoIdx]['proveedor_nombre'] = $prov->nombre_empresa;
        }

        $this->dispatch('close-modal', 'modal-add-provider');
        session()->flash('status_prov', "Proveedor registrado correctamente.");
    }

    public function checkLaborAvailability($cropId)
    {
        if (!$cropId) {
            $this->laborStatusMap = [
                'Preparar' => true,
                'Otros' => true,
                'Siembra' => false,
                'Riego' => false,
                'Fumigar' => false,
                'Aporque' => false,
                'Desierbe' => false,
                'Deshierbe' => false,
                'Abonar' => false,
                'Cosechar' => false
            ];
            return;
        }
        $tienePre = Labor::where('cultivo_id', $cropId)->whereHas('detalleCatalogo', fn($q) => $q->where('nombre', 'Preparar'))->where('estado', 'Completada')->exists();
        $tieneSie = Labor::where('cultivo_id', $cropId)->whereHas('detalleCatalogo', fn($q) => $q->where('nombre', 'Siembra'))->where('estado', 'Completada')->exists();

        $this->laborStatusMap = [
            'Preparar' => true,
            'Otros' => true,
            'Siembra' => $tienePre,
            'Riego' => $tieneSie,
            'Fumigar' => $tieneSie,
            'Aporque' => $tieneSie,
            'Desierbe' => ($tienePre && $tieneSie),
            'Deshierbe' => ($tienePre && $tieneSie),
            'Abonar' => $tieneSie,
            'Cosechar' => $tieneSie
        ];
    }

    public function selectLaborType($catId)
    {
        $cat = CatalogoLabor::find($catId);
        if ($this->cultivo_id) {
            $this->checkLaborAvailability($this->cultivo_id);
            if (isset($this->laborStatusMap[$cat->nombre]) && !$this->laborStatusMap[$cat->nombre]) {
                session()->flash('error', "Fase previa pendiente."); return;
            }
        }
        $this->catalogo_labor_id = $catId;
        $this->step = 2;
    }

    public function resetForm()
    {
        $this->reset(['laborId', 'cultivo_id', 'catalogo_labor_id', 'costo_mano_obra_total', 'costo_maquinaria_total', 'costo_total', 'estado', 'observaciones', 'laborPhoto', 'currentPhotoPath', 'queryCat', 'queryVar', 'queryLand', 'queryCrop', 'selStatus', 'selCatId', 'selVarName', 'selLandId', 'catNombreSelected', 'landNombreSelected', 'cropNombreSelected', 'cropFechaPlanificada', 'cropEstado', 'step']);
        $this->fecha_realizacion = date('Y-m-d');
        $this->estado = 'Pendiente';
        $this->step = 1;
        $this->checkLaborAvailability(null);
    }

    public function resetDownstreamData()
    {
        $this->itemsInsumos = [];
        $this->itemsManoObra = [];
        $this->itemsMaquinaria = [];
        $this->calculateTotals();
    }

    public function selectStatus($status) {
        $this->selStatus = $status;
        $this->reset(['selLandId', 'landNombreSelected', 'selCatId', 'catNombreSelected', 'selVarName', 'cultivo_id', 'cropNombreSelected', 'cropFechaPlanificada', 'cropEstado']);
        $this->checkLaborAvailability(null);
        $this->resetDownstreamData();
    }

    public function selectLand($id, $nombre) {
        $this->selLandId = $id;
        $this->landNombreSelected = strtoupper($nombre);
        $this->reset(['selCatId', 'catNombreSelected', 'selVarName', 'cultivo_id', 'cropNombreSelected', 'cropFechaPlanificada', 'cropEstado']);
        $this->checkLaborAvailability(null);
        $this->resetDownstreamData();
    }

    public function selectCat($id, $nombre) {
        $this->selCatId = $id;
        $this->catNombreSelected = strtoupper($nombre);
        $this->reset(['selVarName', 'cultivo_id', 'cropNombreSelected', 'cropFechaPlanificada', 'cropEstado']);
        $this->checkLaborAvailability(null);
        $this->resetDownstreamData();
    }

    public function selectCrop($id, $nombre) {
        if (!$id) {
            $this->reset(['cultivo_id', 'cropNombreSelected', 'cropFechaPlanificada', 'cropEstado']);
            $this->checkLaborAvailability(null);
            $this->resetDownstreamData();
            return;
        }
        $this->cultivo_id = $id;
        $this->cropNombreSelected = strtoupper($nombre);
        $c = Cultivo::find($id);
        if ($c) {
            $this->cropFechaPlanificada = $c->fecha_planificada;
            $this->cropEstado = $c->estado;
            $this->selVarName = $c->variedad;
        }
        $this->checkLaborAvailability($id);
    }

    public function searchInsumo($idx, $val) {
        $this->activeIdx = $idx;
        $this->queryIns = $val;
        $this->showIns = true;
        $this->showProv = false;
        $this->itemsInsumos[$idx]['insumo_id'] = null; // Reset ID if typing
    }

    public function searchProveedor($idx, $val) {
        $this->activeIdx = $idx;
        $this->queryProv = $val;
        $this->showProv = true;
        $this->showIns = false;
        $this->itemsInsumos[$idx]['proveedor_id'] = null; // Reset ID if typing
    }

    public function selectInsumoItem($idx, $id, $nombre) {
        $this->itemsInsumos[$idx]['insumo_id'] = $id;
        $this->itemsInsumos[$idx]['insumo_nombre'] = $nombre;
        $this->showIns = false;
        $this->activeIdx = null;
    }

    public function selectProvItem($idx, $id, $nombre) {
        $this->itemsInsumos[$idx]['proveedor_id'] = $id;
        $this->itemsInsumos[$idx]['proveedor_nombre'] = $nombre;
        $this->showProv = false;
        $this->activeIdx = null;
    }

    // --- MÉTODOS DINÁMICOS (agrosys_labores_v3) ---

    public function addItemInsumo() {
        $this->itemsInsumos[] = [
            'id' => '',
            'insumo_id' => '',
            'insumo_nombre' => '',
            'proveedor_id' => '',
            'proveedor_nombre' => '',
            'cantidad' => 1,
            'costo_unitario' => 0,
            'costo_flete' => 0,
            'habilitar_proveedor' => false
        ];
    }

    public function addItemManoObra() {
        $this->itemsManoObra[] = ['id' => '', 'tipo_id' => '', 'tipo_nombre' => '', 'cantidad' => 1, 'dias' => 1, 'costo_dia' => 0];
    }

    public function addItemMaquinaria() {
        $this->itemsMaquinaria[] = ['id' => '', 'nombre' => '', 'labor' => '', 'horas' => 1, 'costo_total' => 0];
    }

    public function removeItem($type, $index) {
        if ($type === 'insumo') unset($this->itemsInsumos[$index]);
        if ($type === 'mano') unset($this->itemsManoObra[$index]);
        if ($type === 'maq') unset($this->itemsMaquinaria[$index]);
        $this->itemsInsumos = array_values($this->itemsInsumos);
        $this->itemsManoObra = array_values($this->itemsManoObra);
        $this->itemsMaquinaria = array_values($this->itemsMaquinaria);
        $this->calculateTotals();
    }

    public function calculateTotals() {
        $this->costo_mano_obra_total = 0;
        foreach($this->itemsManoObra as $item) {
            $this->costo_mano_obra_total += ($item['cantidad'] * $item['dias'] * $item['costo_dia']);
        }

        $this->costo_maquinaria_total = 0;
        foreach($this->itemsMaquinaria as $item) {
            $this->costo_maquinaria_total += $item['costo_total'];
        }

        $insumosTotal = 0;
        foreach($this->itemsInsumos as $item) {
            $insumosTotal += ($item['cantidad'] * $item['costo_unitario']) + $item['costo_flete'];
        }

        $this->costo_total = $this->costo_mano_obra_total + $this->costo_maquinaria_total + $insumosTotal;
    }

    public function save()
    {
        $this->validate([
            'cultivo_id' => 'required',
            'catalogo_labor_id' => 'required',
            'fecha_realizacion' => 'required|date|before_or_equal:today' . ($this->cropFechaPlanificada ? '|after_or_equal:'.$this->cropFechaPlanificada : ''),
            'costo_total' => 'required|numeric',
            'laborPhoto' => 'nullable|file|max:20480',
        ], [
            'fecha_realizacion.before_or_equal' => 'No puedes registrar labores futuras.',
            'fecha_realizacion.after_or_equal' => 'La fecha no puede ser anterior a la planificación del cultivo ('.$this->cropFechaPlanificada.').',
        ]);

        return DB::transaction(function() {
            $user = Auth::user();
            $path = $this->currentPhotoPath;
            if ($this->laborPhoto) {
                $fileData = AgroStorageService::storeUserFile($this->laborPhoto, $user, 'labor', $this->selectedOrgId);
                $path = $fileData['ruta_completa'];
                ArchivoMultimedia::create($fileData);
            }
            $data = [
                'cultivo_id' => $this->cultivo_id,
                'catalogo_labor_id' => $this->catalogo_labor_id,
                'fecha_realizacion' => $this->fecha_realizacion,
                'costo_mano_obra_total' => $this->costo_mano_obra_total,
                'costo_maquinaria_total' => $this->costo_maquinaria_total,
                'costo_total' => $this->costo_total,
                'estado' => $this->estado,
                'observaciones' => $this->observaciones,
                'foto_path' => $path
            ];

            if ($this->laborId) {
                $labor = Labor::find($this->laborId);
                $labor->update($data);
                // Limpiar hijos para re-insertar (o actualizar)
                $labor->insumos()->delete();
                $labor->manoDeObra()->delete();
                $labor->maquinaria()->delete();
            } else {
                $labor = Labor::create($data);
            }

            // Insertar Insumos
            foreach($this->itemsInsumos as $item) {
                if ($item['insumo_id']) {
                    $labor->insumos()->create([
                        'catalogo_insumo_id' => $item['insumo_id'],
                        'proveedor_id' => $item['proveedor_id'] ?: null,
                        'cantidad' => $item['cantidad'],
                        'costo_unitario' => $item['costo_unitario'],
                        'costo_flete' => $item['costo_flete'],
                    ]);
                }
            }

            // Insertar Mano de Obra
            foreach($this->itemsManoObra as $item) {
                if ($item['tipo_id']) {
                    $labor->manoDeObra()->create([
                        'tipo_id' => $item['tipo_id'],
                        'cantidad_trabajadores' => $item['cantidad'],
                        'dias_trabajados' => $item['dias'],
                        'costo_por_dia' => $item['costo_dia'],
                        'subtotal' => ($item['cantidad'] * $item['dias'] * $item['costo_dia'])
                    ]);
                }
            }

            // Insertar Maquinaria
            foreach($this->itemsMaquinaria as $item) {
                if ($item['nombre']) {
                    $labor->maquinaria()->create([
                        'nombre_maquinaria' => $item['nombre'],
                        'labor_realizada' => $item['labor'],
                        'horas_trabajadas' => $item['horas'],
                        'costo_total' => $item['costo_total']
                    ]);
                }
            }

            $this->dispatch('close-modal', 'modal-labor-manager');
            $this->resetForm();
            session()->flash('status', "Labor y sus componentes registrados.");
        });
    }

    public function render()
    {
        $user = Auth::user();
        $allowedIds = [$user->id];

        // Si es Super Admin y no hay organización seleccionada, permitir ver todo para pruebas
        if ($user->rol_id === 1 && !$this->selectedOrgId) {
            $allowedIds = \App\Models\User::pluck('id')->toArray();
        }

        if ($this->selectedOrgId) {
            $m = $user->membresias()->where('organizacion_id', $this->selectedOrgId)->where('estado', 1)->first();
            if ($user->rol_id === 1 || ($m && $m->roles()->whereHas('rolDetalle', fn($q)=>$q->where('nombre','Administrador'))->exists())) {
                $baseQuery = Labor::whereHas('cultivo.terreno', fn($q)=>$q->where('organizacion_id', $this->selectedOrgId));
            } elseif ($m && $m->roles()->whereHas('rolDetalle', fn($q)=>$q->where('nombre','Supervisor'))->exists()) {
                $allowedIds = array_merge($allowedIds, $user->getIdsAgricultoresAsignados($this->selectedOrgId));
                $baseQuery = Labor::whereHas('cultivo.terreno', fn($q)=>$q->whereIn('usuario_id', $allowedIds)->where('organizacion_id', $this->selectedOrgId));
            } else {
                $baseQuery = Labor::whereHas('cultivo.terreno', fn($q)=>$q->where('usuario_id', $user->id)->where('organizacion_id', $this->selectedOrgId));
            }
        } else {
            $baseQuery = Labor::whereHas('cultivo.terreno', fn($q)=>$q->whereIn('usuario_id', $allowedIds));
        }

        // Aplicar Filtros
        if ($this->searchType) {
            $baseQuery->whereHas('detalleCatalogo', fn($q) => $q->where('nombre', $this->searchType));
        }
        if ($this->filterCropId) {
            $baseQuery->where('cultivo_id', $this->filterCropId);
        }
        if ($this->filterStatus) {
            $baseQuery->where('estado', $this->filterStatus);
        }
        if ($this->filterDateStart) {
            $baseQuery->whereDate('fecha_realizacion', '>=', $this->filterDateStart);
        }
        if ($this->filterDateEnd) {
            $baseQuery->whereDate('fecha_realizacion', '<=', $this->filterDateEnd);
        }

        $labores = (clone $baseQuery)->with(['cultivo.terreno', 'detalleCatalogo'])->orderBy('fecha_realizacion', 'desc')->paginate(12);

        // Mapeo de estados UI a DB
        $dbStatus = $this->selStatus;
        if ($dbStatus === 'En proceso') $dbStatus = 'En crecimiento';
        if ($dbStatus === 'Completada') $dbStatus = 'Cosechado';

        // Resultados Búsqueda Secuencial
        $resultsStatus = ['TODOS', 'En proceso', 'Completada', 'Perdido'];

        $resultsLands = $this->selStatus
            ? Terreno::query()
                ->when(!($user->rol_id === 1 && !$this->selectedOrgId), fn($q) => $q->whereIn('usuario_id', $allowedIds))
                ->when($this->selStatus !== 'TODOS', function($q) use ($dbStatus) {
                    $q->whereHas('cultivos', fn($sq) => $sq->where('estado', $dbStatus));
                }, function($q) {
                    $q->whereHas('cultivos');
                })
                ->where('nombre', 'like', '%'.$this->queryLand.'%')
                ->take(5)->get()
            : [];

        $resultsCats = $this->selLandId
            ? CatalogoCultivo::whereIn('id',
                Cultivo::where('terreno_id', $this->selLandId)
                    ->when($this->selStatus !== 'TODOS', fn($q) => $q->where('estado', $dbStatus))
                    ->pluck('catalogo_cultivo_id')
            )
            ->where('nombre', 'like', '%'.$this->queryCat.'%')
            ->take(5)->get()
            : [];

        $resultsCrops = ($this->selLandId && $this->selCatId)
            ? Cultivo::where('terreno_id', $this->selLandId)
                ->where('catalogo_cultivo_id', $this->selCatId)
                ->when($this->selStatus !== 'TODOS', fn($q) => $q->where('estado', $dbStatus))
                ->where('nombre_lote', 'like', '%'.$this->queryCrop.'%')
                ->with(['terreno', 'detalleCatalogo'])
                ->take(5)->get()
            : [];

        // Resultados Insumos y Proveedores
        $resultsIns = $this->showIns ? \App\Models\CatalogoInsumo::where('nombre','like','%'.$this->queryIns.'%')->take(5)->get() : [];
        $resultsProvs = $this->showProv ? \App\Models\Proveedor::where('nombre_empresa','like','%'.$this->queryProv.'%')->take(5)->get() : [];

        $allLaborsForStats = (clone $baseQuery)->with('detalleCatalogo')->get();
        $costByType = [
            'labels' => $allLaborsForStats->groupBy('detalleCatalogo.nombre')->keys(),
            'values' => $allLaborsForStats->groupBy('detalleCatalogo.nombre')->map->sum('costo_total')->values(),
            'title' => 'Inversión por Labor',
            'unit' => 'S/'
        ];

        return view('livewire.admin.labores-manager', [
            'labores' => $labores,
            'catalogoLabores' => CatalogoLabor::all(),
            'manoObraTipos' => \App\Models\ManoObraTipo::all(),
            'resultsStatus' => $resultsStatus,
            'resultsCats' => $resultsCats,
            'resultsLands' => $resultsLands,
            'resultsCrops' => $resultsCrops,
            'resultsIns' => $resultsIns, 'resultsProvs' => $resultsProvs,
            'misCultivos' => Cultivo::whereHas('terreno', fn($q)=>$q->whereIn('usuario_id', $allowedIds))->get(),
            'stats' => ['total' => $labores->total(), 'costo_total' => $baseQuery->sum('costo_total'), 'pendientes' => (clone $baseQuery)->where('estado','Pendiente')->count(), 'avg_cost' => $labores->total() > 0 ? $baseQuery->avg('costo_total') : 0],
            'costByType' => $costByType
        ]);
    }

    public function resetFilters() { $this->reset(['searchType', 'filterCropId', 'filterStatus', 'filterDateStart', 'filterDateEnd']); }

    public function backToGrid() { $this->step = 1; }

    public function edit($id)
    {
        $labor = Labor::with(['cultivo.terreno', 'detalleCatalogo', 'insumos.catalogoInsumo', 'insumos.proveedor', 'manoDeObra.tipo', 'maquinaria'])->find($id);
        if (!$labor) return;

        $this->resetForm();
        $this->laborId = $labor->id;
        $this->cultivo_id = $labor->cultivo_id;
        $this->catalogo_labor_id = $labor->catalogo_labor_id;
        $this->fecha_realizacion = $labor->fecha_realizacion->format('Y-m-d');
        $this->costo_mano_obra_total = $labor->costo_mano_obra_total;
        $this->costo_maquinaria_total = $labor->costo_maquinaria_total;
        $this->costo_total = $labor->costo_total;
        $this->estado = $labor->estado;
        $this->observaciones = $labor->observaciones;
        $this->currentPhotoPath = $labor->foto_path;

        // Cargar Datos de Búsqueda Secuencial
        $this->selStatus = $labor->cultivo->estado;
        if ($this->selStatus === 'En crecimiento') $this->selStatus = 'En proceso';
        if ($this->selStatus === 'Cosechado') $this->selStatus = 'Completada';

        $this->selLandId = $labor->cultivo->terreno_id;
        $this->landNombreSelected = strtoupper($labor->cultivo->terreno->nombre);
        $this->selCatId = $labor->cultivo->catalogo_cultivo_id;
        $this->catNombreSelected = strtoupper($labor->cultivo->detalleCatalogo->nombre);
        $this->cropNombreSelected = strtoupper($labor->cultivo->nombre_lote);
        $this->cropFechaPlanificada = $labor->cultivo->fecha_planificada;
        $this->cropEstado = $labor->cultivo->estado;
        $this->selVarName = $labor->cultivo->variedad;

        // Cargar Insumos
        foreach ($labor->insumos as $ins) {
            $this->itemsInsumos[] = [
                'id' => $ins->id,
                'insumo_id' => $ins->catalogo_insumo_id,
                'insumo_nombre' => $ins->catalogoInsumo->nombre,
                'proveedor_id' => $ins->proveedor_id,
                'proveedor_nombre' => $ins->proveedor->nombre_empresa ?? '',
                'cantidad' => $ins->cantidad,
                'costo_unitario' => $ins->costo_unitario,
                'costo_flete' => $ins->costo_flete,
            ];
        }

        // Cargar Mano de Obra
        foreach ($labor->manoDeObra as $mo) {
            $this->itemsManoObra[] = [
                'id' => $mo->id,
                'tipo_id' => $mo->tipo_id,
                'tipo_nombre' => $mo->tipo->nombre,
                'cantidad' => $mo->cantidad_trabajadores,
                'dias' => $mo->dias_trabajados,
                'costo_dia' => $mo->costo_por_dia,
            ];
        }

        // Cargar Maquinaria
        foreach ($labor->maquinaria as $maq) {
            $this->itemsMaquinaria[] = [
                'id' => $maq->id,
                'nombre' => $maq->nombre_maquinaria,
                'labor' => $maq->labor_realizada,
                'horas' => $maq->horas_trabajadas,
                'costo_total' => $maq->costo_total,
            ];
        }

        $this->step = 2;
        $this->checkLaborAvailability($this->cultivo_id);
        $this->dispatch('open-modal', 'modal-labor-manager');
    }

    public function delete($id)
    {
        DB::transaction(function() use ($id) {
            $labor = Labor::find($id);
            if ($labor) {
                $labor->insumos()->delete();
                $labor->manoDeObra()->delete();
                $labor->maquinaria()->delete();
                $labor->delete();
            }
        });
        session()->flash('status', "Registro de labor eliminado.");
    }
}

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
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Mis Labores')]
class LaboresManager extends Component
{
    use WithPagination, WithFileUploads;

    // Filtros de Barra (agrosys_labores_v7)
    public $fStatus = '';
    public $fLand = '';
    public $fCat = '';
    public $fVariety = '';
    public $fExactCrop = '';
    public $fLaborId = '';

    public $filterDateStart = '';
    public $filterDateEnd = '';
    public $selectedOrgId = null;

    // Formulario Modal
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

    // Registros Dinámicos
    public $itemsInsumos = [];
    public $itemsManoObra = [];
    public $itemsMaquinaria = [];

    // Búsqueda Modal (Cascada)
    public $activeIdx = null;
    public $queryIns = '';
    public $showIns = false;

    public $selStatus = 'TODOS';
    public $selLandId = null;
    public $selCatId = null;
    public $selVarName = '';

    public $landNombreSelected = '';
    public $catNombreSelected = '';
    public $cropNombreSelected = '';
    public $cropFechaPlanificada = null;
    public $cropHectareas = null;
    public $cropEstado = '';

    public $step = 1;
    public $laborStatusMap = [];
    public $strictMode = false;
    public $viewingLabor = null;
    public $viewTimestamp = '';

    protected $queryString = ['fStatus', 'fLand', 'fCat', 'fVariety', 'fExactCrop', 'fLaborId', 'filterDateStart', 'filterDateEnd'];

    public function mount()
    {
        $this->fecha_realizacion = date('Y-m-d');
        $membresia = MiembroOrganizacion::where('usuario_id', Auth::id())->where('estado', 1)->first();
        if ($membresia) $this->selectedOrgId = $membresia->organizacion_id;

        if (request()->has('filterCropId')) {
            $c = Cultivo::find(request('filterCropId'));
            if ($c) {
                $this->fExactCrop = $c->nombre_lote;
                $this->fLand = $c->terreno->nombre;
            }
        }
        if (request()->has('strict')) $this->strictMode = true;
        $this->checkLaborAvailability($this->cultivo_id);
    }

    public function resetFilters() { $this->reset(['fStatus', 'fLand', 'fCat', 'fVariety', 'fExactCrop', 'fLaborId', 'filterDateStart', 'filterDateEnd']); }

    public function checkLaborAvailability($cropId = null)
    {
        $this->strictMode = true;
        $user = Auth::user();

        // 1. Definir el ámbito de cultivos (Personal u Organización)
        $cropQuery = Cultivo::query();
        if ($this->selectedOrgId) {
            $cropQuery->whereHas('terreno', function($q) {
                $q->where('organizacion_id', $this->selectedOrgId)
                  ->orWhere('usuario_id', Auth::id());
            });
        } else {
            $cropQuery->whereHas('terreno', fn($q) => $q->where('usuario_id', $user->id));
        }

        if ($cropId) {
            // Validación específica para un CULTIVO SELECCIONADO (Paso 2)
            $tienePre = Labor::where('cultivo_id', $cropId)
                ->whereHas('detalleCatalogo', fn($q) => $q->where('categoria', 'preparacion'))
                ->exists();

            $tieneSie = Labor::where('cultivo_id', $cropId)
                ->whereHas('detalleCatalogo', fn($q) => $q->where('categoria', 'siembra'))
                ->exists();

            $this->laborStatusMap = [
                'preparacion' => true,
                'siembra' => $tienePre,
                'mantenimiento' => $tieneSie,
                'cosecha' => $tieneSie,
                'Otros' => true,
                'OTROS' => true
            ];
        } else {
            // Validación GLOBAL para los Iconos (Paso 1)
            // Se activa SIEMBRA si el usuario tiene al menos un cultivo con preparación registrada
            $anyPrepared = (clone $cropQuery)
                ->whereHas('labores', fn($q) => $q->whereHas('detalleCatalogo', fn($sq) => $sq->where('categoria', 'preparacion')))
                ->exists();

            // Se activa MANTENIMIENTO/COSECHA si el usuario tiene al menos un cultivo con siembra registrada
            $anySown = (clone $cropQuery)
                ->whereHas('labores', fn($q) => $q->whereHas('detalleCatalogo', fn($sq) => $sq->where('categoria', 'siembra')))
                ->exists();

            $this->laborStatusMap = [
                'preparacion' => true,
                'siembra' => $anyPrepared,
                'mantenimiento' => $anySown,
                'cosecha' => $anySown,
                'Otros' => true,
                'OTROS' => true
            ];
        }
    }

    public function selectLaborType($catId)
    {
        $cat = CatalogoLabor::find($catId);
        if ($this->cultivo_id) {
            $this->checkLaborAvailability($this->cultivo_id);
            $catKey = $cat->categoria;
            if (isset($this->laborStatusMap[$catKey]) && !$this->laborStatusMap[$catKey]) {
                $msg = $catKey === 'siembra' ? "Debe completar la Preparación de Terreno primero." : "Debe completar la Siembra primero.";
                session()->flash('error', $msg);
                return;
            }
        }
        $this->catalogo_labor_id = $catId;
        $this->step = 2;
    }

    public function updatedCultivoId($val)
    {
        $c = Cultivo::find($val);
        if ($c) {
            $this->cropNombreSelected = strtoupper($c->nombre_lote);
            $this->cropFechaPlanificada = $c->fecha_siembra;
            $this->cropHectareas = $c->area_destinada;
            $this->cropEstado = $c->estado;

            $this->checkLaborAvailability($val);

            // Validar si la labor seleccionada en el paso 1 es válida para este nuevo cultivo
            $cat = CatalogoLabor::find($this->catalogo_labor_id);
            if ($cat && isset($this->laborStatusMap[$cat->categoria]) && !$this->laborStatusMap[$cat->categoria]) {
                $this->enforceValidLabor($val, $cat);
            }
        } else {
            $this->reset(['cropNombreSelected', 'cropHectareas', 'cropFechaPlanificada', 'cropEstado']);
            $this->checkLaborAvailability(null);
        }
    }

    private function enforceValidLabor($cropId, $currentCat)
    {
        $tienePre = Labor::where('cultivo_id', $cropId)->whereHas('detalleCatalogo', fn($q) => $q->where('categoria', 'preparacion'))->where('estado', 'Completada')->exists();
        $tieneSie = Labor::where('cultivo_id', $cropId)->whereHas('detalleCatalogo', fn($q) => $q->where('categoria', 'siembra'))->where('estado', 'Completada')->exists();

        if (!$tienePre) {
            $newCat = CatalogoLabor::where('categoria', 'preparacion')->first();
            $this->catalogo_labor_id = $newCat->id;
            session()->flash('warning', "Este cultivo no tiene Preparación. Se cambió la labor a " . strtoupper($newCat->nombre));
        } elseif (!$tieneSie) {
            $newCat = CatalogoLabor::where('categoria', 'siembra')->first();
            $this->catalogo_labor_id = $newCat->id;
            session()->flash('warning', "Este cultivo no tiene Siembra. Se cambió la labor a " . strtoupper($newCat->nombre));
        }
    }

    public function calculateTotals() {
        $this->costo_mano_obra_total = 0;
        foreach($this->itemsManoObra as $item) {
            $this->costo_mano_obra_total += ((float)$item['cantidad'] * (float)$item['dias'] * (float)$item['costo_dia']);
        }

        $this->costo_maquinaria_total = 0;
        foreach($this->itemsMaquinaria as $item) {
            $this->costo_maquinaria_total += (float)$item['costo_total'];
        }

        $insTotal = 0;
        foreach($this->itemsInsumos as $item) {
            $insTotal += ((float)$item['cantidad'] * (float)$item['costo_unitario']) + (float)$item['costo_flete'];
        }

        $this->costo_total = $this->costo_mano_obra_total + $this->costo_maquinaria_total + $insTotal;
    }

    public function save()
    {
        $this->validate(['cultivo_id' => 'required', 'catalogo_labor_id' => 'required', 'fecha_realizacion' => 'required|date|before_or_equal:today', 'costo_total' => 'required|numeric']);
        return DB::transaction(function() {
            $user = Auth::user(); $path = $this->currentPhotoPath;
            if ($this->laborPhoto) { $fileData = AgroStorageService::storeUserFile($this->laborPhoto, $user, 'labor', $this->selectedOrgId); $path = $fileData['ruta_completa']; ArchivoMultimedia::create($fileData); }
            $data = ['cultivo_id' => $this->cultivo_id, 'catalogo_labor_id' => $this->catalogo_labor_id, 'fecha_realizacion' => $this->fecha_realizacion, 'costo_mano_obra_total' => $this->costo_mano_obra_total, 'costo_maquinaria_total' => $this->costo_maquinaria_total, 'costo_total' => $this->costo_total, 'estado' => $this->estado, 'observaciones' => $this->observaciones, 'foto_path' => $path];
            if ($this->laborId) { $labor = Labor::find($this->laborId); $labor->update($data); $labor->insumos()->delete(); $labor->manoDeObra()->delete(); $labor->maquinaria()->delete(); } else { $labor = Labor::create($data); }
            $mainLaborName = CatalogoLabor::find($this->catalogo_labor_id)->nombre ?? 'Labor Realizada';
            foreach($this->itemsInsumos as $item) if ($item['insumo_id']) $labor->insumos()->create(['catalogo_insumo_id' => $item['insumo_id'], 'proveedor_id' => $item['proveedor_id'] ?: null, 'cantidad' => $item['cantidad'], 'costo_unitario' => $item['costo_unitario'], 'costo_flete' => $item['costo_flete']]);
            foreach($this->itemsManoObra as $item) if ($item['tipo_id']) $labor->manoDeObra()->create(['tipo_id' => $item['tipo_id'], 'cantidad_trabajadores' => $item['cantidad'], 'dias_trabajados' => $item['dias'], 'costo_por_dia' => $item['costo_dia'], 'subtotal' => ($item['cantidad'] * $item['dias'] * $item['costo_dia'])]);
            foreach($this->itemsMaquinaria as $item) if ($item['nombre']) $labor->maquinaria()->create(['nombre_maquinaria' => $item['nombre'], 'labor_realizada' => $item['labor'] ?: $mainLaborName, 'horas_trabajadas' => $item['horas'], 'costo_total' => $item['costo_total']]);
            $this->dispatch('close-modal', 'modal-labor-manager'); $this->resetForm(); session()->flash('status', "Labor guardada.");
        });
    }

    public function render()
    {
        $user = Auth::user(); $allowedIds = [$user->id];
        if ($this->selectedOrgId) {
            $m = $user->membresias()->where('organizacion_id', $this->selectedOrgId)->where('estado', 1)->first();
            if ($user->rol_id === 1 || ($m && $m->roles()->whereHas('rolDetalle', fn($q)=>$q->where('nombre','Administrador'))->exists())) {
                $baseQuery = Labor::whereHas('cultivo.terreno', function($q) use ($user) {
                    $q->where('organizacion_id', $this->selectedOrgId)
                      ->orWhere('usuario_id', $user->id);
                });
            } else {
                $baseQuery = Labor::whereHas('cultivo.terreno', fn($q)=>$q->where('usuario_id', $user->id));
            }
        } else {
            $baseQuery = Labor::whereHas('cultivo.terreno', fn($q)=>$q->whereIn('usuario_id', $allowedIds));
        }

        // Filtros de Barra %...%
        if ($this->fStatus) $baseQuery->where('estado', $this->fStatus);
        if ($this->fLand) $baseQuery->whereHas('cultivo.terreno', fn($q) => $q->where('nombre', 'like', '%'.$this->fLand.'%'));
        if ($this->fCat) $baseQuery->whereHas('cultivo.detalleCatalogo', fn($q) => $q->where('nombre', 'like', '%'.$this->fCat.'%'));
        if ($this->fVariety) $baseQuery->whereHas('cultivo', fn($q) => $q->where('variedad', 'like', '%'.$this->fVariety.'%'));
        if ($this->fExactCrop) $baseQuery->whereHas('cultivo', fn($q) => $q->where('nombre_lote', 'like', '%'.$this->fExactCrop.'%'));
        if ($this->fLaborId) $baseQuery->where('catalogo_labor_id', $this->fLaborId);
        if ($this->filterDateStart) $baseQuery->whereDate('fecha_realizacion', '>=', $this->filterDateStart);
        if ($this->filterDateEnd) $baseQuery->whereDate('fecha_realizacion', '<=', $this->filterDateEnd);

        $labores = $baseQuery->with(['cultivo.terreno', 'detalleCatalogo'])->orderBy('fecha_realizacion', 'desc')->paginate(12);

        // --- LÓGICA DE CASCADA PARA BARRA DE FILTROS ---
        $dbStatusBarra = $this->fStatus === 'En progreso' ? 'En crecimiento' : ($this->fStatus === 'Completada' ? 'Cosechado' : $this->fStatus);
        $terrenosBarra = Terreno::whereIn('usuario_id', $allowedIds)->get();
        $catalogosBarra = CatalogoCultivo::whereIn('id', Cultivo::query()
            ->when($this->fLand, fn($q) => $q->whereHas('terreno', fn($sq) => $sq->where('nombre', 'like', '%'.$this->fLand.'%')))
            ->when($this->fStatus, fn($q) => $q->where('estado', $dbStatusBarra))
            ->pluck('catalogo_cultivo_id'))->get();
        $variedadesBarra = Cultivo::query()
            ->when($this->fLand, fn($q) => $q->whereHas('terreno', fn($sq) => $sq->where('nombre', 'like', '%'.$this->fLand.'%')))
            ->when($this->fCat, fn($q) => $q->whereHas('detalleCatalogo', fn($sq) => $sq->where('nombre', 'like', '%'.$this->fCat.'%')))
            ->when($this->fStatus, fn($q) => $q->where('estado', $dbStatusBarra))
            ->pluck('variedad')->filter()->unique();
        $cultivosExactosBarra = Cultivo::query()
            ->when($this->fLand, fn($q) => $q->whereHas('terreno', fn($sq) => $sq->where('nombre', 'like', '%'.$this->fLand.'%')))
            ->when($this->fCat, fn($q) => $q->whereHas('detalleCatalogo', fn($sq) => $sq->where('nombre', 'like', '%'.$this->fCat.'%')))
            ->when($this->fVariety, fn($q) => $q->where('variedad', 'like', '%'.$this->fVariety.'%'))
            ->when($this->fStatus, fn($q) => $q->where('estado', $dbStatusBarra))
            ->get()->map(function($c) {
                $c->label_exacto = "{$c->detalleCatalogo->nombre} - " . ($c->variedad ?: 'GENERICA') . " - {$c->area_destinada} Ha - " . Carbon::parse($c->fecha_siembra)->format('d/m/Y');
                return $c;
            });

        // --- LÓGICA DE CASCADA PARA MODAL ---
        $dbStatusModal = $this->selStatus === 'En proceso' ? 'En crecimiento' : ($this->selStatus === 'Completada' ? 'Cosechado' : $this->selStatus);

        // Obtener categoría de la labor seleccionada en el Paso 1
        $selectedLabor = CatalogoLabor::find($this->catalogo_labor_id);
        $laborCat = $selectedLabor ? $selectedLabor->categoria : null;

        $resultsLands = $this->selStatus ? Terreno::whereIn('usuario_id', $allowedIds)->when($this->selStatus !== 'TODOS', fn($q) => $q->whereHas('cultivos', fn($sq) => $sq->where('estado', $dbStatusModal)))->get() : [];
        $resultsCats = $this->selLandId ? CatalogoCultivo::whereIn('id', Cultivo::where('terreno_id', $this->selLandId)->when($this->selStatus !== 'TODOS', fn($q) => $q->where('estado', $dbStatusModal))->pluck('catalogo_cultivo_id'))->get() : [];
        $resultsVars = ($this->selLandId && $this->selCatId) ? Cultivo::where('terreno_id', $this->selLandId)->where('catalogo_cultivo_id', $this->selCatId)->when($this->selStatus !== 'TODOS', fn($q) => $q->where('estado', $dbStatusModal))->pluck('variedad')->unique() : [];

        $resultsCrops = ($this->selLandId && $this->selCatId && $this->selVarName)
            ? Cultivo::with('detalleCatalogo')->where('terreno_id', $this->selLandId)
                ->where('catalogo_cultivo_id', $this->selCatId)
                ->where('variedad', $this->selVarName)
                ->when($this->selStatus !== 'TODOS', fn($q) => $q->where('estado', $dbStatusModal))
                // FILTRADO DINÁMICO SEGÚN LA LABOR SELECCIONADA
                ->when($laborCat === 'siembra', function($q) {
                    // Para sembrar, el terreno debe estar PREPARADO (al menos una labor de preparación registrada)
                    $q->whereHas('labores', fn($l) => $l->whereHas('detalleCatalogo', fn($dc) => $dc->where('categoria', 'preparacion')));
                })
                ->when(in_array($laborCat, ['mantenimiento', 'cosecha']), function($q) {
                    // Para mantenimiento o cosecha, el cultivo debe estar SEMBRADO (al menos una labor de siembra registrada)
                    $q->whereHas('labores', fn($l) => $l->whereHas('detalleCatalogo', fn($dc) => $dc->where('categoria', 'siembra')));
                })
                ->get()->map(function($c) {
                    $fecha = $c->fecha_siembra ? $c->fecha_siembra->format('d/m/Y') : ($c->fecha_planificada ? $c->fecha_planificada->format('d/m/Y') : '---');
                    $c->label_display = rtrim(rtrim(number_format($c->area_destinada, 2, '.', ''), '0'), '.') . " Ha. de " . strtoupper($c->detalleCatalogo->nombre) . " " . strtoupper($c->variedad ?: 'GENERICA') . " - {$fecha}";
                    return $c;
                })
            : [];
        $resultsIns = $this->showIns ? \App\Models\CatalogoInsumo::where('nombre','like','%'.$this->queryIns.'%')->take(5)->get() : [];

        return view('livewire.admin.labores-manager', [
            'labores' => $labores, 'catalogoLabores' => CatalogoLabor::all(), 'manoObraTipos' => \App\Models\ManoObraTipo::all(),
            'terrenosBarra' => $terrenosBarra, 'catalogosBarra' => $catalogosBarra, 'variedadesBarra' => $variedadesBarra, 'cultivosExactosBarra' => $cultivosExactosBarra,
            'resultsLands' => $resultsLands, 'resultsCats' => $resultsCats, 'resultsVars' => $resultsVars, 'resultsCrops' => $resultsCrops, 'resultsIns' => $resultsIns,
            'stats' => ['total' => $labores->total(), 'costo_total' => $baseQuery->sum('costo_total'), 'pendientes' => (clone $baseQuery)->where('estado','Pendiente')->count(), 'avg_cost' => $labores->total() > 0 ? $baseQuery->avg('costo_total') : 0]
        ]);
    }

    public function resetForm() {
        $this->reset([
            'laborId', 'cultivo_id', 'catalogo_labor_id', 'costo_mano_obra_total',
            'costo_maquinaria_total', 'costo_total', 'estado', 'observaciones',
            'laborPhoto', 'currentPhotoPath', 'selStatus', 'selLandId', 'selCatId',
            'selVarName', 'landNombreSelected', 'catNombreSelected', 'cropNombreSelected',
            'cropFechaPlanificada', 'cropHectareas', 'cropEstado', 'step', 'strictMode',
            'viewingLabor', 'itemsInsumos', 'itemsManoObra', 'itemsMaquinaria'
        ]);
        $this->fecha_realizacion = date('Y-m-d');
        $this->estado = 'Pendiente';
        $this->step = 1;
        $this->checkLaborAvailability(null);
    }
    public function backToGrid() { $this->step = 1; }
    public function showDetails($id) {
        $this->viewTimestamp = now()->getPreciseTimestamp(3);
        $this->viewingLabor = Labor::with([
            'cultivo.terreno',
            'detalleCatalogo',
            'insumos.detalleCatalogo',
            'insumos.proveedor',
            'manoDeObra.tipoPersona',
            'maquinaria'
        ])->find($id);
        $this->dispatch('open-modal', 'modal-view-labor');
    }
    public function edit($id) {
        $l = Labor::with(['cultivo.terreno', 'insumos.detalleCatalogo', 'insumos.proveedor', 'manoDeObra.tipoPersona', 'maquinaria'])->find($id);
        $this->laborId = $l->id;
        $this->cultivo_id = $l->cultivo_id;
        $this->catalogo_labor_id = $l->catalogo_labor_id;
        $this->fecha_realizacion = $l->fecha_realizacion->format('Y-m-d');
        $this->estado = $l->estado;
        $this->observaciones = $l->observaciones;
        $this->costo_mano_obra_total = $l->costo_mano_obra_total;
        $this->costo_maquinaria_total = $l->costo_maquinaria_total;
        $this->costo_total = $l->costo_total;
        $this->currentPhotoPath = $l->foto_path;

        // Cargar ítems dinámicos con nombres explícitos
        $this->itemsInsumos = $l->insumos->map(fn($i) => [
            'id' => $i->id,
            'insumo_id' => $i->catalogo_insumo_id,
            'insumo_nombre' => $i->detalleCatalogo->nombre ?? 'DESCONOCIDO',
            'proveedor_id' => $i->proveedor_id,
            'proveedor_nombre' => $i->proveedor?->nombre_empresa ?? '',
            'cantidad' => $i->cantidad,
            'costo_unitario' => $i->costo_unitario,
            'costo_flete' => $i->costo_flete,
            'habilitar_proveedor' => $i->proveedor_id ? true : false
        ])->toArray();

        $this->itemsManoObra = $l->manoDeObra->map(fn($m) => [
            'id' => $m->id,
            'tipo_id' => $m->tipo_id,
            'cantidad' => $m->cantidad_trabajadores,
            'dias' => $m->dias_trabajados,
            'costo_dia' => $m->costo_por_dia
        ])->toArray();

        $this->itemsMaquinaria = $l->maquinaria->map(fn($mq) => [
            'id' => $mq->id,
            'nombre' => $mq->nombre_maquinaria,
            'labor' => $mq->labor_realizada,
            'horas' => $mq->horas_trabajadas,
            'costo_total' => $mq->costo_total
        ])->toArray();

        // Configurar cascada para resumen del modal
        $c = $l->cultivo;
        $this->selStatus = $c->estado === 'En crecimiento' ? 'En proceso' : ($c->estado === 'Cosechado' ? 'Completada' : $c->estado);
        $this->selLandId = $c->terreno_id;
        $this->selCatId = $c->catalogo_cultivo_id;
        $this->selVarName = $c->variedad;

        $this->landNombreSelected = strtoupper($c->terreno->nombre);
        $this->catNombreSelected = strtoupper($c->detalleCatalogo->nombre);
        $this->cropNombreSelected = strtoupper($c->nombre_lote);
        $this->cropFechaPlanificada = $c->fecha_siembra;
        $this->cropHectareas = $c->area_destinada;

        $this->step = 2;
        $this->dispatch('open-modal', 'modal-labor-manager');
    }
    public function delete($id) { Labor::destroy($id); session()->flash('status', "Registro eliminado."); }
    public function addItemInsumo() { $this->itemsInsumos[] = ['id' => '', 'insumo_id' => '', 'insumo_nombre' => '', 'proveedor_id' => '', 'proveedor_nombre' => '', 'cantidad' => 1, 'costo_unitario' => 0, 'costo_flete' => 0]; }
    public function addItemManoObra() { $this->itemsManoObra[] = ['id' => '', 'tipo_id' => '', 'tipo_nombre' => '', 'cantidad' => 1, 'dias' => 1, 'costo_dia' => 0]; }
    public function addItemMaquinaria() { $this->itemsMaquinaria[] = ['id' => '', 'nombre' => '', 'labor' => '', 'horas' => 1, 'costo_total' => 0]; }
    public function removeItem($type, $index) { if ($type === 'insumo') unset($this->itemsInsumos[$index]); elseif ($type === 'mano') unset($this->itemsManoObra[$index]); elseif ($type === 'maq') unset($this->itemsMaquinaria[$index]); $this->itemsInsumos = array_values($this->itemsInsumos); $this->itemsManoObra = array_values($this->itemsManoObra); $this->itemsMaquinaria = array_values($this->itemsMaquinaria); $this->calculateTotals(); }
    public function searchInsumo($idx, $val) { $this->activeIdx = $idx; $this->queryIns = $val; $this->showIns = true; }
    public function selectInsumoItem($idx, $id, $nombre) { $this->itemsInsumos[$idx]['insumo_id'] = $id; $this->itemsInsumos[$idx]['insumo_nombre'] = $nombre; $this->showIns = false; $this->activeIdx = null; }
    public function openAddProvider($idx = null) { $this->currentInsumoIdx = $idx; $this->dispatch('open-modal', 'modal-add-provider'); }
    public function saveQuickProvider() { $this->validate(['newProvNombre' => 'required']); $prov = \App\Models\Proveedor::create(['nombre_empresa' => $this->newProvNombre, 'tipo_servicio' => 'Insumos']); if ($this->currentInsumoIdx !== null) { $this->itemsInsumos[$this->currentInsumoIdx]['proveedor_id'] = $prov->id; $this->itemsInsumos[$this->currentInsumoIdx]['proveedor_nombre'] = $prov->nombre_empresa; } $this->dispatch('close-modal', 'modal-add-provider'); }
}

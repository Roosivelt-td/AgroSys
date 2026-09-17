<?php

namespace App\Livewire\Admin;

use App\Models\Cultivo;
use App\Models\Labor;
use App\Models\Terreno;
use App\Models\CatalogoCultivo;
use App\Models\MiembroOrganizacion;
use App\Models\ArchivoMultimedia;
use App\Services\AgroStorageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Mis Cosechas')]
class CosechasManager extends Component
{
    use WithPagination, WithFileUploads;

    public $searchCultivo = '';
    public $filterDateStart = '';
    public $filterDateEnd = '';
    public $selectedOrgId = null;

    // Formulario de Edición (Copiado de CultivosManager para consistencia)
    public $cropId = null;
    public $terreno_id = '';
    public $catalogo_cultivo_id = '';
    public $nombre_lote = '';
    public $variedad = '';
    public $fecha_planificada = '';
    public $fecha_siembra = '';
    public $fecha_cosecha_estimada = '';
    public $estado = 'Cosechado';
    public $area_destinada = '';
    public $plantas_estimadas = '';
    public $rendimiento_esperado_tn_ha = '';
    public $observaciones = '';
    public $cropPhoto;
    public $currentPhotoPath;

    public $queryTerreno = '';
    public $queryCultivo = '';
    public $areaDisponible = 0;
    public $terrenoNombreSelected = '';
    public $cultivoNombreSelected = '';
    public $cropNombreSelected = '';

    // Propiedades para EDITAR RESULTADO DE COSECHA
    public $laborId = null;
    public $itemsCosecha = [];
    public $observaciones_cosecha = '';
    public $fecha_cosecha_edit = '';

    // Registros Dinámicos (Labores)
    public $itemsInsumos = [];
    public $itemsManoObra = [];
    public $itemsMaquinaria = [];
    public $costo_insumos_total = 0;
    public $costo_mano_obra_total = 0;
    public $costo_maquinaria_total = 0;
    public $costo_total = 0;

    // Búsqueda de Insumos
    public $activeIdx = null;
    public $queryIns = '';
    public $showIns = false;

    // Para el Modal de Reporte
    public $selectedCropForReport = null;
    public $reportData = [
        'costoInsumos' => 0,
        'costoManoObra' => 0,
        'costoMaquinaria' => 0,
        'ingresosTotales' => 0,
        'cantidadCosechada' => 0,
        'unidadCosecha' => 'kg',
        'balance' => 0,
        'chart' => []
    ];

    public function mount()
    {
        $membresia = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->where('estado', 1)
            ->first();

        if ($membresia) {
            $this->selectedOrgId = $membresia->organizacion_id;
        }
    }

    public function showCropReport($id)
    {
        $crop = Cultivo::with(['labores.insumos', 'labores.cosechas.ventas', 'detalleCatalogo', 'terreno'])->findOrFail($id);
        $this->selectedCropForReport = $crop;

        $costoInsumos = 0;
        $costoManoObra = 0;
        $costoMaquinaria = 0;
        $costoFletes = 0;
        $ingresosTotales = 0;
        $cantidadTotalCosechada = 0;
        $cantidadTotalVendida = 0;
        $unidadCosecha = 'kg';

        $cosechasDetalladas = [];

        foreach ($crop->labores as $labor) {
            $costoManoObra += (float)$labor->costo_mano_obra_total;
            $costoMaquinaria += (float)$labor->costo_maquinaria_total;

            // Insumos calculados
            $insumoLabor = max(0, (float)$labor->costo_total - ((float)$labor->costo_mano_obra_total + (float)$labor->costo_maquinaria_total));
            $costoInsumos += $insumoLabor;

            foreach ($labor->cosechas as $cos) {
                $cantidadTotalCosechada += $cos->cantidad_kg;
                $unidadCosecha = $cos->unidad_medida;

                $ventasRel = $cos->ventas;
                $vendidoCos = $ventasRel->sum('cantidad_vendida_kg');
                $cantidadTotalVendida += $vendidoCos;

                $ingresoCos = $ventasRel->sum(fn($v) => $v->cantidad_vendida_kg * $v->precio_por_kg);
                $ingresosTotales += $ingresoCos;

                $costoFletes += $ventasRel->sum('costo_flete');

                // Información detallada de cada cosecha
                $cosechasDetalladas[] = [
                    'fecha' => $cos->fecha_cosecha->format('d/m/Y'),
                    'cantidad' => $cos->cantidad_kg,
                    'unidad' => $cos->unidad_medida,
                    'calidad' => $cos->calidad,
                    'vendido' => $vendidoCos,
                    'ingreso' => $ingresoCos,
                    'precio_estimado' => (float)($cos->costo_operativo_cosecha ?: 0),
                    'completado' => ($vendidoCos >= $cos->cantidad_kg && $cos->cantidad_kg > 0)
                ];
            }
        }

        $totalGastosCampaña = $costoInsumos + $costoManoObra + $costoMaquinaria + $costoFletes;

        // Agrupación de Producción Total por Calidad y Unidad (Corrección de duplicidad)
        $produccionAgrupada = [];
        foreach($cosechasDetalladas as $item) {
            $key = strtolower($item['calidad']) . '_' . strtolower($item['unidad']);
            if(!isset($produccionAgrupada[$key])) {
                $produccionAgrupada[$key] = [
                    'calidad' => $item['calidad'],
                    'unidad' => $item['unidad'],
                    'cantidad' => 0
                ];
            }
            $produccionAgrupada[$key]['cantidad'] += $item['cantidad'];
        }

        // Cálculo del Balance Proyectado:
        // Suma de Ventas Reales + (Stock disponible * Precio Estimado) - Gastos Totales
        $valorProyectadoTotal = 0;
        foreach($cosechasDetalladas as $item) {
            $valorProyectadoTotal += $item['ingreso'] + (max(0, $item['cantidad'] - $item['vendido']) * $item['precio_estimado']);
        }

        $isCompletamenteVendido = ($cantidadTotalVendida >= $cantidadTotalCosechada && $cantidadTotalCosechada > 0);

        $chartValues = [$costoInsumos, $costoManoObra, $costoMaquinaria];
        if ($costoFletes > 0) $chartValues[] = $costoFletes;
        $hasData = array_sum($chartValues) > 0;

        $this->reportData = [
            'costoInsumos' => $costoInsumos,
            'costoManoObra' => $costoManoObra,
            'costoMaquinaria' => $costoMaquinaria,
            'costoFletes' => $costoFletes,
            'ingresosTotales' => $ingresosTotales,
            'cantidadCosechada' => $cantidadTotalCosechada,
            'unidadCosecha' => $unidadCosecha,
            'produccionAgrupada' => array_values($produccionAgrupada),
            'balance' => $valorProyectadoTotal - $totalGastosCampaña,
            'esReal' => $isCompletamenteVendido,
            'cosechasDetalladas' => $cosechasDetalladas,
            'chart' => [
                'labels' => $costoFletes > 0 ? ['Insumos', 'Mano de Obra', 'Maquinaria', 'Fletes'] : ['Insumos', 'Mano de Obra', 'Maquinaria'],
                'values' => $hasData ? $chartValues : [],
                'colors' => ['#3b82f6', '#f59e0b', '#8b5cf6', '#10b981'],
                'unit' => 'S/'
            ]
        ];

        $this->dispatch('open-modal', 'modal-crop-report');
    }

    public function editHarvest($cropId)
    {
        $crop = Cultivo::with(['labores' => function($q) {
            $q->whereHas('detalleCatalogo', fn($dc) => $dc->where('categoria', 'cosecha'))
              ->with(['cosechas', 'insumos.detalleCatalogo', 'manoDeObra.tipoPersona', 'maquinaria']);
        }])->findOrFail($cropId);

        $labor = $crop->labores->first();

        if (!$labor) {
            session()->flash('error', 'No se encontró un registro de labor de cosecha para este cultivo.');
            return;
        }

        $this->laborId = $labor->id;
        $this->cropId = $crop->id;
        $this->cropNombreSelected = strtoupper($crop->detalleCatalogo->nombre) . " - " . strtoupper($crop->variedad ?: 'GENERICA');
        $this->fecha_cosecha_edit = $labor->fecha_realizacion->format('Y-m-d');
        $this->observaciones_cosecha = $labor->observaciones;

        // Resultados de Producción
        $this->itemsCosecha = $labor->cosechas->map(fn($c) => [
            'id' => $c->id,
            'cantidad' => $c->cantidad_kg,
            'unidad' => $c->unidad_medida,
            'calidad' => $c->calidad,
            'costo_operativo' => $c->costo_operativo_cosecha
        ])->toArray();

        if (count($this->itemsCosecha) === 0) $this->addItemCosecha();

        // Insumos
        $this->itemsInsumos = $labor->insumos->map(fn($i) => [
            'insumo_id' => $i->catalogo_insumo_id,
            'insumo_nombre' => $i->detalleCatalogo->nombre,
            'cantidad' => $i->cantidad,
            'costo_unitario' => $i->costo_unitario,
            'costo_flete' => $i->costo_flete
        ])->toArray();

        // Mano de Obra
        $this->itemsManoObra = $labor->manoDeObra->map(fn($m) => [
            'tipo_id' => $m->tipo_id,
            'cantidad' => $m->cantidad_trabajadores,
            'dias' => $m->dias_trabajados,
            'costo_dia' => $m->costo_por_dia
        ])->toArray();

        // Maquinaria
        $this->itemsMaquinaria = $labor->maquinaria->map(fn($mq) => [
            'nombre' => $mq->nombre_maquinaria,
            'labor' => $mq->labor_realizada,
            'horas' => $mq->horas_trabajadas,
            'costo_total' => $mq->costo_total
        ])->toArray();

        $this->calculateTotals();
        $this->dispatch('open-modal', 'modal-edit-harvest-result');
    }

    public function calculateTotals()
    {
        $this->costo_mano_obra_total = 0;
        foreach($this->itemsManoObra as $item) {
            $this->costo_mano_obra_total += ((float)$item['cantidad'] * (float)$item['dias'] * (float)$item['costo_dia']);
        }

        $this->costo_maquinaria_total = 0;
        foreach($this->itemsMaquinaria as $item) {
            $this->costo_maquinaria_total += (float)$item['costo_total'];
        }

        $this->costo_insumos_total = 0;
        foreach($this->itemsInsumos as $item) {
            $this->costo_insumos_total += ((float)$item['cantidad'] * (float)$item['costo_unitario']) + (float)$item['costo_flete'];
        }

        $this->costo_total = $this->costo_mano_obra_total + $this->costo_maquinaria_total + $this->costo_insumos_total;
    }

    public function addItemInsumo() { $this->itemsInsumos[] = ['insumo_id' => '', 'insumo_nombre' => '', 'cantidad' => 1, 'costo_unitario' => 0, 'costo_flete' => 0]; }
    public function addItemManoObra() { $this->itemsManoObra[] = ['tipo_id' => '', 'cantidad' => 1, 'dias' => 1, 'costo_dia' => 0]; }
    public function addItemMaquinaria() { $this->itemsMaquinaria[] = ['nombre' => '', 'labor' => 'Cosecha', 'horas' => 1, 'costo_total' => 0]; }

    public function removeItem($type, $index)
    {
        if ($type === 'insumo') unset($this->itemsInsumos[$index]);
        elseif ($type === 'mano') unset($this->itemsManoObra[$index]);
        elseif ($type === 'maq') unset($this->itemsMaquinaria[$index]);
        $this->itemsInsumos = array_values($this->itemsInsumos);
        $this->itemsManoObra = array_values($this->itemsManoObra);
        $this->itemsMaquinaria = array_values($this->itemsMaquinaria);
        $this->calculateTotals();
    }

    public function searchInsumo($idx, $val) { $this->activeIdx = $idx; $this->queryIns = $val; $this->showIns = true; }
    public function selectInsumoItem($idx, $id, $nombre) { $this->itemsInsumos[$idx]['insumo_id'] = $id; $this->itemsInsumos[$idx]['insumo_nombre'] = $nombre; $this->showIns = false; $this->activeIdx = null; }

    public function addItemCosecha()
    {
        $this->itemsCosecha[] = ['id' => null, 'cantidad' => 0, 'unidad' => 'kg', 'calidad' => 'primera', 'costo_operativo' => 0];
    }

    public function removeItemCosecha($index)
    {
        unset($this->itemsCosecha[$index]);
        $this->itemsCosecha = array_values($this->itemsCosecha);
    }

    public function saveHarvest()
    {
        $this->validate([
            'fecha_cosecha_edit' => 'required|date',
            'itemsCosecha.*.cantidad' => 'required|numeric|min:0.01',
            'itemsCosecha.*.unidad' => 'required',
            'itemsCosecha.*.calidad' => 'required',
            'costo_total' => 'required|numeric'
        ]);

        DB::transaction(function() {
            $labor = Labor::findOrFail($this->laborId);
            $labor->update([
                'fecha_realizacion' => $this->fecha_cosecha_edit,
                'costo_mano_obra_total' => (float)$this->costo_mano_obra_total,
                'costo_maquinaria_total' => (float)$this->costo_maquinaria_total,
                'costo_total' => (float)$this->costo_total,
                'observaciones' => $this->observaciones_cosecha
            ]);

            // 1. Insumos
            $labor->insumos()->delete();
            foreach($this->itemsInsumos as $item) {
                if (!empty($item['insumo_id'])) {
                    $labor->insumos()->create([
                        'catalogo_insumo_id' => $item['insumo_id'],
                        'cantidad' => $item['cantidad'],
                        'costo_unitario' => $item['costo_unitario'],
                        'costo_flete' => $item['costo_flete'] ?: 0
                    ]);
                }
            }

            // 2. Mano de Obra
            $labor->manoDeObra()->delete();
            foreach($this->itemsManoObra as $item) {
                if (!empty($item['tipo_id'])) {
                    $labor->manoDeObra()->create([
                        'tipo_id' => $item['tipo_id'],
                        'cantidad_trabajadores' => $item['cantidad'],
                        'dias_trabajados' => $item['dias'],
                        'costo_por_dia' => $item['costo_dia'],
                        'subtotal' => ($item['cantidad'] * $item['dias'] * $item['costo_dia'])
                    ]);
                }
            }

            // 3. Maquinaria
            $labor->maquinaria()->delete();
            foreach($this->itemsMaquinaria as $item) {
                if (!empty($item['nombre'])) {
                    $labor->maquinaria()->create([
                        'nombre_maquinaria' => $item['nombre'],
                        'labor_realizada' => $item['labor'] ?: 'Cosecha',
                        'horas_trabajadas' => $item['horas'],
                        'costo_total' => $item['costo_total']
                    ]);
                }
            }

            // 4. Resultados de Cosecha (Producción)
            $idsMantener = collect($this->itemsCosecha)->pluck('id')->filter()->toArray();
            $cosechasAEliminar = \App\Models\Cosecha::where('labor_id', $labor->id)
                ->whereNotIn('id', $idsMantener)
                ->get();

            foreach($cosechasAEliminar as $cos) {
                if ($cos->ventas()->exists()) {
                    throw new \Exception("No se puede eliminar la cosecha de calidad {$cos->calidad} porque ya tiene ventas registradas.");
                }
                $cos->delete();
            }

            $crop = Cultivo::find($this->cropId);
            foreach ($this->itemsCosecha as $item) {
                if ($item['cantidad'] > 0) {
                    $cosechaData = [
                        'labor_id' => $labor->id,
                        'fecha_cosecha' => $this->fecha_cosecha_edit,
                        'cantidad_kg' => $item['cantidad'],
                        'unidad_medida' => $item['unidad'],
                        'calidad' => $item['calidad'],
                        'lote_codigo' => $crop->nombre_lote,
                        'costo_operativo_cosecha' => $item['costo_operativo'] ?: 0,
                        'observaciones' => $this->observaciones_cosecha
                    ];

                    if (!empty($item['id'])) {
                        \App\Models\Cosecha::where('id', $item['id'])->update($cosechaData);
                    } else {
                        \App\Models\Cosecha::create($cosechaData);
                    }
                }
            }

            $crop->update(['fecha_cosecha_finalizada' => $this->fecha_cosecha_edit]);
        });

        $this->dispatch('close-modal', 'modal-edit-harvest-result');
        session()->flash('status', 'Resultados de cosecha actualizados.');
    }

    public function edit($id)
    {
        $c = Cultivo::findOrFail($id);
        $this->cropId = $c->id;
        $this->cropNombreSelected = strtoupper($c->nombre_lote);
        $this->terreno_id = $c->terreno_id;
        $this->catalogo_cultivo_id = $c->catalogo_cultivo_id;
        $this->nombre_lote = $c->nombre_lote;
        $this->variedad = $c->variedad;
        $this->fecha_planificada = $c->fecha_planificada->format('Y-m-d');
        $this->fecha_siembra = $c->fecha_siembra ? $c->fecha_siembra->format('Y-m-d') : '';
        $this->fecha_cosecha_estimada = $c->fecha_cosecha_estimada ? $c->fecha_cosecha_estimada->format('Y-m-d') : '';
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

    public function save()
    {
        $this->validate([
            'terreno_id' => 'required',
            'catalogo_cultivo_id' => 'required',
            'nombre_lote' => 'required',
            'area_destinada' => "required|numeric|min:0.01",
            'fecha_planificada' => 'required|date',
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
            'variedad' => $this->variedad,
            'fecha_planificada' => $this->fecha_planificada,
            'fecha_siembra' => $this->fecha_siembra ?: $this->fecha_planificada,
            'fecha_cosecha_estimada' => $this->fecha_cosecha_estimada ?: null,
            'estado' => $this->estado,
            'area_destinada' => $this->area_destinada,
            'plantas_estimadas' => $this->plantas_estimadas ?: 0,
            'rendimiento_esperado_tn_ha' => $this->rendimiento_esperado_tn_ha ?: 0,
            'observaciones' => $this->observaciones,
            'foto_path' => $photoPath,
        ];

        Cultivo::find($this->cropId)->update($data);

        $this->dispatch('close-modal', 'modal-crop-manager');
        $this->resetForm();
        session()->flash('status', 'Información de campaña actualizada.');
    }

    public function resetForm()
    {
        $this->reset([
            'cropId', 'terreno_id', 'catalogo_cultivo_id', 'nombre_lote', 'variedad',
            'fecha_planificada', 'fecha_siembra', 'fecha_cosecha_estimada',
            'estado', 'area_destinada', 'plantas_estimadas', 'rendimiento_esperado_tn_ha',
            'observaciones', 'cropPhoto', 'currentPhotoPath', 'areaDisponible',
            'terrenoNombreSelected', 'cultivoNombreSelected', 'cropNombreSelected', 'queryTerreno', 'queryCultivo'
        ]);
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
            $idLabel = $this->cropId ?: (Cultivo::max('id') + 1);
            $fecha = $this->fecha_siembra ? date('d-m-Y', strtotime($this->fecha_siembra)) : date('d-m-Y');
            $this->nombre_lote = "{$idLabel}-" . strtoupper($cultivoNombre) . "-" . strtoupper($this->variedad ?: 'GENERICA') . "-{$fecha}";
        }
    }

    public function updatedVariedad() { $this->generarNombreLote(); }
    public function updatedFechaSiembra() { $this->generarNombreLote(); }

    public function render()
    {
        $user = Auth::user();
        $query = Cultivo::where('estado', 'Cosechado');

        // Búsqueda dinámica para el Modal
        $resultsTerrenos = [];
        if (strlen(trim($this->queryTerreno)) > 0) {
            $resultsTerrenos = Terreno::where('usuario_id', $user->id)
                ->where('nombre', 'like', "%" . trim($this->queryTerreno) . "%")
                ->take(5)->get()->map(function($t) {
                    $ocupado = Cultivo::where('terreno_id', $t->id)->whereIn('estado', ['Planificado', 'En crecimiento'])->where('id', '!=', $this->cropId)->sum('area_destinada');
                    $t->disponible = max(0, $t->hectareas - $ocupado);
                    return $t;
                });
        }

        $resultsCatalogo = [];
        if (strlen(trim($this->queryCultivo)) > 0) {
            $resultsCatalogo = CatalogoCultivo::where('nombre', 'like', '%' . trim($this->queryCultivo) . '%')
                ->take(5)->get();
        }

        $resultsIns = [];
        if ($this->showIns && strlen(trim($this->queryIns)) > 0) {
            $resultsIns = \App\Models\CatalogoInsumo::where('nombre', 'like', '%' . trim($this->queryIns) . '%')
                ->take(5)->get();
        }

        $selTerreno = $this->terreno_id ? Terreno::find($this->terreno_id) : null;
        $selCultivo = $this->catalogo_cultivo_id ? CatalogoCultivo::find($this->catalogo_cultivo_id) : null;

        // Lógica de visibilidad (simplificada pero segura)
        if ($this->selectedOrgId) {
            $query->whereHas('terreno', function($q) {
                $q->where('organizacion_id', $this->selectedOrgId)
                  ->orWhere('usuario_id', Auth::id());
            });
        } else {
            $query->whereHas('terreno', function($q) {
                $q->where('usuario_id', Auth::id());
            });
        }

        if ($this->searchCultivo) {
            $query->whereHas('detalleCatalogo', fn($q) => $q->where('nombre', 'like', "%{$this->searchCultivo}%"));
        }

        $cosechas = $query->with(['terreno.latestClima', 'detalleCatalogo', 'labores.cosechas'])->orderBy('updated_at', 'desc')->paginate(12);

        // Datos para gráfico general de rendimiento
        $stats = [
            'labels' => [],
            'ganancias' => [],
            'produccion' => []
        ];

        $allCosechados = clone $query;
        $grouped = $allCosechados->get()->groupBy('detalleCatalogo.nombre');

        foreach ($grouped as $nombre => $items) {
            $stats['labels'][] = $nombre;
            $gananciaTotal = 0;
            $prodTotal = 0; // Solo sumaremos Calidad Primera para el KPI de peso, evitando mezclar tipos

            foreach ($items as $item) {
                $ingresos = 0;
                $gastos = $item->labores->sum('costo_total');
                foreach ($item->labores as $l) {
                    // Sumamos Primera con Primera únicamente (Normalizando para evitar fallos por mayúsculas)
                    $prodTotal += $l->cosechas->filter(fn($cos) => strtolower($cos->calidad) === 'primera')->sum('cantidad_kg');

                    foreach ($l->cosechas as $cos) {
                        foreach ($cos->ventas as $v) {
                            $ingresos += ($v->cantidad_vendida_kg * $v->precio_por_kg);
                        }
                    }
                }
                $gananciaTotal += ($ingresos - $gastos);
            }
            $stats['ganancias'][] = $gananciaTotal;
            $stats['produccion'][] = $prodTotal;
        }

        return view('livewire.admin.cosechas-manager', [
            'cosechas' => $cosechas,
            'stats' => $stats,
            'resultsTerrenos' => $resultsTerrenos,
            'resultsCatalogo' => $resultsCatalogo,
            'selectedTerrenoModel' => $selTerreno,
            'selectedCultivoModel' => $selCultivo,
            'manoObraTipos' => \App\Models\ManoObraTipo::all(),
            'resultsIns' => $resultsIns,
            'hasLaboresCosecha' => Cultivo::whereHas('terreno', function($q) use ($user) {
                if ($this->selectedOrgId) {
                    $q->where('organizacion_id', $this->selectedOrgId)->orWhere('usuario_id', Auth::id());
                } else {
                    $q->where('usuario_id', Auth::id());
                }
            })->whereHas('labores.detalleCatalogo', fn($q) => $q->where('categoria', 'cosecha'))->exists()
        ]);
    }
}

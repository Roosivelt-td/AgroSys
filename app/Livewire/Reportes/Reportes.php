<?php

namespace App\Livewire\Reportes;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Cultivo;
use App\Models\Labor;
use App\Models\Venta;
use App\Models\Cosecha;
use App\Models\CatalogoCultivo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Reportes Detallados')]
class Reportes extends Component
{
    public $filtro = 'todo';
    public $fechaInicio;
    public $fechaFin;
    public $catalogoCultivoId = '';
    public $cultivoId = '';
    public $search = '';

    public $listaCatalogo = [];
    public $listaCultivos = [];
    public $datos = [];
    public $detalleInversiones = [];

    public function mount()
    {
        $this->fechaInicio = '';
        $this->fechaFin = now()->format('Y-m-d');
        $this->cargarListas();
        $this->actualizarDatos();
    }

    public function cargarListas()
    {
        $user = Auth::user();

        // Cargar solo los tipos de cultivo (Catálogo) que el usuario realmente ha sembrado
        $this->listaCatalogo = CatalogoCultivo::whereHas('cultivosRealizados', function($q) use ($user) {
            $q->whereHas('terreno', fn($t) => $t->where('usuario_id', $user->id));
        })->orderBy('nombre')->get();

        $this->listaCultivos = Cultivo::whereHas('terreno', fn($q) => $q->where('usuario_id', $user->id))
            ->with('detalleCatalogo')
            ->when($this->search, function($q) {
                $q->where(function($sq) {
                    $sq->where('nombre_lote', 'like', '%' . $this->search . '%')
                      ->orWhereHas('detalleCatalogo', fn($ssq) => $ssq->where('nombre', 'like', '%' . $this->search . '%'));
                });
            })
            ->orderBy('fecha_siembra', 'desc')->get();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filtro', 'fechaInicio', 'fechaFin', 'catalogoCultivoId', 'cultivoId', 'search'])) {
            if ($propertyName === 'search') {
                $this->cargarListas();
            }
            if ($propertyName === 'filtro' && $this->filtro !== 'personalizado') {
                $this->ajustarFechasPorPredefinido();
            }
            $this->actualizarDatos();
        }
    }

    private function ajustarFechasPorPredefinido()
    {
        if ($this->filtro === 'todo') $this->fechaInicio = '';
        elseif ($this->filtro === 'semana') $this->fechaInicio = now()->startOfWeek()->format('Y-m-d');
        elseif ($this->filtro === 'mes') $this->fechaInicio = now()->startOfMonth()->format('Y-m-d');
        elseif ($this->filtro === 'anio') $this->fechaInicio = now()->startOfYear()->format('Y-m-d');
        $this->fechaFin = now()->format('Y-m-d');
    }

    public function actualizarDatos()
    {
        $user = Auth::user();

        if ($this->cultivoId) {
            $inicio = Carbon::parse('2000-01-01')->startOfDay();
            $fin = Carbon::parse('2099-12-31')->endOfDay();
        } else {
            $inicio = $this->fechaInicio ? Carbon::parse($this->fechaInicio)->startOfDay() : Carbon::parse('2000-01-01')->startOfDay();
            $fin = Carbon::parse($this->fechaFin)->endOfDay();
        }

        // Producción y Eficiencia
        $this->datos['cosecha'] = Cosecha::whereHas('labor.cultivo.terreno', fn($q) => $q->where('usuario_id', $user->id))
            ->whereBetween('fecha_cosecha', [$inicio, $fin])
            ->when($this->cultivoId, fn($q) => $q->whereHas('labor', fn($l) => $l->where('cultivo_id', $this->cultivoId)))
            ->sum('cantidad_kg');

        $hectareas = Cultivo::whereHas('terreno', fn($q) => $q->where('usuario_id', $user->id))
            ->when($this->cultivoId, fn($q) => $q->where('id', $this->cultivoId))
            ->sum('area_destinada') ?: 1;

        $this->datos['rendimiento_ha'] = $this->datos['cosecha'] / $hectareas;

        // Ventas y Precios
        $queryVentas = Venta::with(['comprador', 'cosecha.labor'])->whereHas('cosecha.labor.cultivo.terreno', fn($q) => $q->where('usuario_id', $user->id))
            ->whereBetween('fecha_venta', [$inicio, $fin])
            ->when($this->cultivoId, fn($q) => $q->whereHas('cosecha.labor', fn($l) => $l->where('cultivo_id', $this->cultivoId)));

        $ventas_lista = $queryVentas->get();
        $this->datos['ventas_brutas'] = $ventas_lista->sum(fn($v) => ($v->cantidad_vendida_kg * $v->precio_por_kg));
        $total_kg_v = $ventas_lista->sum('cantidad_vendida_kg');
        $this->datos['precio_promedio'] = $total_kg_v > 0 ? ($this->datos['ventas_brutas'] / $total_kg_v) : 0;

        // Flete Inteligente
        $this->datos['total_flete'] = $ventas_lista->groupBy(function($v) {
            return $v->fecha_venta->format('Y-m-d') . '_' . $v->comprador_id . '_' . ($v->cosecha->labor->cultivo_id ?? '0');
        })->sum(fn($g) => $g->max('costo_flete'));

        if ($this->cultivoId) {
            $this->cargarDetalleUnicoCultivo($this->cultivoId, $inicio, $fin);

            // For individual report, we use the wide range already calculated in cargarDetalleUnicoCultivo
            $this->datos['alquiler_terreno'] = $this->detalleInversiones['alquiler'];
            $this->datos['mo_total'] = $this->detalleInversiones['labores']->sum('costo_mano_obra_total');
            $this->datos['maq_total'] = $this->detalleInversiones['labores']->sum('costo_maquinaria_total');
            $this->datos['ins_total'] = $this->detalleInversiones['labores']->sum(fn($l) => $l->costo_total - ($l->costo_mano_obra_total + $l->costo_maquinaria_total));
            $this->datos['cos_lab_total'] = $this->detalleInversiones['labores_cosecha']->sum('costo_total');
            $this->datos['total_inversion'] = $this->datos['alquiler_terreno'] + $this->detalleInversiones['labores']->sum('costo_total') + $this->datos['cos_lab_total'] + $this->datos['total_flete'];
        } else {
            $this->cargarResumenPorLotes($user, $inicio, $fin);

            // Header KPIs based on the sum of campaign totals of active/selected crops
            $this->datos['alquiler_terreno'] = $this->datos['tabla_resumen_v3']->sum('alquiler');
            $this->datos['total_flete'] = $this->datos['tabla_resumen_v3']->sum('flete');
            $this->datos['ventas_brutas'] = $this->datos['tabla_resumen_v3']->sum('venta_bruta');

            // Breakdown from summary data
            $this->datos['mo_total'] = $this->datos['tabla_resumen_v3']->sum('mo_total');
            $this->datos['maq_total'] = $this->datos['tabla_resumen_v3']->sum('maq_total');
            $this->datos['ins_total'] = $this->datos['tabla_resumen_v3']->sum('ins_total');
            $this->datos['cos_lab_total'] = $this->datos['tabla_resumen_v3']->sum('cosecha');

            $this->datos['total_inversion'] = $this->datos['alquiler_terreno'] + $this->datos['mo_total'] + $this->datos['maq_total'] + $this->datos['ins_total'] + $this->datos['cos_lab_total'] + $this->datos['total_flete'];
        }

        $this->datos['utilidad_neta'] = $this->datos['ventas_brutas'] - $this->datos['total_inversion'];
        $this->datos['roi'] = $this->datos['total_inversion'] > 0 ? ($this->datos['utilidad_neta'] / $this->datos['total_inversion']) * 100 : 0;

        if ($this->cultivoId) {
            $this->cargarDetalleUnicoCultivo($this->cultivoId, $inicio, $fin);
        } else {
            $this->cargarResumenPorLotes($user, $inicio, $fin);
        }

        $this->datos['top_insumos'] = $this->getTopInsumos($inicio, $fin);
        $this->datos['detalle_categorias'] = $this->getDetalleGastosCategorias($inicio, $fin);
        $this->datos['ultimas_ventas'] = $this->getUltimasVentas($inicio, $fin);
    }

    private function calcularAlquiler($user, $inicio, $fin)
    {
        return DB::table('terrenos')->where('usuario_id', $user->id)->where('tipo_tenencia', 'alquilado')
            ->when($this->cultivoId, fn($q) => $q->whereExists(fn($sub) => $sub->select(DB::raw(1))->from('cultivos')->whereRaw('cultivos.terreno_id = terrenos.id')->where('cultivos.id', $this->cultivoId)))
            ->sum('costo_alquiler_anual');
    }

    private function cargarResumenPorLotes($user, $inicio, $fin)
    {
        $cultivos = Cultivo::with(['terreno', 'detalleCatalogo', 'labores.detalleCatalogo', 'cosechas.ventas'])
            ->whereHas('terreno', fn($q) => $q->where('usuario_id', $user->id))
            ->where(function($q) use ($inicio, $fin) {
                $q->whereBetween('fecha_siembra', [$inicio, $fin])
                  ->orWhereBetween('fecha_cosecha_finalizada', [$inicio, $fin])
                  ->orWhereHas('labores', fn($l) => $l->whereBetween('fecha_realizacion', [$inicio, $fin]))
                  ->orWhereHas('cosechas.ventas', fn($v) => $v->whereBetween('fecha_venta', [$inicio, $fin]))
                  ->orWhereNull('fecha_cosecha_finalizada');
            })
            ->get();

        $lotes_procesados = collect();

        foreach ($cultivos as $c) {
            $alquiler = $c->terreno->tipo_tenencia === 'alquilado' ? $c->terreno->costo_alquiler_anual : 0;

            // Labores de Mantenimiento (Total Campaña)
            $lab_mantenimiento = $c->labores->filter(fn($l) => strtolower($l->detalleCatalogo->categoria ?? '') !== 'cosecha');
            $lab_ops = $lab_mantenimiento->sum('costo_total');

            $mo_lote = $lab_mantenimiento->sum('costo_mano_obra_total');
            $maq_lote = $lab_mantenimiento->sum('costo_maquinaria_total');
            $ins_lote = $lab_mantenimiento->sum(fn($l) => $l->costo_total - ($l->costo_mano_obra_total + $l->costo_maquinaria_total));

            $ventas_periodo = $c->cosechas->flatMap->ventas->whereBetween('fecha_venta', [$inicio, $fin])->sortBy('fecha_venta');

            // Agrupar por Cosecha record
            $cosechas_con_ventas = $ventas_periodo->groupBy('cosecha_id');

            $sale_rows = [];
            $total_flete_lote = 0;
            $v_bruta_lote = 0;
            $total_cos_lote = 0;

            foreach ($cosechas_con_ventas as $cosId => $ventasDeCosecha) {
                $cosechaRec = $ventasDeCosecha->first()->cosecha;
                $costoCos = $cosechaRec->labor->costo_total ?? 0;
                $total_cos_lote += $costoCos;

                $grupos = $ventasDeCosecha->groupBy(fn($v) => $v->fecha_venta->format('Y-m-d') . '|' . ($v->cosecha->calidad ?? 'N/A') . '|' . ($v->cosecha->unidad_medida ?? 'Kg') . '|' . $v->precio_por_kg);

                $firstInCos = true;
                $spanCos = count($grupos);

                foreach ($grupos as $key => $grupo) {
                    list($fecha, $calidad, $unidad, $precio) = explode('|', $key);
                    $cant = $grupo->sum('cantidad_vendida_kg');
                    $subt = $grupo->sum(fn($v) => $v->cantidad_vendida_kg * $v->precio_por_kg);
                    $v_bruta_lote += $subt;

                    $viajes_flete = $grupo->groupBy(fn($v) => $v->fecha_venta->format('Y-m-d') . '_' . $v->comprador_id);
                    $flete_grp = $viajes_flete->sum(fn($g) => $g->max('costo_flete'));
                    $total_flete_lote += $flete_grp;

                    $sale_rows[] = (object)[
                        'fecha' => Carbon::parse($fecha)->format('d/m/Y'),
                        'produccion' => (float)$cant . " " . $unidad . " (" . ucfirst($calidad) . ")",
                        'precio' => (float)$precio,
                        'bruto' => $subt,
                        'flete' => $flete_grp,
                        'costo_cosecha' => $costoCos,
                        'cosecha_span' => ($firstInCos ? $spanCos : 0)
                    ];
                    $firstInCos = false;
                }
            }

            if (empty($sale_rows)) {
                $sale_rows[] = (object)['fecha' => '---', 'produccion' => 'SIN VENTAS', 'precio' => 0, 'bruto' => 0, 'flete' => 0, 'costo_cosecha' => 0, 'cosecha_span' => 1];
            }

            $lotes_procesados->push((object)[
                'lote' => $c->nombre_lote,
                'cultivo' => "{$c->detalleCatalogo->nombre} ({$c->variedad})",
                'fechas' => ($c->fecha_siembra ? $c->fecha_siembra->format('d/m/y') : 'S/F') . " al " . ($c->fecha_cosecha_finalizada ? $c->fecha_cosecha_finalizada->format('d/m/y') : 'PND'),
                'alquiler' => $alquiler,
                'labores' => $lab_ops,
                'mo_total' => $mo_lote,
                'maq_total' => $maq_lote,
                'ins_total' => $ins_lote,
                'cosecha' => $total_cos_lote,
                'flete' => $total_flete_lote,
                'venta_bruta' => $v_bruta_lote,
                'ganancia' => $v_bruta_lote - ($alquiler + $lab_ops + $total_cos_lote + $total_flete_lote),
                'filas' => $sale_rows,
                'span' => count($sale_rows)
            ]);
        }
        $this->datos['tabla_resumen_v3'] = $lotes_procesados;

        $this->datos['resumen_ejecutivo'] = $lotes_procesados->map(fn($l) => (object)[
            'lote' => $l->lote,
            'cultivo' => $l->cultivo,
            'fechas' => $l->fechas,
            'alquiler' => $l->alquiler,
            'labores' => $l->labores,
            'cosecha' => $l->cosecha,
            'flete' => $l->flete,
            'inversion_total' => $l->alquiler + $l->labores + $l->cosecha + $l->flete,
            'venta_bruta' => $l->venta_bruta,
            'ganancia' => $l->ganancia
        ]);
    }

    private function cargarDetalleUnicoCultivo($id, $inicio, $fin)
    {
        $cultivo = Cultivo::with(['labores.detalleCatalogo', 'labores.insumos.detalleCatalogo', 'cosechas.ventas.comprador', 'terreno', 'detalleCatalogo'])->find($id);
        $labores = $cultivo->labores()->orderBy('fecha_realizacion', 'asc')->get();

        $lab_mantenimiento = $labores->filter(fn($l) => ($l->detalleCatalogo->categoria ?? '') !== 'cosecha');
        $lab_cosecha = $labores->filter(fn($l) => ($l->detalleCatalogo->categoria ?? '') === 'cosecha');

        // Procesamiento de Ventas para Flete Inteligente (Misma Fecha Venta + Comprador + Cultivo)
        $ventas_raw = $cultivo->cosechas->flatMap->ventas->sortBy([['fecha_venta', 'asc'], ['comprador_id', 'asc']]);

        // Agrupar ventas idénticas (mismo comprador, fecha, cosecha y precio) para "juntar habas"
        $ventas_agrupadas = $ventas_raw->groupBy(function($v) {
            return $v->fecha_venta->format('Y-m-d') . '_' .
                   $v->comprador_id . '_' .
                   $v->cosecha->fecha_cosecha->format('Y-m-d') . '_' .
                   $v->precio_por_kg;
        })->map(function($grupo) {
            $first = $grupo->first();
            $first->cantidad_vendida_kg = $grupo->sum('cantidad_vendida_kg');
            return $first;
        })->values()->sortBy([['fecha_venta', 'asc'], ['comprador_id', 'asc']]);

        $processed_ventas = collect();
        $v_items = $ventas_agrupadas;
        $count = $v_items->count();

        for ($i = 0; $i < $count; $i++) {
            $v = $v_items[$i];
            $key = $v->fecha_venta->format('Y-m-d') . '_' . $v->comprador_id . '_' . $v->cosecha->labor->cultivo_id;

            if ($i == 0 || ($v_items[$i-1]->fecha_venta->format('Y-m-d') . '_' . $v_items[$i-1]->comprador_id . '_' . $v_items[$i-1]->cosecha->labor->cultivo_id) != $key) {
                $span = 1;
                for ($j = $i + 1; $j < $count; $j++) {
                    if (($v_items[$j]->fecha_venta->format('Y-m-d') . '_' . $v_items[$j]->comprador_id . '_' . $v_items[$j]->cosecha->labor->cultivo_id) == $key) $span++;
                    else break;
                }
                $v->flete_span = $span;
            } else {
                $v->flete_span = 0;
            }
            $processed_ventas->push($v);
        }

        $this->detalleInversiones = [
            'info' => (object)['lote' => $cultivo->nombre_lote, 'cultivo' => $cultivo->detalleCatalogo->nombre, 'variedad' => $cultivo->variedad, 'fecha_siembra' => $cultivo->fecha_siembra, 'fecha_cosecha' => $cultivo->fecha_cosecha_finalizada ?? $cultivo->fecha_cosecha_estimada, 'hectareas' => $cultivo->area_destinada ?? $cultivo->terreno->hectareas],
            'chartData' => [
                'labels' => ['Mano Obra', 'Maquinaria', 'Insumos', 'Cosecha'],
                'values' => [
                    (float)$lab_mantenimiento->sum('costo_mano_obra_total'),
                    (float)$lab_mantenimiento->sum('costo_maquinaria_total'),
                    (float)$lab_mantenimiento->sum(fn($l) => $l->costo_total - ($l->costo_mano_obra_total + $l->costo_maquinaria_total)),
                    (float)$lab_cosecha->sum('costo_total')
                ]
            ],
            'stats' => [
                'inversion_total' => ($cultivo->terreno->tipo_tenencia === 'alquilado' ? (float)$cultivo->terreno->costo_alquiler_anual : 0) + (float)$labores->sum('costo_total'),
                'venta_total' => (float)$ventas_agrupadas->sum(fn($v) => $v->cantidad_vendida_kg * $v->precio_por_kg),
            ],
            'alquiler' => $cultivo->terreno->tipo_tenencia === 'alquilado' ? $cultivo->terreno->costo_alquiler_anual : 0,
            'labores' => $lab_mantenimiento,
            'labores_cosecha' => $lab_cosecha,
            'ventas' => $processed_ventas
        ];
    }

    private function getTopInsumos($inicio, $fin) {
        return DB::table('insumos_usados')->join('catalogo_insumos', 'insumos_usados.catalogo_insumo_id', '=', 'catalogo_insumos.id')->join('labores', 'insumos_usados.labor_id', '=', 'labores.id')->join('cultivos', 'labores.cultivo_id', '=', 'cultivos.id')->join('terrenos', 'cultivos.terreno_id', '=', 'terrenos.id')
            ->select('catalogo_insumos.nombre', 'catalogo_insumos.unidad_medida', DB::raw('SUM(insumos_usados.cantidad) as total_cant'), DB::raw('SUM(insumos_usados.cantidad * insumos_usados.costo_unitario) as inversion'))
            ->where('terrenos.usuario_id', Auth::id())->whereBetween('labores.fecha_realizacion', [$inicio, $fin])->when($this->cultivoId, fn($q) => $q->where('labores.cultivo_id', $this->cultivoId))->groupBy('catalogo_insumos.nombre', 'catalogo_insumos.unidad_medida')->orderByDesc('inversion')->take(6)->get();
    }

    private function getDetalleGastosCategorias($inicio, $fin) {
        return DB::table('labores')->join('catalogo_labores', 'labores.catalogo_labor_id', '=', 'catalogo_labores.id')->join('cultivos', 'labores.cultivo_id', '=', 'cultivos.id')->join('terrenos', 'cultivos.terreno_id', '=', 'terrenos.id')
            ->select('catalogo_labores.nombre', DB::raw('SUM(labores.costo_total) as total'))
            ->where('terrenos.usuario_id', Auth::id())->whereBetween('labores.fecha_realizacion', [$inicio, $fin])->when($this->cultivoId, fn($q) => $q->where('labores.cultivo_id', $this->cultivoId))->groupBy('catalogo_labores.nombre')->get();
    }

    private function getUltimasVentas($inicio, $fin) {
        return Venta::with(['comprador', 'cosecha.labor.cultivo.detalleCatalogo'])->whereHas('cosecha.labor.cultivo.terreno', fn($q) => $q->where('usuario_id', Auth::id()))
            ->whereBetween('fecha_venta', [$inicio, $fin])->when($this->cultivoId, fn($q) => $q->whereHas('cosecha.labor', fn($l) => $l->where('cultivo_id', $this->cultivoId)))->orderBy('fecha_venta', 'desc')->take(10)->get();
    }

    public function render() { return view('livewire.reportes.reportes'); }
}

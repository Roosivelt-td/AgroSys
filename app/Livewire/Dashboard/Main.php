<?php

namespace App\Livewire\Dashboard;

use App\Models\Solicitud;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\Labor;
use App\Models\Venta;
use App\Models\HistorialProceso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Title;

/**
 * LÓGICA (BACKEND) - Dashboard Principal Analítico de Alta Fidelidad.
 */
#[Title('Dashboard')]
class Main extends Component
{
    public $cultivoFiltroId = ''; // ID para filtro interactivo por campaña
    public $selectedLabor = null; // Labor seleccionada para el modal

    /**
     * Carga el detalle de una labor específica para mostrar en el modal.
     */
    public function mostrarDetalleLabor($id)
    {
        $this->selectedLabor = Labor::with(['detalleCatalogo', 'cultivo.detalleCatalogo', 'insumos.producto', 'manoDeObra.personal', 'maquinaria.maquina'])
            ->findOrFail($id);

        $this->dispatch('open-modal', 'labor-detail-modal');
    }

    public function render()
    {
        $usuario = Auth::user();

        // 1. Contexto y Membresía (Lógica para Administradores de Finca, Supervisores y Agricultores)
        $membresia = $usuario->membresias()->where('estado', 1)->first();
        $solicitudPendiente = Solicitud::where('solicitante_usuario_id', $usuario->id)
            ->where('tipo', 'creacion_organizacion')->where('estado', 0)->first();

        $contexto = function($query) use ($usuario, $membresia) {
            if ($membresia) return $query->where('organizacion_id', $membresia->organizacion_id);
            return $query->where('usuario_id', $usuario->id);
        };

        // 2. Consultas Maestras
        $queryTerrenos = Terreno::query(); $contexto($queryTerrenos);
        $queryCultivos = Cultivo::whereHas('terreno', fn($q) => $contexto($q));
        $queryLabores = Labor::whereHas('cultivo.terreno', fn($q) => $contexto($q));
        $queryVentas = Venta::whereHas('cosecha.labor.cultivo.terreno', fn($q) => $contexto($q));

        // 3. Lista de Cultivos para el Filtro Superior
        $listaCultivosFiltro = (clone $queryCultivos)->with('detalleCatalogo')->orderBy('fecha_siembra', 'desc')->get();

        $cultivoSeleccionado = null;
        if ($this->cultivoFiltroId) {
            $cultivoSeleccionado = Cultivo::with('detalleCatalogo')->find($this->cultivoFiltroId);
            $queryCultivos->where('id', $this->cultivoFiltroId);
            $queryLabores->where('cultivo_id', $this->cultivoFiltroId);
            $queryVentas->whereHas('cosecha.labor', fn($l) => $l->where('cultivo_id', $this->cultivoFiltroId));
            $queryTerrenos->whereHas('cultivos', fn($c) => $c->where('id', $this->cultivoFiltroId));
        }

        $totalTerrenosCount = $queryTerrenos->count();

        // 4. Cálculos para Gráficos Cronológicos
        $anio = date('Y');
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $sVentas = array_fill(0, 12, 0); $sInversiones = array_fill(0, 12, 0); $sGanancias = array_fill(0, 12, 0); $sActividad = array_fill(0, 12, 0); $sROI = array_fill(0, 12, 0);

        $vData = (clone $queryVentas)->whereYear('fecha_venta', $anio)->select(DB::raw('MONTH(fecha_venta) as m'), DB::raw('SUM(cantidad_vendida_kg * precio_por_kg) as t'))->groupBy('m')->get();
        foreach($vData as $v) $sVentas[$v->m - 1] = (float)$v->t;

        $lData = (clone $queryLabores)->whereYear('fecha_realizacion', $anio)->select(DB::raw('MONTH(fecha_realizacion) as m'), DB::raw('SUM(costo_total) as t'))->groupBy('m')->get();
        foreach($lData as $l) $sInversiones[$l->m - 1] += (float)$l->t;

        $fData = (clone $queryVentas)->whereYear('fecha_venta', $anio)->select(DB::raw('MONTH(fecha_venta) as m'), DB::raw('SUM(costo_flete) as t'))->groupBy('m')->get();
        foreach($fData as $f) $sInversiones[$f->m - 1] += (float)$f->t;

        for($i=0; $i<12; $i++) {
            $sGanancias[$i] = $sVentas[$i] - $sInversiones[$i];
            if ($sInversiones[$i] > 0) {
                $sROI[$i] = round(($sGanancias[$i] / $sInversiones[$i]) * 100, 1);
            }
        }

        $cData = (clone $queryCultivos)->whereYear('fecha_siembra', $anio)->select(DB::raw('MONTH(fecha_siembra) as m'), DB::raw('COUNT(*) as t'))->groupBy('m')->get();
        foreach($cData as $c) $sActividad[$c->m - 1] = $c->t;

        // 5. KPIs y Análisis de Rendimiento
        $totVentas = $queryVentas->get()->sum(fn($v) => $v->cantidad_vendida_kg * $v->precio_por_kg);

        $laboresQuery = (clone $queryLabores);
        $ventasQuery = (clone $queryVentas);

        $manoObraTotal = (clone $laboresQuery)->sum('costo_mano_obra_total');
        $maquinariaTotal = (clone $laboresQuery)->sum('costo_maquinaria_total');
        $fleteTotal = (clone $ventasQuery)->sum('costo_flete');
        $insumosTotal = (clone $laboresQuery)->sum('costo_total') - ($manoObraTotal + $maquinariaTotal);
        if ($insumosTotal < 0) $insumosTotal = 0;

        $totInversiones = (clone $laboresQuery)->sum('costo_total') + (clone $queryTerrenos)->where('tipo_tenencia', 'alquilado')->sum('costo_alquiler_anual') + $fleteTotal;
        $gananciaTotal = $totVentas - $totInversiones;

        $totalAreaLote = (clone $queryCultivos)->sum('area_destinada') ?: 1;
        $totalKgCosechados = (clone $queryVentas)->sum('cantidad_vendida_kg');
        $tnRealHa = ($totalKgCosechados / 1000) / $totalAreaLote;
        $tnEstimadaHa = (clone $queryCultivos)->avg('rendimiento_esperado_tn_ha') ?: 0;

        // Progreso del Cultivo Filtrado
        $progresoCultivo = 0;
        if ($cultivoSeleccionado && $cultivoSeleccionado->fecha_siembra && $cultivoSeleccionado->fecha_cosecha_estimada) {
            $inicio = $cultivoSeleccionado->fecha_siembra;
            $fin = $cultivoSeleccionado->fecha_cosecha_estimada;
            $totalDias = $inicio->diffInDays($fin) ?: 1;
            $diasTranscurridos = $inicio->diffInDays(now());
            $progresoCultivo = min(100, max(0, round(($diasTranscurridos / $totalDias) * 100)));
        }

        $stats = [
            'terrenos' => $totalTerrenosCount,
            'cultivos' => $queryCultivos->count(),
            'labores' => (clone $queryLabores)->where('estado', 'Pendiente')->count(),
            'ventas' => $totVentas,
            'inversiones' => $totInversiones,
            'ganancia' => $gananciaTotal,
            'roi' => ($totInversiones > 0) ? round(($gananciaTotal / $totInversiones) * 100, 1) : 0,
            'margen_ha' => round($gananciaTotal / $totalAreaLote, 0),
            'yield' => ['real' => round($tnRealHa, 2), 'estimado' => round($tnEstimadaHa, 2)],
            'progreso' => $progresoCultivo,
            'invest_breakdown' => [
                'insumos' => (float)$insumosTotal,
                'mano_obra' => (float)$manoObraTotal,
                'maquinaria' => (float)$maquinariaTotal,
                'cosecha' => (clone $laboresQuery)->whereHas('detalleCatalogo', fn($q) => $q->where('nombre', 'like', '%cosecha%'))->sum('costo_total'),
                'flete' => (float)$fleteTotal
            ]
        ];

        // 6. ÚLTIMAS 5 LABORES
        $ultimas5Labores = (clone $queryLabores)->with(['detalleCatalogo', 'cultivo.detalleCatalogo'])->orderBy('fecha_realizacion', 'desc')->take(5)->get();

        // 7. LABORES CRÍTICAS
        $laboresCriticas = (clone $queryLabores)->with(['detalleCatalogo', 'cultivo.detalleCatalogo'])
            ->where('estado', 'Pendiente')
            ->where('fecha_realizacion', '<=', now()->addHours(48))
            ->orderBy('fecha_realizacion', 'asc')
            ->take(3)
            ->get();

        return view('livewire.dashboard.main', [
            'hasOrg' => ($totalTerrenosCount > 0 || $membresia),
            'isWaiting' => (bool)$solicitudPendiente,
            'solicitud' => $solicitudPendiente,
            'organizacion' => $membresia ? $membresia->organizacion : (object)['nombre' => 'Mis Terrenos'],
            'stats' => $stats,
            'ultimas5Labores' => $ultimas5Labores,
            'laboresCriticas' => $laboresCriticas,
            'cultivoSeleccionado' => $cultivoSeleccionado,
            'listaCultivosFiltro' => $listaCultivosFiltro,
            'chartData' => ['meses' => $meses, 'ventas' => $sVentas, 'inversiones' => $sInversiones, 'ganancias' => $sGanancias, 'actividad' => $sActividad, 'roi' => $sROI]
        ]);
    }
}

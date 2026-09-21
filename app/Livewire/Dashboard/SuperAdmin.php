<?php

namespace App\Livewire\Dashboard;

use App\Models\Organizacion;
use App\Models\User;
use App\Models\Cultivo;
use App\Models\Cosecha;
use App\Models\Solicitud;
use App\Models\HistorialProceso;
use App\Models\Terreno;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Title;
use Carbon\Carbon;

/**
 * CONTROLADOR MAESTRO - Super Admin Dashboard.
 * Gestiona la analítica global y auditoría del sistema SaaS.
 */
#[Title('Panel Global - Super Admin')]
class SuperAdmin extends Component
{
    // Filtros para Producción (Maestro)
    public $fechaDesde;
    public $fechaHasta;

    // Filtros para Empresas
    public $fDesdeOrgs;
    public $fHastaOrgs;

    // Filtros para Usuarios
    public $fDesdeUsers;
    public $fHastaUsers;

    public function mount()
    {
        // Inicialización General (Últimos 12 meses)
        $this->fechaDesde = Carbon::now()->subMonths(11)->startOfMonth()->format('Y-m-d');
        $this->fechaHasta = Carbon::now()->format('Y-m-d');

        // Inicialización Secciones (Últimos 6 meses)
        $this->fDesdeOrgs = Carbon::now()->subMonths(5)->startOfMonth()->format('Y-m-d');
        $this->fHastaOrgs = Carbon::now()->format('Y-m-d');

        $this->fDesdeUsers = Carbon::now()->subMonths(5)->startOfMonth()->format('Y-m-d');
        $this->fHastaUsers = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        // --- 1. PROCESAR PRODUCCIÓN GLOBAL (SIEMBRAS VS COSECHAS) ---
        $fDP = Carbon::parse($this->fechaDesde);
        $fHP = Carbon::parse($this->fechaHasta);
        $diffP = $fDP->diffInMonths($fHP);

        $labelsProd = [];
        $dataSiembras = [];
        $dataCosechas = [];

        // Generar serie mensual y obtener conteos reales
        $tempDate = (clone $fDP)->startOfMonth();
        while ($tempDate <= $fHP) {
            $labelsProd[] = $tempDate->translatedFormat('M Y');

            // Conteo Global de Siembras en este mes/año
            $dataSiembras[] = Cultivo::whereMonth('fecha_siembra', $tempDate->month)
                ->whereYear('fecha_siembra', $tempDate->year)
                ->count();

            // Conteo Global de Cosechas en este mes/año
            $dataCosechas[] = Cultivo::where('estado', 'Cosechado')
                ->whereMonth('fecha_cosecha_finalizada', $tempDate->month)
                ->whereYear('fecha_cosecha_finalizada', $tempDate->year)
                ->count();

            $tempDate->addMonth();
        }

        // --- 2. PROCESAR EMPRESAS (TENDENCIA) ---
        $fDO = Carbon::parse($this->fDesdeOrgs);
        $fHO = Carbon::parse($this->fHastaOrgs);
        $labelsOrgs = [];
        $dataOrgs = [];
        $tempO = (clone $fDO)->startOfMonth();
        while ($tempO <= $fHO) {
            $labelsOrgs[] = $tempO->translatedFormat('M Y');
            $dataOrgs[] = Organizacion::whereMonth('created_at', $tempO->month)
                ->whereYear('created_at', $tempO->year)->count();
            $tempO->addMonth();
        }

        // --- 3. PROCESAR USUARIOS (TENDENCIA) ---
        $fDU = Carbon::parse($this->fDesdeUsers);
        $fHU = Carbon::parse($this->fHastaUsers);
        $labelsUsers = [];
        $dataUsers = [];
        $tempU = (clone $fDU)->startOfMonth();
        while ($tempU <= $fHU) {
            $labelsUsers[] = $tempU->translatedFormat('M Y');
            $dataUsers[] = User::whereMonth('created_at', $tempU->month)
                ->whereYear('created_at', $tempU->year)->count();
            $tempU->addMonth();
        }

        // 4. ANÁLISIS FINANCIERO GLOBAL (BALANCE DE RENTABILIDAD)
        // Ventas Brutas
        $globalVentas = DB::table('ventas')
            ->whereBetween('created_at', [$fDP, $fHP])
            ->select(DB::raw('SUM(cantidad_vendida_kg * precio_por_kg) as total'))
            ->first()->total ?? 0;

        // Inversión (Labores + Alquileres Proporcionales + Fletes)
        $globalCostosLabores = DB::table('labores')
            ->whereBetween('fecha_realizacion', [$fDP, $fHP])
            ->sum('costo_total');

        $globalFletes = DB::table('ventas')
            ->whereBetween('created_at', [$fDP, $fHP])
            ->sum('costo_flete');

        // Alquileres (Solo terrenos alquilados)
        $globalAlquileres = DB::table('terrenos')
            ->where('tipo_tenencia', 'alquilado')
            ->sum('costo_alquiler_anual'); // Nota: Simplificado a anual por ahora

        $globalInversion = $globalCostosLabores + $globalFletes + ($globalAlquileres / 12 * ($diffP + 1));
        $globalGanancia = $globalVentas - $globalInversion;

        // 5. LISTAS Y KPIS GLOBALES
        $orgsList = Organizacion::orderBy('nombre')->get();
        $usersList = User::with('rol')->orderBy('nombres')->get();
        $terrenosList = Terreno::with('responsable')->get();
        $solicitudesList = Solicitud::with('solicitante', 'organizacion')->where('tipo', 'creacion_organizacion')->where('estado', 0)->get();

        // Rankings filtrados por el rango maestro (Producción)
        $topCultivos = DB::table('cultivos')
            ->join('catalogo_cultivos', 'cultivos.catalogo_cultivo_id', '=', 'catalogo_cultivos.id')
            ->whereBetween('cultivos.fecha_siembra', [$fDP, $fHP])
            ->select('catalogo_cultivos.nombre', DB::raw('count(*) as total'))
            ->groupBy('catalogo_cultivos.nombre')
            ->orderByDesc('total')->take(5)->get();

        $cosechaPorProducto = DB::table('cosechas')
            ->join('labores', 'cosechas.labor_id', '=', 'labores.id')
            ->join('cultivos', 'labores.cultivo_id', '=', 'cultivos.id')
            ->join('catalogo_cultivos', 'cultivos.catalogo_cultivo_id', '=', 'catalogo_cultivos.id')
            ->whereBetween('cosechas.created_at', [$fDP, $fHP])
            ->select('catalogo_cultivos.nombre', 'cosechas.unidad_medida', DB::raw('SUM(cosechas.cantidad_kg) as total_cantidad'))
            ->groupBy('catalogo_cultivos.nombre', 'cosechas.unidad_medida')
            ->orderByDesc('total_cantidad')->take(5)->get();

        return view('livewire.dashboard.super-admin', [
            'stats' => [
                'orgs' => $orgsList->count(),
                'users' => $usersList->count(),
                'cultivos' => Cultivo::whereBetween('fecha_siembra', [$fDP, $fHP])->count(),
                'cosechados' => Cultivo::where('estado', 'Cosechado')->whereBetween('fecha_cosecha_finalizada', [$fDP, $fHP])->count(),
                'solicitudes' => $solicitudesList->count(),
                'hectareas' => $terrenosList->sum('hectareas'),
                'global_ventas' => (float)$globalVentas,
                'global_inversion' => (float)$globalInversion,
                'global_ganancia' => (float)$globalGanancia,
            ],
            'lists' => [
                'orgs' => $orgsList, 'users' => $usersList, 'terrenos' => $terrenosList, 'solicitudes' => $solicitudesList,
                'cultivos_activos' => Cultivo::with('detalleCatalogo')->whereIn('estado', ['Planificado', 'En crecimiento'])->whereBetween('fecha_siembra', [$fDP, $fHP])->get(),
                'cultivos_cosechados' => Cultivo::with('detalleCatalogo')->where('estado', 'Cosechado')->whereBetween('fecha_cosecha_finalizada', [$fDP, $fHP])->get(),
            ],
            'labelsProd' => $labelsProd, 'dataSiembras' => $dataSiembras, 'dataCosechas' => $dataCosechas,
            'labelsOrgs' => $labelsOrgs, 'dataOrgs' => $dataOrgs,
            'labelsUsers' => $labelsUsers, 'dataUsers' => $dataUsers,
            'topCultivos' => $topCultivos, 'cosechaPorProducto' => $cosechaPorProducto,
            'actividadGlobal' => HistorialProceso::with('usuario')->orderBy('created_at', 'desc')->take(5)->get(),
            'mesesLista' => [
                1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
            ]
        ]);
    }
}

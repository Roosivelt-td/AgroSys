<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\Labor;
use App\Models\Cosecha;
use App\Models\Venta;
use App\Models\SugerenciaTarea;
use App\Models\Notificacion;
use App\Models\AsignacionSupervisor;
use App\Models\MiembroOrganizacion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AgricultorSupervisionDetail extends Component
{
    public $agricultor;
    public $organizacion;

    // Filtro para ver Todos los Cultivos o Solo los Activos (Sin Cosechar)
    public $verTodosCultivos = false;

    // Modal para Enviar Sugerencia al Agricultor
    public $showSugerenciaModal = false;
    public $selectedCultivoId = null;
    public $tituloSugerencia = '';
    public $descripcionSugerencia = '';
    public $fechaSugerida = '';

    public function mount($id)
    {
        // 1. Cargar datos del agricultor
        $this->agricultor = User::findOrFail($id);

        // 2. Verificar que el supervisor autenticado tenga asignado a este agricultor en la organización
        $misMembresiasSup = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Supervisor'))
            ->pluck('id');

        $asignacion = AsignacionSupervisor::whereIn('supervisor_miembro_id', $misMembresiasSup)
            ->where('agricultor_usuario_id', $id)
            ->with('organizacion')
            ->first();

        if (!$asignacion) {
            abort(403, 'No tienes permisos para supervisar a este agricultor.');
        }

        $this->organizacion = $asignacion->organizacion;
        $this->fechaSugerida = now()->format('Y-m-d');
    }

    public function openSugerenciaModal($cultivoId = null)
    {
        $this->selectedCultivoId = $cultivoId;
        $this->tituloSugerencia = '';
        $this->descripcionSugerencia = '';
        $this->fechaSugerida = now()->format('Y-m-d');
        $this->showSugerenciaModal = true;
    }

    public function closeSugerenciaModal()
    {
        $this->showSugerenciaModal = false;
        $this->selectedCultivoId = null;
        $this->tituloSugerencia = '';
        $this->descripcionSugerencia = '';
    }

    public function enviarSugerencia()
    {
        $this->validate([
            'tituloSugerencia' => 'required|string|max:100',
            'descripcionSugerencia' => 'required|string',
            'fechaSugerida' => 'required|date',
        ]);

        SugerenciaTarea::create([
            'organizacion_id' => $this->organizacion->id,
            'supervisor_usuario_id' => Auth::id(),
            'agricultor_usuario_id' => $this->agricultor->id,
            'cultivo_id' => $this->selectedCultivoId,
            'titulo' => $this->tituloSugerencia,
            'descripcion' => $this->descripcionSugerencia,
            'fecha_sugerida' => $this->fechaSugerida,
            'estado' => 0, // 0 = Pendiente
        ]);

        // Crear notificación para el agricultor
        Notificacion::create([
            'usuario_id' => $this->agricultor->id,
            'titulo' => '💡 Nueva Sugerencia Técnica',
            'mensaje' => 'El Supervisor ' . Auth::user()->nombres . ' te envió una instrucción: ' . $this->tituloSugerencia,
            'tipo' => 'informativa',
        ]);

        session()->flash('status', 'Sugerencia técnica enviada con éxito al agricultor.');
        $this->closeSugerenciaModal();
    }

    public function render()
    {
        $orgId = $this->organizacion->id;
        $agriId = $this->agricultor->id;

        // Terrenos del agricultor (pertenecientes a la organización o sin organización explícita)
        $terrenos = Terreno::where('usuario_id', $agriId)
            ->where(function($q) use ($orgId) {
                $q->where('organizacion_id', $orgId)
                  ->orWhereNull('organizacion_id');
            })
            ->with(['cultivos.detalleCatalogo'])
            ->get();

        // Consulta base de cultivos del agricultor
        $cultivosQuery = Cultivo::whereHas('terreno', function($q) use ($agriId, $orgId) {
            $q->where('usuario_id', $agriId)
              ->where(function($q2) use ($orgId) {
                  $q2->where('organizacion_id', $orgId)
                     ->orWhereNull('organizacion_id');
              });
        })->with(['terreno', 'detalleCatalogo', 'labores.detalleCatalogo']);

        // Si NO está activo "Ver Todos", filtramos solo los ACTIVOS (sin cosechar)
        if (!$this->verTodosCultivos) {
            $cultivosQuery->whereNotIn('estado', ['Finalizado', 'Cosechado']);
        }

        $cultivosActivos = $cultivosQuery->latest()->get();

        // Todos los IDs de cultivos para las estadísticas acumuladas
        $todosCultivosIds = Cultivo::whereHas('terreno', function($q) use ($agriId, $orgId) {
            $q->where('usuario_id', $agriId)
              ->where(function($q2) use ($orgId) {
                  $q2->where('organizacion_id', $orgId)
                     ->orWhereNull('organizacion_id');
              });
        })->pluck('id');

        // Totales y estadísticas clave
        $totalHectareas = $terrenos->sum('hectareas');
        $totalCultivosActivos = Cultivo::whereIn('id', $todosCultivosIds)
            ->whereNotIn('estado', ['Finalizado', 'Cosechado'])
            ->count();

        $totalInversion = Labor::whereIn('cultivo_id', $todosCultivosIds)->sum('costo_total');

        $laborIds = Labor::whereIn('cultivo_id', $todosCultivosIds)->pluck('id');
        $totalCosechadoKg = Cosecha::whereIn('labor_id', $laborIds)->sum('cantidad_kg');

        $cosechaIds = Cosecha::whereIn('labor_id', $laborIds)->pluck('id');
        $totalVentasMonto = Venta::whereIn('cosecha_id', $cosechaIds)
            ->selectRaw('SUM(cantidad_vendida_kg * precio_por_kg) as total')
            ->value('total') ?? 0;

        // Historial de Sugerencias enviadas a este agricultor
        $sugerenciasEnviadas = SugerenciaTarea::where('agricultor_usuario_id', $agriId)
            ->where('organizacion_id', $orgId)
            ->with(['cultivo.detalleCatalogo'])
            ->latest()
            ->get();

        return view('livewire.admin.agricultor-supervision-detail', [
            'terrenos' => $terrenos,
            'cultivosActivos' => $cultivosActivos,
            'totalHectareas' => $totalHectareas,
            'totalCultivos' => $totalCultivosActivos,
            'totalInversion' => $totalInversion,
            'totalCosechadoKg' => $totalCosechadoKg,
            'totalVentasMonto' => $totalVentasMonto,
            'sugerenciasEnviadas' => $sugerenciasEnviadas,
        ]);
    }
}

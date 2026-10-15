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
        $user = Auth::user();

        // 1. Cargar datos del agricultor
        $this->agricultor = User::findOrFail($id);

        // 2. Verificar que el supervisor/admin tenga acceso a las organizaciones del agricultor
        $misOrgsSup = MiembroOrganizacion::where('usuario_id', $user->id)
            ->where('estado', 1)
            ->where(function($q) use ($user) {
                if ($user->rol_id === 1) return;
                $q->whereHas('roles.rolDetalle', fn($rq) => $rq->whereIn('nombre', ['Supervisor', 'Administrador']))
                  ->orWhere('es_propietario', 1);
            })
            ->pluck('organizacion_id');

        $agriMembresia = MiembroOrganizacion::where('usuario_id', $id)
            ->where('estado', 1)
            ->when($user->rol_id !== 1, fn($q) => $q->whereIn('organizacion_id', $misOrgsSup))
            ->with('organizacion')
            ->first();

        if (!$agriMembresia && $user->rol_id !== 1) {
            abort(403, 'No tienes permisos para supervisar a este agricultor.');
        }

        $this->organizacion = $agriMembresia ? $agriMembresia->organizacion : (\App\Models\Organizacion::first() ?: new \App\Models\Organizacion(['id' => 1, 'nombre' => 'Organización']));
        $this->fechaSugerida = now()->format('Y-m-d');
    }

    /**
     * Evalúa si el usuario autenticado tiene el cargo/rol activo de Supervisor.
     * Los Administradores pueden ingresar y ver los datos pero no enviar sugerencias técnicas.
     */
    public function getCanSendSugerenciaProperty()
    {
        $user = Auth::user();
        if (!$user || !$this->organizacion) return false;

        // Si es SuperAdmin (rol_id === 1), puede enviar
        if ($user->rol_id === 1) return true;

        // Verificar si tiene el cargo activo de Supervisor en la organización
        return MiembroOrganizacion::where('usuario_id', $user->id)
            ->where('organizacion_id', $this->organizacion->id)
            ->where('estado', 1)
            ->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Supervisor'))
            ->exists();
    }

    public function openSugerenciaModal($cultivoId = null)
    {
        if (!$this->canSendSugerencia) {
            session()->flash('error', 'Solo los usuarios con cargo de Supervisor pueden enviar sugerencias e instrucciones técnicas.');
            return;
        }

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
        if (!$this->canSendSugerencia) {
            session()->flash('error', 'Solo los usuarios con cargo de Supervisor pueden enviar sugerencias e instrucciones técnicas.');
            return;
        }

        $this->validate([
            'tituloSugerencia' => 'required|string|min:3|max:100',
            'descripcionSugerencia' => 'required|string|min:3|max:2000',
            'fechaSugerida' => 'required|date',
            'selectedCultivoId' => 'nullable|exists:cultivos,id',
        ], [
            'tituloSugerencia.required' => 'El título de la sugerencia o acción es obligatorio.',
            'tituloSugerencia.min' => 'El título debe tener al menos 3 caracteres.',
            'descripcionSugerencia.required' => 'Ingrese las instrucciones detalladas para el agricultor.',
            'descripcionSugerencia.min' => 'Las instrucciones deben contener al menos 3 caracteres.',
            'fechaSugerida.required' => 'Seleccione la fecha sugerida para la labor.',
        ]);

        $sug = SugerenciaTarea::create([
            'organizacion_id' => $this->organizacion->id,
            'supervisor_usuario_id' => Auth::id(),
            'agricultor_usuario_id' => $this->agricultor->id,
            'cultivo_id' => $this->selectedCultivoId ?: null,
            'titulo' => trim($this->tituloSugerencia),
            'descripcion' => trim($this->descripcionSugerencia),
            'fecha_sugerida' => $this->fechaSugerida,
            'estado' => 0, // 0 = Pendiente
        ]);

        // Crear notificación para el agricultor
        Notificacion::create([
            'usuario_id' => $this->agricultor->id,
            'sugerencia_id' => $sug->id,
            'titulo' => '💡 Nueva Sugerencia Técnica',
            'mensaje' => 'El Supervisor ' . Auth::user()->nombres . ' te envió una instrucción: ' . $sug->titulo,
            'tipo' => 'sugerencia',
            'leido' => 0,
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
            'canSendSugerencia' => $this->canSendSugerencia,
        ]);
    }
}

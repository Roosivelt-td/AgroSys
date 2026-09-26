<?php

namespace App\Livewire\Ia;

use Livewire\Component;
use App\Models\AlertaSistema;
use App\Models\SugerenciaTarea;
use App\Models\MiembroOrganizacion;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Tareas y Sugerencias IA')]
class Alertas extends Component
{
    public $loading = false;
    public $alertasIa = [];
    public $alertasSistema = [];
    public $sugerenciasSupervisor = [];

    // Propiedades para Vista de Supervisor / Admin
    public $isSupervisorOrAdmin = false;
    public $filtroEstadoSupervision = 'todos'; // todos | pendientes | cumplidos

    // Modal de Detalle de Sugerencia
    public $showSugerenciaModal = false;
    public $selectedSugerencia = null;
    public $comentarioRespuesta = '';

    public function mount()
    {
        $this->cargarAlertas();
    }

    public function cargarAlertas()
    {
        $user = Auth::user();

        // Verificar si es Supervisor o Admin en alguna organización
        $this->isSupervisorOrAdmin = MiembroOrganizacion::where('usuario_id', $user->id)
            ->where('estado', 1)
            ->whereHas('roles.rolDetalle', fn($q) => $q->whereIn('nombre', ['Supervisor', 'Administrador']))
            ->exists();

        // 1. Cargar sugerencias enviadas por Supervisores a este agricultor
        $this->sugerenciasSupervisor = SugerenciaTarea::with(['supervisor', 'cultivo.terreno', 'organizacion'])
            ->where('agricultor_usuario_id', $user->id)
            ->latest()
            ->get();

        // 2. Cargar alertas del sistema
        $this->alertasSistema = AlertaSistema::where('visible', 1)
            ->where(function($q) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Cargar sugerencias en formato de tarjeta
        $this->alertasIa = SugerenciaTarea::with(['cultivo', 'supervisor'])
            ->where('agricultor_usuario_id', $user->id)
            ->where('estado', 0) // 0 = Pendiente
            ->orderBy('fecha_sugerida', 'asc')
            ->get();

        // Si no hay sugerencias pendientes, cargamos unas simuladas de IA para demostración
        if ($this->alertasIa->isEmpty() && $this->sugerenciasSupervisor->isEmpty() && !$this->isSupervisorOrAdmin) {
            $this->alertasIa = collect([
                (object)[
                    'titulo' => 'Riesgo de Estrés Hídrico',
                    'descripcion' => 'La IA detectó una probabilidad del 85% de sequía en el Sector B para los próximos 5 días.',
                    'tipo' => 'critico',
                    'confianza' => 0.85
                ],
                (object)[
                    'titulo' => 'Detección Temprana de Plaga',
                    'descripcion' => 'Análisis de imágenes satelitales sugiere presencia de "Roya del Café" en el lote 4.',
                    'tipo' => 'advertencia',
                    'confianza' => 0.72
                ]
            ]);
        }
    }

    public function verSugerencia($id)
    {
        $this->selectedSugerencia = SugerenciaTarea::with(['supervisor', 'agricultor', 'cultivo.terreno', 'organizacion'])->find($id);
        if ($this->selectedSugerencia) {
            $this->comentarioRespuesta = $this->selectedSugerencia->comentario_agricultor ?? '';
            $this->showSugerenciaModal = true;
        }
    }

    public function closeSugerenciaModal()
    {
        $this->showSugerenciaModal = false;
        $this->selectedSugerencia = null;
        $this->comentarioRespuesta = '';
    }

    public function completarSugerencia()
    {
        if (!$this->selectedSugerencia) return;

        $sugerencia = SugerenciaTarea::find($this->selectedSugerencia->id);
        if ($sugerencia) {
            $sugerencia->update([
                'estado' => 1, // 1 = Completado
                'fecha_respuesta' => now(),
                'comentario_agricultor' => $this->comentarioRespuesta,
            ]);

            // Notificar al supervisor
            if ($sugerencia->supervisor_usuario_id) {
                Notificacion::create([
                    'usuario_id' => $sugerencia->supervisor_usuario_id,
                    'titulo' => '✅ Tarea Completada',
                    'mensaje' => 'El agricultor ' . Auth::user()->nombres . ' completó la instrucción: ' . $sugerencia->titulo,
                    'tipo' => 'exito',
                ]);
            }

            session()->flash('status', 'Tarea marcada como completada y notificada al supervisor.');
        }

        $this->closeSugerenciaModal();
        $this->cargarAlertas();
    }

    public function simularAnalisis()
    {
        $this->loading = true;
        sleep(1);
        $this->loading = false;

        $this->dispatch('notify', ['message' => 'Análisis de IA completado.', 'type' => 'success']);
        $this->cargarAlertas();
    }

    public function render()
    {
        $user = Auth::user();

        // Si es Supervisor o Admin, cargamos las sugerencias globales para su tablero
        $sugerenciasGlobales = collect();
        $totalEnviadas = 0;
        $totalCumplidas = 0;
        $totalPendientes = 0;

        if ($this->isSupervisorOrAdmin) {
            $misOrgs = MiembroOrganizacion::where('usuario_id', $user->id)->pluck('organizacion_id');

            $query = SugerenciaTarea::with(['agricultor', 'supervisor', 'cultivo.terreno', 'organizacion'])
                ->where(function($q) use ($user, $misOrgs) {
                    $q->where('supervisor_usuario_id', $user->id)
                      ->orWhereIn('organizacion_id', $misOrgs);
                });

            $todas = $query->latest()->get();

            $totalEnviadas = $todas->count();
            $totalCumplidas = $todas->where('estado', 1)->count();
            $totalPendientes = $todas->where('estado', 0)->count();

            if ($this->filtroEstadoSupervision === 'pendientes') {
                $sugerenciasGlobales = $todas->where('estado', 0);
            } elseif ($this->filtroEstadoSupervision === 'cumplidos') {
                $sugerenciasGlobales = $todas->where('estado', 1);
            } else {
                $sugerenciasGlobales = $todas;
            }
        }

        return view('livewire.ia.alertas', [
            'sugerenciasGlobales' => $sugerenciasGlobales,
            'totalEnviadas' => $totalEnviadas,
            'totalCumplidas' => $totalCumplidas,
            'totalPendientes' => $totalPendientes,
        ]);
    }
}

<?php

namespace App\Livewire\Supervisor;

use Livewire\Component;
use App\Models\SugerenciaTarea;
use App\Models\MiembroOrganizacion;
use App\Models\Notificacion;
use App\Models\User;
use App\Models\Cultivo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Tareas y Sugerencias')]
class TareasSugerenciasManager extends Component
{
    public $loading = false;
    public $sugerenciasSupervisor = [];

    // Propiedades para Vista de Supervisor / Admin
    public $isSupervisorOrAdmin = false;
    public $filtroEstadoSupervision = 'todos'; // todos | pendientes | cumplidos
    public $searchQuery = '';
    public $filtroAgricultorId = '';

    // Modal de Detalle de Sugerencia
    public $showSugerenciaModal = false;
    public $selectedSugerencia = null;
    public $comentarioRespuesta = '';

    // Modal de Nueva Sugerencia / Tarea (Para Supervisores)
    public $showCreateSugerenciaModal = false;
    public $newAgricultorId = '';
    public $newTitulo = '';
    public $newDescripcion = '';
    public $newFechaSugerida = '';
    public $newCultivoId = '';

    public function mount()
    {
        $this->cargarDatos();
        $this->newFechaSugerida = now()->format('Y-m-d');

        if (session()->has('open_sugerencia')) {
            $sugId = session('open_sugerencia');
            $this->verSugerencia($sugId);
        }
    }

    public function cargarDatos()
    {
        $user = Auth::user();

        // Verificar si es Super Admin (rol_id === 1) o es Supervisor/Admin en alguna organización
        $this->isSupervisorOrAdmin = ($user->rol_id === 1) || MiembroOrganizacion::where('usuario_id', $user->id)
            ->where('estado', 1)
            ->whereHas('roles.rolDetalle', fn($q) => $q->whereIn('nombre', ['Supervisor', 'Administrador']))
            ->exists();

        // Cargar sugerencias enviadas por Supervisores a este agricultor
        $this->sugerenciasSupervisor = SugerenciaTarea::with(['supervisor', 'cultivo.terreno', 'organizacion'])
            ->where('agricultor_usuario_id', $user->id)
            ->latest()
            ->get();
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
                    'sugerencia_id' => $sugerencia->id,
                    'titulo' => '✅ Tarea Completada',
                    'mensaje' => 'El agricultor ' . Auth::user()->nombres . ' completó la instrucción: ' . $sugerencia->titulo,
                    'tipo' => 'exito',
                ]);
            }

            session()->flash('status', 'Tarea marcada como completada y notificada al supervisor.');
        }

        $this->closeSugerenciaModal();
        $this->cargarDatos();
    }

    public function openCreateSugerenciaModal()
    {
        $this->resetValidation();
        $this->newAgricultorId = '';
        $this->newTitulo = '';
        $this->newDescripcion = '';
        $this->newCultivoId = '';
        $this->newFechaSugerida = now()->format('Y-m-d');
        $this->showCreateSugerenciaModal = true;
    }

    public function closeCreateSugerenciaModal()
    {
        $this->showCreateSugerenciaModal = false;
    }

    public function crearSugerencia()
    {
        $user = Auth::user();

        $this->validate([
            'newAgricultorId' => 'required|exists:usuarios,id',
            'newTitulo' => 'required|string|min:3|max:100',
            'newDescripcion' => 'required|string|min:5|max:2000',
            'newFechaSugerida' => 'required|date',
            'newCultivoId' => 'nullable|exists:cultivos,id',
        ], [
            'newAgricultorId.required' => 'Seleccione el agricultor destinatario de la instrucción.',
            'newTitulo.required' => 'El título de la tarea es obligatorio.',
            'newDescripcion.required' => 'Ingrese las instrucciones agronómicas detalladas.',
            'newFechaSugerida.required' => 'Defina la fecha programada para la tarea.',
        ]);

        // Obtener organización activa
        $membresia = MiembroOrganizacion::where('usuario_id', $user->id)->where('estado', 1)->first();
        $orgId = $membresia ? $membresia->organizacion_id : ($user->membresias()->first()?->organizacion_id ?? 1);

        $sug = SugerenciaTarea::create([
            'organizacion_id' => $orgId,
            'supervisor_usuario_id' => $user->id,
            'agricultor_usuario_id' => $this->newAgricultorId,
            'cultivo_id' => $this->newCultivoId ?: null,
            'titulo' => trim($this->newTitulo),
            'descripcion' => trim($this->newDescripcion),
            'fecha_sugerida' => $this->newFechaSugerida,
            'estado' => 0, // 0 = Pendiente
        ]);

        // Enviar notificación a la campanita del agricultor
        Notificacion::create([
            'usuario_id' => $this->newAgricultorId,
            'sugerencia_id' => $sug->id,
            'titulo' => '💡 Nueva Sugerencia Técnica',
            'mensaje' => "El supervisor {$user->nombres} te ha asignado la instrucción: {$sug->titulo}",
            'tipo' => 'sugerencia',
            'leido' => 0,
        ]);

        session()->flash('status', "Instrucción '{$sug->titulo}' asignada correctamente al agricultor.");

        $this->closeCreateSugerenciaModal();
        $this->cargarDatos();
    }

    public function render()
    {
        $user = Auth::user();

        $sugerenciasGlobales = collect();
        $totalEnviadas = 0;
        $totalCumplidas = 0;
        $totalPendientes = 0;
        $misAgricultores = collect();
        $cultivosDisponibles = collect();

        if ($this->isSupervisorOrAdmin) {
            $misOrgs = MiembroOrganizacion::where('usuario_id', $user->id)->pluck('organizacion_id');

            // Obtener lista de agricultores bajo supervisión o en las organizaciones
            if ($user->rol_id === 1) {
                $misAgricultores = User::where('is_activo', 1)->orderBy('nombres')->get();
                $cultivosDisponibles = Cultivo::with('detalleCatalogo')->get();
            } else {
                $misAgricultores = User::where('is_activo', 1)
                    ->whereHas('membresias', function($q) use ($misOrgs) {
                        $q->whereIn('organizacion_id', $misOrgs)->where('estado', 1);
                    })->orderBy('nombres')->get();

                $cultivosDisponibles = Cultivo::whereHas('terreno', function($q) use ($misOrgs) {
                    $q->whereIn('organizacion_id', $misOrgs);
                })->with('detalleCatalogo')->get();
            }

            $query = SugerenciaTarea::with(['agricultor', 'supervisor', 'cultivo.terreno', 'cultivo.detalleCatalogo', 'organizacion']);

            if ($user->rol_id !== 1) {
                $query->where(function($q) use ($user, $misOrgs) {
                    $q->where('supervisor_usuario_id', $user->id)
                      ->orWhereIn('organizacion_id', $misOrgs);
                });
            }

            // Aplicar búsqueda por texto
            if (!empty($this->searchQuery)) {
                $term = '%' . trim($this->searchQuery) . '%';
                $query->where(function($q) use ($term) {
                    $q->where('titulo', 'like', $term)
                      ->orWhere('descripcion', 'like', $term)
                      ->orWhereHas('agricultor', function($sq) use ($term) {
                          $sq->where('nombres', 'like', $term)
                            ->orWhere('apellidos', 'like', $term)
                            ->orWhere('dni', 'like', $term);
                      })
                      ->orWhereHas('cultivo', function($sq) use ($term) {
                          $sq->where('nombre_lote', 'like', $term);
                      });
                });
            }

            // Aplicar filtro por agricultor específico
            if (!empty($this->filtroAgricultorId)) {
                $query->where('agricultor_usuario_id', $this->filtroAgricultorId);
            }

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

        return view('livewire.supervisor.tareas-sugerencias-manager', [
            'sugerenciasGlobales' => $sugerenciasGlobales,
            'totalEnviadas' => $totalEnviadas,
            'totalCumplidas' => $totalCumplidas,
            'totalPendientes' => $totalPendientes,
            'misAgricultores' => $misAgricultores,
            'cultivosDisponibles' => $cultivosDisponibles,
        ]);
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\EventoOrganizacion;
use App\Models\EventoUsuarioOculto;
use App\Models\Organizacion;
use App\Models\MiembroOrganizacion;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Eventos y Reuniones')]
class EventosManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'proximos'; // proximos, hoy, pasados, todos
    public $selectedOrgId = null;

    // Form fields
    public $eventId = null;
    public $titulo = '';
    public $descripcion = '';
    public $fecha_evento = '';
    public $duracion_minutos = 60;
    public $lugar = '';
    public $enlace_virtual = '';
    public $destinatario_tipo = 'organizacion_completa'; // opciones segun rol

    // Deletion confirmation
    public $deletingEventId = null;
    public $deletingEventTitle = '';
    public $isDeletingCreator = false; // Indica si el usuario actual es el creador del evento a borrar

    // Active event for detailed view
    public $detailEvent = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->resolveOrGetActiveOrgId();

        // Por defecto, establecer la fecha del evento a mañana a las 09:00
        $this->fecha_evento = Carbon::now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i');
    }

    /**
     * Evalúa si el usuario actual puede crear/programar eventos.
     */
    public function getCanManageEventsProperty()
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->rol_id === 1) {
            return true;
        }

        if ($user->esAdminDeOrganizacion()) {
            return true;
        }

        if ($user->membresias()->where('estado', 1)->where('es_propietario', 1)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Evalúa si el usuario actual puede EDITAR un evento específico.
     * Regla estricta: Solo el creador original del evento o el SuperAdmin pueden editarlo.
     */
    public function canEditEvent($event)
    {
        $user = Auth::user();
        if (!$user || !$event) return false;

        if ($user->rol_id === 1) {
            return true;
        }

        return (int)$event->creado_por_usuario_id === (int)$user->id;
    }

    /**
     * Evalúa si el usuario actual puede ELIMINAR GLOBALMENTE un evento específico para todos.
     */
    public function canDeleteGlobal($event)
    {
        return $this->canEditEvent($event);
    }

    private function resolveOrGetActiveOrgId()
    {
        if ($this->selectedOrgId) {
            return $this->selectedOrgId;
        }

        $user = Auth::user();
        if (!$user) return null;

        if ($user->rol_id === 1) {
            $firstOrg = Organizacion::first();
            if ($firstOrg) {
                $this->selectedOrgId = $firstOrg->id;
                return $this->selectedOrgId;
            }
        } else {
            $membresia = $user->membresias()->where('estado', 1)->first();
            if ($membresia) {
                $this->selectedOrgId = $membresia->organizacion_id;
                return $this->selectedOrgId;
            }
        }

        return $this->selectedOrgId;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedSelectedOrgId()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        if (!$this->canManageEvents) {
            session()->flash('error', 'No tiene permisos para programar o registrar eventos.');
            return;
        }

        $this->resetForm();
        $this->resolveOrGetActiveOrgId();

        $user = Auth::user();
        $this->destinatario_tipo = ($user->rol_id === 1) ? 'todos_usuarios' : 'organizacion_completa';
        $this->fecha_evento = Carbon::now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i');

        $this->dispatch('open-modal', 'modal-evento');
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->eventId = null;
        $this->titulo = '';
        $this->descripcion = '';
        $this->fecha_evento = '';
        $this->duracion_minutos = 60;
        $this->lugar = '';
        $this->enlace_virtual = '';
        $this->destinatario_tipo = 'organizacion_completa';
    }

    public function editEvento($id)
    {
        $event = EventoOrganizacion::findOrFail($id);

        if (!$this->canEditEvent($event)) {
            session()->flash('error', 'Solo el creador que envió esta notificación/evento o el SuperAdmin puede editarlo.');
            return;
        }

        $this->resetValidation();
        $this->eventId = $event->id;
        $this->selectedOrgId = $event->organizacion_id;
        $this->titulo = $event->titulo;
        $this->descripcion = $event->descripcion ?? '';
        $this->fecha_evento = Carbon::parse($event->fecha_evento)->format('Y-m-d\TH:i');
        $this->duracion_minutos = $event->duracion_minutos ?? 60;
        $this->lugar = $event->lugar ?? '';
        $this->enlace_virtual = $event->enlace_virtual ?? '';
        $this->destinatario_tipo = $event->destinatario_tipo ?? 'organizacion_completa';

        $this->dispatch('open-modal', 'modal-evento');
    }

    public function showDetail($id)
    {
        $this->detailEvent = EventoOrganizacion::with(['creador', 'organizacion'])->find($id);

        if ($this->detailEvent) {
            $this->dispatch('open-modal', 'modal-detalle-evento');
        }
    }

    public function saveEvento()
    {
        if (!$this->canManageEvents) {
            session()->flash('error', 'No tiene permisos para registrar o modificar eventos.');
            return;
        }

        $user = Auth::user();

        // Autocompletar enlace virtual con https:// si el usuario no incluyó el protocolo
        if ($this->enlace_virtual) {
            $link = trim($this->enlace_virtual);
            if (!preg_match('~^(?:f|ht)tps?://~i', $link)) {
                $this->enlace_virtual = 'https://' . $link;
            }
        }

        // Obtener o resolver organización activa
        $this->resolveOrGetActiveOrgId();

        $this->validate([
            'selectedOrgId' => 'required|exists:organizaciones,id',
            'titulo' => 'required|string|min:3|max:150',
            'descripcion' => 'nullable|string|max:2000',
            'fecha_evento' => 'required|date',
            'duracion_minutos' => 'required|integer|min:15|max:1440',
            'lugar' => 'nullable|string|max:255',
            'enlace_virtual' => 'nullable|url|max:500',
            'destinatario_tipo' => 'required|string|max:50',
        ], [
            'selectedOrgId.required' => 'Debe seleccionar una organización para el evento.',
            'selectedOrgId.exists' => 'La organización seleccionada no es válida.',
            'titulo.required' => 'El título del evento es obligatorio.',
            'titulo.min' => 'El título debe contener al menos 3 caracteres.',
            'titulo.max' => 'El título no debe exceder los 150 caracteres.',
            'fecha_evento.required' => 'Debe especificar la fecha y hora del evento.',
            'fecha_evento.date' => 'Ingrese una fecha y hora válida.',
            'duracion_minutos.required' => 'Especifique la duración en minutos.',
            'enlace_virtual.url' => 'Ingrese una URL o enlace válido para la reunión virtual (ej: https://meet.google.com/...)',
            'destinatario_tipo.required' => 'Seleccione el alcance o tipo de destinatarios del evento.',
        ]);

        $data = [
            'organizacion_id' => $this->selectedOrgId,
            'creado_por_usuario_id' => $user->id,
            'titulo' => trim($this->titulo),
            'descripcion' => trim($this->descripcion) ?: null,
            'fecha_evento' => Carbon::parse($this->fecha_evento)->format('Y-m-d H:i:s'),
            'duracion_minutos' => (int)$this->duracion_minutos,
            'lugar' => trim($this->lugar) ?: null,
            'enlace_virtual' => trim($this->enlace_virtual) ?: null,
            'destinatario_tipo' => $this->destinatario_tipo,
        ];

        $isNew = !$this->eventId;

        if ($this->eventId) {
            $event = EventoOrganizacion::findOrFail($this->eventId);

            if (!$this->canEditEvent($event)) {
                session()->flash('error', 'Solo el creador que envió esta notificación/evento o el SuperAdmin puede editarlo.');
                return;
            }

            $event->update($data);
            session()->flash('status', "Evento '{$event->titulo}' actualizado con éxito.");
        } else {
            $event = EventoOrganizacion::create($data);
            session()->flash('status', "Evento '{$event->titulo}' programado correctamente.");
        }

        // Generar notificaciones automáticas a los destinatarios elegidos
        $this->notifyTargetUsers($event, $isNew);

        $this->dispatch('close-modal', 'modal-evento');
        $this->resetForm();
    }

    /**
     * Notificar a los usuarios destinatarios según el alcance seleccionado.
     * REGLA: Los SuperAdmins (rol_id === 1) solo envían los eventos, jamás reciben notificaciones de eventos.
     */
    private function notifyTargetUsers(EventoOrganizacion $event, bool $isNew)
    {
        $user = Auth::user();
        $targetUserIds = collect();

        $destTipo = $event->destinatario_tipo ?? 'organizacion_completa';

        if ($destTipo === 'todos_usuarios') {
            // SuperAdmin enviando a todos los usuarios (excluyendo SuperAdmins)
            $targetUserIds = User::where('is_activo', 1)
                ->where('rol_id', '!=', 1)
                ->pluck('id');
        } elseif ($destTipo === 'todos_admins_org') {
            // SuperAdmin enviando a todos los administradores de organizaciones (excluyendo SuperAdmins)
            $targetUserIds = User::where('is_activo', 1)
                ->where('rol_id', '!=', 1)
                ->whereHas('membresias', function($q) {
                    $q->where('estado', 1)->where(function($sq) {
                        $sq->where('es_propietario', 1)
                          ->orWhereHas('roles', function($rq) {
                              $rq->where('estado', 1)->whereHas('rolDetalle', function($rdq) {
                                  $rdq->where('nombre', 'Administrador');
                              });
                          });
                    });
                })->pluck('id');
        } elseif ($destTipo === 'organizacion_completa') {
            // Todos los miembros de la organización seleccionada (excluyendo SuperAdmins)
            $targetUserIds = User::where('is_activo', 1)
                ->where('rol_id', '!=', 1)
                ->whereHas('membresias', function($q) use ($event) {
                    $q->where('organizacion_id', $event->organizacion_id)->where('estado', 1);
                })->pluck('id');
        } elseif ($destTipo === 'organizacion_admins') {
            // Administradores de la organización (excluyendo SuperAdmins)
            $targetUserIds = User::where('is_activo', 1)
                ->where('rol_id', '!=', 1)
                ->whereHas('membresias', function($q) use ($event) {
                    $q->where('organizacion_id', $event->organizacion_id)
                      ->where('estado', 1)
                      ->where(function($sq) {
                          $sq->where('es_propietario', 1)
                            ->orWhereHas('roles', function($rq) {
                                $rq->where('estado', 1)->whereHas('rolDetalle', function($rdq) {
                                    $rdq->where('nombre', 'Administrador');
                                });
                            });
                      });
                })->pluck('id');
        } elseif ($destTipo === 'organizacion_supervisores_agricultores') {
            // Supervisores y Agricultores de la organización (excluyendo SuperAdmins)
            $targetUserIds = User::where('is_activo', 1)
                ->where('rol_id', '!=', 1)
                ->whereHas('membresias', function($q) use ($event) {
                    $q->where('organizacion_id', $event->organizacion_id)->where('estado', 1);
                })->pluck('id');
        } else {
            $targetUserIds = User::where('is_activo', 1)
                ->where('rol_id', '!=', 1)
                ->whereHas('membresias', function($q) use ($event) {
                    $q->where('organizacion_id', $event->organizacion_id)->where('estado', 1);
                })->pluck('id');
        }

        // Exclusión total de cualquier usuario con rol_id === 1 (SuperAdmin)
        $superAdminUserIds = User::where('rol_id', 1)->pluck('id')->toArray();
        $targetUserIds = $targetUserIds->reject(fn($id) => in_array($id, $superAdminUserIds))->unique();

        $fechaFormatted = Carbon::parse($event->fecha_evento)->translatedFormat('d/m/Y \a \l\a\s h:i A');
        $lugarTexto = $event->enlace_virtual ? "Virtual ({$event->enlace_virtual})" : ($event->lugar ?: 'Por confirmar');

        $tituloNotif = $isNew ? "📅 Nuevo Evento: " . mb_strtoupper($event->titulo) : "📅 Evento Actualizado: " . mb_strtoupper($event->titulo);
        $mensajeNotif = "Se ha " . ($isNew ? "programado" : "actualizado") . " la reunión '{$event->titulo}' para el {$fechaFormatted}. Lugar/Acceso: {$lugarTexto}.";

        foreach ($targetUserIds as $targetId) {
            Notificacion::create([
                'usuario_id' => $targetId,
                'titulo' => mb_substr($tituloNotif, 0, 100),
                'mensaje' => $mensajeNotif,
                'tipo' => 'evento',
                'leido' => 0,
            ]);
        }
    }

    public function confirmDelete($id)
    {
        $event = EventoOrganizacion::findOrFail($id);

        $this->deletingEventId = $event->id;
        $this->deletingEventTitle = $event->titulo;
        $this->isDeletingCreator = $this->canDeleteGlobal($event);

        $this->dispatch('open-modal', 'modal-delete-evento');
    }

    /**
     * Oculta o elimina el evento.
     * @param string $mode 'personal' (oculta solo para la vista de este usuario) o 'global' (borra de la BD para todos)
     */
    public function deleteEvento($mode = 'personal')
    {
        $user = Auth::user();

        if ($this->deletingEventId) {
            $event = EventoOrganizacion::find($this->deletingEventId);
            if ($event) {
                $title = $event->titulo;

                if ($mode === 'global' && $this->canDeleteGlobal($event)) {
                    // Borrado permanente global para todos los usuarios
                    $event->delete();
                    session()->flash('status', "El evento '{$title}' ha sido eliminado definitivamente para todos los usuarios.");
                } else {
                    // Ocultamiento exclusivo para la vista de este usuario
                    EventoUsuarioOculto::firstOrCreate([
                        'evento_id' => $event->id,
                        'usuario_id' => $user->id,
                    ]);
                    session()->flash('status', "El evento '{$title}' se ha quitado de su vista personal.");
                }
            }
        }

        $this->dispatch('close-modal', 'modal-delete-evento');
        $this->deletingEventId = null;
        $this->deletingEventTitle = '';
        $this->isDeletingCreator = false;
    }

    public function render()
    {
        $user = Auth::user();

        // Verificar si el usuario pertenece a alguna organización activa
        $hasActiveOrg = ($user->rol_id === 1) || $user->membresias()->where('estado', 1)->exists();

        if (!$hasActiveOrg) {
            return view('livewire.admin.eventos-manager', [
                'eventos' => EventoOrganizacion::whereRaw('1 = 0')->paginate(12),
                'organizaciones' => collect(),
                'stats' => ['total' => 0, 'proximos' => 0, 'hoy' => 0, 'virtuales' => 0],
                'canManageEvents' => false,
                'hasActiveOrg' => false,
            ]);
        }

        // Obtener la lista de organizaciones accesibles
        if ($user->rol_id === 1) {
            $organizaciones = Organizacion::orderBy('nombre')->get();
        } else {
            $orgIds = $user->membresias()->where('estado', 1)->pluck('organizacion_id');
            $organizaciones = Organizacion::whereIn('id', $orgIds)->orderBy('nombre')->get();
        }

        $now = Carbon::now();

        // IDs de eventos que este usuario ha ocultado de su propia vista
        $ocultosIds = EventoUsuarioOculto::where('usuario_id', $user->id)->pluck('evento_id');

        // Consulta base de eventos considerando rol, visibilidad y elementos ocultos por el usuario
        $query = EventoOrganizacion::with(['creador', 'organizacion'])
            ->whereNotIn('eventos_organizacion.id', $ocultosIds);

        if ($user->rol_id === 1) {
            $query->when($this->selectedOrgId, fn($q) => $q->where('organizacion_id', $this->selectedOrgId));
        } else {
            $userOrgIds = $user->membresias()->where('estado', 1)->pluck('organizacion_id');
            $userJoinedAt = $user->created_at;

            $query->where(function($q) use ($user, $userOrgIds) {
                $q->whereIn('organizacion_id', $userOrgIds)
                  ->orWhere('destinatario_tipo', 'todos_usuarios');

                if ($user->esAdminDeOrganizacion()) {
                    $q->orWhere('destinatario_tipo', 'todos_admins_org');
                }
            });

            // Restringir eventos pasados antiguos creados antes de que el usuario se registrara
            $query->where(function($q) use ($userJoinedAt, $now) {
                $q->where('eventos_organizacion.created_at', '>=', $userJoinedAt->copy()->subHours(24))
                  ->orWhere('fecha_evento', '>=', $now->copy()->startOfDay());
            });

            if ($this->selectedOrgId && in_array($this->selectedOrgId, $userOrgIds->toArray())) {
                $query->where('organizacion_id', $this->selectedOrgId);
            }
        }

        $query->when($this->search, function($q) {
            $term = '%' . trim($this->search) . '%';
            $q->where(function($sq) use ($term) {
                $sq->where('titulo', 'like', $term)
                   ->orWhere('descripcion', 'like', $term)
                   ->orWhere('lugar', 'like', $term);
            });
        });

        // Filtrar por estado de fecha
        if ($this->filterStatus === 'proximos') {
            $query->where('fecha_evento', '>=', $now->copy()->startOfDay());
        } elseif ($this->filterStatus === 'hoy') {
            $query->whereDate('fecha_evento', $now->toDateString());
        } elseif ($this->filterStatus === 'pasados') {
            $query->where('fecha_evento', '<', $now->copy()->startOfDay());
        }

        $eventos = $query->orderBy('fecha_evento', $this->filterStatus === 'pasados' ? 'desc' : 'asc')
            ->paginate(12);

        // Estadísticas rápidas (excluyendo también ocultos)
        $baseStatsQuery = EventoOrganizacion::whereNotIn('id', $ocultosIds);
        if ($this->selectedOrgId && $user->rol_id === 1) {
            $baseStatsQuery->where('organizacion_id', $this->selectedOrgId);
        }

        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'proximos' => (clone $baseStatsQuery)->where('fecha_evento', '>=', $now->copy()->startOfDay())->count(),
            'hoy' => (clone $baseStatsQuery)->whereDate('fecha_evento', $now->toDateString())->count(),
            'virtuales' => (clone $baseStatsQuery)->whereNotNull('enlace_virtual')->where('fecha_evento', '>=', $now->copy()->startOfDay())->count(),
        ];

        return view('livewire.admin.eventos-manager', [
            'eventos' => $eventos,
            'organizaciones' => $organizaciones,
            'stats' => $stats,
            'canManageEvents' => $this->canManageEvents,
            'hasActiveOrg' => true,
        ]);
    }
}

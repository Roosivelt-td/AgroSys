<?php

namespace App\Livewire\Chat;

use App\Models\User;
use App\Models\MiembroOrganizacion;
use App\Models\Conversacion;
use App\Models\ConversacionParticipante;
use App\Models\MensajeChat;
use App\Models\ArchivoMultimedia;
use App\Services\AgroStorageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ChatManager extends Component
{
    use WithFileUploads;

    public $search = '';
    public $selectedConversacionId = null;
    public $message = '';

    // Edición
    public $editingMessageId = null;

    // Archivo adjunto
    public $file = null;
    public $mediaList = [];

    // Para el resaltado de notificaciones
    public $highlightId = null;

    // Estado IA
    public $isAiThinking = false;

    public function mount()
    {
        $this->ensureOrganizationGroups();

        if (request()->has('open')) {
            $this->selectedConversacionId = request()->query('open');
        } elseif (session()->has('open_conversacion')) {
            $this->selectedConversacionId = session('open_conversacion');
        }
    }

    /**
     * Asegura que el usuario esté en los grupos de sus organizaciones.
     */
    protected function ensureOrganizationGroups()
    {
        $myId = Auth::id();
        $misOrgs = MiembroOrganizacion::where('usuario_id', $myId)->where('estado', 1)->get();

        foreach ($misOrgs as $m) {
            // Buscar o crear conversación de grupo para la organización
            $conv = Conversacion::firstOrCreate(
                ['organizacion_id' => $m->organizacion_id, 'tipo_conversacion' => 'grupal'],
                ['nombre_grupo' => 'Canal: ' . $m->organizacion->nombre]
            );

            // Asegurar que el usuario sea participante
            ConversacionParticipante::firstOrCreate([
                'conversacion_id' => $conv->id,
                'usuario_id' => $myId
            ]);
        }
    }

    public function selectContact($userId)
    {
        // 1. Manejo de AgroBot
        if (!$userId || $userId == 0) {
            $agrobot = User::firstOrCreate(
                ['email' => 'agrobot@agrosys.com'],
                [
                    'nombres' => 'AgroBot',
                    'apellidos' => 'IA Asistente',
                    'dni' => '99999999',
                    'password' => \Illuminate\Support\Facades\Hash::make('agrobot123'),
                    'rol_id' => 2,
                    'biografia' => 'Asistente de IA experto en fitopatología.',
                    'ubicacion' => 'Servidor Central AgroSys'
                ]
            );
            $userId = $agrobot->id;
        }

        $myId = Auth::id();

        // 1.5 Manejo de Chat Individual (con uno mismo)
        if ($userId == $myId) {
            $conversacionId = Conversacion::where('tipo_conversacion', 'individual')
                ->whereHas('participantes', function($q) use ($myId) {
                    $q->where('usuario_id', $myId);
                })
                ->value('id');

            if (!$conversacionId) {
                $conversacionId = DB::transaction(function () use ($myId) {
                    $conv = Conversacion::create(['tipo_conversacion' => 'individual', 'nombre_grupo' => 'Mis Notas (Solo yo)']);
                    ConversacionParticipante::create(['conversacion_id' => $conv->id, 'usuario_id' => $myId]);
                    return $conv->id;
                });
            }
        } else {
            // 2. BUSQUEDA ESTRICTA: Solo chats con EXACTAMENTE estos 2 participantes
            $conversacionId = Conversacion::where('tipo_conversacion', 'privada')
                ->whereHas('participantes', function($q) use ($myId) {
                    $q->where('usuario_id', $myId);
                })
                ->whereHas('participantes', function($q) use ($userId) {
                    $q->where('usuario_id', $userId);
                })
                ->has('participantes', '=', 2)
                ->value('id');

            // 3. Crear si no existe
            if (!$conversacionId) {
                $conversacionId = DB::transaction(function () use ($myId, $userId) {
                    $conv = Conversacion::create(['tipo_conversacion' => 'privada']);
                    ConversacionParticipante::create(['conversacion_id' => $conv->id, 'usuario_id' => $myId]);
                    ConversacionParticipante::create(['conversacion_id' => $conv->id, 'usuario_id' => $userId]);
                    return $conv->id;
                });
            }
        }

        $this->selectedConversacionId = $conversacionId;
        $this->message = '';
        $this->markAsRead();
    }

    public function selectConversacion($id)
    {
        $this->selectedConversacionId = $id;
        $this->message = '';
        $this->markAsRead();
    }

    public function markAsRead()
    {
        if ($this->selectedConversacionId) {
            MensajeChat::where('conversacion_id', $this->selectedConversacionId)
                ->where('remitente_usuario_id', '!=', Auth::id())
                ->where('leido', 0)
                ->update(['leido' => 1]);
        }
    }

    public function sendMessage()
    {
        // 1. VALIDACIÓN DE SUPER ADMIN (Nadie responde al SA)
        $conv = Conversacion::find($this->selectedConversacionId);
        if ($conv->tipo_conversacion === 'privada') {
            $otro = $conv->participantes->where('usuario_id', '!=', Auth::id())->first()?->usuario;
            if ($otro && $otro->rol_id === 1 && Auth::user()->rol_id !== 1) {
                return; // Bloqueo silencioso por seguridad
            }
        }

        if ($this->editingMessageId) {
            $this->updateMessage();
            return;
        }

        $this->validate([
            'message' => 'required_without:file|string|max:1000',
            'file' => 'nullable|file|max:102400', // 100MB
        ], [
            'file.max' => 'El archivo es demasiado pesado. El límite máximo permitido es de 100MB.',
            'file.file' => 'El archivo no es válido.',
        ]);

        if (!$this->selectedConversacionId) return;

        $user = Auth::user();
        $otroUser = $conv->participantes->where('usuario_id', '!=', $user->id)->first()?->usuario;

        $attachmentId = null;
        if ($this->file) {
            $fileData = AgroStorageService::storeChatFile($this->file, $user, $conv);
            $media = ArchivoMultimedia::create($fileData);
            $attachmentId = $media->id;
        }

        MensajeChat::create([
            'conversacion_id' => $this->selectedConversacionId,
            'remitente_usuario_id' => $user->id,
            'mensaje' => $this->message ?? 'Archivo adjunto',
            'archivo_adjunto_id' => $attachmentId,
            'es_ia' => 0
        ]);

        $textToSend = $this->message;
        $this->message = '';
        $this->file = null;
        $this->dispatch('scroll-chat');
        $this->dispatch('file-uploaded'); // Trigger to reset file input in JS

        if ($otroUser && $otroUser->email === 'agrobot@agrosys.com') {
            $this->respondAsIA($textToSend);
        }
    }

    protected function respondAsIA($userMessage)
    {
        $this->isAiThinking = true;

        $service = new \App\Services\AgroBotService();
        $aiResponse = $service->getResponse($userMessage);

        MensajeChat::create([
            'conversacion_id' => $this->selectedConversacionId,
            'remitente_usuario_id' => User::where('email', 'agrobot@agrosys.com')->value('id'),
            'mensaje' => $aiResponse,
            'es_ia' => 1
        ]);

        $this->isAiThinking = false;
        $this->dispatch('scroll-chat');
    }

    public function deleteForMe($id)
    {
        $msg = MensajeChat::findOrFail($id);
        $msg->borradoPorUsuarios()->syncWithoutDetaching([Auth::id()]);
    }

    public function deleteChat()
    {
        if (!$this->selectedConversacionId) return;

        $conv = Conversacion::find($this->selectedConversacionId);

        ConversacionParticipante::where('conversacion_id', $this->selectedConversacionId)
            ->where('usuario_id', Auth::id())
            ->update(['limpiado_at' => now()]);

        // Si es privada, deseleccionamos para que desaparezca de la lista (comportamiento WhatsApp)
        // Si es grupal, la mantenemos seleccionada pero con la vista vacía
        if ($conv->tipo_conversacion === 'privada') {
            $this->selectedConversacionId = null;
        }

        $this->dispatch('scroll-chat');
    }

    public function download($mediaId)
    {
        $media = ArchivoMultimedia::findOrFail($mediaId);
        return Storage::disk('public')->download($media->ruta_completa, $media->nombre_original);
    }

    public function deleteForEveryone($id)
    {
        $msg = MensajeChat::findOrFail($id);
        if ($msg->remitente_usuario_id !== Auth::id()) return;

        $msg->update([
            'eliminado_todos' => 1,
            'mensaje' => 'Este mensaje fue eliminado'
        ]);

        if ($msg->archivo_adjunto_id) {
            $media = $msg->adjunto;
            Storage::disk('public')->delete($media->ruta_completa);
            $msg->update(['archivo_adjunto_id' => null]);
            $media->delete();
        }
    }

    public function startEditing($id)
    {
        $msg = MensajeChat::findOrFail($id);
        if ($msg->remitente_usuario_id !== Auth::id() || $msg->eliminado_todos) return;

        $this->editingMessageId = $id;
        $this->message = $msg->mensaje;
    }

    public function cancelEditing()
    {
        $this->editingMessageId = null;
        $this->message = '';
    }

    public function updateMessage()
    {
        $this->validate(['message' => 'required|string|max:1000']);
        $msg = MensajeChat::findOrFail($this->editingMessageId);
        if ($msg->remitente_usuario_id !== Auth::id()) return;

        $msg->update([
            'mensaje' => $this->message,
            'editado_at' => now()
        ]);

        $this->cancelEditing();
    }

    public function render()
    {
        $myId = Auth::id();

        $conversaciones = Conversacion::whereHas('participantes', function($q) use ($myId) {
                $q->where('usuario_id', $myId);
            })
            ->with(['participantes.usuario', 'mensajes.borradoPorUsuarios'])
            ->get()
            ->map(function($conv) use ($myId) {
                $miPart = $conv->participantes->where('usuario_id', $myId)->first();
                $limpiadoAt = $miPart?->limpiado_at;

                $conv->ultimo_mensaje_visible = $conv->mensajes->filter(function($m) use ($myId, $limpiadoAt) {
                    $noBorradoManual = !$m->borradoPorUsuarios->contains($myId);
                    $despuesDeLimpieza = $limpiadoAt ? $m->created_at > $limpiadoAt : true;
                    return $noBorradoManual && $despuesDeLimpieza;
                })->sortByDesc('created_at')->first();

                $conv->conteo_no_leidos = $conv->mensajes->filter(function($m) use ($myId, $limpiadoAt) {
                    return $m->remitente_usuario_id !== $myId &&
                           $m->leido === 0 &&
                           ($limpiadoAt ? $m->created_at > $limpiadoAt : true);
                })->count();

                return $conv;
            })
            ->filter(function($conv) {
                // REGLA: Los grupos SIEMPRE se muestran si eres miembro.
                // Los privados solo si tienen mensajes visibles o están seleccionados.
                return $conv->tipo_conversacion === 'grupal' ||
                       $conv->ultimo_mensaje_visible !== null ||
                       $conv->id == $this->selectedConversacionId;
            })
            ->sortByDesc(function($conv) {
                return $conv->ultimo_mensaje_visible?->created_at ?? $conv->created_at;
            });

        $contactos = [];
        if (strlen($this->search) > 1) {
            $contactos = User::where('id', '!=', $myId)
                ->where(function($q) {
                    $q->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                      ->orWhere('dni', 'like', '%' . $this->search . '%');
                })->take(10)->get();
        }

        $mensajes = [];
        $otroParticipante = null;
        $esSoloLectura = false;
        $mediaList = [];

        if ($this->selectedConversacionId) {
            $convActual = Conversacion::with(['mensajes.remitente', 'mensajes.borradoPorUsuarios', 'mensajes.adjunto', 'participantes'])->find($this->selectedConversacionId);
            if ($convActual) {
                $miParticipante = $convActual->participantes->where('usuario_id', $myId)->first();
                $limpiadoAt = $miParticipante?->limpiado_at;

                $mensajes = $convActual->mensajes->filter(function($m) use ($myId, $limpiadoAt) {
                    $noBorradoManual = !$m->borradoPorUsuarios->contains($myId);
                    $despuesDeLimpieza = $limpiadoAt ? $m->created_at > $limpiadoAt : true;
                    return $noBorradoManual && $despuesDeLimpieza;
                });

                // Lista de medios para el visor (slider) - SOLO IMAGENES Y VIDEOS
                $this->mediaList = $mensajes->whereNotNull('archivo_adjunto_id')
                    ->filter(function($m) {
                        return str_contains($m->adjunto->tipo_mime, 'image') || str_contains($m->adjunto->tipo_mime, 'video');
                    })
                    ->map(function($m) {
                        return [
                            'url' => Storage::url($m->adjunto->ruta_completa),
                            'tipo' => str_contains($m->adjunto->tipo_mime, 'image') ? 'image' : 'video',
                            'id' => $m->id
                        ];
                    })->values()->toArray();

                if ($convActual->tipo_conversacion === 'privada') {
                    $otroParticipante = $convActual->participantes->where('usuario_id', '!=', $myId)->first()?->usuario;
                    // REGLA: Nadie responde al Super Admin (Rol 1)
                    if ($otroParticipante && $otroParticipante->rol_id === 1 && Auth::user()->rol_id !== 1) {
                        $esSoloLectura = true;
                    }
                }
            }
        }

        return view('livewire.chat.chat-manager', [
            'conversaciones' => $conversaciones,
            'contactos' => $contactos,
            'mensajes' => $mensajes,
            'otroParticipante' => $otroParticipante,
            'esSoloLectura' => $esSoloLectura,
            'mediaList' => $this->mediaList
        ]);
    }
}

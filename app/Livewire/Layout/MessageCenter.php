<?php

namespace App\Livewire\Layout;

use App\Models\Conversacion;
use App\Models\MensajeChat;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MessageCenter extends Component
{
    public function goToChat($conversacionId)
    {
        // Marcar como leídos los mensajes de esta conversación al entrar
        MensajeChat::where('conversacion_id', $conversacionId)
            ->where('remitente_usuario_id', '!=', Auth::id())
            ->update(['leido' => 1]);

        return $this->redirect(route('chat.index') . '?open=' . $conversacionId, navigate: true);
    }

    public function render()
    {
        $myId = Auth::id();

        // Obtener las últimas conversaciones con mensajes no leídos o recientes
        $recientes = Conversacion::whereHas('participantes', function($q) use ($myId) {
                $q->where('usuario_id', $myId);
            })
            ->with(['participantes.usuario', 'mensajes' => function($q) {
                $q->latest()->limit(1);
            }])
            ->get()
            ->sortByDesc(function($conv) {
                return $conv->mensajes->first()?->created_at ?? $conv->created_at;
            })
            ->take(5);

        $unreadCount = MensajeChat::whereHas('conversacion.participantes', function($q) use ($myId) {
                $q->where('usuario_id', $myId);
            })
            ->where('remitente_usuario_id', '!=', $myId)
            ->where('leido', 0)
            ->count();

        return view('livewire.layout.message-center', [
            'recientes' => $recientes,
            'unreadCount' => $unreadCount
        ]);
    }
}

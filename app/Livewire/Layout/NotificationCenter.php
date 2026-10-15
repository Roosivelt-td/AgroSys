<?php

namespace App\Livewire\Layout;

use App\Models\Notificacion;
use App\Models\SugerenciaTarea;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationCenter extends Component
{
    public function markAsRead($id)
    {
        $notificacion = Notificacion::where('usuario_id', Auth::id())->find($id);
        if ($notificacion) {
            $notificacion->update(['leido' => 1]);
        }
    }

    public function markAllAsRead()
    {
        Notificacion::where('usuario_id', Auth::id())->update(['leido' => 1]);
    }

    public function handleNotificationClick($id)
    {
        $notif = Notificacion::where('usuario_id', Auth::id())->find($id);
        if (!$notif) return;

        $notif->update(['leido' => 1]);

        // Redirección si la notificación es de un evento o reunión
        if ($notif->tipo === 'evento') {
            return $this->redirect(route('eventos.index'), navigate: true);
        }

        // Redirección si la notificación es de una sugerencia o instrucción técnica
        $isSugerenciaNotif = ($notif->tipo === 'sugerencia') ||
            $notif->sugerencia_id ||
            str_contains(mb_strtolower($notif->titulo), 'sugerencia') ||
            str_contains(mb_strtolower($notif->titulo), 'instrucción') ||
            str_contains(mb_strtolower($notif->titulo), 'tarea');

        if ($isSugerenciaNotif) {
            if ($notif->sugerencia_id) {
                session()->flash('open_sugerencia', $notif->sugerencia_id);
            } else {
                $sug = SugerenciaTarea::where('agricultor_usuario_id', Auth::id())->latest()->first();
                if ($sug) {
                    session()->flash('open_sugerencia', $sug->id);
                }
            }

            return $this->redirect(route('supervisor.tareas'), navigate: true);
        }

        // Redirección si la notificación está vinculada a un trámite / solicitud
        if ($notif->solicitud_id) {
            session()->flash('open_solicitud', $notif->solicitud_id);

            if (Auth::user()->rol_id === 1) {
                return $this->redirect(route('admin.solicitudes'), navigate: true);
            } elseif (Auth::user()->esAdminDeOrganizacion()) {
                return $this->redirect(route('admin.solicitudes.internas'), navigate: true);
            } else {
                return $this->redirect(route('profile.tramites'), navigate: true);
            }
        }
    }

    public function goToTramite($id)
    {
        return $this->handleNotificationClick($id);
    }

    public function render()
    {
        $notificaciones = Notificacion::where('usuario_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $unreadCount = Notificacion::where('usuario_id', Auth::id())
            ->where('leido', 0)
            ->count();

        return view('livewire.layout.notification-center', [
            'notificaciones' => $notificaciones,
            'unreadCount' => $unreadCount
        ]);
    }
}

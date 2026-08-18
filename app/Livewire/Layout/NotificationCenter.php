<?php

namespace App\Livewire\Layout;

use App\Models\Notificacion;
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

    public function goToTramite($id)
    {
        $notif = Notificacion::find($id);
        if (!$notif || !$notif->solicitud_id) return;

        $notif->update(['leido' => 1]);

        // Guardamos en sesión el ID de la solicitud para que el componente destino lo abra
        session()->flash('open_solicitud', $notif->solicitud_id);

        if (Auth::user()->rol_id === 1) {
            return $this->redirect(route('admin.solicitudes'), navigate: true);
        } elseif (Auth::user()->esAdminDeOrganizacion()) {
            return $this->redirect(route('admin.solicitudes.internas'), navigate: true);
        } else {
            return $this->redirect(route('profile.tramites'), navigate: true);
        }
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

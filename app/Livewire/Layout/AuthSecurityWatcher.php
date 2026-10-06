<?php

namespace App\Livewire\Layout;

use App\Models\SolicitudAccesoSesion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AuthSecurityWatcher extends Component
{
    public $confirmPassword = '';
    public $pendingRequestId = null;

    public function checkPendingRequests()
    {
        if (!Auth::check() || Auth::user()->rol_id !== 1) return;

        $solicitud = SolicitudAccesoSesion::where('usuario_id', Auth::id())
            ->where('estado', 'pendiente')
            ->where('created_at', '>=', now()->subMinutes(3))
            ->latest()
            ->first();

        if ($solicitud) {
            $this->pendingRequestId = $solicitud->id;
        } else {
            $this->pendingRequestId = null;
        }
    }

    public function autorizarAcceso()
    {
        $this->validate([
            'confirmPassword' => 'required|string',
        ], [
            'confirmPassword.required' => 'Ingresa tu contraseña para autorizar.',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->confirmPassword, $user->password)) {
            $this->addError('confirmPassword', 'La contraseña ingresada es incorrecta.');
            return;
        }

        if ($this->pendingRequestId) {
            $solicitud = SolicitudAccesoSesion::find($this->pendingRequestId);
            if ($solicitud && $solicitud->estado === 'pendiente') {
                $solicitud->update(['estado' => 'autorizado']);
            }
        }

        // Limpiar contraseña
        $this->reset('confirmPassword');

        // Cerrar sesión del dispositivo actual
        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();

        session()->flash('status', 'Has autorizado el acceso desde otro dispositivo y tu sesión actual ha sido cerrada.');
        return redirect()->route('login');
    }

    public function rechazarAcceso()
    {
        if ($this->pendingRequestId) {
            $solicitud = SolicitudAccesoSesion::find($this->pendingRequestId);
            if ($solicitud && $solicitud->estado === 'pendiente') {
                $solicitud->update(['estado' => 'rechazado']);
            }
        }

        $this->reset(['confirmPassword', 'pendingRequestId']);
        $this->resetErrorBag();
    }

    public function render()
    {
        $solicitudActual = null;
        if ($this->pendingRequestId && Auth::check()) {
            $solicitudActual = SolicitudAccesoSesion::find($this->pendingRequestId);
        }

        return view('livewire.layout.auth-security-watcher', [
            'solicitudActual' => $solicitudActual
        ]);
    }
}

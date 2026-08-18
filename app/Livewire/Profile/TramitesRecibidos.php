<?php

namespace App\Livewire\Profile;

use App\Models\Solicitud;
use App\Http\Controllers\OrganizacionController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TramitesRecibidos extends Component
{
    use WithPagination;

    public $openSolicitudId = null;
    public $highlightId = null;

    public function mount()
    {
        if (session()->has('open_solicitud')) {
            $this->openSolicitudId = session('open_solicitud');
            $this->highlightId = session('open_solicitud');
        }
    }

    public function clearHighlight($id)
    {
        $this->highlightId = null;
        $this->openSolicitudId = $id;
        $this->dispatch('open-modal', 'process-invitacion-' . $id);
    }

    public function close()
    {
        $this->openSolicitudId = null;
    }

    public function aceptar($id)
    {
        $controller = new OrganizacionController();
        $resultado = $controller->aprobarIngresoMiembro($id);
        if ($resultado['success']) session()->flash('status', $resultado['mensaje']);
    }

    public function rechazar($id)
    {
        $controller = new OrganizacionController();
        $resultado = $controller->rechazarSolicitud($id, 'Denegeada por el usuario.');
        if ($resultado['success']) session()->flash('status', $resultado['mensaje']);
    }

    public function render()
    {
        $solicitudes = Solicitud::where(function($q) {
                $q->where('destinatario_usuario_id', Auth::id())
                  ->orWhere('solicitante_usuario_id', Auth::id());
            })
            ->with(['solicitante', 'organizacion'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.profile.tramites-recibidos', ['solicitudes' => $solicitudes]);
    }
}

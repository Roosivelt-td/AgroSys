<?php

namespace App\Livewire\Admin;

use App\Models\Solicitud;
use App\Http\Controllers\OrganizacionController;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SolicitudesManager extends Component
{
    use WithPagination;

    public $openSolicitudId = null;
    public $highlightId = null; // ID para el parpadeo visual

    public function mount()
    {
        // Recuperar solicitud desde sesión (redirección de notificación)
        if (session()->has('open_solicitud')) {
            $this->openSolicitudId = session('open_solicitud');
            $this->highlightId = session('open_solicitud');
        }
    }

    public function clearHighlight($id)
    {
        $this->highlightId = null;
        $this->openSolicitudId = $id;
        $this->dispatch('open-modal', 'process-solicitud-' . $id);
    }

    public function close()
    {
        $this->openSolicitudId = null;
    }

    public function aprobar(int $id)
    {
        $controller = new OrganizacionController();
        $resultado = $controller->aprobarSolicitud($id);
        if ($resultado['success']) session()->flash('status', $resultado['mensaje']);
    }

    public function rechazar(int $id)
    {
        $controller = new OrganizacionController();
        $resultado = $controller->rechazarSolicitud($id, 'No cumple requisitos.');
        if ($resultado['success']) session()->flash('status', $resultado['mensaje']);
    }

    public function render()
    {
        $solicitudes = Solicitud::where('tipo', 'creacion_organizacion')
            ->with('solicitante')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.solicitudes-manager', ['solicitudes' => $solicitudes]);
    }
}

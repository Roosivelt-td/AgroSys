<?php

namespace App\Livewire\Admin;

use App\Models\Solicitud;
use App\Models\MiembroOrganizacion;
use App\Http\Controllers\OrganizacionController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SolicitudesInternas extends Component
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
        $this->dispatch('open-modal', 'process-solicitud-' . $id);
    }

    public function close()
    {
        $this->openSolicitudId = null;
    }

    public function aprobar($id)
    {
        $controller = new OrganizacionController();
        $resultado = $controller->aprobarIngresoMiembro($id);
        if ($resultado['success']) session()->flash('status', $resultado['mensaje']);
    }

    public function rechazar($id)
    {
        $controller = new OrganizacionController();
        $resultado = $controller->rechazarSolicitud($id, 'Rechazada por el administrador.');
        if ($resultado['success']) session()->flash('status', $resultado['mensaje']);
    }

    public function render()
    {
        $misOrgs = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))
            ->pluck('organizacion_id');

        $solicitudes = Solicitud::whereIn('organizacion_id', $misOrgs)
            ->whereIn('tipo', ['unirse_organizacion', 'ascenso_rol', 'renuncia_rol'])
            ->with(['solicitante', 'organizacion'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.solicitudes-internas', ['solicitudes' => $solicitudes]);
    }
}

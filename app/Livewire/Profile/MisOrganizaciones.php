<?php

namespace App\Livewire\Profile;

use App\Models\MiembroOrganizacion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class MisOrganizaciones extends Component
{
    public function mount()
    {
        $membresias = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->where('estado', 1)
            ->get();

        // Si solo pertenece a una organización, redirigir directo al espacio de trabajo
        if ($membresias->count() === 1) {
            return redirect()->route('admin.organizacion.miembros', ['id' => $membresias->first()->organizacion_id]);
        }
    }

    public function render()
    {
        $membresias = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->with(['organizacion', 'roles.rolDetalle'])
            ->where('estado', 1)
            ->get();

        return view('livewire.profile.mis-organizaciones', [
            'membresias' => $membresias
        ]);
    }
}

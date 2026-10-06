<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Rol;
use App\Models\Terreno;
use App\Models\HistorialProceso;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UsersManager extends Component
{
    use WithPagination;

    public $search = '';

    // Perfil Modal
    public $viewingUser = null;
    public $userTerrenosCount = 0;
    public $userTotalHectareas = 0;
    public $userActivityChartData = [
        'labels' => [],
        'values' => [],
    ];

    // Edición Modal
    public $editingUser = null;
    public $editUserId = null;
    public $editNombres = '';
    public $editApellidos = '';
    public $editDni = '';
    public $editEmail = '';
    public $editTelefono = '';
    public $editRolId = '';
    public $editIsActivo = true;

    protected $listeners = ['refreshUsers' => '$refresh'];

    public function toggleStatus($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'No puedes bloquear tu propia cuenta.');
            return;
        }

        $user->is_activo = !$user->is_activo;
        $user->save();

        $estadoStr = $user->is_activo ? 'reactivado' : 'bloqueado';
        session()->flash('status', "El usuario {$user->nombres} fue {$estadoStr} correctamente.");
    }

    public function openProfileModal($userId)
    {
        $user = User::with(['rol', 'membresias.organizacion'])->findOrFail($userId);
        $this->viewingUser = $user;

        // Cantidad de Terrenos y Hectáreas acumuladas
        $terrenos = Terreno::where('usuario_id', $user->id)->get();
        $this->userTerrenosCount = $terrenos->count();
        $this->userTotalHectareas = $terrenos->sum('hectareas');

        // Lógica del Gráfico de Horas de Actividad x Días (Últimos 7 Días)
        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayLabel = $date->translatedFormat('D d M');
            $labels[] = $dayLabel;

            // Contar eventos/actividades registradas en el día
            $eventsCount = HistorialProceso::where('usuario_id', $user->id)
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->count();

            // Estimar horas aproximadas de uso activo (mínimo 0.5h por evento registrado)
            $estimatedHours = round(min(8, $eventsCount * 0.75), 1);
            $values[] = $estimatedHours;
        }

        $this->userActivityChartData = [
            'labels' => $labels,
            'values' => $values,
        ];

        $this->dispatch('open-modal', 'modal-user-profile');
    }

    public function closeProfileModal()
    {
        $this->viewingUser = null;
        $this->dispatch('close-modal', 'modal-user-profile');
    }

    public function openEditModal($userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUser = $user;
        $this->editUserId = $user->id;
        $this->editNombres = $user->nombres;
        $this->editApellidos = $user->apellidos;
        $this->editDni = $user->dni ?: '';
        $this->editEmail = $user->email;
        $this->editTelefono = $user->telefono ?: '';
        $this->editRolId = $user->rol_id;
        $this->editIsActivo = (bool)$user->is_activo;

        $this->dispatch('open-modal', 'modal-edit-user');
    }

    public function saveUser()
    {
        $this->validate([
            'editNombres' => 'required|string|min:2|max:100',
            'editApellidos' => 'required|string|min:2|max:100',
            'editDni' => 'required|string|digits:8|unique:usuarios,dni,' . $this->editUserId,
            'editEmail' => 'required|email|max:255|unique:usuarios,email,' . $this->editUserId,
            'editRolId' => 'required|exists:rol,id',
        ], [
            'editNombres.required' => 'Los nombres son obligatorios.',
            'editApellidos.required' => 'Los apellidos son obligatorios.',
            'editDni.required' => 'El DNI es obligatorio.',
            'editDni.digits' => 'El DNI debe tener 8 dígitos.',
            'editDni.unique' => 'Este DNI ya está registrado.',
            'editEmail.required' => 'El correo es obligatorio.',
            'editEmail.unique' => 'Este correo ya pertenece a otro usuario.',
        ]);

        $user = User::findOrFail($this->editUserId);
        $user->update([
            'nombres' => $this->editNombres,
            'apellidos' => $this->editApellidos,
            'dni' => $this->editDni,
            'email' => $this->editEmail,
            'telefono' => $this->editTelefono,
            'rol_id' => $this->editRolId,
            'is_activo' => $this->editIsActivo,
        ]);

        $this->dispatch('close-modal', 'modal-edit-user');
        $this->reset(['editingUser', 'editUserId', 'editNombres', 'editApellidos', 'editDni', 'editEmail', 'editTelefono', 'editRolId', 'editIsActivo']);
        session()->flash('status', 'Información de usuario actualizada correctamente.');
    }

    public function render()
    {
        $users = User::with(['rol', 'membresias.organizacion'])
            ->where(function($query) {
                $query->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('dni', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.users-manager', [
            'users' => $users,
            'roles' => Rol::all(),
        ]);
    }
}

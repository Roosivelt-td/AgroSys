<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\HistorialProceso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class CreateSuperAdmin extends Component
{
    public $nombres = '';
    public $apellidos = '';
    public $email = '';
    public $dni = '';
    public $password = '';
    public $admin_password = ''; // Contraseña del admin actual para confirmar

    protected $rules = [
        'nombres' => 'required|string|max:100',
        'apellidos' => 'required|string|max:100',
        'email' => 'required|email|unique:usuarios,email',
        'dni' => 'required|string|unique:usuarios,dni',
        'password' => 'required|string|min:8',
        'admin_password' => 'required|string',
    ];

    public function save()
    {
        $this->validate();

        // 1. Validar contraseña del Super Admin actual
        if (!Hash::check($this->admin_password, Auth::user()->password)) {
            $this->addError('admin_password', 'La contraseña de administrador es incorrecta.');
            return;
        }

        // 2. Crear nuevo Super Admin
        $newUser = User::create([
            'rol_id' => 1, // Super Admin
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'dni' => $this->dni,
            'password' => Hash::make($this->password),
            'estado' => 1,
            'is_activo' => 1,
        ]);

        // 3. Registrar en Historial
        HistorialProceso::create([
            'usuario_id' => Auth::id(),
            'tabla_afectada' => 'usuarios',
            'registro_id' => $newUser->id,
            'accion' => 'REGISTRO',
            'descripcion' => 'Super Admin creó un nuevo Super Admin: ' . $newUser->email,
        ]);

        session()->flash('status', 'Nuevo Super Administrador creado con éxito.');
        $this->dispatch('close-modal', 'create-superadmin');
        $this->dispatch('refreshUsers');

        $this->reset(['nombres', 'apellidos', 'email', 'dni', 'password', 'admin_password']);
    }

    public function render()
    {
        return view('livewire.admin.create-super-admin');
    }
}

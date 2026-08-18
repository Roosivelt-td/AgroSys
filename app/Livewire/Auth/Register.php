<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Rol;
use App\Models\HistorialProceso;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Component;

class Register extends Component
{
    public string $nombres = '';
    public string $apellidos = '';
    public string $dni = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Maneja el registro de un nuevo agricultor.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'dni' => ['required', 'string', 'max:20', 'unique:usuarios,dni'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $rolAgricultor = Rol::where('nombre', 'Agricultor')->first();

        $user = User::create([
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'dni' => $validated['dni'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rol_id' => $rolAgricultor ? $rolAgricultor->id : 2,
            'estado' => 1,
            'is_activo' => true,
        ]);

        event(new Registered($user));

        // Registrar en historial de procesos
        HistorialProceso::create([
            'usuario_id' => $user->id,
            'tabla_afectada' => 'usuarios',
            'registro_id' => $user->id,
            'accion' => 'REGISTRO',
            'descripcion' => 'Nuevo agricultor registrado en la plataforma: ' . $user->email,
        ]);

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.guest');
    }
}

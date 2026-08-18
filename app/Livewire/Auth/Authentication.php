<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Rol;
use App\Models\HistorialProceso;
use App\Livewire\Forms\LoginForm;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Component;

class Authentication extends Component
{
    // Mode: 'login', 'register', 'forgot'
    public string $mode = 'login';

    // Login Form
    public LoginForm $loginForm;

    // Register Fields
    public string $nombres = '';
    public string $apellidos = '';
    public string $dni = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Forgot Password Fields
    public string $forgotEmail = '';

    public function mount()
    {
        if (request()->routeIs('register')) {
            $this->mode = 'register';
        } elseif (request()->routeIs('password.request')) {
            $this->mode = 'forgot';
        }
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
        $this->resetErrorBag();
        $this->reset(['nombres', 'apellidos', 'dni', 'email', 'password', 'password_confirmation', 'forgotEmail']);

        // Reset login form fields too
        $this->loginForm->email = '';
        $this->loginForm->password = '';
    }

    /**
     * Handle Login
     */
    public function login(): void
    {
        $this->loginForm->validate();

        $this->loginForm->authenticate();
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false));
    }

    /**
     * Handle Registration
     */
    public function register(): void
    {
        $validated = $this->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'dni' => ['required', 'string', 'digits:8', 'unique:usuarios,dni'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ], [
            'nombres.required' => 'Necesitamos tus nombres para el registro.',
            'apellidos.required' => 'Tus apellidos son obligatorios.',
            'dni.required' => 'El número de DNI es fundamental.',
            'dni.digits' => 'El DNI debe tener exactamente 8 números.',
            'dni.unique' => 'Este DNI ya está registrado en el sistema.',
            'email.required' => 'El correo es necesario para contactarte.',
            'email.email' => 'Ingresa un correo electrónico real.',
            'email.unique' => 'Este correo ya pertenece a un miembro.',
            'password.required' => 'Crea una contraseña segura.',
            'password.confirmed' => 'Las contraseñas no coinciden, verifícalas.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
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

        $this->redirect(route('dashboard', absolute: false));
    }

    /**
     * Handle Forgot Password
     */
    public function sendResetLink(): void
    {
        $this->validate([
            'forgotEmail' => 'required|email',
        ], [
            'forgotEmail.required' => 'Ingresa tu correo para enviarte las instrucciones.',
            'forgotEmail.email' => 'El formato del correo no es válido.',
        ]);

        $status = Password::broker()->sendResetLink(
            ['email' => $this->forgotEmail]
        );

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __($status));
            $this->reset('forgotEmail');
        } else {
            $this->addError('forgotEmail', __($status));
        }
    }

    public function render()
    {
        return view('livewire.auth.authentication')->layout('layouts.guest');
    }
}

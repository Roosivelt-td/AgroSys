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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

    public function updatedNombres($value): void
    {
        $this->nombres = preg_replace('/[^a-zA-ZÁÉÍÓÚáéíóúÑñ ]/u', '', $value);
    }

    public function updatedApellidos($value): void
    {
        $this->apellidos = preg_replace('/[^a-zA-ZÁÉÍÓÚáéíóúÑñ ]/u', '', $value);
    }

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
        // Limpieza forzosa antes de validar
        $this->nombres = preg_replace('/[^a-zA-ZÁÉÍÓÚáéíóúÑñ ]/u', '', $this->nombres);
        $this->apellidos = preg_replace('/[^a-zA-ZÁÉÍÓÚáéíóúÑñ ]/u', '', $this->apellidos);

        $validated = $this->validate([
            'nombres' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(?:\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)*$/u',
                'not_regex:/[0-9]/'
            ],
            'apellidos' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(?:\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)*$/u',
                'not_regex:/[0-9]/'
            ],
            'dni' => ['required', 'string', 'digits:8', 'unique:usuarios,dni'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ], [
            'nombres.required' => 'Necesitamos tus nombres para el registro.',
            'nombres.regex' => 'Los nombres solo pueden contener letras y un espacio simple entre ellos.',
            'nombres.not_regex' => 'Los nombres no pueden contener números.',
            'nombres.min' => 'El nombre debe tener al menos 3 letras.',
            'apellidos.required' => 'Tus apellidos son obligatorios.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y un espacio simple entre ellos.',
            'apellidos.not_regex' => 'Los apellidos no pueden contener números.',
            'apellidos.min' => 'Los apellidos deben tener al menos 3 letras.',
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

        /*
        // MICROSERVIDOR DETENIDO TEMPORALMENTE: Se deshabilita la validación externa
        // para facilitar la clonación del proyecto en otras máquinas sin el microservicio.

        // VALIDACIÓN CON EL MICROSERVICIO (Evitar "test", "hola", etc)
        $validatorUrl = rtrim((string) config('services.name_validator.url'), '/');

        try {
            $response = Http::timeout(3)
                ->acceptJson()
                ->post($validatorUrl . '/v1/names/validate', [
                    'first_name' => trim($validated['nombres']),
                    'last_name' => trim($validated['apellidos']),
                ]);

            if ($response->successful()) {
                $result = $response->json();
                if (!($result['valid'] ?? false)) {
                    $blockedWords = $result['blocked_words'] ?? [];
                    if (!empty($blockedWords)) {
                        $words = implode(', ', $blockedWords);
                        $this->addError('nombres', "El nombre o apellido contiene palabras no permitidas: {$words}.");
                    } else {
                        $this->addError('nombres', 'El nombre o apellido no parece ser real según nuestros registros.');
                    }
                    return;
                }
            } else {
                // Si el microservicio responde con error (4xx o 5xx), bloqueamos por seguridad
                Log::error('El microservicio de validación respondió con error.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                $this->addError('nombres', 'Error de verificación de identidad. Intente más tarde.');
                return;
            }
        } catch (\Throwable $e) {
            \Log::error('Error conectando con el microservicio de validación de nombres.', [
                'error' => $e->getMessage(),
            ]);
            // En caso de caída del microservicio, decidimos si permitir o bloquear.
            // Para ser estrictos como pide el usuario, bloqueamos:
            $this->addError('nombres', 'No se pudo verificar el nombre en este momento. Intente nuevamente.');
            return;
        }
        */

        $rolAgricultor = Rol::where('nombre', 'Agricultor')->first();

        $user = User::create([
            'nombres' => mb_convert_case(trim($validated['nombres']), MB_CASE_TITLE, "UTF-8"),
            'apellidos' => mb_convert_case(trim($validated['apellidos']), MB_CASE_TITLE, "UTF-8"),
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

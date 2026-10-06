<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Rol;
use App\Models\HistorialProceso;
use App\Models\SolicitudAccesoSesion;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Livewire\Component;

class Authentication extends Component
{
    // Mode: 'login', 'register', 'forgot', 'waiting_authorization'
    public string $mode = 'login';

    // Login Fields
    public string $loginEmail = '';
    public string $loginPassword = '';
    public bool $remember = false;
    public $solicitudId = null;

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
        $this->reset(['nombres', 'apellidos', 'dni', 'email', 'password', 'password_confirmation', 'forgotEmail', 'loginEmail', 'loginPassword', 'remember', 'solicitudId']);
    }

    /**
     * Handle Login
     */
    public function login(): void
    {
        if (empty($this->loginEmail) && request()->has('loginEmail')) {
            $this->loginEmail = (string) request('loginEmail');
        }
        if (empty($this->loginPassword) && request()->has('loginPassword')) {
            $this->loginPassword = (string) request('loginPassword');
        }

        $this->validate([
            'loginEmail' => 'required|string|email',
            'loginPassword' => 'required|string',
        ], [
            'loginEmail.required' => 'El correo electrónico es obligatorio.',
            'loginEmail.email' => 'Ingresa un correo electrónico válido.',
            'loginPassword.required' => 'La contraseña es obligatoria.',
        ]);

        $user = User::where('email', $this->loginEmail)->first();

        if (!$user || !Hash::check($this->loginPassword, $user->password) || !$user->is_activo) {
            $this->addError('loginEmail', 'El correo o la contraseña son incorrectos. Por favor, verifica tus datos.');
            return;
        }

        // EXCLUSIVO PARA SUPER ADMIN (rol_id === 1): Control de Sesión Única y Autorización
        if ($user->rol_id === 1) {
            $lifetimeMinutes = config('session.lifetime', 120);
            $activeSession = DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', session()->getId())
                ->where('last_activity', '>=', now()->subMinutes($lifetimeMinutes)->timestamp)
                ->first();

            if ($activeSession) {
                $request = request();
                $ip = $request->ip() ?: '127.0.0.1';
                $ua = $request->userAgent() ?: 'Navegador Web';

                $os = 'Sistema Desconocido';
                if (preg_match('/windows nt 10/i', $ua)) $os = 'Windows 10/11';
                elseif (preg_match('/windows/i', $ua)) $os = 'Windows';
                elseif (preg_match('/android/i', $ua)) $os = 'Android';
                elseif (preg_match('/iphone|ipad|ipod/i', $ua)) $os = 'iOS';
                elseif (preg_match('/macintosh|mac os x/i', $ua)) $os = 'macOS';
                elseif (preg_match('/linux/i', $ua)) $os = 'Linux';

                $browser = 'Navegador';
                if (preg_match('/edg/i', $ua)) $browser = 'Microsoft Edge';
                elseif (preg_match('/chrome/i', $ua)) $browser = 'Google Chrome';
                elseif (preg_match('/firefox/i', $ua)) $browser = 'Mozilla Firefox';
                elseif (preg_match('/safari/i', $ua)) $browser = 'Apple Safari';

                $deviceType = preg_match('/mobile|android|iphone|ipad/i', $ua) ? 'Móvil' : 'Escritorio';
                $dispositivo = "{$browser} en {$os} ({$deviceType})";

                $solicitud = SolicitudAccesoSesion::create([
                    'usuario_id' => $user->id,
                    'session_id_solicitante' => session()->getId(),
                    'ip_solicitante' => $ip,
                    'user_agent_solicitante' => $ua,
                    'dispositivo_solicitante' => $dispositivo,
                    'estado' => 'pendiente',
                ]);

                $this->solicitudId = $solicitud->id;
                $this->mode = 'waiting_authorization';
                return;
            }
        }

        // Para usuarios estándares (Agricultores/Admins de Org) o Super Admin sin otra sesión activa: Autenticar directo
        Auth::login($user, $this->remember);
        Session::regenerate();

        $this->redirect(route('dashboard'));
    }

    public function checkAuthorizationStatus()
    {
        if (!$this->solicitudId) return;

        $solicitud = SolicitudAccesoSesion::find($this->solicitudId);

        if (!$solicitud) {
            $this->mode = 'login';
            $this->addError('loginEmail', 'La solicitud de acceso no fue encontrada.');
            return;
        }

        if ($solicitud->estado === 'autorizado') {
            // Eliminar sesiones previas activas y autenticar este dispositivo
            DB::table('sessions')->where('user_id', $solicitud->usuario_id)->delete();

            Auth::loginUsingId($solicitud->usuario_id);
            Session::regenerate();

            $solicitud->delete();
            $this->redirect(route('dashboard'));
            return;
        }

        if ($solicitud->estado === 'rechazado') {
            $this->mode = 'login';
            $this->addError('loginEmail', 'La solicitud de inicio de sesión fue rechazada desde el dispositivo activo.');
            $solicitud->delete();
            $this->solicitudId = null;
            return;
        }

        if ($solicitud->created_at->lt(now()->subMinutes(2))) {
            $solicitud->update(['estado' => 'expirado']);
            $this->mode = 'login';
            $this->addError('loginEmail', 'La solicitud de autorización expiró por falta de respuesta.');
            $this->solicitudId = null;
            return;
        }
    }

    public function cancelWaitingAuthorization()
    {
        if ($this->solicitudId) {
            SolicitudAccesoSesion::where('id', $this->solicitudId)->delete();
        }
        $this->solicitudId = null;
        $this->mode = 'login';
    }

    /**
     * Handle Registration
     */
    public function register(): void
    {
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
            'nombres.regex' => 'Los nombres solo pueden contener letras.',
            'nombres.min' => 'El nombre debe tener al menos 3 letras.',
            'apellidos.required' => 'Tus apellidos son obligatorios.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras.',
            'apellidos.min' => 'Los apellidos deben tener al menos 3 letras.',
            'dni.required' => 'El número de DNI es fundamental.',
            'dni.digits' => 'El DNI debe tener exactamente 8 números.',
            'dni.unique' => 'Este DNI ya está registrado en el sistema.',
            'email.required' => 'El correo es necesario para contactarte.',
            'email.email' => 'Ingresa un correo electrónico real.',
            'email.unique' => 'Este correo ya pertenece a un miembro.',
            'password.required' => 'Crea una contraseña segura.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $rolAgricultor = Rol::where('nombre', 'Agricultor')->first() ?: Rol::firstOrCreate(['id' => 2], ['nombre' => 'Agricultor']);

        // Eliminar sesiones previas antes de registrar el nuevo usuario
        DB::table('sessions')->where('user_id', null)->where('id', session()->getId())->delete();

        $user = User::create([
            'nombres' => mb_convert_case(trim($validated['nombres']), MB_CASE_TITLE, "UTF-8"),
            'apellidos' => mb_convert_case(trim($validated['apellidos']), MB_CASE_TITLE, "UTF-8"),
            'dni' => $validated['dni'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'rol_id' => $rolAgricultor->id,
            'estado' => 1,
            'is_activo' => true,
        ]);

        event(new Registered($user));

        HistorialProceso::create([
            'usuario_id' => $user->id,
            'tabla_afectada' => 'usuarios',
            'registro_id' => $user->id,
            'accion' => 'REGISTRO',
            'descripcion' => 'Nuevo agricultor registrado en la plataforma: ' . $user->email,
        ]);

        Auth::login($user);

        $this->redirect(route('dashboard'));
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
        return view('livewire.auth.authentication')->layout('presentacion.layouts.guest');
    }
}

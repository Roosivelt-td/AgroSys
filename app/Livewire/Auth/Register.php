<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Rol;
use App\Models\HistorialProceso;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
     * Limpia los nombres mientras el usuario escribe.
     */
    public function updatedNombres($value): void
    {
        $this->nombres = preg_replace(
            '/[^a-zA-ZÁÉÍÓÚáéíóúÑñ ]/u',
            '',
            $value
        );
    }

    /**
     * Limpia los apellidos mientras el usuario escribe.
     */
    public function updatedApellidos($value): void
    {
        $this->apellidos = preg_replace(
            '/[^a-zA-ZÁÉÍÓÚáéíóúÑñ ]/u',
            '',
            $value
        );
    }

    /**
     * Registro de usuario.
     */
    public function register(): void
    {
        // =====================================================
        // 1. LIMPIEZA DE NOMBRES Y APELLIDOS
        // =====================================================

        $this->nombres = preg_replace(
            '/[^a-zA-ZÁÉÍÓÚáéíóúÑñ ]/u',
            '',
            $this->nombres
        );

        $this->apellidos = preg_replace(
            '/[^a-zA-ZÁÉÍÓÚáéíóúÑñ ]/u',
            '',
            $this->apellidos
        );

        // =====================================================
        // 2. VALIDACIONES DE LARAVEL
        // =====================================================

        $rules = [
            'nombres' => [
                'required',
                'string',
                'min:3',
                'max:100',
                // Solo letras y espacios
                'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(?:\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)*$/u',
                // No permitir números
                'not_regex:/[0-9]/',
            ],
            'apellidos' => [
                'required',
                'string',
                'min:3',
                'max:100',
                // Solo letras y espacios
                'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(?:\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)*$/u',
                // No permitir números
                'not_regex:/[0-9]/',
            ],
            'dni' => [
                'required',
                'string',
                'digits:8',
                'unique:usuarios,dni',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:usuarios,email',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ];
        $messages = [
            'nombres.required' =>'El nombre es obligatorio.',
            'nombres.regex' =>'Los nombres solo pueden contener letras y un espacio entre nombres.',
            'nombres.not_regex' =>'Los nombres no pueden contener números.',
            'nombres.min' =>'El nombre debe tener al menos 3 letras.',
            'nombres.max' =>'El nombre no puede superar los 100 caracteres.',
            'apellidos.required' =>'Los apellidos son obligatorios.',
            'apellidos.regex' =>'Los apellidos solo pueden contener letras y un espacio entre ellos.',
            'apellidos.not_regex' =>'Los apellidos no pueden contener números.',
            'apellidos.min' =>'Los apellidos deben tener al menos 3 letras.',
            'apellidos.max' =>'Los apellidos no pueden superar los 100 caracteres.',
            'dni.required' =>'El DNI es obligatorio.',
            'dni.digits' =>'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique' =>'Este DNI ya se encuentra registrado.',
            'email.required' =>'El correo electrónico es obligatorio.',
            'email.email' =>'Ingrese un correo electrónico válido.',
            'email.unique' =>'Este correo electrónico ya se encuentra registrado.',
            'password.required' =>'La contraseña es obligatoria.',
            'password.confirmed' =>'Las contraseñas no coinciden.',
        ];

        /*
         * Laravel realiza primero todas las validaciones normales.
         */
        $validated = $this->validate($rules, $messages);

        // =====================================================
        // 3. VALIDACIÓN CON EL MICROSERVICIO PYTHON
        // =====================================================

        /*
         * Obtenemos la URL desde el .env de Laravel:
         *
         * NAME_VALIDATOR_URL=http://localhost:8001
         *
         * La URL final será:
         *
         * http://localhost:8001/v1/names/validate
         */
        $validatorUrl = rtrim(
            (string) config('services.name_validator.url'),
            '/'
        );

        try {

            $response = Http::timeout(3)
                ->acceptJson()
                ->post(
                    $validatorUrl . '/v1/names/validate',
                    [
                        'first_name' => trim($validated['nombres']),
                        'last_name' => trim($validated['apellidos']),
                    ]
                );

        } catch (\Throwable $e) {

            /*
             * Si Python está apagado o no responde,
             * NO creamos la cuenta.
             */

            Log::error(
                'Error conectando con el microservicio de validación de nombres.',
                [
                    'error' => $e->getMessage(),
                ]
            );

            $this->addError(
                'nombres',
                'No se pudo verificar el nombre en este momento. Intente nuevamente.'
            );

            return;
        }

        // =====================================================
        // 4. VERIFICAR RESPUESTA DEL MICROSERVICIO
        // =====================================================

        if (!$response->successful()) {

            Log::error(
                'El microservicio de validación respondió con error.',
                [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]
            );

            $this->addError(
                'nombres',
                'No se pudo verificar el nombre en este momento. Intente nuevamente.'
            );

            return;
        }

        $result = $response->json();

        // =====================================================
        // 5. NOMBRE/APELLIDO RECHAZADO
        // =====================================================

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
        // =====================================================
        // 6. NOMBRE VALIDADO → CONTINUAR CON EL REGISTRO
        // =====================================================

        $rolAgricultor = Rol::where(
            'nombre',
            'Agricultor'
        )->first();
        $user = User::create([
            'nombres' => mb_convert_case(trim($validated['nombres']),
                MB_CASE_TITLE,
                'UTF-8'
            ),
            'apellidos' => mb_convert_case(
                trim($validated['apellidos']),
                MB_CASE_TITLE,
                'UTF-8'
            ),
            'dni' => $validated['dni'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']
            ),
            'rol_id' => $rolAgricultor
                ? $rolAgricultor->id
                : 2,
            'estado' => 1,
            'is_activo' => true,
        ]);

        // =====================================================
        // 7. EVENTO DE REGISTRO
        // =====================================================

        event(
            new Registered($user)
        );

        // =====================================================
        // 8. HISTORIAL
        // =====================================================

        HistorialProceso::create([
            'usuario_id' => $user->id,
            'tabla_afectada' => 'usuarios',
            'registro_id' => $user->id,
            'accion' => 'REGISTRO',
            'descripcion' =>
                'Nuevo agricultor registrado en la plataforma: '
                . $user->email,
        ]);


        // =====================================================
        // 9. INICIAR SESIÓN
        // =====================================================

        Auth::login($user);

        // =====================================================
        // 10. REDIRECCIÓN
        // =====================================================

        $this->redirect(
            route('dashboard', absolute: false),
            navigate: true
        );
    }

    /**
     * Vista de registro.
     */
    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.guest');
    }
}

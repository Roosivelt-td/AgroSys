<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\AgroStorageService;
use App\Models\ArchivoMultimedia;

class UpdateProfileInformation extends Component
{
    use WithFileUploads;

    public string $nombres = '';
    public string $apellidos = '';
    public string $email = '';
    public string $dni = '';
    public string $telefono = '';
    public string $experiencia_anios = '';
    public string $nivel_educativo = '';
    public string $ubicacion = '';
    public string $descripcion = '';

    // Archivos temporales
    public $foto_perfil;
    public $foto_portada;

    /**
     * Inicializa los datos del componente con la info del usuario.
     */
    public function mount(): void
    {
        $usuario = Auth::user();
        $this->nombres = $usuario->nombres ?? '';
        $this->apellidos = $usuario->apellidos ?? '';
        $this->email = $usuario->email ?? '';
        $this->dni = $usuario->dni ?? '';
        $this->telefono = $usuario->telefono ?? '';
        $this->experiencia_anios = $usuario->experiencia_anios ?? '';
        $this->nivel_educativo = $usuario->nivel_educativo ?? '';
        $this->ubicacion = $usuario->ubicacion ?? '';
        $this->descripcion = $usuario->descripcion ?? '';
    }

    /**
     * Guarda los cambios del perfil.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'dni' => ['required', 'string', 'digits:8', Rule::unique(User::class)->ignore($user->id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'experiencia_anios' => ['nullable', 'numeric', 'min:0', 'max:60'],
            'nivel_educativo' => ['nullable', 'string', 'max:100'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'foto_perfil' => ['nullable', 'image', 'max:2048'], // 2MB
            'foto_portada' => ['nullable', 'image', 'max:5120'], // 5MB
        ], [
            'dni.digits' => 'El DNI debe tener exactamente 8 dígitos.',
            'experiencia_anios.numeric' => 'La experiencia debe ser un número.',
            'foto_perfil.image' => 'El archivo debe ser una imagen.',
            'foto_portada.image' => 'La portada debe ser una imagen válida.',
        ]);

        // Manejo de Foto de Perfil con AgroStorageService
        if ($this->foto_perfil) {
            $data = AgroStorageService::storeUserFile($this->foto_perfil, $user, 'perfil');
            $user->foto_perfil_url = $data['url_publica'];

            // Registrar en multimedia
            ArchivoMultimedia::create($data);
        }

        // Manejo de Foto de Portada con AgroStorageService
        if ($this->foto_portada) {
            $data = AgroStorageService::storeUserFile($this->foto_portada, $user, 'portada');
            $user->foto_portada_url = $data['url_publica'];

            // Registrar en multimedia
            ArchivoMultimedia::create($data);
        }

        $user->fill([
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'email' => $validated['email'],
            'dni' => $validated['dni'],
            'telefono' => $validated['telefono'],
            'experiencia_anios' => $validated['experiencia_anios'],
            'nivel_educativo' => $validated['nivel_educativo'],
            'ubicacion' => $validated['ubicacion'],
            'descripcion' => $validated['descripcion'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->nombres . ' ' . $user->apellidos);
    }

    /**
     * Re-envía la verificación de email.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    public function render()
    {
        return view('livewire.profile.update-profile-information-form');
    }
}

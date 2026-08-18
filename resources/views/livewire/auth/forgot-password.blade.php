<div class="space-y-6">
    <!-- VISTA (FRONTEND) - Olvidé mi contraseña -->
    <div class="mb-4 text-sm text-slate-600">
        {{ __('¿Olvidó su contraseña? No hay problema. Simplemente háganos saber su dirección de correo electrónico y le enviaremos un enlace para restablecer la contraseña.') }}
    </div>

    <!-- Estatus de la Sesión -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="bg-agri-green">
                {{ __('Enviar enlace de restablecimiento') }}
            </x-primary-button>
        </div>
    </form>
</div>

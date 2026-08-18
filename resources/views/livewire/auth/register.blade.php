<div class="space-y-6">
    <!-- VISTA (FRONTEND) - Registro -->
    <div class="mb-4 text-sm text-slate-600">
        {{ __('Cree su cuenta de Agricultor en AgroSys para comenzar a gestionar sus terrenos y cultivos.') }}
    </div>

    <form wire:submit="register">
        <!-- Nombres -->
        <div>
            <x-input-label for="nombres" :value="__('Nombres')" />
            <x-text-input wire:model="nombres" id="nombres" class="block mt-1 w-full" type="text" name="nombres" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('nombres')" class="mt-2" />
        </div>

        <!-- Apellidos -->
        <div class="mt-4">
            <x-input-label for="apellidos" :value="__('Apellidos')" />
            <x-text-input wire:model="apellidos" id="apellidos" class="block mt-1 w-full" type="text" name="apellidos" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('apellidos')" class="mt-2" />
        </div>

        <!-- DNI -->
        <div class="mt-4">
            <x-input-label for="dni" :value="__('DNI / Documento')" />
            <x-text-input wire:model="dni" id="dni" class="block mt-1 w-full" type="text" name="dni" required />
            <x-input-error :messages="$errors->get('dni')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-slate-600 hover:text-agri-green rounded-md focus:outline-none" href="{{ route('login') }}" wire:navigate>
                {{ __('¿Ya está registrado?') }}
            </a>

            <x-primary-button class="ms-4 bg-agri-green">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>
</div>

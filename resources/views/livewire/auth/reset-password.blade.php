<div class="space-y-6">
    <!-- VISTA (FRONTEND) - Restablecer Contraseña -->
    <div class="mb-4 text-sm text-slate-600">
        {{ __('Cree una nueva contraseña segura para su cuenta.') }}
    </div>

    <form wire:submit="resetPassword">
        <!-- Token -->
        <input type="hidden" wire:model="token">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required readonly />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Nueva Contraseña')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="bg-agri-green">
                {{ __('Restablecer Contraseña') }}
            </x-primary-button>
        </div>
    </form>
</div>

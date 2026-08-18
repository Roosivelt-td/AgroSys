<div class="space-y-6">
    <!-- VISTA (FRONTEND) - Confirmar Contraseña -->
    <div class="mb-4 text-sm text-slate-600">
        {{ __('Esta es una zona segura de la aplicación. Por favor, confirme su contraseña antes de continuar.') }}
    </div>

    <form wire:submit="confirmPassword">
        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button class="bg-agri-green">
                {{ __('Confirmar') }}
            </x-primary-button>
        </div>
    </form>
</div>

<section class="space-y-6">
    <header>
        <h2 class="text-xl font-black text-slate-800 dark:text-white italic tracking-tight">
            {{ __('Seguridad de Acceso') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500 italic">
            {{ __('Mantenga su cuenta blindada utilizando una contraseña robusta y única.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="space-y-6">
        <div class="space-y-2">
            <x-input-label for="current_password" :value="__('Contraseña Actual')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
            <x-text-input wire:model="current_password" id="current_password" type="password" class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" />
        </div>

        <div class="space-y-2">
            <x-input-label for="password" :value="__('Nueva Contraseña')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
            <x-text-input wire:model="password" id="password" type="password" class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="space-y-2">
            <x-input-label for="password_confirmation" :value="__('Confirmar Nueva Contraseña')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" type="password" class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="flex items-center justify-end gap-4">
            <x-action-message class="text-emerald-500 font-black italic text-sm" on="password-updated">
                <i class="fa-solid fa-shield-check mr-1"></i> {{ __('Contraseña blindada.') }}
            </x-action-message>

            <button type="submit" class="px-8 py-3 bg-slate-800 text-white rounded-xl font-black uppercase text-[10px] tracking-[0.2em] shadow-lg hover:bg-black transition-all italic">
                Cambiar Contraseña
            </button>
        </div>
    </form>
</section>

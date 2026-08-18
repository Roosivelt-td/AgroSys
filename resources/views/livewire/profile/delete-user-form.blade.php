<section class="space-y-6">
    <header>
        <h2 class="text-xl font-black text-rose-500 italic tracking-tight">
            {{ __('Zona de Peligro: Baja del Sistema') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500 italic">
            {{ __('La eliminación de la cuenta es irreversible. Todos sus terrenos, labores e historial serán eliminados de la red.') }}
        </p>
    </header>

    <div class="flex justify-start">
        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="px-8 py-3 bg-rose-50 dark:bg-rose-500/10 text-rose-600 border border-rose-200 dark:border-rose-500/20 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-rose-600 hover:text-white transition-all italic">
            Eliminar Mi Cuenta permanentemente
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="bg-white dark:bg-agri-d_bg rounded-2xl overflow-hidden shadow-2xl">
            <div class="p-10 text-center space-y-6">
                <div class="w-20 h-20 bg-rose-100 dark:bg-rose-500/20 rounded-full flex items-center justify-center mx-auto text-rose-600 text-3xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div class="space-y-2">
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tighter">
                        {{ __('¿Confirmar baja definitiva?') }}
                    </h2>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-widest leading-relaxed">
                        {{ __('Para su seguridad, ingrese su contraseña actual.') }}
                    </p>
                </div>

                <div class="max-w-xs mx-auto">
                    <x-text-input
                        wire:model="password"
                        id="password"
                        type="password"
                        class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-center font-bold tracking-widest"
                        placeholder="••••••••"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            </div>

            <div class="p-8 bg-slate-50 dark:bg-black/40 border-t border-slate-100 dark:border-white/5 flex justify-center gap-4">
                <button type="button" x-on:click="$dispatch('close')" class="px-8 py-3 bg-white dark:bg-white/5 text-slate-600 dark:text-slate-400 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-100 transition-all">
                    {{ __('Abortar') }}
                </button>
                <button type="submit" class="px-8 py-3 bg-rose-600 text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-rose-600/30 hover:scale-105 active:scale-95 transition-all">
                    {{ __('Eliminar Ahora') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>

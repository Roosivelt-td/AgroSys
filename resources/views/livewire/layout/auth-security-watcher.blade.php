<div wire:poll.3s="checkPendingRequests">
    @if($solicitudActual && $solicitudActual->estado === 'pendiente')
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-fade-in">
        <div class="bg-white dark:bg-slate-900 border-2 border-amber-500 rounded-3xl p-8 max-w-md w-full shadow-2xl space-y-6">
            <div class="flex items-center gap-4 text-amber-500">
                <div class="w-12 h-12 rounded-full bg-amber-500/10 flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-shield-cat"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white leading-tight italic">Solicitud de Acceso Detectada</h3>
                    <p class="text-[10px] uppercase font-bold text-amber-500 tracking-wider">Control de Sesión Única</p>
                </div>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-black/30 rounded-2xl border border-slate-100 dark:border-white/10 space-y-2 text-xs">
                <p class="text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
                    Un nuevo dispositivo está intentando iniciar sesión en tu cuenta de <strong class="text-slate-800 dark:text-white font-black">AgroSys</strong>.
                </p>

                <div class="pt-2 border-t border-slate-200 dark:border-white/10 space-y-1">
                    <p class="text-[10px] uppercase text-slate-400 font-bold">Dispositivo Solicitante:</p>
                    <p class="font-bold text-slate-800 dark:text-amber-400 italic">
                        {{ $solicitudActual->dispositivo_solicitante ?? 'Dispositivo Remoto' }}
                    </p>
                    <p class="text-[10px] text-slate-400">IP: {{ $solicitudActual->ip_solicitante }}</p>
                </div>
            </div>

            <form wire:submit.prevent="autorizarAcceso" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Ingresa tu contraseña para autorizar y cerrar esta sesión:
                    </label>
                    <input wire:model="confirmPassword" type="password" required placeholder="••••••••"
                           class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <x-input-error :messages="$errors->get('confirmPassword')" class="mt-1 text-xs" />
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-full font-bold text-xs uppercase tracking-wider shadow-md transition">
                        Autorizar y Cerrar
                    </button>
                    <button type="button"
                            wire:click="rechazarAcceso"
                            class="px-6 py-3 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-full font-bold text-xs uppercase tracking-wider transition">
                        Rechazar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

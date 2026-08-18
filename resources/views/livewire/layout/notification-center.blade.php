<div class="relative" wire:poll.30s>
    <button @click="notificationsOpen = !notificationsOpen; messagesOpen = false; profileOpen = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-agri-green relative transition-all duration-300">
        <i class="fa-regular fa-bell text-xl"></i>
        @if($unreadCount > 0)
            <span class="absolute top-2 right-2 w-4 h-4 bg-rose-500 text-white text-[9px] font-black rounded-full flex items-center justify-center border-2 border-white dark:border-agri-d_bg animate-bounce">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="notificationsOpen" @click.away="notificationsOpen = false" x-cloak x-transition class="absolute right-0 mt-4 w-80 bg-white dark:bg-slate-900 shadow-2xl rounded-2xl border border-slate-100 dark:border-white/10 z-50 overflow-hidden slide-down-panel">
        <div class="bg-agri-green p-4 text-white flex justify-between items-center">
            <span class="font-black italic text-sm">Notificaciones</span>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[9px] uppercase font-black hover:underline tracking-widest opacity-80">Marcar todo</button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto custom-scrollbar">
            @forelse($notificaciones as $notif)
                <div wire:click="goToTramite({{ $notif->id }})"
                     class="p-4 border-b border-slate-50 dark:border-white/5 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer {{ !$notif->leido ? 'bg-blue-50/30 dark:bg-agri-green/5' : '' }}">
                    <div class="flex items-start space-x-3">
                        <div class="mt-1">
                            @if($notif->tipo === 'exito')
                                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                            @elseif($notif->tipo === 'error')
                                <i class="fa-solid fa-circle-xmark text-rose-500 text-sm"></i>
                            @else
                                <i class="fa-solid fa-circle-info text-blue-500 text-sm"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-[11px] font-black text-slate-800 dark:text-white italic leading-tight">{{ $notif->titulo }}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">{{ $notif->mensaje }}</p>
                            <p class="text-[8px] text-slate-400 mt-2 uppercase font-black tracking-widest">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                        @if(!$notif->leido)
                            <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-1.5 shadow-sm shadow-blue-500/50"></div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400">
                    <i class="fa-solid fa-bell-slash text-2xl mb-3 opacity-20"></i>
                    <p class="text-[10px] font-black uppercase tracking-widest italic">Sin notificaciones nuevas</p>
                </div>
            @endforelse
        </div>

        <div class="p-3 bg-slate-50 dark:bg-white/5 text-center">
            <a href="{{ route('profile.tramites') }}" class="text-[9px] font-black text-agri-green uppercase tracking-widest hover:underline" wire:navigate>Ver mi historial de trámites</a>
        </div>
    </div>
</div>

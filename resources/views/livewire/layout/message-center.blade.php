<div class="relative" wire:poll.10s>
    <button @click="messagesOpen = !messagesOpen; notificationsOpen = false; profileOpen = false"
            class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-agri-green relative transition-all duration-300">
        <i class="fa-regular fa-comment-dots text-xl"></i>
        @if($unreadCount > 0)
            <span class="absolute top-2 right-2 w-4 h-4 bg-emerald-500 text-white text-[9px] font-black rounded-full flex items-center justify-center border-2 border-white dark:border-agri-d_bg animate-bounce">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="messagesOpen" @click.away="messagesOpen = false" x-cloak x-transition class="absolute right-0 mt-4 w-80 bg-white dark:bg-slate-900 shadow-2xl rounded-2xl border border-slate-100 dark:border-white/10 z-50 overflow-hidden slide-down-panel">
        <div class="bg-agri-d_sidebar p-4 text-white flex justify-between items-center">
            <span class="font-black italic text-sm">Mensajes Recientes</span>
            <a href="{{ route('chat.index') }}" class="text-[9px] uppercase font-black hover:underline tracking-widest opacity-80" wire:navigate>Abrir Chat</a>
        </div>

        <div class="max-h-96 overflow-y-auto custom-scrollbar">
            @forelse($recientes as $conv)
                @php
                    $otro = $conv->participantes->where('usuario_id', '!=', auth()->id())->first()?->usuario;
                    $ultimo = $conv->mensajes->first();
                    $noLeidos = $conv->mensajes->where('remitente_usuario_id', '!=', auth()->id())->where('leido', 0)->count();
                @endphp
                <div wire:click="goToChat({{ $conv->id }})"
                     class="p-4 border-b border-slate-50 dark:border-white/5 hover:bg-slate-50 dark:hover:bg-agri-green/5 transition-colors cursor-pointer {{ $noLeidos > 0 ? 'bg-emerald-50/30 dark:bg-emerald-500/5' : '' }}">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-100 dark:border-white/10">
                                <img src="{{ $otro->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($otro->nombres ?? 'U').'&background=00ba2e&color=fff' }}" class="w-full h-full object-cover">
                            </div>
                            @if($noLeidos > 0)
                                <div class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white dark:border-slate-900"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <p class="text-[11px] font-black text-slate-800 dark:text-white truncate italic">{{ $otro->nombres ?? 'Compañero' }}</p>
                                <span class="text-[7px] text-slate-400 font-bold uppercase">{{ $ultimo ? $ultimo->created_at->diffForHumans(null, true) : '' }}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5 italic">
                                {{ $ultimo ? $ultimo->mensaje : 'Inicia una conversación' }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400">
                    <i class="fa-solid fa-comments-slash text-2xl mb-3 opacity-20"></i>
                    <p class="text-[10px] font-black uppercase tracking-widest italic">Sin chats activos</p>
                </div>
            @endforelse
        </div>

        <div class="p-3 bg-slate-50 dark:bg-white/5 text-center">
            <a href="{{ route('chat.index') }}" class="text-[9px] font-black text-agri-green uppercase tracking-widest hover:underline" wire:navigate>Nueva Coordinación Técnica</a>
        </div>
    </div>
</div>

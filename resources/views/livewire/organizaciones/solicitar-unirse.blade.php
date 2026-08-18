<div class="p-8">
    <div class="mb-8 flex justify-between items-center border-b border-slate-50 dark:border-white/5 pb-6">
        <div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tight">Unirse a un Equipo</h3>
            <p class="text-[10px] text-agri-green font-black uppercase tracking-widest mt-1">Búsqueda de Organizaciones y Cooperativas</p>
        </div>
        <button @click="$dispatch('close')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-white/5 text-slate-400 hover:text-rose-500 transition-all">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    @if (session('error'))
        <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 text-sm font-bold rounded-r-xl italic">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-6">
        <div class="relative group">
            <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-slate-400 group-focus-within:text-agri-green transition-colors">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre o RUC de la organización..."
                   class="w-full pl-14 pr-4 py-5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-[1.5rem] text-sm focus:ring-4 focus:ring-agri-green/10 focus:border-agri-green outline-none transition-all shadow-inner">
        </div>

        @if($confirmingJoin)
            <div class="bg-agri-mint/20 dark:bg-agri-green/5 p-10 rounded-[2.5rem] border-2 border-dashed border-agri-green/30 text-center animate-in fade-in zoom-in duration-300">
                <div class="w-20 h-20 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-agri-green shadow-xl">
                    <i class="fa-solid fa-paper-plane text-agri-green text-2xl"></i>
                </div>
                <h4 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tight">¿Enviar solicitud a?</h4>
                <p class="text-lg font-black text-agri-green mt-2 italic">"{{ $confirmingJoin->nombre }}"</p>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-4">El administrador recibirá su perfil para revisión</p>

                <div class="flex justify-center space-x-4 mt-10">
                    <button wire:click="$set('confirmingJoin', null)" class="px-8 py-3 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em]">
                        Cancelar
                    </button>
                    <button wire:click="sendRequest" class="px-10 py-3 bg-agri-green text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-2xl shadow-agri-green/40 hover:scale-105 transition-all">
                        Confirmar Envío
                    </button>
                </div>
            </div>
        @else
            <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar pr-2">
                @forelse($organizaciones as $org)
                    <div wire:click="selectOrg({{ $org->id }})"
                         class="flex items-center justify-between p-5 bg-white dark:bg-white/5 border border-slate-100 dark:border-white/5 rounded-3xl hover:border-agri-green hover:shadow-xl hover:shadow-agri-green/5 transition-all cursor-pointer group active:scale-95">
                        <div class="flex items-center space-x-5">
                            <div class="w-14 h-14 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-agri-green/10 group-hover:text-agri-green transition-all shadow-sm">
                                <i class="fa-solid fa-building-circle-arrow-right text-xl"></i>
                            </div>
                            <div>
                                <p class="text-base font-black text-slate-700 dark:text-slate-200 italic leading-tight group-hover:text-agri-green transition-colors">{{ $org->nombre }}</p>
                                <p class="text-[11px] text-slate-400 font-bold mt-1 uppercase tracking-tighter">RUC: {{ $org->ruc }}</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-slate-50 dark:bg-white/5 group-hover:bg-agri-green group-hover:text-white transition-all">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </div>
                    </div>
                @empty
                    @if(strlen($search) > 2)
                        <div class="text-center py-16 bg-slate-50 dark:bg-white/5 rounded-[2.5rem] border border-dashed border-slate-200 dark:border-white/10">
                            <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                                <i class="fa-solid fa-magnifying-glass-minus text-2xl"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-500 italic">No se encontraron resultados para "{{ $search }}"</p>
                            <p class="text-[10px] text-slate-400 uppercase font-black mt-2 tracking-widest">Intente con otro nombre o número de RUC</p>
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-agri-mint/10 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-agri-green/30 animate-bounce">
                                <i class="fa-solid fa-keyboard text-3xl"></i>
                            </div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Esperando términos de búsqueda...</p>
                        </div>
                    @endif
                @endforelse
            </div>
        @endif
    </div>
</div>

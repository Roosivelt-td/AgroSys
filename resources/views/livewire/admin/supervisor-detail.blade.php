<div class="space-y-6 p-4 md:p-8 transition-colors duration-500">
    <!-- Header: Detalles del Supervisor -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-100 dark:border-white/5 pb-2">
        <div class="flex items-center space-x-6">
            <a href="{{ route('admin.organizacion.asignar-supervisores', ['id' => $orgId]) }}" wire:navigate class="w-12 h-12 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center shadow-lg hover:bg-agri-green hover:text-white transition-all group">
                <i class="fa-solid fa-chevron-left text-xl group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div class="flex items-center space-x-5">
                <div class="w-16 h-16 rounded-full border-4 border-amber-500 p-0.5 shadow-xl relative">
                    <img src="{{ $supervisor->usuario->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($supervisor->usuario->nombres).'&background=ff8f00&color=fff' }}" class="w-full h-full rounded-full object-cover">
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-amber-500 rounded-full border-2 border-white dark:border-agri-d_bg flex items-center justify-center text-white">
                        <i class="fa-solid fa-star text-[7px]"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter">
                        {{ $supervisor->usuario_id === auth()->id() ? 'Mi Equipo de Supervisión' : 'Expediente de Supervisión: ' . $supervisor->usuario->nombres }}
                    </h2>
                    <p class="text-[10px] text-amber-600 font-black uppercase tracking-[0.3em] mt-1 italic">Control de Supervisión Asignada</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Buscador de Agricultores Libres (Solo Admin) -->
    @if(auth()->user()->membresias()->where('organizacion_id', $orgId)->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->exists())
    <div class="bg-white dark:bg-agri-d_bg p-6 rounded-2xl border border-slate-100 dark:border-white/5 shadow-2xl relative transition-all">
        <div class="flex items-center space-x-4 mb-4">
            <div class="w-10 h-10 bg-agri-green/10 rounded-xl flex items-center justify-center text-agri-green shadow-inner">
                <i class="fa-solid fa-user-plus text-lg"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Asignar Nuevo Integrante</p>
                <p class="text-xs font-bold text-slate-500 italic">Agricultores sin supervisor en esta organización.</p>
            </div>
        </div>

        <div class="relative group">
            <input wire:model.live="searchNewMember" type="text" placeholder="Escriba nombre o DNI para buscar..."
                   class="w-full pl-5 pr-4 py-4 bg-agri-l_bg dark:bg-white/5 border-none rounded-2xl text-sm focus:ring-4 focus:ring-agri-green/10 outline-none transition-all placeholder:italic">

            @if(!empty($agricultoresLibres))
            <div class="absolute left-0 right-0 mt-3 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 dark:border-white/10 z-50 overflow-hidden animate-in fade-in slide-in-from-top-4 duration-300">
                <div class="p-3 space-y-1">
                    @foreach($agricultoresLibres as $libre)
                    <div wire:click="toggleAsignacion({{ $libre->id }})"
                         class="flex items-center justify-between p-4 hover:bg-agri-l_card/30 dark:hover:bg-agri-green/10 rounded-2xl cursor-pointer group transition-all">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-full border-2 border-agri-green/20 overflow-hidden shadow-sm">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($libre->nombres) }}&background=00ba2e&color=fff" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-800 dark:text-white leading-tight italic">{{ $libre->nombres }} {{ $libre->apellidos }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">DNI: {{ $libre->dni }}</p>
                            </div>
                        </div>
                        <button class="w-10 h-10 rounded-full bg-agri-green text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-xl shadow-agri-green/40 hover:scale-110">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    @if (session('status'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-r-xl italic shadow-sm animate-in fade-in duration-500">
            {{ session('status') }}
        </div>
    @endif

    <!-- GRID DE ASIGNADOS - Estilo Compacto 6 por fila -->
    <div class="bg-[#ced4da] dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden border border-slate-300 dark:border-white/10 transition-colors duration-500">
        <div class="bg-[#003a38] px-10 py-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-xl font-bold text-white tracking-tight italic">Equipo bajo su Vigilancia</h3>

            <div class="relative w-full md:w-72 group">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-white/40 group-focus-within:text-agri-green transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input wire:model.live="search" type="text" placeholder="Buscar entre asignados..."
                       class="w-full pl-10 pr-4 py-2 bg-white/10 border border-white/20 rounded-xl text-xs text-white placeholder-white/40 focus:ring-4 focus:ring-agri-green/20 outline-none transition-all backdrop-blur-sm italic">
            </div>
        </div>

        <div class="bg-[#dee2e6] dark:bg-agri-d_bg/30 p-6 md:p-10">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($asignaciones as $asig)
                <div class="bg-white dark:bg-agri-d_bg rounded-xl p-5 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all border border-slate-200 dark:border-white/5 flex flex-col items-center text-center group relative">

                    <!-- Avatar Circular -->
                    <div class="relative mb-4">
                        <div class="w-16 h-16 rounded-full border-2 border-agri-green/20 p-0.5 shadow-inner transition-transform group-hover:scale-110">
                            <img src="{{ $asig->agricultor->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($asig->agricultor->nombres).'&background=00ba2e&color=fff' }}"
                                 class="w-full h-full rounded-full object-cover">
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-2 border-white dark:border-agri-d_bg flex items-center justify-center text-white shadow-lg">
                            <i class="fa-solid fa-user-check text-[7px]"></i>
                        </div>
                    </div>

                    <div class="space-y-1 w-full overflow-hidden">
                        <p class="text-xs font-black text-slate-800 dark:text-white leading-tight truncate px-1 italic">{{ $asig->agricultor->nombres }}</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter truncate">{{ $asig->agricultor->dni }}</p>
                    </div>

                    <div class="mt-5 w-full">
                        @if(auth()->user()->membresias()->where('organizacion_id', $orgId)->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->exists())
                            <button wire:click="quitarAsignacion({{ $asig->id }})" wire:confirm="¿Quitar a este agricultor de la supervisión?"
                                    class="w-full py-2.5 bg-rose-50 dark:bg-rose-900/10 text-rose-600 rounded-xl font-black text-[8px] uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                Quitar Vigilancia
                            </button>
                        @else
                            <div class="py-2.5 text-[8px] font-black text-slate-400 uppercase tracking-widest italic">Miembro del Equipo</div>
                        @endif
                    </div>

                    <!-- Icono flotante informativo -->
                    <div class="absolute top-3 right-3 opacity-20 group-hover:opacity-100 transition-opacity">
                        <i class="fa-solid fa-shield-halved text-agri-green text-xs"></i>
                    </div>
                </div>
                @endforeach
            </div>

            @if($asignaciones->isEmpty())
                <div class="bg-white dark:bg-slate-900/50 p-16 rounded-xl text-center italic border border-dashed border-slate-300 dark:border-white/10">
                    <div class="w-16 h-16 bg-agri-l_card dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <i class="fa-solid fa-user-slash text-2xl"></i>
                    </div>
                    <p class="text-slate-500">No hay agricultores asignados para este supervisor en esta organización.</p>
                </div>
            @endif

            <div class="mt-10 px-4">
                {{ $asignaciones->links() }}
            </div>
        </div>
    </div>
</div>

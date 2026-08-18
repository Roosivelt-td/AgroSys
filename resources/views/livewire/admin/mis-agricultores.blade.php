<div class="space-y-6 p-4 md:p-1 transition-colors duration-500">

    @if(!$selectedOrgId)
        <!-- Selector de Organización (Solo si hay > 1) -->
        <div class="max-w-4xl mx-auto space-y-10 py-10">
            <div class="text-center">
                <h2 class="text-4xl font-black text-slate-800 dark:text-white italic tracking-tighter">Panel de Supervisión</h2>
                <p class="text-[10px] text-agri-green font-black uppercase tracking-[0.3em] mt-2">Seleccione una organización para ver sus agricultores</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($misOrgsSup as $membresia)
                    <div wire:click="selectOrg({{ $membresia->organizacion_id }})"
                         class="bg-white dark:bg-agri-d_bg p-10 rounded-2xl border border-slate-100 dark:border-white/5 shadow-2xl hover:border-agri-green hover:-translate-y-2 transition-all cursor-pointer group text-center">
                        <div class="w-20 h-20 bg-agri-l_card dark:bg-white/5 rounded-xl flex items-center justify-center mx-auto mb-8 text-agri-green shadow-inner group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-building-wheat text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white italic leading-tight">{{ $membresia->organizacion->nombre }}</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-3 tracking-widest">RUC: {{ $membresia->organizacion->ruc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Panel de Supervisión Contextual -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-100 dark:border-white/5 pb-6">
            <div class="flex items-center space-x-5">
                @if($misOrgsSup->count() > 1)
                    <button wire:click="$set('selectedOrgId', null)" class="w-12 h-12 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center shadow-lg hover:bg-amber-500 hover:text-white transition-all">
                        <i class="fa-solid fa-chevron-left text-xl"></i>
                    </button>
                @else
                    <div class="w-14 h-14 bg-amber-500 rounded-xl flex items-center justify-center shadow-xl shadow-amber-500/20 border border-white/10">
                        <i class="fa-solid fa-people-roof text-white text-2xl"></i>
                    </div>
                @endif
                <div>
                    <h2 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter">Mis Agricultores</h2>
                    <p class="text-[10px] text-amber-600 font-black uppercase tracking-[0.3em] mt-1 italic">{{ $currentOrg->nombre }}</p>
                </div>
            </div>

            <div class="relative w-full md:w-80 group">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-agri-green transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input wire:model.live="search" type="text" placeholder="Buscar agricultor por nombre o DNI..."
                       class="w-full pl-10 pr-4 py-3.5 bg-white dark:bg-agri-d_bg border border-slate-200 dark:border-white/10 rounded-xl text-xs focus:ring-4 focus:ring-agri-green/10 outline-none backdrop-blur-sm italic shadow-sm transition-all">
            </div>
        </div>

        <!-- GRID DE AGRICULTORES - Estilo Compacto 6 por fila -->
        <div class="bg-[#ced4da] dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden border border-slate-300 dark:border-white/10 transition-colors duration-500">
            <div class="bg-[#003a38] px-10 py-5">
                <h3 class="text-xl font-bold text-white tracking-tight italic">Personal bajo Vigilancia</h3>
            </div>

            <div class="bg-[#dee2e6] dark:bg-agri-d_bg/30 p-6 md:p-10">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    @forelse($agricultores as $asig)
                        <a href="{{ route('supervisor.agricultor.detalle', ['id' => $asig->agricultor_usuario_id]) }}"
                           class="bg-white dark:bg-agri-d_bg rounded-2xl p-5 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all border border-slate-200 dark:border-white/5 flex flex-col items-center text-center group relative cursor-pointer">

                            <!-- Avatar Circular -->
                            <div class="relative mb-4">
                                <div class="w-16 h-16 rounded-full border-2 border-agri-green/20 p-0.5 shadow-inner transition-transform group-hover:scale-110">
                                    <img src="{{ $asig->agricultor->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($asig->agricultor->nombres).'&background=00ba2e&color=fff' }}"
                                         class="w-full h-full rounded-full object-cover">
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-2 border-white dark:border-agri-d_bg flex items-center justify-center text-white shadow-lg">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                </div>
                            </div>

                            <div class="space-y-1 w-full overflow-hidden">
                                <p class=" font-black text-slate-800 dark:text-white leading-tight truncate px-1 italic">{{ $asig->agricultor->nombres }}</p>
                                <p class="text-[12px] text-slate-400 font-bold uppercase tracking-tighter truncate">DNI: {{ $asig->agricultor->dni }}</p>
                            </div>

                            <div class="border-t border-slate-50 dark:border-white/5 w-full">
                                <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">
                                    En Vigilancia
                                </span>
                            </div>

                            <!-- Icono de acceso flotante -->
                            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fa-solid fa-circle-chevron-right text-agri-green text-lg"></i>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full bg-white dark:bg-slate-900/50 p-20 rounded-xl text-center italic border border-dashed border-slate-300 dark:border-white/10">
                            <div class="w-16 h-16 bg-agri-l_card dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-400">
                                <i class="fa-solid fa-user-slash text-2xl"></i>
                            </div>
                            <p class="text-slate-500">No hay agricultores asignados para esta organización.</p>
                        </div>
                    @endforelse
                </div>

                @if($agricultores->hasPages())
                    <div class="mt-10 px-4">
                        {{ $agricultores->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

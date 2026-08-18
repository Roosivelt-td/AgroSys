<div class="space-y-4 p-4 md:p-1">
    <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 pb-4">
        <div>
            <h2 class="text-xl font-black text-slate-800 dark:text-white italic tracking-tighter">Mis Organizaciones</h2>
            <p class="text-[9px] text-agri-green font-black uppercase tracking-widest mt-0.5">Ecosistema de Producción Agrícola</p>
        </div>
        <button @click="$dispatch('open-modal', 'create-organization')" class="px-5 py-2 bg-agri-green text-white rounded-xl font-black text-[9px] uppercase tracking-widest shadow-lg shadow-agri-green/20 hover:scale-105 transition-all">
            Nueva Organización
        </button>
    </div>

    @if($membresias->isEmpty())
        <div class="bg-white dark:bg-agri-d_bg p-10 rounded-3xl text-center border border-slate-100 dark:border-white/5 shadow-xl">
            <div class="w-16 h-16 bg-agri-l_card dark:bg-agri-green/10 rounded-full flex items-center justify-center mx-auto mb-3 text-agri-green">
                <i class="fa-solid fa-wheat-awn text-xl"></i>
            </div>
            <h3 class="text-base font-black text-slate-800 dark:text-white italic">No tienes membresías activas</h3>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($membresias as $membresia)
                @php
                    $esAdmin = $membresia->roles->contains(fn($r) => $r->rolDetalle->nombre === 'Administrador');
                @endphp
                <div class="bg-white dark:bg-agri-d_bg rounded-2xl overflow-hidden shadow-sm border border-slate-100 dark:border-white/5 group hover:border-agri-green transition-all duration-300">
                    <div class="p-5 space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-agri-l_card dark:bg-white/10 rounded-lg flex items-center justify-center text-agri-green shadow-inner">
                                <i class="fa-solid fa-building-wheat text-sm"></i>
                            </div>
                            <h3 class="text-sm font-black text-slate-800 dark:text-white italic tracking-tight truncate">{{ $membresia->organizacion->nombre }}</h3>
                        </div>

                        <div class="flex flex-wrap gap-1 min-h-[18px]">
                            @foreach($membresia->roles as $mRol)
                                <span class="px-2 py-0.5 bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 rounded font-black text-[7px] uppercase tracking-tighter
                                    {{ $mRol->rolDetalle->nombre === 'Administrador' ? 'text-blue-500' : ($mRol->rolDetalle->nombre === 'Supervisor' ? 'text-amber-500' : 'text-agri-green') }}">
                                    {{ $mRol->rolDetalle->nombre }}
                                </span>
                            @endforeach
                        </div>

                        <div class="pt-3 border-t border-slate-50 dark:border-white/5 flex items-center justify-between">
                            <span class="text-[8px] font-black text-slate-400 uppercase font-mono italic">#{{ $membresia->organizacion->ruc }}</span>

                            <a href="{{ route('admin.organizacion.miembros', ['id' => $membresia->organizacion_id]) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-agri-l_card/30 dark:bg-white/5 text-agri-green rounded-lg font-black text-[8px] uppercase tracking-widest hover:bg-agri-green hover:text-white transition-all group/btn shadow-sm">
                                {{ $esAdmin ? 'Gestionar' : 'Entrar' }}
                                <i class="fa-solid fa-chevron-right ms-1.5 group-hover/btn:translate-x-0.5 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="space-y-8">
    <!-- VISTA (FRONTEND) - Dashboard Principal -->

    @if(!$hasOrg)
        @if($isWaiting)
            <!-- Estado: Esperando Aprobación -->
            <div class="bg-white dark:bg-slate-900 p-12 rounded-2xl shadow-2xl text-center max-w-4xl mx-auto border-t-4 border-amber-400">
                <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-8 text-amber-500 animate-pulse">
                    <i class="fa-solid fa-clock-rotate-left text-3xl"></i>
                </div>
                <h2 class="text-3xl font-black text-slate-800 dark:text-white mb-4 italic tracking-tight">Solicitud en Proceso</h2>
                <p class="text-slate-500 dark:text-slate-400 mb-6 text-lg">
                    Estamos revisando los datos de su organización <span class="font-black text-slate-800 dark:text-white">"{{ $solicitud->datos_extra['nombre'] ?? 'N/A' }}"</span>.
                </p>
                <div class="bg-slate-50 dark:bg-white/5 p-6 rounded-2xl inline-block text-left mb-8">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Estado del Trámite</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Esperando validación del Super Admin</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400">Recibirá una notificación en su campana cuando el proceso termine.</p>

                <!-- Actividad Reciente mientras espera -->
                <div class="mt-12 text-left max-w-md mx-auto">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] mb-4">Mi Actividad Reciente</p>
                    <div class="space-y-3">
                        @foreach($historial as $item)
                            <div class="flex items-center space-x-3 p-3 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                                <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-800 flex items-center justify-center text-agri-green shadow-sm">
                                    <i class="fa-solid fa-fingerprint text-xs"></i>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 truncate">{{ $item->descripcion }}</p>
                                    <p class="text-[9px] text-slate-400 uppercase font-black">{{ $item->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <!-- Estado: Bienvenido (Sin Org) -->
            <div class="bg-white dark:bg-slate-900 p-16 rounded-2xl shadow-xl text-center max-w-4xl mx-auto border border-slate-100 dark:border-white/5 transition-colors">
                <div class="w-24 h-24 bg-agri-mint rounded-xl flex items-center justify-center mx-auto mb-10 text-agri-green shadow-inner">
                    <i class="fa-solid fa-tractor text-4xl"></i>
                </div>
                <h2 class="text-4xl font-black text-slate-900 dark:text-white mb-6 italic tracking-tight">Bienvenido a su Finca Digital</h2>
                <p class="text-slate-500 dark:text-slate-600 mb-12 text-xl max-w-md mx-auto leading-relaxed">Registre su organización agrícola para comenzar con la monitorización IA.</p>

                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button
                        x-on:click.prevent="$dispatch('open-modal', 'create-organization')"
                        class="inline-block px-10 py-5 bg-agri-green text-white rounded-2xl font-black shadow-2xl shadow-agri-green/30 hover:scale-105 active:scale-95 transition-all"
                    >
                        Solicitar Organización
                    </button>

                    <button
                        x-on:click.prevent="$dispatch('open-modal', 'join-organization')"
                        class="inline-block px-10 py-5 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 rounded-2xl font-black hover:bg-slate-50 transition-all">
                        Unirse a Equipo
                    </button>
                </div>
            </div>
        @endif
    @else
        <!-- Dashboard con Datos Activos -->
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-slate-800 italic tracking-tight">Dashboard Principal</h2>
                <p class="text-slate-400 font-bold text-xs uppercase mt-1 tracking-widest">{{ $organizacion->nombre }}</p>
            </div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <i class="fa-solid fa-house-chimney text-agri-green mr-2"></i> Dashboard / Default
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-agri-d_card p-8 rounded-2xl border border-slate-100 dark:border-white/10 shadow-sm transition-colors group hover:border-agri-green">
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-4 group-hover:text-agri-green transition-colors">Total Terrenos</p>
                <p class="text-4xl font-black text-slate-900 dark:text-slate-800 italic tracking-tighter">{{ $stats['terrenos'] }}</p>
            </div>
            <div class="bg-white dark:bg-agri-d_card p-8 rounded-2xl border border-slate-100 dark:border-white/10 shadow-sm transition-colors group hover:border-agri-green">
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-4 group-hover:text-agri-green transition-colors">En curso</p>
                <p class="text-4xl font-black text-slate-900 dark:text-slate-800 italic tracking-tighter">{{ $stats['cultivos'] }}</p>
            </div>
            <div class="bg-white dark:bg-agri-d_card p-8 rounded-2xl border border-slate-100 dark:border-white/10 shadow-sm transition-colors group hover:border-agri-green">
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-4 group-hover:text-agri-green transition-colors">Tareas Hoy</p>
                <p class="text-4xl font-black text-slate-900 dark:text-slate-800 italic tracking-tighter">{{ $stats['labores'] }}</p>
            </div>
        </div>

        <!-- Historial Estilizado ADMIRO -->
        <div class="bg-white dark:bg-agri-d_card rounded-2xl border border-slate-100 dark:border-white/10 shadow-sm overflow-hidden mt-10 transition-colors">
            <div class="bg-agri-green p-6 text-white flex justify-between items-center">
                <h3 class="text-lg font-black italic tracking-tight">Mi Actividad Reciente</h3>
            </div>
            <div class="p-8">
                @if(count($historial) > 0)
                    <div class="space-y-4">
                        @foreach($historial as $item)
                            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/5 hover:border-agri-green transition-all group">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 flex items-center justify-center text-agri-green shadow-sm group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $item->descripcion }}</p>
                                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">{{ $item->created_at->format('d M, Y - H:i') }}</p>
                                    </div>
                                </div>
                                <span class="text-[9px] px-2 py-1 bg-agri-green/10 text-agri-green rounded-lg font-black uppercase">{{ $item->accion }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">No hay registros de actividad recientes.</p>
                @endif
            </div>
        </div>
    @endif
</div>

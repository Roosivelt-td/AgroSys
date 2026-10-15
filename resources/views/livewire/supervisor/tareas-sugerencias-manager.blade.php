<div class="space-y-8 p-4 md:p-1 transition-colors duration-500">
    <!-- Header: Tareas y Sugerencias -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/10 pb-6">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">
                📋 Tareas y Sugerencias
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-bold italic">
                {{ $isSupervisorOrAdmin ? 'Control, monitoreo y asignación de instrucciones agronómicas a los agricultores.' : 'Instrucciones y sugerencias técnicas enviadas por tu Supervisor.' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if($isSupervisorOrAdmin)
                <button wire:click="openCreateSugerenciaModal"
                        class="px-6 py-3.5 bg-agri-green hover:bg-emerald-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all flex items-center gap-2 italic">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Nueva Instrucción</span>
                </button>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-r-xl italic shadow-sm animate-in fade-in duration-500 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-sm"></i>
                <span>{{ session('status') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- VISTA DE SUPERVISOR / ADMINISTRADOR: TABLERO DE CONTROL GLOBAL DE SUGERENCIAS -->
    @if($isSupervisorOrAdmin)
    <div class="space-y-6">
        <!-- Métricas de Cumplimiento -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Total Tareas Enviadas</p>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-paper-plane text-lg"></i></div>
                    <div>
                        <p class="text-xl font-black text-slate-800 dark:text-white tabular-nums leading-none">{{ $totalEnviadas }}</p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">A todos los agricultores</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Cumplidas / Completadas</p>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-circle-check text-lg"></i></div>
                    <div>
                        <p class="text-xl font-black text-slate-800 dark:text-white tabular-nums leading-none">{{ $totalCumplidas }}</p>
                        <p class="text-[10px] font-bold text-emerald-600 mt-1 uppercase">Verificadas</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Pendientes de Acción</p>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-hourglass-half text-lg"></i></div>
                    <div>
                        <p class="text-xl font-black text-slate-800 dark:text-white tabular-nums leading-none">{{ $totalPendientes }}</p>
                        <p class="text-[10px] font-bold text-amber-600 mt-1 uppercase">En espera</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Tasa de Cumplimiento</p>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center"><i class="fa-solid fa-chart-line text-lg"></i></div>
                    <div>
                        <p class="text-xl font-black text-slate-800 dark:text-white tabular-nums leading-none">
                            {{ $totalEnviadas > 0 ? round(($totalCumplidas / $totalEnviadas) * 100) : 0 }}%
                        </p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Efectividad global</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla / Lista de Control de Tareas de la Empresa -->
        <div class="bg-white dark:bg-agri-d_bg rounded-3xl p-6 md:p-8 border border-slate-100 dark:border-white/5 shadow-xl space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between border-b border-slate-100 dark:border-white/5 pb-6 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-agri-green/10 text-agri-green flex items-center justify-center text-xl shrink-0"><i class="fa-solid fa-list-check"></i></div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white italic uppercase tracking-tight leading-none">Seguimiento a Todos los Agricultores</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Control de sugerencias y fechas de cumplimiento</p>
                    </div>
                </div>

                <!-- Buscador y Filtros -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Buscador -->
                    <div class="relative flex-1 min-w-[200px]">
                        <input type="text" wire:model.live.debounce.300ms="searchQuery" placeholder="BUSCAR POR TÍTULO O AGRICULTOR..."
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl p-2.5 pl-9 text-xs font-bold uppercase text-slate-800 dark:text-white focus:ring-agri-green">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    </div>

                    <!-- Selector por Agricultor -->
                    @if($misAgricultores->isNotEmpty())
                        <div class="relative">
                            <select wire:model.live="filtroAgricultorId" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white text-xs font-bold rounded-xl p-2.5 uppercase focus:ring-agri-green pr-8">
                                <option value="">Todos los Agricultores</option>
                                @foreach($misAgricultores as $ag)
                                    <option value="{{ $ag->id }}">{{ $ag->nombres }} {{ $ag->apellidos }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Pestañas de Estado -->
                    <div class="flex items-center gap-1 bg-slate-100 dark:bg-white/5 p-1 rounded-2xl">
                        <button wire:click="$set('filtroEstadoSupervision', 'todos')"
                                class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all italic
                                {{ $filtroEstadoSupervision === 'todos' ? 'bg-agri-green text-white shadow-md' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                            Todas ({{ $totalEnviadas }})
                        </button>
                        <button wire:click="$set('filtroEstadoSupervision', 'pendientes')"
                                class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all italic
                                {{ $filtroEstadoSupervision === 'pendientes' ? 'bg-agri-green text-white shadow-md' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                            ⏳ Pendientes ({{ $totalPendientes }})
                        </button>
                        <button wire:click="$set('filtroEstadoSupervision', 'cumplidos')"
                                class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all italic
                                {{ $filtroEstadoSupervision === 'cumplidos' ? 'bg-agri-green text-white shadow-md' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                            ✅ Cumplidos ({{ $totalCumplidas }})
                        </button>
                    </div>
                </div>
            </div>

            <!-- Grilla de Tarjetas de Tareas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($sugerenciasGlobales as $sug)
                    @php $esCumplido = ($sug->estado == 1); @endphp
                    <div class="bg-slate-50 dark:bg-white/5 p-6 rounded-3xl border border-slate-200/60 dark:border-white/5 space-y-4 relative group hover:border-agri-green/30 transition-all shadow-sm flex flex-col justify-between">
                        <div>
                            <!-- Agricultor Receptor -->
                            <div class="flex items-center justify-between border-b border-slate-200/50 dark:border-white/5 pb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full border-2 border-agri-green/30 overflow-hidden shadow-sm bg-slate-100 dark:bg-slate-800 shrink-0">
                                        <img src="{{ $sug->agricultor->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($sug->agricultor->nombres ?? 'Agricultor').'&background=00ba2e&color=fff' }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-xs font-black text-slate-800 dark:text-white uppercase leading-tight italic truncate">
                                            {{ $sug->agricultor->nombres ?? 'Agricultor' }} {{ $sug->agricultor->apellidos ?? '' }}
                                        </p>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">DNI: {{ $sug->agricultor->dni ?? 'N/D' }}</p>
                                    </div>
                                </div>

                                <a href="{{ route('chat.index', ['user' => $sug->agricultor_usuario_id]) }}" wire:navigate
                                   class="w-8 h-8 rounded-xl bg-agri-green/10 text-agri-green flex items-center justify-center hover:bg-agri-green hover:text-white transition-all shrink-0"
                                   title="Abrir Chat con Agricultor">
                                    <i class="fa-solid fa-comments text-xs"></i>
                                </a>
                            </div>

                            <!-- Título e Información de la Tarea -->
                            <div class="space-y-1.5 pt-3">
                                <div class="flex items-center justify-between">
                                    <span class="px-2.5 py-0.5 text-[8px] font-black uppercase rounded-lg italic tracking-widest
                                        {{ $esCumplido ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-600 border border-amber-500/20' }}">
                                        {{ $esCumplido ? '✅ Cumplido' : '⏳ Pendiente' }}
                                    </span>
                                    <span class="text-[9px] font-bold text-slate-400">📅 {{ \Carbon\Carbon::parse($sug->fecha_sugerida)->format('d/m/Y') }}</span>
                                </div>

                                <h4 class="text-base font-black text-slate-800 dark:text-white italic uppercase leading-tight pt-1">
                                    {{ $sug->titulo }}
                                </h4>

                                @if($sug->descripcion)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium line-clamp-2 leading-relaxed">
                                        {{ $sug->descripcion }}
                                    </p>
                                @endif

                                @if($sug->cultivo)
                                    <p class="text-[10px] font-black text-agri-green uppercase italic pt-1">
                                        🌾 Lote: {{ $sug->cultivo->nombre_lote }} ({{ $sug->cultivo->detalleCatalogo->nombre ?? 'Cultivo' }})
                                    </p>
                                @endif
                            </div>

                            <!-- Datos de Cumplimiento -->
                            <div class="pt-3">
                                @if($esCumplido)
                                    <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl space-y-1">
                                        <p class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest italic">
                                            ✅ Cumplido el: {{ \Carbon\Carbon::parse($sug->fecha_respuesta)->format('d/m/Y h:i A') }}
                                        </p>
                                        @if($sug->comentario_agricultor)
                                            <p class="text-[10px] font-medium text-slate-700 dark:text-slate-300 italic">
                                                "{{ $sug->comentario_agricultor }}"
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <div class="p-3 bg-amber-500/5 border border-amber-500/10 rounded-2xl text-[9px] font-bold text-amber-600 italic">
                                        ⏳ El agricultor aún no ha confirmado el cumplimiento.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-slate-200/50 dark:border-white/5 text-[10px]">
                            <button wire:click="verSugerencia({{ $sug->id }})" class="font-black text-agri-green hover:underline italic flex items-center gap-1">
                                <i class="fa-solid fa-eye text-xs"></i> Ver Detalles
                            </button>
                            <span class="text-[9px] font-bold text-slate-400 truncate max-w-[120px]">Por: {{ $sug->supervisor->nombres ?? 'Supervisor' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-12 text-center italic text-slate-400 text-xs border border-dashed border-slate-200 dark:border-white/10 rounded-3xl">
                        No se encontraron sugerencias ni tareas registradas bajo el filtro seleccionado.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <!-- VISTA DEL AGRICULTOR: TAREAS RECIBIDAS -->
    @if(!$isSupervisorOrAdmin && !$sugerenciasSupervisor->isEmpty())
    <div class="space-y-4">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center font-black"><i class="fa-solid fa-user-tie"></i></div>
            <h3 class="text-xl font-black text-slate-800 dark:text-white italic uppercase tracking-tight">Instrucciones de tu Supervisor</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($sugerenciasSupervisor as $sug)
                @php $esPendiente = ($sug->estado == 0); @endphp
                <div wire:click="verSugerencia({{ $sug->id }})"
                     class="bg-white dark:bg-agri-d_bg p-6 rounded-3xl border-2 {{ $esPendiente ? 'border-amber-500/40 shadow-xl' : 'border-slate-100 dark:border-white/5 opacity-80' }} hover:-translate-y-1 transition-all cursor-pointer group relative">

                    <div class="flex justify-between items-start mb-3">
                        <span class="px-3 py-1 text-[8px] font-black uppercase rounded-lg italic tracking-widest
                            {{ $esPendiente ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' }}">
                            {{ $esPendiente ? '⏳ Pendiente' : '✅ Completado' }}
                        </span>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">
                            📅 {{ \Carbon\Carbon::parse($sug->fecha_sugerida)->format('d/m/Y') }}
                        </span>
                    </div>

                    <h4 class="text-base font-black text-slate-800 dark:text-white mb-2 uppercase leading-tight italic group-hover:text-agri-green transition-colors">
                        {{ $sug->titulo }}
                    </h4>

                    <p class="text-xs text-slate-600 dark:text-slate-300 italic line-clamp-2 mb-4">
                        {{ $sug->descripcion }}
                    </p>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-white/5 text-[10px]">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-user-check text-amber-500"></i>
                            <span class="font-bold text-slate-700 dark:text-slate-300 italic">{{ $sug->supervisor->nombres ?? 'Supervisor' }}</span>
                        </div>
                        <span class="font-black text-agri-green group-hover:underline">Ver Detalles Modal ↗</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- MODAL DETALLADO DE SUGERENCIA / INSTRUCCIÓN -->
    @if($showSugerenciaModal && $selectedSugerencia)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
        <div class="bg-white dark:bg-agri-d_bg w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/10 animate-in zoom-in duration-300">
            <div class="bg-[#003a38] px-8 py-6 flex justify-between items-center text-white border-b border-white/5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-black">
                        <i class="fa-solid fa-lightbulb text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black italic tracking-tighter uppercase leading-none">Detalles de la Instrucción</h3>
                        <p class="text-[9px] opacity-60 uppercase font-black tracking-widest mt-1 italic">Vigilancia Técnica AgroSys</p>
                    </div>
                </div>
                <button wire:click="closeSugerenciaModal" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-rose-500 transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-8 space-y-6">
                <!-- Ficha del Supervisor y Agricultor -->
                <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/5">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Supervisor:</p>
                        <p class="text-xs font-black text-slate-800 dark:text-white uppercase italic mt-0.5">
                            {{ $selectedSugerencia->supervisor->nombres ?? 'Supervisor' }} {{ $selectedSugerencia->supervisor->apellidos ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Agricultor Receptor:</p>
                        <p class="text-xs font-black text-agri-green uppercase italic mt-0.5">
                            {{ $selectedSugerencia->agricultor->nombres ?? 'Agricultor' }} {{ $selectedSugerencia->agricultor->apellidos ?? '' }}
                        </p>
                    </div>
                </div>

                <!-- Título y Cultivo Destino -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Título de la Labor</span>
                        <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase">
                            📅 Programada: {{ \Carbon\Carbon::parse($selectedSugerencia->fecha_sugerida)->format('d/m/Y') }}
                        </span>
                    </div>
                    <h4 class="text-lg font-black text-slate-800 dark:text-white italic uppercase leading-tight">
                        {{ $selectedSugerencia->titulo }}
                    </h4>
                    @if($selectedSugerencia->cultivo)
                        <p class="text-xs font-bold text-agri-green uppercase italic">
                            🌾 Lote Destino: {{ $selectedSugerencia->cultivo->nombre_lote }} ({{ $selectedSugerencia->cultivo->detalleCatalogo->nombre ?? 'Cultivo' }})
                        </p>
                    @endif
                </div>

                <!-- Instrucciones Detalladas -->
                <div class="p-5 bg-amber-50 dark:bg-amber-900/10 border border-amber-200/60 dark:border-amber-900/30 rounded-2xl space-y-2">
                    <p class="text-[9px] font-black text-amber-700 dark:text-amber-400 uppercase tracking-widest italic">Instrucción Agronómica:</p>
                    <p class="text-xs font-medium text-slate-700 dark:text-slate-200 italic leading-relaxed whitespace-pre-line">
                        {{ $selectedSugerencia->descripcion }}
                    </p>
                </div>

                <!-- Estado de Cumplimiento -->
                @if($selectedSugerencia->estado == 1)
                    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl space-y-1">
                        <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest italic">
                            ✅ Cumplido el: {{ \Carbon\Carbon::parse($selectedSugerencia->fecha_respuesta)->format('d/m/Y h:i A') }}
                        </p>
                        @if($selectedSugerencia->comentario_agricultor)
                            <p class="text-xs font-medium text-slate-700 dark:text-slate-300 italic">
                                Nota del Agricultor: "{{ $selectedSugerencia->comentario_agricultor }}"
                            </p>
                        @endif
                    </div>
                @else
                    @if(Auth::id() === $selectedSugerencia->agricultor_usuario_id)
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Tu Nota o Comentario de Respuesta (Opcional)</label>
                            <textarea wire:model="comentarioRespuesta" rows="2" placeholder="Ej. Labor realizada hoy a las 7:00 AM con 20L de riego..."
                                      class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl text-xs font-bold text-slate-800 dark:text-white outline-none focus:ring-4 focus:ring-agri-green/10 transition-all italic"></textarea>
                        </div>
                    @else
                        <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl text-[10px] font-bold text-amber-600 italic">
                            ⏳ Esta instrucción se encuentra en estado Pendiente por parte del agricultor.
                        </div>
                    @endif
                @endif
            </div>

            <div class="p-6 bg-slate-50 dark:bg-black/20 border-t border-slate-100 dark:border-white/5 flex gap-3">
                <button wire:click="closeSugerenciaModal" class="flex-1 py-3 bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-slate-300 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-300 transition-all italic">
                    Cerrar
                </button>
                @if($selectedSugerencia->estado == 0 && Auth::id() === $selectedSugerencia->agricultor_usuario_id)
                    <button wire:click="completarSugerencia" class="flex-1 py-3 bg-agri-green text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-agri-green/20 hover:scale-105 transition-all italic">
                        ✅ Marcar como Completada
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL CREAR NUEVA INSTRUCCIÓN / SUGERENCIA (SUPERVISORES) -->
    @if($showCreateSugerenciaModal && $isSupervisorOrAdmin)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
        <div class="bg-white dark:bg-agri-d_bg w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/10 animate-in zoom-in duration-300">
            <div class="bg-[#003a38] px-8 py-6 flex justify-between items-center text-white border-b border-white/5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-agri-green/20 text-agri-green flex items-center justify-center font-black">
                        <i class="fa-solid fa-list-check text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black italic tracking-tighter uppercase leading-none">Asignar Nueva Instrucción</h3>
                        <p class="text-[9px] opacity-60 uppercase font-black tracking-widest mt-1 italic">Vigilancia Técnica AgroSys</p>
                    </div>
                </div>
                <button wire:click="closeCreateSugerenciaModal" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-rose-500 transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form wire:submit.prevent="crearSugerencia" class="p-8 space-y-4">
                <!-- Seleccionar Agricultor Destino -->
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400">Agricultor Destinatario *</label>
                    <select wire:model="newAgricultorId" class="w-full bg-slate-50 dark:bg-slate-800 border @error('newAgricultorId') border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-2xl p-3.5 text-xs font-bold uppercase text-slate-800 dark:text-white">
                        <option value="">-- Seleccionar Agricultor --</option>
                        @foreach($misAgricultores as $ag)
                            <option value="{{ $ag->id }}">{{ $ag->nombres }} {{ $ag->apellidos }} (DNI: {{ $ag->dni ?? 'N/D' }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('newAgricultorId')" class="mt-1 text-xs" />
                </div>

                <!-- Seleccionar Lote/Cultivo (Opcional) -->
                @if($cultivosDisponibles->isNotEmpty())
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400">Lote / Cultivo Asociado (Opcional)</label>
                        <select wire:model="newCultivoId" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-2xl p-3.5 text-xs font-bold uppercase text-slate-800 dark:text-white">
                            <option value="">-- Sin Lote Específico --</option>
                            @foreach($cultivosDisponibles as $cul)
                                <option value="{{ $cul->id }}">{{ $cul->nombre_lote }} - {{ $cul->detalleCatalogo->nombre ?? 'Cultivo' }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Título -->
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400">Título de la Instrucción *</label>
                    <input type="text" wire:model="newTitulo" placeholder="EJ. APLICAR RIEGO PREVENTIVO DE 2 HORAS"
                           class="w-full bg-slate-50 dark:bg-slate-800 border @error('newTitulo') border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-2xl p-3.5 text-xs font-bold uppercase text-slate-800 dark:text-white">
                    <x-input-error :messages="$errors->get('newTitulo')" class="mt-1 text-xs" />
                </div>

                <!-- Fecha Programada -->
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400">Fecha Programada de Ejecución *</label>
                    <input type="date" wire:model="newFechaSugerida"
                           class="w-full bg-slate-50 dark:bg-slate-800 border @error('newFechaSugerida') border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-2xl p-3.5 text-xs font-bold text-slate-800 dark:text-white">
                    <x-input-error :messages="$errors->get('newFechaSugerida')" class="mt-1 text-xs" />
                </div>

                <!-- Descripción / Instrucciones -->
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400">Instrucciones Detalladas *</label>
                    <textarea wire:model="newDescripcion" rows="3" placeholder="EXPLIQUE LAS INDICACIONES TÉCNICAS, DOSIS O RECOMENDACIONES DE CAMPO..."
                              class="w-full bg-slate-50 dark:bg-slate-800 border @error('newDescripcion') border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-2xl p-3.5 text-xs font-bold text-slate-800 dark:text-white"></textarea>
                    <x-input-error :messages="$errors->get('newDescripcion')" class="mt-1 text-xs" />
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-white/5 flex gap-3">
                    <button type="button" wire:click="closeCreateSugerenciaModal" class="flex-1 py-3 bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-slate-300 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all italic">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-agri-green text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-agri-green/20 hover:scale-105 transition-all italic flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Asignar Instrucción</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

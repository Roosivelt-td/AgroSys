<div class="space-y-8 p-4 md:p-1 transition-colors duration-500">
    <!-- Header: Perfil del Agricultor Supervisado -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/10 pb-6">
        <div class="flex items-center space-x-6">
            <a href="{{ route('supervisor.agricultores') }}" wire:navigate class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center shadow-lg hover:bg-agri-green hover:text-white transition-all group">
                <i class="fa-solid fa-chevron-left text-xl group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div class="flex items-center space-x-5">
                <div class="relative">
                    <div class="w-20 h-20 rounded-full border-4 border-agri-green p-0.5 shadow-2xl overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img src="{{ $agricultor->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($agricultor->nombres).'&background=00ba2e&color=fff' }}"
                             class="w-full h-full rounded-full object-cover">
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-agri-green rounded-full border-4 border-white dark:border-agri-d_bg flex items-center justify-center text-white text-[10px] shadow-lg">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">{{ $agricultor->nombres }} {{ $agricultor->apellidos }}</h2>
                    <p class="text-[10px] text-agri-green font-black uppercase tracking-[0.3em] mt-2 italic">Vigilancia Técnica en {{ $organizacion->nombre }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button wire:click="openSugerenciaModal()"
                    class="px-6 py-3.5 bg-amber-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-amber-500/20 hover:scale-105 transition-all flex items-center gap-2 italic">
                <i class="fa-solid fa-lightbulb text-sm"></i> Enviar Sugerencia Técnica
            </button>
            <a href="{{ route('chat.index', ['user' => $agricultor->id]) }}" wire:navigate class="px-6 py-3.5 bg-agri-green text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all flex items-center gap-2 italic">
                <i class="fa-solid fa-comments text-sm"></i> Chat con Agricultor
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-r-xl italic shadow-sm animate-in fade-in duration-500">
            {{ session('status') }}
        </div>
    @endif

    <!-- Ficha Técnica y Métricas Clave -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl transition-all hover:border-agri-green/30">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Cultivos Activos</p>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-wheat-awn text-lg"></i></div>
                <div>
                    <p class="text-lg font-black text-slate-800 dark:text-white tabular-nums leading-none">{{ $totalCultivos }} Lotes</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Pendientes de Cosecha</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl transition-all hover:border-agri-green/30">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Superficie Terrenos</p>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-map-location-dot text-lg"></i></div>
                <div>
                    <p class="text-lg font-black text-slate-800 dark:text-white tabular-nums leading-none">{{ number_format($totalHectareas, 2) }} HA</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">{{ $terrenos->count() }} Parcelas</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl transition-all hover:border-agri-green/30">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Inversión en Labores</p>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-hand-holding-dollar text-lg"></i></div>
                <div>
                    <p class="text-lg font-black text-slate-800 dark:text-white tabular-nums leading-none">S/ {{ number_format($totalInversion, 2) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Gasto acumulado</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl transition-all hover:border-agri-green/30">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Producción & Ventas</p>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center"><i class="fa-solid fa-sack-dollar text-lg"></i></div>
                <div>
                    <p class="text-lg font-black text-slate-800 dark:text-white tabular-nums leading-none">S/ {{ number_format($totalVentasMonto, 2) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">{{ number_format($totalCosechadoKg / 1000, 2) }} TN Cosechadas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN PRINCIPAL: CULTIVOS Y SUS LABORES PENDIENTES/EJECUTADAS -->
    <div class="bg-white dark:bg-agri-d_bg rounded-3xl p-8 border border-slate-100 dark:border-white/5 shadow-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 dark:border-white/5 pb-4 gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-seedling text-lg"></i></div>
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white italic uppercase tracking-tight leading-none">Cultivos de la Finca</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Monitoreo de estado y desarrollo agronómico</p>
                </div>
            </div>

            <!-- Botones de Filtro Activos vs Todos -->
            <div class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 p-1 rounded-2xl">
                <button wire:click="$set('verTodosCultivos', false)"
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all italic
                        {{ !$verTodosCultivos ? 'bg-agri-green text-white shadow-md' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    🌾 Activos (Sin Cosechar)
                </button>
                <button wire:click="$set('verTodosCultivos', true)"
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all italic
                        {{ $verTodosCultivos ? 'bg-agri-green text-white shadow-md' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    📦 Todos (Incluye Cosechados)
                </button>
            </div>
        </div>

        <div class="space-y-8">
            @forelse($cultivosActivos as $cultivo)
                @php
                    $esCosechado = in_array($cultivo->estado, ['Finalizado', 'Cosechado']);
                @endphp
                <div class="bg-slate-50 dark:bg-white/5 p-6 rounded-3xl border border-slate-200/60 dark:border-white/5 space-y-6 relative group hover:border-agri-green/30 transition-all shadow-sm">
                    <!-- Ficha del Lote -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200/50 dark:border-white/5 pb-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-2xl {{ $esCosechado ? 'bg-purple-500/10 text-purple-600' : 'bg-emerald-500/10 text-emerald-600' }} flex items-center justify-center font-black text-xl italic shadow-inner">
                                <i class="fa-solid {{ $esCosechado ? 'fa-boxes-packing' : 'fa-wheat-field' }}"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-3">
                                    <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase leading-none italic">{{ $cultivo->nombre_lote }}</h4>
                                    <span class="px-3 py-1 text-[9px] font-black uppercase rounded-lg border italic
                                        {{ $esCosechado ? 'bg-purple-500/10 text-purple-600 border-purple-500/20' : 'bg-amber-500/10 text-amber-600 border-amber-500/20' }}">
                                        {{ $cultivo->estado }}
                                    </span>
                                </div>
                                <p class="text-xs font-bold text-agri-green dark:text-emerald-400 uppercase mt-1">
                                    {{ $cultivo->detalleCatalogo->nombre ?? 'Cultivo' }} ({{ $cultivo->variedad ?: 'Genérica' }}) &bull; Terreno: {{ $cultivo->terreno->nombre ?? 'N/D' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <div class="text-right px-4 py-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-white/5 shadow-inner">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Área Destinada</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white tabular-nums">{{ number_format($cultivo->area_destinada, 2) }} HA</p>
                            </div>
                            <div class="text-right px-4 py-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-white/5 shadow-inner">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Fecha Siembra</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white tabular-nums">{{ $cultivo->fecha_siembra ? $cultivo->fecha_siembra->format('d/m/Y') : '---' }}</p>
                            </div>
                            @if(!$esCosechado)
                                <button wire:click="openSugerenciaModal({{ $cultivo->id }})"
                                        class="px-5 py-3 bg-agri-green text-white rounded-2xl font-black text-[9px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-agri-green/20 flex items-center gap-2 italic">
                                    <i class="fa-solid fa-lightbulb"></i> Sugerir Acción
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Labores asociadas a este Lote -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Labores y Trabajos Registrados en este Lote</p>
                            <span class="text-[9px] font-bold text-slate-400">{{ $cultivo->labores->count() }} Labores</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @forelse($cultivo->labores as $labor)
                                <div class="p-4 bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-white/5 space-y-2 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-black text-slate-800 dark:text-white uppercase italic leading-none">
                                            {{ $labor->detalleCatalogo->nombre ?? 'Labor Agrícola' }}
                                        </p>
                                        <span class="px-2 py-0.5 text-[8px] font-black uppercase rounded-md italic
                                            {{ $labor->estado === 'Completada' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600' }}">
                                            {{ $labor->estado ?: 'Ejecutada' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-500">
                                        <span>📅 {{ $labor->fecha_realizacion ? $labor->fecha_realizacion->format('d/m/Y') : '---' }}</span>
                                        <span class="text-blue-600 dark:text-blue-400 font-black tabular-nums">S/ {{ number_format($labor->costo_total, 2) }}</span>
                                    </div>
                                    @if($labor->observaciones)
                                        <p class="text-[10px] text-slate-400 italic truncate">{{ $labor->observaciones }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="col-span-full p-4 bg-white/50 dark:bg-slate-800/30 rounded-2xl text-center text-[10px] font-bold italic text-slate-400 border border-dashed border-slate-200 dark:border-white/5">
                                    Aún no hay labores registradas para este lote. ¡Sugiérele la primera labor al agricultor!
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center italic text-slate-400 text-xs border border-dashed border-slate-200 dark:border-white/10 rounded-3xl">
                    <div class="w-14 h-14 bg-amber-500/10 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-wheat-field-semi text-2xl"></i>
                    </div>
                    <p class="font-bold">No hay cultivos registrados para este agricultor bajo el filtro seleccionado.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- SECCIÓN DE TERRENOS / PARCELAS -->
    <div class="bg-white dark:bg-agri-d_bg rounded-3xl p-8 border border-slate-100 dark:border-white/5 shadow-xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 pb-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-agri-green/10 text-agri-green flex items-center justify-center"><i class="fa-solid fa-map"></i></div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white italic uppercase tracking-tight">Parcelas y Terrenos del Agricultor</h3>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $terrenos->count() }} Registrados</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($terrenos as $terreno)
                <div class="bg-slate-50 dark:bg-white/5 p-6 rounded-2xl border border-slate-100 dark:border-white/5 space-y-3 relative group hover:border-agri-green/30 transition-all">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="text-base font-black text-slate-800 dark:text-white uppercase leading-tight italic">{{ $terreno->nombre }}</h4>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">{{ $terreno->ubicacion ?: 'Sin dirección' }}</p>
                        </div>
                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase rounded-lg border border-emerald-500/20">
                            {{ number_format($terreno->hectareas, 2) }} HA
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px] font-bold text-slate-500 pt-2 border-t border-slate-200/50 dark:border-white/5">
                        <div><span class="text-slate-400 uppercase">Tenencia:</span> <span class="text-slate-700 dark:text-slate-300 font-black uppercase">{{ $terreno->tipo_tenencia }}</span></div>
                        <div><span class="text-slate-400 uppercase">Suelo:</span> <span class="text-slate-700 dark:text-slate-300 font-black uppercase">{{ $terreno->calidad_suelo ?: 'N/D' }}</span></div>
                    </div>

                    @if($terreno->latitud && $terreno->longitud)
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $terreno->latitud }},{{ $terreno->longitud }}" target="_blank"
                           class="mt-2 inline-flex items-center gap-2 text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider hover:underline">
                            <i class="fa-solid fa-location-arrow"></i> Ver mapa Google Maps ↗
                        </a>
                    @endif
                </div>
            @empty
                <div class="col-span-full py-10 text-center italic text-slate-400 text-xs">
                    El agricultor aún no tiene parcelas registradas en esta organización.
                </div>
            @endforelse
        </div>
    </div>

    <!-- SECCIÓN DE HISTORIAL DE SUGERENCIAS ENVIADAS -->
    <div class="bg-white dark:bg-agri-d_bg rounded-3xl p-8 border border-slate-100 dark:border-white/5 shadow-xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 pb-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-lightbulb"></i></div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white italic uppercase tracking-tight">Sugerencias e Instrucciones Enviadas</h3>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $sugerenciasEnviadas->count() }} Historial</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($sugerenciasEnviadas as $sug)
                <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-2xl border border-slate-100 dark:border-white/5 space-y-3 relative">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase leading-tight italic">{{ $sug->titulo }}</h4>
                            <p class="text-[9px] text-agri-green font-bold uppercase mt-0.5">
                                Lote: {{ $sug->cultivo->nombre_lote ?? 'General' }}
                            </p>
                        </div>
                        <span class="px-2.5 py-1 text-[8px] font-black uppercase rounded-lg italic
                            {{ $sug->estado == 1 ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-600 border border-amber-500/20' }}">
                            {{ $sug->estado == 1 ? 'Completado' : 'Pendiente' }}
                        </span>
                    </div>

                    <p class="text-[11px] text-slate-600 dark:text-slate-300 italic leading-snug">{{ $sug->descripcion }}</p>

                    <div class="flex items-center justify-between text-[9px] text-slate-400 font-bold pt-2 border-t border-slate-200/50 dark:border-white/5">
                        <span>📅 Fecha Sugerida: {{ \Carbon\Carbon::parse($sug->fecha_sugerida)->format('d/m/Y') }}</span>
                        <span>{{ $sug->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center italic text-slate-400 text-xs">
                    No has enviado sugerencias todavía. Haz clic en "Enviar Sugerencia Técnica" para guiar al agricultor.
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL PARA ENVIAR SUGERENCIA TÉCNICA -->
    @if($showSugerenciaModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
        <div class="bg-white dark:bg-agri-d_bg w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/10 animate-in zoom-in duration-300">
            <div class="bg-[#003a38] px-8 py-6 flex justify-between items-center text-white border-b border-white/5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-black">
                        <i class="fa-solid fa-lightbulb text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black italic tracking-tighter uppercase leading-none">Enviar Sugerencia Técnica</h3>
                        <p class="text-[9px] opacity-60 uppercase font-black tracking-widest mt-1 italic">Instrucción directa para {{ $agricultor->nombres }}</p>
                    </div>
                </div>
                <button wire:click="closeSugerenciaModal" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-rose-500 transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-8 space-y-5">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Cultivo / Lote Destino</label>
                    <select wire:model="selectedCultivoId" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl text-xs font-bold text-slate-800 dark:text-white outline-none focus:ring-4 focus:ring-agri-green/10 transition-all cursor-pointer">
                        <option value="">-- Toda la Finca (Instrucción General) --</option>
                        @foreach($cultivosActivos as $c)
                            <option value="{{ $c->id }}">🌾 Lote: {{ $c->nombre_lote }} ({{ $c->detalleCatalogo->nombre ?? 'Cultivo' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Título de la Sugerencia / Acción</label>
                    <input wire:model="tituloSugerencia" type="text" placeholder="Ej. Aplicar Riego por goteo de 2 horas / Posponer fumigación"
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl text-xs font-bold text-slate-800 dark:text-white outline-none focus:ring-4 focus:ring-agri-green/10 transition-all italic">
                    @error('tituloSugerencia') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Fecha Sugerida para la Labor</label>
                    <input wire:model="fechaSugerida" type="date"
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl text-xs font-bold text-slate-800 dark:text-white outline-none focus:ring-4 focus:ring-agri-green/10 transition-all">
                    @error('fechaSugerida') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Instrucciones Detalladas para el Agricultor</label>
                    <textarea wire:model="descripcionSugerencia" rows="3" placeholder="Escriba aquí los detalles técnicos, dosis de insumos o recomendaciones climáticas..."
                              class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl text-xs font-bold text-slate-800 dark:text-white outline-none focus:ring-4 focus:ring-agri-green/10 transition-all italic"></textarea>
                    @error('descripcionSugerencia') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="p-6 bg-slate-50 dark:bg-black/20 border-t border-slate-100 dark:border-white/5 flex gap-3">
                <button wire:click="closeSugerenciaModal" class="flex-1 py-3 bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-slate-300 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-300 transition-all italic">
                    Cancelar
                </button>
                <button wire:click="enviarSugerencia" class="flex-1 py-3 bg-amber-500 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-amber-500/20 hover:scale-105 transition-all italic">
                    Enviar Instrucción
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

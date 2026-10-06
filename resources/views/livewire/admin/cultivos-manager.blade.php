<div class="space-y-8 pb-20">
    <!-- Header Page -->
    <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-100 dark:border-white/5 p-4 md:p-6">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-agri-green/5 rounded-full blur-3xl"></div>

        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-4">
                <div>
                    <span class="px-3 py-1 bg-agri-green/10 text-agri-green text-[8px] font-black uppercase tracking-widest rounded border border-agri-green/20 italic">
                        {{ auth()->user()->rol_id === 1 ? __('Ecosistema Global') : __('Producción Agrícola') }}
                    </span>
                    <h1 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white italic tracking-tighter mt-1.5 leading-tight uppercase">
                        {{ auth()->user()->rol_id === 1 ? __('Catálogo de') : __('Mis') }}<br><span class="text-agri-green">{{ auth()->user()->rol_id === 1 ? __('Cultivos SaaS') : __('Cultivos') }}</span>
                    </h1>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-medium italic max-w-xs leading-relaxed">
                    {{ auth()->user()->rol_id === 1 ? __('Monitoreo y administración general de todas las campañas agrícolas activas.') : __('Control de siembras, desarrollo y fechas estimadas de cosecha por lote.') }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if(auth()->user()->rol_id !== 1)
                    <button wire:click="openCreateModal" class="px-8 py-3 bg-agri-green text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all italic">
                        <i class="fa-solid fa-plus mr-2"></i> Registrar Siembra
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <input type="text" wire:model.live="searchCultivo" placeholder="Buscar por cultivo..." class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner uppercase">
                <i class="fa-solid fa-magnifying-glass absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>
            <div class="relative">
                <input type="text" wire:model.live="searchVariedad" placeholder="Buscar por variedad..." class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner uppercase">
                <i class="fa-solid fa-seedling absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>
            <div class="relative">
                <input type="text" wire:model.live="searchTerreno" placeholder="Buscar por terreno..." class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner uppercase">
                <i class="fa-solid fa-mountain absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>
            <div>
                <select wire:model.live="filterStatus" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner uppercase">
                    <option value="">TODOS LOS ESTADOS</option>
                    <option value="Planificado">Planificado</option>
                    <option value="En crecimiento">En Crecimiento</option>
                    <option value="Cosechado">Cosechado</option>
                    <option value="Perdido">Perdido</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Grid de Cultivos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
        @foreach($cultivos as $cultivo)
        @php
            $siembra = \Carbon\Carbon::parse($cultivo->fecha_siembra);
            $cosecha = $cultivo->fecha_cosecha_estimada ? \Carbon\Carbon::parse($cultivo->fecha_cosecha_estimada) : $siembra->copy()->addMonths(4);
            $totalDias = $siembra->diffInDays($cosecha);
            $diasTranscurridos = $siembra->diffInDays(now(), false);
            $progreso = $totalDias > 0 ? max(0, min(100, ($diasTranscurridos / $totalDias) * 100)) : 0;
            $colorProgreso = $progreso < 30 ? 'bg-emerald-300' : ($progreso < 70 ? 'bg-amber-400' : 'bg-agri-green');
        @endphp
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-2xl border border-slate-100 group relative transition-all duration-700 hover:-translate-y-2" x-data="{ menuOpen: false }">
            <div class="h-72 bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
                @if($cultivo->foto_path)
                    <img src="{{ Storage::url($cultivo->foto_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-200 bg-slate-50 dark:bg-white/5"><i class="fa-solid fa-leaf text-8xl"></i></div>
                @endif

                <!-- Capa de Degradado Base -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/30 to-transparent z-0"></div>

                <!-- 1. Clima y Identificador de Lote (Z-40: Arriba a la izquierda, NUNCA opacos, siempre 100% nítidos) -->
                @php
                    $clima = $cultivo->terreno->latestClima ?: \App\Models\ClimaRegistro::latest('fecha_hora')->first();
                    $icon = 'fa-sun';
                    if($clima) {
                        $cond = strtolower($clima->condicion);
                        if(str_contains($cond, 'lluvia') || str_contains($cond, 'llovizna')) $icon = 'fa-cloud-showers-heavy';
                        elseif(str_contains($cond, 'nublado')) $icon = 'fa-cloud';
                        elseif(str_contains($cond, 'tormenta')) $icon = 'fa-cloud-bolt';
                    }
                @endphp
                <div class="absolute top-4 left-4 z-40 flex flex-col gap-1.5 pointer-events-none">
                    <!-- Clima Badge -->
                    <div class="flex items-center space-x-2 bg-black/60 backdrop-blur-md px-3 py-1 rounded-xl shadow-lg border border-white/10 w-fit pointer-events-auto">
                        <i class="fa-solid {{ $icon }} {{ $clima ? 'text-amber-400' : 'text-amber-500' }} text-xs"></i>
                        <span class="text-[10px] font-black text-white italic uppercase">
                            @if($clima)
                                {{ round($clima->temperatura) }}°C | {{ $clima->humedad }}% HR
                            @else
                                23°C | 65% HR
                            @endif
                        </span>
                    </div>

                    <!-- Identificador de Lote -->
                    <div class="bg-amber-500 text-white px-3 py-1 rounded-xl shadow-lg border border-white/20 w-fit pointer-events-auto">
                        <p class="text-[10px] font-black tracking-widest uppercase italic leading-none flex items-center gap-1.5">
                            <i class="fa-solid fa-barcode text-[11px]"></i>
                            LOTE: {{ $cultivo->nombre_lote }}
                        </p>
                    </div>
                </div>

                <!-- 2. Menú de 3 Puntos (Z-40: Arriba a la derecha, NUNCA opaco, siempre 100% visible) -->
                <div class="absolute top-4 right-4 z-40">
                    <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false" class="w-8 h-8 bg-white/10 backdrop-blur-md border border-white/20 rounded-lg text-white flex items-center justify-center shadow-lg hover:bg-white/30 transition-all"><i class="fa-solid fa-ellipsis-vertical text-xs"></i></button>
                    <div x-show="menuOpen" x-transition class="absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden" x-cloak><button wire:click="edit({{ $cultivo->id }})" class="w-full px-6 py-4 text-left text-[11px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition-all italic">EDITAR</button><button wire:click="delete({{ $cultivo->id }})" wire:confirm="¿Borrar?" class="w-full px-6 py-4 text-left text-[11px] font-black uppercase text-rose-500 hover:bg-rose-50 transition-all italic">BORRAR</button></div>
                </div>

                <!-- 3. Contenido de la Imagen que SE OPACA AL 20% EN HOVER -->
                <div class="group-hover:opacity-20 transition-all duration-500 pointer-events-none">
                    <!-- Badges de Fechas Siembra y Cosecha -->
                    <div class="absolute top-24 left-4 z-10 flex flex-col gap-1.5">
                        <!-- Punto 1: Badge Fecha de Siembra -->
                        <div class="bg-black/70 backdrop-blur-md px-3 py-1 rounded-xl shadow-lg border border-white/10 w-fit text-white">
                            <div class="flex items-center gap-1.5">
                                <i class="fa-solid fa-seedling text-agri-green text-xs"></i>
                                <p class="text-[10px] font-black uppercase italic leading-none">
                                    @if($cultivo->estado === 'Planificado')
                                        Siembra Plan: {{ $cultivo->fecha_planificada ? $cultivo->fecha_planificada->format('d/m/Y') : '---' }}
                                    @else
                                        Siembra: {{ $cultivo->fecha_siembra ? $cultivo->fecha_siembra->format('d/m/Y') : ($cultivo->fecha_planificada ? $cultivo->fecha_planificada->format('d/m/Y') : '---') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Punto 2: Badge Fecha de Cosecha -->
                        <div class="bg-black/70 backdrop-blur-md px-3 py-1 rounded-xl shadow-lg border border-white/10 w-fit text-white">
                            <div class="flex items-center gap-1.5">
                                <i class="fa-solid fa-wheat-awn text-amber-400 text-xs"></i>
                                <p class="text-[10px] font-black uppercase italic leading-none">
                                    @if($cultivo->estado === 'Cosechado')
                                        Cosechado: {{ $cultivo->fecha_cosecha_finalizada ? $cultivo->fecha_cosecha_finalizada->format('d/m/Y') : ($cultivo->fecha_cosecha_estimada ? $cultivo->fecha_cosecha_estimada->format('d/m/Y') : '---') }}
                                    @else
                                        Cosecha Est: {{ $cultivo->fecha_cosecha_estimada ? $cultivo->fecha_cosecha_estimada->format('d/m/Y') : ($cultivo->fecha_planificada ? \Carbon\Carbon::parse($cultivo->fecha_planificada)->addMonths(4)->format('d/m/Y') : '---') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Información Principal del Cultivo (Nombre, Variedad, Inversión, Riego, Lugar) -->
                    <div class="absolute bottom-5 left-7 right-7 text-white space-y-4 z-10">
                        <div class="flex justify-between items-end">
                            <div class="min-w-0 flex-1 pr-2">
                                <h3 class="text-2xl font-black italic tracking-tighter uppercase leading-none flex items-baseline gap-2">
                                    <span>{{ $cultivo->detalleCatalogo->nombre }}</span>
                                    <span class="text-[12px] font-black text-emerald-400 uppercase tracking-widest italic truncate">{{ $cultivo->variedad ?: 'VAR. GENERICA' }}</span>
                                </h3>
                            </div>
                            <div class="px-3 py-1.5 bg-agri-green rounded-lg text-[11px] font-black italic shadow-lg shrink-0 border border-white/20">{{ number_format($cultivo->area_destinada, 2) }} ha</div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 pt-3 border-t border-white/20">
                            @php
                                $esCosechado = $cultivo->estado === 'Cosechado';
                            @endphp
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none mb-1">
                                    @if($esCosechado) RENDIMIENTO @else INVERSIÓN @endif
                                </span>
                                @if($esCosechado)
                                    <span class="text-[12px] font-black italic text-emerald-400">
                                        {{ number_format($cultivo->rendimiento_esperado_tn_ha, 2) }} <span class="text-[8px] opacity-70">TN/HA</span>
                                    </span>
                                    <span class="text-[7px] font-black text-white/40 uppercase tracking-tighter">DATO REAL VENTA</span>
                                @else
                                    <span class="text-[12px] font-black italic text-amber-400">S/ {{ number_format($cultivo->total_inversion, 2) }}</span>
                                @endif
                            </div>
                            <div class="flex flex-col border-l border-white/10 pl-2">
                                <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none mb-1">Riego</span>
                                <span class="text-[10px] font-bold italic opacity-90 truncate">{{ $cultivo->terreno->fuente_agua }}</span>
                            </div>
                            <div class="flex flex-col border-l border-white/10 pl-2 text-right">
                                <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none mb-1">Lugar</span>
                                <span class="text-[10px] font-bold italic opacity-90 truncate block">{{ $cultivo->terreno->nombre }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Overlay Central de Sombreado y Botón "GESTIONAR LABORES" (Z-30: En el centro, 100% visible) -->
                <div class="absolute inset-0 z-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none bg-black/60 backdrop-blur-[3px]">
                    <a href="{{ route('admin.labores', ['filterCropId' => $cultivo->id, 'strict' => 1]) }}" class="pointer-events-auto transform scale-75 group-hover:scale-100 transition-all duration-300 hover:scale-105">
                        <div class="px-6 py-3 bg-agri-green text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-[0_0_30px_rgba(16,185,129,0.5)] border border-white/20 italic flex items-center justify-center gap-3">
                            <i class="fa-solid fa-gears text-sm animate-spin-slow"></i> GESTIONAR LABORES
                        </div>
                    </a>
                </div>
            </div>

            <!-- Footer con Barra de Fenología (Punto 1: Al hacer clic abre los detalles/informe del cultivo) -->
            <div wire:click="showCropReport({{ $cultivo->id }})"
                 class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-white/5 space-y-3 cursor-pointer hover:bg-slate-100 dark:hover:bg-white/5 transition-all group/fenologia"
                 title="Ver Detalles e Informe del Cultivo">
                <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-wider">
                    <span class="text-slate-400 group-hover/fenologia:text-agri-green transition-colors">
                        FENOLOGÍA: <strong class="text-agri-green dark:text-emerald-400 italic">{{ $cultivo->estado }}</strong>
                    </span>
                    <span class="text-agri-green italic flex items-center gap-1">
                        <span>{{ round($progreso) }}% COMPLETO</span>
                        <i class="fa-solid fa-chevron-right text-[9px] opacity-70 group-hover/fenologia:translate-x-1 transition-transform"></i>
                    </span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2 overflow-hidden p-0.5 shadow-inner">
                    <div class="{{ $colorProgreso }} h-full rounded-full transition-all duration-1000" style="width: {{ $progreso }}%"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Paginación -->
    <div class="mt-8">
        {{ $cultivos->links() }}
    </div>

    <!-- MODAL DE GESTIÓN (NUEVA CAMPAÑA AGROSYS / EDITAR) -->
    <x-modal name="modal-crop-manager" :show="false" focusable maxWidth="2xl">
        <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <!-- Header Modal estilo Verde Oscuro -->
            <div class="bg-[#003a38] px-8 py-5 flex justify-between items-center text-white border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-agri-green rounded-xl flex items-center justify-center text-white shadow-lg">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <h3 class="text-xl font-black italic tracking-tight uppercase text-white">
                        {{ $cropId ? 'EDITAR CAMPAÑA AGROSYS' : 'NUEVA CAMPAÑA AGROSYS' }}
                    </h3>
                </div>
                <button type="button" @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form wire:submit.prevent="save" class="p-8 space-y-6 max-h-[85vh] overflow-y-auto custom-scrollbar">
                <!-- Fila 1: 1. SELECCIONAR TERRENO & 2. TIPO DE CULTIVO (CON IMÁGENES Y ÁREA RESTANTE) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Terreno Card Selector (Con Imagen y Hectáreas Disponibles) -->
                    <div class="space-y-1.5 relative" x-data="{ openTerrenos: false }" @click.outside="openTerrenos = false">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-wider">1. SELECCIONAR TERRENO *</label>

                        <!-- Card Principal Seleccionada -->
                        @if($selectedTerrenoModel)
                            <div @click="if(!{{ $cropId ? 'true' : 'false' }}) openTerrenos = !openTerrenos"
                                 class="flex items-center justify-between bg-emerald-50 dark:bg-emerald-950/30 border-2 border-emerald-300 dark:border-emerald-800/50 rounded-2xl p-2.5 shadow-sm {{ !$cropId ? 'cursor-pointer hover:border-agri-green' : 'cursor-not-allowed opacity-80' }} transition-all">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-white dark:bg-slate-800 border-2 border-agri-green shrink-0 shadow-sm flex items-center justify-center">
                                        @if($selectedTerrenoModel->foto_path)
                                            <img src="{{ Storage::url($selectedTerrenoModel->foto_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-agri-green text-white font-black text-sm flex items-center justify-center">
                                                {{ mb_substr($selectedTerrenoModel->nombre, 0, 2) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black uppercase text-slate-800 dark:text-white truncate leading-tight">{{ $selectedTerrenoModel->nombre }}</p>
                                        <p class="text-[10px] font-black text-agri-green uppercase tracking-wider mt-0.5">
                                            DISP: {{ number_format($selectedTerrenoModel->area_disponible, 2) }} HA / {{ number_format($selectedTerrenoModel->hectareas, 2) }} HA
                                        </p>
                                    </div>
                                </div>
                                @if(!$cropId)
                                    <button type="button" class="p-2 text-agri-green hover:text-emerald-700 transition-colors">
                                        <i class="fa-solid fa-arrows-rotate text-sm"></i>
                                    </button>
                                @else
                                    <i class="fa-solid fa-lock text-slate-400 mr-2 text-xs"></i>
                                @endif
                            </div>
                        @else
                            <!-- Sin Terreno Seleccionado -->
                            <button type="button" @click="openTerrenos = !openTerrenos"
                                    class="w-full flex items-center justify-between bg-emerald-50/50 dark:bg-emerald-950/20 border-2 border-dashed border-emerald-300 dark:border-emerald-800/40 rounded-2xl p-3.5 text-xs font-black uppercase text-slate-500 dark:text-slate-400 hover:border-agri-green transition-all">
                                <span class="italic">SELECCIONE UN TERRENO...</span>
                                <i class="fa-solid fa-chevron-down text-agri-green text-xs"></i>
                            </button>
                        @endif

                        <!-- Dropdown Modal de Lista de Terrenos con Imágenes y Área Restante -->
                        <div x-show="openTerrenos" x-transition class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl z-50 max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1" x-cloak>
                            @foreach($misTerrenos as $t)
                                @php
                                    $isDisabled = $t->is_alquiler_vencido && !$cropId;
                                @endphp
                                <div @if(!$isDisabled) wire:click="selectTerreno({{ $t->id }}, '{{ addslashes($t->nombre) }}', {{ $t->area_disponible }}); openTerrenos = false" @endif
                                     class="p-2 rounded-xl flex items-center justify-between {{ $isDisabled ? 'opacity-50 cursor-not-allowed bg-rose-50/50 dark:bg-rose-950/20' : 'cursor-pointer hover:bg-emerald-50 dark:hover:bg-white/5' }} transition-colors">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 border-2 {{ $isDisabled ? 'border-rose-400' : 'border-agri-green' }} shrink-0 flex items-center justify-center">
                                            @if($t->foto_path)
                                                <img src="{{ Storage::url($t->foto_path) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full {{ $isDisabled ? 'bg-rose-500' : 'bg-agri-green' }} text-white font-black text-xs flex items-center justify-center">
                                                    {{ mb_substr($t->nombre, 0, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-black uppercase text-slate-800 dark:text-white truncate leading-tight">{{ $t->nombre }}</p>
                                            <p class="text-[9px] font-bold {{ $isDisabled ? 'text-rose-500' : 'text-agri-green' }} uppercase tracking-wider mt-0.5">
                                                @if($isDisabled)
                                                    ⛔ INHABILITADO — ALQUILER VENCIDO
                                                @else
                                                    DISP: {{ number_format($t->area_disponible, 2) }} HA / {{ number_format($t->hectareas, 2) }} HA
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @if($terreno_id == $t->id)
                                        <i class="fa-solid fa-check text-agri-green font-bold mr-2 text-xs"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('terreno_id')" class="mt-1 text-xs" />
                    </div>

                    <!-- Tipo de Cultivo Card Selector (Con Imagen de Cultivo) -->
                    <div class="space-y-1.5 relative" x-data="{ openCultivos: false }" @click.outside="openCultivos = false">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-wider">2. TIPO DE CULTIVO *</label>

                        <!-- Card Principal Seleccionada -->
                        @if($selectedCultivoModel)
                            <div @click="openCultivos = !openCultivos"
                                 class="flex items-center justify-between bg-blue-50 dark:bg-blue-950/30 border-2 border-blue-300 dark:border-blue-800/50 rounded-2xl p-2.5 shadow-sm cursor-pointer hover:border-blue-500 transition-all">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-white dark:bg-slate-800 border-2 border-blue-500 shrink-0 shadow-sm flex items-center justify-center">
                                        @if($selectedCultivoModel->foto_path)
                                            <img src="{{ Storage::url($selectedCultivoModel->foto_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-blue-600 text-white font-black text-sm flex items-center justify-center">
                                                {{ mb_substr($selectedCultivoModel->nombre, 0, 2) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black uppercase text-slate-800 dark:text-white truncate leading-tight">{{ $selectedCultivoModel->nombre }}</p>
                                        <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider mt-0.5">
                                            {{ str_replace('_', ' ', strtoupper($selectedCultivoModel->tipo_ciclo)) }} • {{ $selectedCultivoModel->dias_a_cosecha_promedio ?: 120 }} DÍAS
                                        </p>
                                    </div>
                                </div>
                                <button type="button" class="p-2 text-blue-500 hover:text-blue-700 transition-colors">
                                    <i class="fa-solid fa-arrows-rotate text-sm"></i>
                                </button>
                            </div>
                        @else
                            <!-- Sin Cultivo Seleccionado -->
                            <button type="button" @click="openCultivos = !openCultivos"
                                    class="w-full flex items-center justify-between bg-blue-50/50 dark:bg-blue-950/20 border-2 border-dashed border-blue-300 dark:border-blue-800/40 rounded-2xl p-3.5 text-xs font-black uppercase text-slate-500 dark:text-slate-400 hover:border-blue-500 transition-all">
                                <span class="italic">SELECCIONE CULTIVO...</span>
                                <i class="fa-solid fa-chevron-down text-blue-500 text-xs"></i>
                            </button>
                        @endif

                        <!-- Dropdown Modal de Lista de Cultivos con Imágenes -->
                        <div x-show="openCultivos" x-transition class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl z-50 max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1" x-cloak>
                            @foreach(\App\Models\CatalogoCultivo::orderBy('nombre')->get() as $cat)
                                <div wire:click="selectCultivo({{ $cat->id }}, '{{ addslashes($cat->nombre) }}'); openCultivos = false"
                                     class="p-2 rounded-xl flex items-center justify-between cursor-pointer hover:bg-blue-50 dark:hover:bg-white/5 transition-colors">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 border-2 border-blue-500 shrink-0 flex items-center justify-center">
                                            @if($cat->foto_path)
                                                <img src="{{ Storage::url($cat->foto_path) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-blue-600 text-white font-black text-xs flex items-center justify-center">
                                                    {{ mb_substr($cat->nombre, 0, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-black uppercase text-slate-800 dark:text-white truncate leading-tight">{{ $cat->nombre }}</p>
                                            <p class="text-[9px] font-bold text-blue-500 uppercase tracking-wider mt-0.5">
                                                {{ str_replace('_', ' ', strtoupper($cat->tipo_ciclo)) }} • {{ $cat->dias_a_cosecha_promedio ?: 120 }} DÍAS
                                            </p>
                                        </div>
                                    </div>
                                    @if($catalogo_cultivo_id == $cat->id)
                                        <i class="fa-solid fa-check text-blue-500 font-bold mr-2 text-xs"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('catalogo_cultivo_id')" class="mt-1 text-xs" />
                    </div>
                </div>

                <!-- Fila 2: VARIEDAD DEL PRODUCTO & NOMBRE DE LOTE (AUTO) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-100 dark:border-white/5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-wider">VARIEDAD DEL PRODUCTO</label>
                        <input wire:model="variedad" type="text" placeholder="EJ: MORADA, CANCHAN..." class="w-full bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-white/10 rounded-2xl p-3.5 text-xs font-bold uppercase text-slate-800 dark:text-white focus:ring-agri-green outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-black uppercase text-slate-500 tracking-wider">NOMBRE DE LOTE (AUTO)</label>
                            <span class="text-[9px] text-agri-green font-bold flex items-center gap-1">
                                <i class="fa-solid fa-wand-magic-sparkles text-[10px]"></i> Generado por Sistema
                            </span>
                        </div>
                        <div class="relative">
                            <input wire:model="nombre_lote" type="text" readonly tabindex="-1" class="w-full bg-slate-100 dark:bg-slate-800/80 border-2 border-slate-200 dark:border-white/10 rounded-2xl p-3.5 text-xs font-black uppercase text-agri-green italic tracking-widest cursor-not-allowed select-none outline-none">
                            <i class="fa-solid fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                        <x-input-error :messages="$errors->get('nombre_lote')" class="mt-1 text-xs" />
                    </div>
                </div>

                <!-- Fila 3: Área a Sembrar (ha) * & Rendimiento Esp. (tn/ha) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-100 dark:border-white/5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Área a Sembrar (ha) *</label>
                        <input wire:model="area_destinada" type="number" step="0.01" min="0.01" placeholder="0.00" class="w-full bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-white/10 rounded-2xl p-3.5 text-xs font-bold text-slate-800 dark:text-white focus:ring-agri-green outline-none">
                        <x-input-error :messages="$errors->get('area_destinada')" class="mt-1 text-xs" />
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Rendimiento Esp. (tn/ha)</label>
                        <input wire:model="rendimiento_esperado_tn_ha" type="number" step="0.1" min="0" placeholder="0.0" class="w-full bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-white/10 rounded-2xl p-3.5 text-xs font-bold text-slate-800 dark:text-white focus:ring-agri-green outline-none">
                    </div>
                </div>

                <!-- Fila 4: Fecha Planificada & Cosecha Estimada -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-100 dark:border-white/5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Fecha Planificada *</label>
                        <input wire:model.live="fecha_planificada" type="date" class="w-full bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-white/10 rounded-2xl p-3.5 text-xs font-bold text-slate-800 dark:text-white focus:ring-agri-green outline-none">
                        <x-input-error :messages="$errors->get('fecha_planificada')" class="mt-1 text-xs" />
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Cosecha Estimada</label>
                        <input wire:model="fecha_cosecha_estimada" type="date" class="w-full bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-white/10 rounded-2xl p-3.5 text-xs font-bold text-slate-800 dark:text-white focus:ring-agri-green outline-none">
                    </div>
                </div>

                @if($cropId)
                <div class="space-y-1.5 pt-2">
                    <label class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Estado de la Campaña</label>
                    <select wire:model="estado" class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-white/10 rounded-2xl p-3.5 text-xs font-bold uppercase text-slate-800 dark:text-white">
                        <option value="Planificado">Planificado</option>
                        <option value="En crecimiento">En Crecimiento</option>
                        <option value="Cosechado">Cosechado</option>
                        <option value="Perdido">Perdido</option>
                    </select>
                </div>
                @endif

                <!-- Fila 5: Observaciones Generales -->
                <div class="space-y-1.5 pt-2 border-t border-slate-100 dark:border-white/5">
                    <label class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Observaciones Generales</label>
                    <textarea wire:model="observaciones" placeholder="Notas sobre el cultivo..." class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-slate-200 dark:border-white/10 rounded-2xl p-3.5 text-xs font-semibold text-slate-800 dark:text-white min-h-[90px] shadow-inner focus:ring-agri-green outline-none"></textarea>
                </div>

                <!-- Fila 6: EVIDENCIA FOTOGRÁFICA -->
                <div class="space-y-3 pt-2">
                    <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest italic">EVIDENCIA FOTOGRÁFICA</label>
                    <div class="flex items-center space-x-5 bg-slate-50 dark:bg-white/5 p-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-white/10 shadow-inner">
                        <div class="w-20 h-16 rounded-xl overflow-hidden bg-white dark:bg-slate-800 shrink-0 shadow-md border-2 border-white flex items-center justify-center">
                            @if($cropPhoto)
                                @php
                                    $tempUrl = null;
                                    try {
                                        if (method_exists($cropPhoto, 'temporaryUrl')) {
                                            $tempUrl = $cropPhoto->temporaryUrl();
                                        }
                                    } catch (\Throwable $e) {
                                        $tempUrl = null;
                                    }
                                @endphp
                                @if($tempUrl)
                                    <img src="{{ $tempUrl }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-agri-green"><i class="fa-solid fa-file-image text-2xl"></i></div>
                                @endif
                            @elseif($currentPhotoPath)
                                <img src="{{ Storage::url($currentPhotoPath) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fa-solid fa-camera text-xl"></i></div>
                            @endif
                        </div>
                        <div class="flex-1 flex items-center gap-3">
                            <input type="file" id="cropPhotoInput" wire:model="cropPhoto" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden"/>
                            <label for="cropPhotoInput" class="px-5 py-2.5 bg-agri-green hover:bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer shadow-md inline-block">
                                SELECCIONAR ARCHIVO
                            </label>
                            <span class="text-[10px] font-bold text-slate-400 truncate">
                                {{ $cropPhoto ? $cropPhoto->getClientOriginalName() : ($currentPhotoPath ? 'Imagen cargada' : 'Ningún archivo seleccionado') }}
                            </span>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('cropPhoto')" class="mt-1 text-xs" />
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center justify-center sm:justify-end gap-4 pt-6 border-t border-slate-100 dark:border-white/5">
                    <button type="button" @click="$dispatch('close')" class="px-8 py-3 bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-200 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                        CANCELAR
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="px-10 py-3 bg-agri-green hover:bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up" wire:loading.remove></i>
                        <i class="fa-solid fa-spinner fa-spin" wire:loading></i>
                        <span>CONFIRMAR SIEMBRA</span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- MODAL DE INFORME Y DETALLES DEL CULTIVO (Invocado al tocar Punto 1) -->
    <x-modal name="modal-crop-report" :show="false" maxWidth="2xl" focusable>
        @if($selectedCropForReport)
        <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <!-- Header Informe -->
            <div class="bg-[#003a38] px-8 py-6 flex justify-between items-center text-white relative overflow-hidden">
                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-agri-green text-white flex items-center justify-center text-lg font-bold shadow-md">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black italic uppercase tracking-tight">
                            {{ $selectedCropForReport->detalleCatalogo->nombre }} (LOTE: {{ $selectedCropForReport->nombre_lote }})
                        </h3>
                        <p class="text-[10px] text-agri-green font-black uppercase tracking-widest mt-0.5">
                            Variedad: {{ $selectedCropForReport->variedad ?: 'Genérica' }} • Terreno: {{ $selectedCropForReport->terreno->nombre }}
                        </p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors relative z-10">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar">
                <!-- Grid de Métricas del Cultivo -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Área Destinada</p>
                        <p class="text-base font-black text-slate-800 dark:text-white italic">{{ number_format($selectedCropForReport->area_destinada, 2) }} Ha</p>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Estado</p>
                        <p class="text-base font-black text-agri-green italic uppercase">{{ $selectedCropForReport->estado }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Fecha Siembra</p>
                        <p class="text-xs font-black text-slate-800 dark:text-white italic">
                            {{ $selectedCropForReport->fecha_siembra ? $selectedCropForReport->fecha_siembra->format('d/m/Y') : ($selectedCropForReport->fecha_planificada ? $selectedCropForReport->fecha_planificada->format('d/m/Y') : '---') }}
                        </p>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Fecha Cosecha</p>
                        <p class="text-xs font-black text-slate-800 dark:text-white italic">
                            {{ $selectedCropForReport->fecha_cosecha_finalizada ? $selectedCropForReport->fecha_cosecha_finalizada->format('d/m/Y') : ($selectedCropForReport->fecha_cosecha_estimada ? $selectedCropForReport->fecha_cosecha_estimada->format('d/m/Y') : '---') }}
                        </p>
                    </div>
                </div>

                <!-- Balance Económico y Producción -->
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="p-5 bg-emerald-500/5 dark:bg-emerald-500/10 rounded-2xl border border-emerald-500/20 space-y-1">
                        <p class="text-[10px] font-black text-agri-green uppercase tracking-widest">Ingresos por Ventas</p>
                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 italic">
                            S/ {{ number_format($reportData['ingresosTotales'] ?? 0, 2) }}
                        </p>
                    </div>

                    <div class="p-5 bg-amber-500/5 dark:bg-amber-500/10 rounded-2xl border border-amber-500/20 space-y-1">
                        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Inversión en Labores</p>
                        <p class="text-2xl font-black text-amber-600 dark:text-amber-400 italic">
                            S/ {{ number_format(($reportData['costoInsumos'] ?? 0) + ($reportData['costoManoObra'] ?? 0) + ($reportData['costoMaquinaria'] ?? 0), 2) }}
                        </p>
                    </div>
                </div>

                @if($selectedCropForReport->observaciones)
                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl space-y-1">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Observaciones</p>
                    <p class="text-xs font-medium text-slate-700 dark:text-slate-300 italic">"{{ $selectedCropForReport->observaciones }}"</p>
                </div>
                @endif
            </div>

            <div class="p-6 bg-slate-50 dark:bg-black/40 border-t border-slate-100 dark:border-white/5 flex justify-end">
                <button type="button" @click="$dispatch('close')" class="px-8 py-2.5 bg-[#173B27] hover:bg-[#245337] text-white rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                    Cerrar Informe
                </button>
            </div>
        </div>
        @endif
    </x-modal>
</div>

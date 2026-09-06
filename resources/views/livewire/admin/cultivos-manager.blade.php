<div class="space-y-8 p-4 md:p-1 transition-colors duration-500">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-100 dark:border-white/5 pb-6">
        <div class="flex items-center space-x-5">
            <div class="w-16 h-16 bg-[#003a38] rounded-2xl flex items-center justify-center shadow-2xl border border-white/10">
                <i class="fa-solid fa-seedling text-agri-green text-3xl"></i>
            </div>
            <div>
                <h2 class="text-4xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">Gestión de Cultivos</h2>
                <p class="text-[10px] text-agri-green font-black uppercase tracking-[0.3em] mt-2 italic">Control de Campañas y Lotes</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Filtros de Fecha en Header (Mover a Punto 3) -->
            <div class="flex items-center bg-white dark:bg-slate-900 px-4 py-2 rounded-2xl border border-slate-100 dark:border-white/5 shadow-sm space-x-2">
                <span class="text-[9px] font-black text-slate-400 uppercase italic">Desde:</span>
                <input type="date" wire:model.live="filterDateStart" class="bg-transparent border-none p-0 text-[10px] font-black text-slate-600 dark:text-slate-300 focus:ring-0 outline-none w-28 uppercase">
                <div class="w-px h-4 bg-slate-100 dark:bg-white/10"></div>
                <span class="text-[9px] font-black text-slate-400 uppercase italic">Hasta:</span>
                <input type="date" wire:model.live="filterDateEnd" class="bg-transparent border-none p-0 text-[10px] font-black text-slate-600 dark:text-slate-300 focus:ring-0 outline-none w-28 uppercase">
            </div>

            <button wire:click="openCreateModal" class="px-10 py-3 bg-agri-green text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all italic">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo Registro
            </button>
        </div>
    </div>

    <!-- Filtros Searchables (Compact agrosys_cultivos_v2) -->
    <div class="space-y-4">
        @if($filterTerrenoId && $filteredTerreno)
            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-white/5 px-6 py-4 rounded-[2rem] animate-in fade-in slide-in-from-top-4 duration-500 shadow-xl">
                <div class="flex items-center gap-6">
                    <div class="w-14 h-14 rounded-2xl overflow-hidden shadow-2xl border-2 border-white dark:border-white/10 shrink-0">
                        <img src="{{ $filteredTerreno->foto_path ? Storage::url($filteredTerreno->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($filteredTerreno->nombre).'&background=003a38&color=fff' }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] leading-none">Filtrando por Terreno</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase italic tracking-tighter leading-none">
                                {{ number_format($filteredTerreno->hectareas, 2) }} HA DEL TERRENO {{ $filteredTerreno->nombre }}
                                <span class="text-agri-green text-sm ml-2">({{ number_format($filteredTerreno->area_disponible, 2) }} Ha. disponibles)</span>
                            </h4>

                            @php
                                $tenureClass = 'bg-emerald-500';
                                $tenureText = strtoupper($filteredTerreno->tipo_tenencia);

                                if($filteredTerreno->tipo_tenencia === 'alquilado') {
                                    if($filteredTerreno->is_alquiler_vencido) {
                                        $tenureClass = 'bg-rose-500 animate-pulse';
                                        $tenureText = 'ALQUILER FINALIZADO';
                                    } else {
                                        $tenureClass = 'bg-amber-500';
                                    }
                                }
                            @endphp

                            <span class="{{ $tenureClass }} text-white px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-lg">
                                {{ $tenureText }}
                            </span>

                            @if($filteredTerreno->tipo_tenencia === 'alquilado')
                                <span class="text-[10px] font-black text-slate-400 italic">
                                    ({{ $filteredTerreno->fecha_alquiler ? $filteredTerreno->fecha_alquiler->format('d/m/Y') : 'N/A' }} - {{ $filteredTerreno->fecha_vencimiento_alquiler ? $filteredTerreno->fecha_vencimiento_alquiler->format('d/m/Y') : 'N/A' }})
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <button wire:click="$set('filterTerrenoId', null)" class="px-6 py-2.5 bg-white dark:bg-slate-800 text-rose-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-50 transition-all shadow-sm border border-rose-100 dark:border-rose-900/20 flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark"></i> Quitar Filtro
                </button>
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-2 p-1.5 bg-white/40 dark:bg-slate-900/40 rounded-2xl border border-slate-100 dark:border-white/5 shadow-lg backdrop-blur-md">
        <!-- Terreno -->
        <div class="relative min-w-[140px] flex-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-mountain-sun text-[9px]"></i></span>
            <input list="terrenos-list" wire:model.live.debounce.300ms="searchTerreno" placeholder="TERRENO..." class="w-full pl-8 pr-2 py-2 bg-slate-50 dark:bg-white/5 border-none rounded-xl text-[9px] font-black uppercase focus:ring-1 focus:ring-agri-green/20 outline-none transition-all italic shadow-inner">
            <datalist id="terrenos-list">@foreach($misTerrenos as $t) <option value="{{ $t->nombre }}"> @endforeach</datalist>
        </div>
        <!-- Cultivo -->
        <div class="relative min-w-[140px] flex-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-leaf text-[9px]"></i></span>
            <input list="crops-catalog-list" wire:model.live.debounce.300ms="searchCultivo" placeholder="CULTIVO..." class="w-full pl-8 pr-2 py-2 bg-slate-50 dark:bg-white/5 border-none rounded-xl text-[9px] font-black uppercase focus:ring-1 focus:ring-agri-green/20 outline-none transition-all italic shadow-inner">
            <datalist id="crops-catalog-list">@foreach($catalogo as $cat) <option value="{{ $cat->nombre }}"> @endforeach</datalist>
        </div>
        <!-- Variedad -->
        <div class="relative min-w-[100px] flex-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-tags text-[9px]"></i></span>
            <input wire:model.live.debounce.300ms="searchVariedad" placeholder="VARIEDAD..." class="w-full pl-8 pr-2 py-2 bg-slate-50 dark:bg-white/5 border-none rounded-xl text-[9px] font-black uppercase focus:ring-1 focus:ring-agri-green/20 outline-none transition-all italic shadow-inner">
        </div>
        <!-- Estado -->
        <div class="relative min-w-[150px] flex-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-circle-info text-[9px]"></i></span>
            <select wire:model.live="filterStatus" class="w-full pl-8 pr-6 py-2 bg-slate-50 dark:bg-white/5 border-none rounded-xl text-[9px] font-black uppercase focus:ring-1 focus:ring-agri-green/20 outline-none transition-all italic appearance-none cursor-pointer shadow-inner">
                <option value="">TODOS LOS ESTADOS</option>
                <option value="Planificado">PLANIFICADO</option>
                <option value="En crecimiento">EN CRECIMIENTO</option>
                <option value="Cosechado">COSECHADO</option>
                <option value="Perdido">PERDIDO</option>
            </select>
        </div>
        <!-- Reset -->
        <button wire:click="resetFilters" class="px-5 py-2 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-xl font-black text-[9px] uppercase hover:bg-slate-200 transition-all italic flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-undo-alt"></i> RESET
        </button>
    </div>

    <!-- Chart de Rendimiento (Rediseño agrosys_cultivos_v2) -->
    <div class="animate-in fade-in duration-700">
        <div class="bg-white/90 dark:bg-slate-900/80 p-6 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col">

            <!-- Cabecera de Controles del Gráfico -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-4 px-2">
                <!-- Selector Profesional al 30% (agrosys_cultivos_v2) -->
                <div class="relative w-full lg:w-[30%] min-w-[200px]">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center text-agri-green pointer-events-none">
                        <i class="fa-solid fa-chart-simple text-sm"></i>
                    </div>
                    <select wire:model.live="chartType"
                            class="w-full pl-12 pr-12 py-2.5 bg-white dark:bg-slate-800 border-2 border-agri-green/20 dark:border-agri-green/10 rounded-2xl text-[12px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-agri-green/10 focus:border-agri-green outline-none cursor-pointer transition-all shadow-sm hover:shadow-md appearance-none">
                        <option value="hist_tn">📊 REND. HISTÓRICO (TN/HA)</option>
                        <option value="hist_inv">💰 REND. HISTÓRICO (INV/GAN)</option>
                        <option value="real_inv">📈 REND. REAL (INV/GAN)</option>
                        <option value="real_tn">🌱 REND. REAL (TN/HA)</option>
                    </select>
                    <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-caret-down text-xs"></i>
                    </div>
                </div>

                <!-- Título Dinámico Posicionado Arriba -->
                <div class="text-right flex flex-col items-end border-r-4 border-agri-green pr-5 py-0.5">
                    <span class="text-[8px] font-black text-slate-400 dark:text-white/40 uppercase tracking-[0.3em] mb-1 leading-none">Indicador Seleccionado</span>
                    <span class="text-[11px] font-black text-agri-green uppercase italic tracking-tighter drop-shadow-sm leading-none">
                        {{ $chartData['title'] }}
                    </span>
                </div>
            </div>

            <!-- Contenedor del Chart (Optimizado sin espacios) -->
            <div class="relative w-full h-[260px]">
                <div data-react-component="agro-bar-chart"
                     data-props="{{ json_encode(['data' => $chartData]) }}"
                     wire:key="chart-{{ $chartType }}-{{ count($chartData['values']) }}"
                     class="w-full h-full"
                     wire:ignore.self></div>
            </div>
        </div>
    </div>


    <!-- Grid de Cultivos Premium -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
        @foreach($cultivos as $cultivo)
        @php
            $temp = rand(18, 26); $hmd = rand(55, 75); // Ficticios
            $siembra = \Carbon\Carbon::parse($cultivo->fecha_siembra);
            $cosecha = $cultivo->fecha_cosecha_estimada ? \Carbon\Carbon::parse($cultivo->fecha_cosecha_estimada) : $siembra->copy()->addMonths(4);
            $totalDias = $siembra->diffInDays($cosecha);
            $diasTranscurridos = $siembra->diffInDays(now(), false);
            $progreso = $totalDias > 0 ? max(0, min(100, ($diasTranscurridos / $totalDias) * 100)) : 0;
            $colorProgreso = $progreso < 30 ? 'bg-emerald-300' : ($progreso < 70 ? 'bg-amber-400' : 'bg-agri-green');
        @endphp

        <div class="bg-white dark:bg-slate-900 rounded-[3rem] overflow-hidden shadow-2xl border border-slate-100 dark:border-white/5 group relative transition-all duration-700 hover:-translate-y-2" x-data="{ menuOpen: false }">

            <!-- IMAGEN CON DATOS INTEGRADOS -->
            <div class="h-72 bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
                @if($cultivo->foto_path)
                    <img src="{{ Storage::url($cultivo->foto_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-200 bg-slate-50 dark:bg-white/5 group-hover:bg-slate-100 transition-colors"><i class="fa-solid fa-leaf text-8xl"></i></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/30 to-transparent"></div>

                <!-- Overlay de Gestionar Labores (Hover Premium) -->
                <a href="{{ route('admin.labores', ['filterCropId' => $cultivo->id, 'strict' => 1]) }}"
                   class="absolute inset-0 z-20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500 bg-[#003a38]/40 backdrop-blur-[2px]">
                    <div class="px-8 py-3 bg-agri-green text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl transform translate-y-4 group-hover:translate-y-0 scale-90 group-hover:scale-100 transition-all duration-500 italic flex items-center gap-3 border border-white/20">
                        <i class="fa-solid fa-gears text-sm animate-spin-slow"></i>
                        Gestionar Labores
                    </div>
                </a>

                <!-- 1. Clima (Fondo Blanco) -->
                <div class="absolute top-5 left-5 z-30 space-y-2">
                    <div class="flex items-center space-x-2 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md px-3.5 py-1.5 rounded-xl shadow-2xl border border-white/20">
                        <i class="fa-solid fa-cloud-sun text-amber-500 text-xs"></i>
                        <span class="text-[10px] font-black text-slate-800 dark:text-white italic uppercase leading-none">{{ $temp }}°C | {{ $hmd }}% HR</span>
                    </div>

                    <!-- Indicadores de Fechas (Punto 1 y 2) -->
                    <div class="flex flex-col space-y-1.5">
                        <div class="bg-black/40 backdrop-blur-md px-3 py-1 rounded-lg border border-white/10 w-fit">
                            <p class="text-[11px] font-black text-white uppercase italic tracking-wider leading-none">
                                <i class="fa-solid fa-calendar-check text-agri-green mr-1.5"></i>
                                @if($cultivo->estado === 'Planificado')
                                    siembra planificada el <br> {{ $cultivo->fecha_planificada->format('d/m/Y') }}
                                @else
                                    sembrado el <br> {{ $cultivo->fecha_siembra ? $cultivo->fecha_siembra->format('d/m/Y') : $cultivo->fecha_planificada->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        <div class="bg-black/40 backdrop-blur-md px-3 py-1 rounded-lg border border-white/10 w-fit">
                            <p class="text-[11px] font-black text-white uppercase italic tracking-wider leading-none">
                                <i class="fa-solid fa-calendar-plus text-blue-400 mr-1.5"></i>
                                @if($cultivo->estado === 'Cosechado')
                                    cosechado el <br> {{ $cultivo->fecha_cosecha_finalizada ? $cultivo->fecha_cosecha_finalizada->format('d/m/Y') : date('d/m/Y') }}
                                @else
                                    cosecha planificada el <br>{{ $cultivo->fecha_cosecha_estimada ? $cultivo->fecha_cosecha_estimada->format('d/m/Y') : '---' }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Menú "..." -->
                <div class="absolute top-5 right-5 z-30">
                    <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false" class="w-8 h-8 bg-white/10 backdrop-blur-md border border-white/20 rounded-lg text-white flex items-center justify-center shadow-lg hover:bg-white/30 transition-all">
                        <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                    </button>
                    <div x-show="menuOpen" x-transition class="absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden" x-cloak>
                        <button wire:click="edit({{ $cultivo->id }})" class="w-full px-6 py-4 text-left text-[11px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition-all italic">EDITAR</button>
                        <button wire:click="delete({{ $cultivo->id }})" wire:confirm="¿Borrar?" class="w-full px-6 py-4 text-left text-[11px] font-black uppercase text-rose-500 hover:bg-rose-50 transition-all italic">BORRAR</button>
                    </div>
                </div>

                <!-- DATOS TÉCNICOS (Sobre la imagen) -->
                <div class="absolute bottom-5 left-7 right-7 text-white space-y-4">
                    <div class="flex justify-between items-end">
                        <div class="min-w-0 flex-1 pr-2">
                            <h3 class="text-2xl font-black italic tracking-tighter uppercase leading-none">{{ $cultivo->detalleCatalogo->nombre }}</h3>
                            <p class="text-[11px] font-black text-emerald-400 uppercase tracking-widest italic mt-1">{{ $cultivo->variedad ?: 'VAR. GENERICA' }}</p>
                        </div>
                        <div class="px-3 py-1.5 bg-agri-green rounded-lg text-[11px] font-black italic shadow-lg shrink-0 border border-white/20">{{ number_format($cultivo->area_destinada, 2) }} ha</div>
                    </div>

                    <!-- Fila de Inversión, Riego y Lugar -->
                    <div class="grid grid-cols-3 gap-2 pt-3 border-t border-white/20">
                        <div class="flex flex-col">
                            <span class="text-[7px] font-black text-white/50 uppercase tracking-widest">Inversión Actual</span>
                            <span class="text-[12px] font-black italic text-amber-400">S/ {{ number_format($cultivo->total_inversion, 2) }}</span>
                        </div>
                        <div class="flex flex-col border-l border-white/10 pl-2">
                            <span class="text-[7px] font-black text-white/50 uppercase tracking-widest">Riego</span>
                            <span class="text-[10px] font-bold italic opacity-90 truncate">{{ $cultivo->terreno->fuente_agua }}</span>
                        </div>
                        <div class="flex flex-col border-l border-white/10 pl-2 text-right">
                            <span class="text-[7px] font-black text-white/50 uppercase tracking-widest">Lugar</span>
                            <a href="{{ route('admin.terrenos') }}" class="text-[10px] font-bold italic opacity-90 hover:text-agri-green transition-colors truncate block">
                                {{ $cultivo->terreno->nombre }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LÍNEA DE TIEMPO FENOLÓGICA (Único elemento inferior) -->
            <div wire:click="showCropReport({{ $cultivo->id }})" class="px-8 py-7 space-y-3 bg-white dark:bg-slate-900 cursor-pointer hover:bg-slate-50 dark:hover:bg-white/5 transition-all duration-300">
                <div class="flex justify-between items-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Fenología: {{ strtoupper($cultivo->estado) }}</p>
                    <span class="text-[11px] font-black text-agri-green uppercase tracking-tighter">{{ round($progreso) }}% completo</span>
                </div>
                <div class="w-full h-3 bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden shadow-inner p-0.5 border border-slate-200/50">
                    <div class="h-full {{ $colorProgreso }} transition-all duration-1000 ease-out rounded-full shadow-[0_0_15px_rgba(0,186,46,0.3)]" style="width: {{ $progreso }}%"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Paginación -->
    <div class="mt-12">{{ $cultivos->links() }}</div>

    <!-- MODAL DE REGISTRO (CONSERVANDO DISEÑO COMPLETO) -->
    <x-modal name="modal-crop-manager" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg rounded-[2.5rem] overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10"
             x-data="{ showTerrenos: false, showCultivos: false }">
            <div class="bg-[#003a38] px-10 py-6 flex justify-between items-center text-white">
                <h3 class="text-2xl font-black italic tracking-tighter uppercase">{{ $cropId ? 'Actualizar Ficha Técnica' : 'Nueva Campaña AgroSys' }}</h3>
                <button @click="$dispatch('close')" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <form wire:submit.prevent="save" class="p-10 space-y-8 max-h-[85vh] overflow-y-auto custom-scrollbar">
                <!-- Secciones de búsqueda visual mantenidas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <x-input-label :value="__('1. Seleccionar Terreno *')" class="text-[10px] font-black uppercase text-agri-green tracking-widest" />
                        @if($terreno_id && $selectedTerrenoModel)
                            <div class="flex items-center justify-between bg-emerald-50 dark:bg-white/5 border-2 border-emerald-500/30 rounded-2xl p-4 animate-in zoom-in-95">
                                <div class="flex items-center space-x-4 overflow-hidden">
                                    <img src="{{ $selectedTerrenoModel->foto_path ? Storage::url($selectedTerrenoModel->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($selectedTerrenoModel->nombre).'&background=003a38&color=fff' }}" class="w-12 h-12 rounded-xl object-cover shadow-md">
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $selectedTerrenoModel->nombre }}</p>
                                        <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-tighter">
                                            Total: {{ number_format($selectedTerrenoModel->hectareas, 2) }} ha | Disp: {{ number_format($areaDisponible, 2) }} ha
                                        </p>
                                    </div>
                                </div>
                                @if(!$filterTerrenoId)
                                    <button type="button" wire:click="$set('terreno_id', null)" class="text-slate-400 hover:text-rose-500"><i class="fa-solid fa-rotate-right"></i></button>
                                @else
                                    <div class="text-agri-green opacity-50"><i class="fa-solid fa-lock text-xs"></i></div>
                                @endif
                            </div>
                        @else
                            <div class="relative">
                                <input type="text" wire:model.live="queryTerreno" @focus="showTerrenos = true" @click.away="showTerrenos = false" class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-xs font-bold p-5 shadow-inner" placeholder="Escribe el terreno...">
                                <div x-show="showTerrenos"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="absolute w-full mt-2 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 z-50 overflow-hidden max-h-60 overflow-y-auto custom-scrollbar" x-cloak>
                                    @foreach($resultsTerrenos as $t)
                                    <div wire:click="selectTerreno({{ $t->id }}, '{{ $t->nombre }}', {{ $t->disponible }})" @click="showTerrenos = false" class="flex items-center space-x-4 p-4 hover:bg-emerald-50 dark:hover:bg-white/5 cursor-pointer border-b last:border-0 border-slate-100 dark:border-white/5">
                                        <img src="{{ $t->foto_path ? Storage::url($t->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($t->nombre) }}" class="w-12 h-12 rounded-xl object-cover shadow-sm">
                                        <div class="min-w-0 flex-1"><p class="text-[12px] font-black uppercase truncate text-slate-800 dark:text-white">{{ $t->nombre }}</p><p class="text-[9px] font-bold text-emerald-600 uppercase">Disp: {{ number_format($t->disponible, 2) }} ha</p></div>
                                    </div>
                                    @endforeach
                                    @if(count($resultsTerrenos) === 0 && strlen($queryTerreno) > 0)
                                        <div class="p-4 text-center text-[10px] font-black text-slate-400 uppercase italic">Sin resultados</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <x-input-label :value="__('2. Tipo de Cultivo *')" class="text-[10px] font-black uppercase text-agri-green tracking-widest" />
                        @if($catalogo_cultivo_id && $selectedCultivoModel)
                            <div class="flex items-center justify-between bg-blue-50 dark:bg-white/5 border-2 border-blue-500/30 rounded-2xl p-4 animate-in zoom-in-95">
                                <div class="flex items-center space-x-4 overflow-hidden">
                                    <img src="{{ $selectedCultivoModel->foto_path ? Storage::url($selectedCultivoModel->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($selectedCultivoModel->nombre).'&background=00ba2e&color=fff' }}" class="w-12 h-12 rounded-xl object-cover shadow-md">
                                    <p class="text-[11px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $selectedCultivoModel->nombre }}</p>
                                </div>
                                <button type="button" wire:click="$set('catalogo_cultivo_id', null)" class="text-slate-400 hover:text-rose-500"><i class="fa-solid fa-rotate-right"></i></button>
                            </div>
                        @else
                            <div class="relative">
                                <input type="text" wire:model.live="queryCultivo" @focus="showCultivos = true" @click.away="showCultivos = false" class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-xs font-bold p-5 shadow-inner" placeholder="Busca en el catálogo...">
                                <div x-show="showCultivos"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="absolute w-full mt-2 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 z-50 overflow-hidden max-h-60 overflow-y-auto custom-scrollbar" x-cloak>
                                    @foreach($resultsCatalogo as $cat)
                                    <div wire:click="selectCultivo({{ $cat->id }}, '{{ $cat->nombre }}')" @click="showCultivos = false" class="flex items-center space-x-4 p-4 hover:bg-blue-50 dark:hover:bg-white/5 cursor-pointer border-b last:border-0 border-slate-100 dark:border-white/5">
                                        <img src="{{ $cat->foto_path ? Storage::url($cat->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($cat->nombre).'&background=00ba2e&color=fff' }}" class="w-10 h-10 rounded-xl object-cover shadow-sm">
                                        <p class="text-[12px] font-black uppercase text-slate-800 dark:text-white italic">{{ $cat->nombre }}</p>
                                    </div>
                                    @endforeach
                                    @if(count($resultsCatalogo) === 0 && strlen($queryCultivo) > 0)
                                        <div class="p-4 text-center text-[10px] font-black text-slate-400 uppercase italic">Sin resultados</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Los demás campos técnicos se mantienen abajo según la tabla img -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-slate-50">
                    <div class="space-y-1.5"><x-input-label :value="__('Variedad del Producto')" class="text-[10px] font-black uppercase text-slate-400" /><x-text-input wire:model.live="variedad" type="text" class="block w-full uppercase" placeholder="Ej: Morada, Canchan..." /></div>
                    <div class="space-y-1.5"><x-input-label :value="__('Nombre de Lote (Auto)')" class="text-[10px] font-black text-slate-400" /><x-text-input wire:model="nombre_lote" type="text" readonly class="block w-full bg-slate-50 font-black text-agri-green italic" /></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Área a Sembrar (ha) *')" class="text-[10px] font-black text-slate-400" />
                        <x-text-input wire:model.live="area_destinada" type="number" step="0.01" class="block w-full {{ !$terreno_id ? 'bg-slate-100 opacity-50' : '' }}" :disabled="!$terreno_id" />
                    </div>

                    @if($selectedCultivoModel && $selectedCultivoModel->tipo_ciclo === 'perenne')
                        <div class="space-y-1.5 animate-in slide-in-from-left-4">
                            <x-input-label :value="__('Plantas Estimadas')" class="text-[10px] font-black text-slate-400" />
                            <x-text-input wire:model="plantas_estimadas" type="number" class="block w-full" placeholder="Cant. aprox." />
                        </div>
                    @endif

                    @if($selectedCultivoModel && $selectedCultivoModel->tipo_ciclo === 'ciclo_corto')
                        <div class="space-y-1.5 animate-in slide-in-from-right-4">
                            <x-input-label :value="__('Rendimiento Esp. (tn/ha)')" class="text-[10px] font-black text-slate-400" />
                            <x-text-input wire:model="rendimiento_esperado_tn_ha" type="number" step="0.1" class="block w-full" placeholder="0.0" />
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5"><x-input-label :value="__('Fecha Planificada')" class="text-[10px] font-black text-slate-400" /><x-text-input wire:model="fecha_planificada" type="date" class="block w-full" /></div>
                    <div class="space-y-1.5"><x-input-label :value="__('Cosecha Estimada')" class="text-[10px] font-black text-slate-400" /><x-text-input wire:model="fecha_cosecha_estimada" type="date" class="block w-full" /></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Estado Fenológico')" class="text-[10px] font-black text-slate-400" />
                        <select wire:model="estado" class="block w-full bg-slate-50 border-none rounded-2xl text-xs font-bold p-4">
                            <option value="Planificado">Planificado</option><option value="En crecimiento">En crecimiento</option><option value="Cosechado">Cosechado</option><option value="Perdido">Perdido</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Observaciones Generales')" class="text-[10px] font-black text-slate-400" />
                        <textarea wire:model="observaciones" class="block w-full bg-slate-50 border-none rounded-2xl text-xs font-bold p-4 min-h-[100px]" placeholder="Notas sobre el cultivo..."></textarea>
                    </div>
                </div>

                <!-- SECCION: Foto del Cultivo -->
                <div class="space-y-4 pt-2">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] italic border-b border-slate-100 dark:border-white/5 pb-2">Evidencia Fotográfica</h4>
                    <div class="flex items-center space-x-6 bg-slate-50 dark:bg-white/5 p-6 rounded-3xl border border-dashed border-slate-200 dark:border-white/10">
                    <div class="w-32 h-24 rounded-2xl overflow-hidden bg-white dark:bg-slate-800 shrink-0 shadow-xl border-2 border-white">
                        @if($cropPhoto && method_exists($cropPhoto, 'isPreviewable') && $cropPhoto->isPreviewable())
                            <img src="{{ $cropPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($cropPhoto)
                            <div class="w-full h-full flex flex-col items-center justify-center bg-slate-50 text-slate-400">
                                <i class="fa-solid fa-file-image text-2xl mb-1"></i>
                                <span class="text-[7px] font-black uppercase text-center leading-none px-2">Sin vista previa</span>
                            </div>
                        @elseif($currentPhotoPath)
                            <img src="{{ Storage::url($currentPhotoPath) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fa-solid fa-camera text-3xl"></i></div>
                        @endif
                    </div>
                        <div class="flex-1">
                            <input type="file" wire:model="cropPhoto" class="text-[10px] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-agri-green file:text-white hover:file:bg-emerald-600 transition-all cursor-pointer"/>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-6 pt-10 border-t border-slate-100 dark:border-white/5">
                    <button type="button" @click="$dispatch('close')" class="px-10 py-4 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-slate-200">Cancelar</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-16 py-4 bg-agri-green text-white rounded-2xl font-black text-[12px] uppercase tracking-widest shadow-2xl shadow-agri-green/30 hover:scale-105 active:scale-95 transition-all flex items-center gap-3">
                        <i class="fa-solid fa-cloud-arrow-up" wire:loading.remove></i>
                        <i class="fa-solid fa-spinner fa-spin" wire:loading></i>
                        {{ $cropId ? 'Guardar Cambios' : 'Confirmar Siembra' }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- MODAL DE REPORTE DE CULTIVO -->
    <x-modal name="modal-crop-report" :show="false" focusable>
        @if($selectedCropForReport)
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10 max-w-5xl mx-auto"
             x-init="$watch('show', value => { if(value) { setTimeout(() => window.mountAgroReact(), 500) } })">

            <!-- Header con Imagen de Fondo (Estilo Detalle Terreno) -->
            <div class="relative h-72 w-full ">
                @if($selectedCropForReport->foto_path)
                    <img src="{{ Storage::url($selectedCropForReport->foto_path) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-[#003a38] to-emerald-900 flex items-center justify-center">
                        <i class="fa-solid fa-leaf text-8xl text-white/10"></i>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>

                <!-- 1. Clima (Top Left) -->
                <div class="absolute top-6 left-6 z-30">
                    <div class="flex items-center space-x-2 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md px-4 py-2 rounded-xl shadow-2xl border border-white/20">
                        <i class="fa-solid fa-cloud-sun text-amber-500 text-sm"></i>
                        <span class="text-[11px] font-black text-slate-800 dark:text-white italic uppercase leading-none">{{ rand(18,26) }}°C | {{ rand(55,75) }}% HR</span>
                    </div>
                </div>

                <!-- Botón Cerrar Flotante -->
                <div class="absolute top-6 right-6 z-30">
                    <button @click="$dispatch('close')" class="w-11 h-11 bg-black/20 backdrop-blur-md text-white rounded-full hover:bg-black/40 transition-all flex items-center justify-center border border-white/10">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- 2. Título y Badges Flotantes -->
                <div class="absolute bottom-8 left-10 right-10 p-4 z-30">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="px-3 py-1 bg-agri-green text-white text-[10px] font-black uppercase rounded-lg shadow-lg border border-white/20">
                            Campaña #{{ $selectedCropForReport->id }}
                        </span>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-white text-[10px] font-black uppercase rounded-lg border border-white/10">
                            {{ strtoupper($selectedCropForReport->estado) }}
                        </span>
                    </div>
                    <h2 class="text-6xl font-black text-white italic tracking-tighter uppercase leading-none drop-shadow-2xl mb-4">
                        {{ $selectedCropForReport->detalleCatalogo->nombre }}
                        <span class="text-3xl font-bold text-white/60 ml-4 lowercase">{{ $selectedCropForReport->variedad ?: 'genérica' }}</span>
                    </h2>

                    <div class="flex flex-wrap items-center gap-4">
                        <p class="text-white font-bold text-sm flex items-center gap-2 drop-shadow-md pr-4 border-r border-white/20">
                            <i class="fa-solid fa-location-dot text-agri-green"></i> {{ strtoupper($selectedCropForReport->terreno->nombre) }}
                        </p>

                        <!-- 3. Indicadores de Siembra y Cosecha (Translucidos) -->
                        <div class="flex gap-4">
                            <div class="bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/10">
                                <p class="text-[12px] text-center font-black text-white/50 uppercase leading-none mb-1">Siembra</p>
                                <p class="text-[11px] font-black text-white uppercase italic tracking-wider leading-none">
                                    @if($selectedCropForReport->estado === 'Planificado')
                                        planificada {{ $selectedCropForReport->fecha_planificada->format('d/m/Y') }}
                                    @else
                                        sembrado {{ $selectedCropForReport->fecha_siembra ? $selectedCropForReport->fecha_siembra->format('d/m/Y') : $selectedCropForReport->fecha_planificada->format('d/m/Y') }}
                                    @endif
                                </p>
                            </div>
                            <div class="bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/10">
                                <p class="text-[12px] text-center font-black text-white/50 uppercase leading-none mb-1">Cosecha</p>
                                <p class="text-[11px] font-black text-white uppercase italic tracking-wider leading-none">
                                    @if($selectedCropForReport->estado === 'Cosechado')
                                        cosechado {{ $selectedCropForReport->fecha_cosecha_finalizada ? $selectedCropForReport->fecha_cosecha_finalizada->format('d/m/Y') : date('d/m/Y') }}
                                    @else
                                        planificada {{ $selectedCropForReport->fecha_cosecha_estimada ? $selectedCropForReport->fecha_cosecha_estimada->format('d/m/Y') : '---' }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cuerpo del Informe en 3 Columnas -->
            <div class="p-10 space-y-10 max-h-[65vh] overflow-y-auto custom-scrollbar">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                    <!-- Columna 1: Perfil Operativo -->
                    <div class="space-y-6">
                        <h4 class="text-[11px] font-black text-agri-green uppercase tracking-[0.3em] mb-4 border-b border-slate-100 dark:border-white/5 pb-2">Perfil Operativo</h4>
                        <div class="space-y-5">
                            <div class="flex items-center gap-4 group">
                                <div class="w-12 h-12 bg-slate-50 dark:bg-white/5 rounded-2xl flex items-center justify-center text-agri-green shadow-sm group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-chart-area text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Área Destinada</p>
                                    <p class="text-md font-black text-slate-700 dark:text-slate-300 italic">{{ number_format($selectedCropForReport->area_destinada, 2) }} <span class="text-[11px] opacity-50">HA</span></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 group">
                                <div class="w-12 h-12 bg-slate-50 dark:bg-white/5 rounded-2xl flex items-center justify-center text-blue-500 shadow-sm group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-mountain text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Suelo del Terreno</p>
                                    <p class="text-md font-black text-slate-700 dark:text-slate-300 italic uppercase">{{ $selectedCropForReport->terreno->calidad_suelo }}</p>
                                </div>
                            </div>

                            <!-- 4. Riego en Perfil Operativo -->
                            <div class="flex items-center gap-4 group">
                                <div class="w-12 h-12 bg-slate-50 dark:bg-white/5 rounded-2xl flex items-center justify-center text-emerald-500 shadow-sm group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-droplet text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Sistema de Riego</p>
                                    <p class="text-md font-black text-slate-700 dark:text-slate-300 italic uppercase">{{ $selectedCropForReport->terreno->fuente_agua }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna 2: Distribución de Gastos (5 y 6) -->
                    <div class="space-y-6">
                        <h4 class="text-[11px] font-black text-blue-500 uppercase tracking-[0.3em] mb-4 border-b border-slate-100 dark:border-white/5 pb-2">Distribución de Gastos</h4>
                        <div class="flex flex-col gap-6 bg-slate-50/50 dark:bg-white/5 rounded-[1.5rem] shadow-inner border border-black/5 dark:border-white/5">
                            <!-- 6. Desglose de Texto de Inversión -->
                            <div class="bg-white/80 dark:bg-slate-800/40 p-5  border-slate-100 dark:border-white/5 space-y-4 shadow-sm">
                                <!-- Inversión Total -->
                                <div class="flex flex-col gap-1 border-b border-slate-100 dark:border-white/5 pb-3 mb-3">
                                    <span class="text-[10px] font-black text-slate-400 uppercase italic">Inversión Total:</span>
                                    <span class="text-sm font-black text-slate-800 dark:text-white">
                                        S/ {{ number_format(
                                            $reportData['costoInsumos']
                                            + $reportData['costoManoObra']
                                            + $reportData['costoMaquinaria'],
                                            2
                                        ) }}
                                    </span>
                                </div>
                                <!-- Insumos -->
                                <div class="flex flex-col gap-1">
                                    <span class="text-[9px] font-black text-blue-500 uppercase">Insumos:</span>
                                    <span class="text-xs font-black text-blue-600">S/ {{ number_format($reportData['costoInsumos'], 2) }}</span>
                                </div>
                                <!-- Mano de Obra -->
                                <div class="flex flex-col gap-1">
                                    <span class="text-[9px] font-black text-amber-500 uppercase">Mano de Obra:</span>
                                    <span class="text-xs font-black text-amber-600">S/ {{ number_format($reportData['costoManoObra'], 2) }}</span>
                                </div>
                                <!-- Maquinaria -->
                                <div class="flex flex-col gap-1">
                                    <span class="text-[9px] font-black text-violet-500 uppercase">Maquinaria:</span>
                                    <span class="text-xs font-black text-violet-600">S/ {{ number_format($reportData['costoMaquinaria'], 2) }}</span>
                                </div>
                            </div>
                            <!-- 5. Chart Circular -->
                            <div class="h-64 relative">
                                <div data-react-component="agro-pie-chart"
                                    data-props="{{ json_encode(['data' => $reportData['chart']]) }}"
                                    wire:key="report-pie-final-v4-{{ $selectedCropForReport->id }}-{{ array_sum($reportData['chart']['values']) }}"
                                    class="w-full h-full">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna 3: Balance Económico -->
                    <div class="space-y-6">
                        <h4 class="text-[11px] font-black text-violet-500 uppercase tracking-[0.3em] mb-4 border-b border-slate-100 dark:border-white/5 pb-2">Balance Económico</h4>
                        <div class="space-y-4">
                            <!-- Nuevos Detalles de Cosecha -->
                            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/10 space-y-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5 pb-1 mb-1">Resumen de Producción</p>
                                 <!-- Total Cosechado -->
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] font-black text-slate-500 uppercase italic">Total Cosechado:</span>
                                    <span class="text-sm font-black text-agri-green"> {{ number_format($reportData['cantidadCosechada'], 2) }} {{ strtoupper($reportData['unidadCosecha']) }}</span>
                                </div>
                                <!-- Precio Promedio Venta -->
                                <div class="flex flex-col gap-1">
                                    <span class="text-[12px] font-black text-slate-500 uppercase italic">Precio Promedio Venta:</span>
                                    <span class="text-sm font-black text-blue-500">S/{{ $reportData['cantidadCosechada'] > 0 ? number_format($reportData['ingresosTotales'] / $reportData['cantidadCosechada'], 2): '0.00' }}</span>
                                </div>
                            </div>
                            <div class="p-6 bg-slate-800 dark:bg-black rounded-2xl border border-white/10 shadow-xl overflow-hidden relative group">
                                <div class="absolute right-0 top-0 opacity-10 p-2 transform rotate-12 transition-transform group-hover:rotate-0"><i class="fa-solid fa-vault text-5xl text-white"></i></div>
                                <p class="text-[9px] font-black text-slate-200 uppercase tracking-[0.3em] mb-1 italic">Ganancia Final</p>
                                <h3 class="text-xl font-black {{ $reportData['balance'] >= 0 ? 'text-agri-green' : 'text-rose-500' }} italic tracking-tighter leading-none">S/ {{ number_format($reportData['balance'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 7. Desglose y Observaciones Inferiores -->
                <div class="space-y-6 pt-6 border-t border-slate-100 dark:border-white/5">
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] italic">Resumen de Rubros Operativos</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex items-center gap-5 p-6 bg-slate-50 dark:bg-white/5 rounded-3xl border border-slate-100 dark:border-white/10 shadow-sm">
                            <div class="w-14 h-14 bg-blue-500/10 text-blue-500 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="fa-solid fa-boxes-stacked"></i></div>
                            <div><p class="text-[10px] font-black text-slate-400 uppercase leading-none mb-1">Insumos</p><p class="text-xl font-black text-slate-700 dark:text-white italic leading-none">S/ {{ number_format($reportData['costoInsumos'], 2) }}</p></div>
                        </div>
                        <div class="flex items-center gap-5 p-6 bg-slate-50 dark:bg-white/5 rounded-3xl border border-slate-100 dark:border-white/10 shadow-sm">
                            <div class="w-14 h-14 bg-amber-500/10 text-amber-500 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="fa-solid fa-people-group"></i></div>
                            <div><p class="text-[10px] font-black text-slate-400 uppercase leading-none mb-1">Mano de Obra</p><p class="text-xl font-black text-slate-700 dark:text-white italic leading-none">S/ {{ number_format($reportData['costoManoObra'], 2) }}</p></div>
                        </div>
                        <div class="flex items-center gap-5 p-6 bg-slate-50 dark:bg-white/5 rounded-3xl border border-slate-100 dark:border-white/10 shadow-sm">
                            <div class="w-14 h-14 bg-violet-500/10 text-violet-500 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="fa-solid fa-truck-tractor"></i></div>
                            <div><p class="text-[10px] font-black text-slate-400 uppercase leading-none mb-1">Maquinaria</p><p class="text-xl font-black text-slate-700 dark:text-white italic leading-none">S/ {{ number_format($reportData['costoMaquinaria'], 2) }}</p></div>
                        </div>
                    </div>
                </div>

                @if($selectedCropForReport->observaciones)
                <div class="bg-slate-50 dark:bg-white/5 p-8 rounded-[2.5rem] border-l-4 border-agri-green shadow-inner">
                    <p class="text-[9px] font-black text-agri-green uppercase mb-2 tracking-widest leading-none">Notas Técnicas de Campaña</p>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400 italic">"{{ $selectedCropForReport->observaciones }}"</p>
                </div>
                @endif
            </div>

            <div class="bg-slate-50 dark:bg-black/20 px-10 py-6 flex justify-between items-center border-t border-slate-100 dark:border-white/5">
                <p class="text-[11px] font-black text-slate-400 uppercase italic">Informe generado automáticamente por AgroSys Cloud</p>
                <button @click="$dispatch('close')" class="px-12 py-3 bg-slate-800 hover:bg-black text-white rounded-xl font-black text-[11px] uppercase tracking-widest transition-all italic shadow-xl">Cerrar Informe</button>
            </div>
        </div>
        @endif
    </x-modal>
</div>

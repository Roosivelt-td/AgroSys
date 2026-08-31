<div class="space-y-6 p-4 md:p-1 transition-colors duration-500">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-100 dark:border-white/5 pb-6">
        <div class="flex items-center space-x-5">
            <div class="w-14 h-14 bg-[#003a38] rounded-2xl flex items-center justify-center shadow-2xl border border-white/10">
                <i class="fa-solid fa-map-location-dot text-agri-green text-2xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">Gestión de Terrenos</h2>
                <p class="text-[9px] text-agri-green font-black uppercase tracking-[0.3em] mt-1 italic">Monitorización y Control Geográfico</p>
            </div>
        </div>

        <button @click="$wire.resetForm(); $dispatch('open-modal', 'modal-add-terrain')" class="px-8 py-2.5 bg-agri-green text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all italic">
            <i class="fa-solid fa-plus mr-1"></i> Nuevo Terreno
        </button>
    </div>

    <!-- BARRA DE FILTROS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-white dark:bg-agri-d_bg p-4 rounded-3xl border border-slate-100 dark:border-white/5 shadow-xl">
        <!-- Columna 1: Búsqueda -->
        <div class="relative w-full group">
            <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-slate-400">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar parcela..."
                   class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-[11px] font-bold text-slate-600 dark:text-slate-300 placeholder-slate-400 focus:ring-4 focus:ring-agri-green/10 outline-none transition-all italic">
        </div>

        <!-- Columna 2: Filtro de cultivos -->
        <div class="w-full">
            <input list="crops-list-filters" wire:model.live="filterCrop" placeholder="TODOS LOS CULTIVOS"
                   class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-[10px] font-black uppercase px-6 py-3.5 outline-none focus:ring-4 focus:ring-agri-green/10 transition-all cursor-pointer shadow-inner placeholder:text-slate-500">
            <datalist id="crops-list-filters">
                <option value="Maíz">MAÍZ</option>
                <option value="Tomate">TOMATE</option>
                <option value="Papa">PAPA</option>
                <option value="Palta">PALTA</option>
            </datalist>
        </div>

        <!-- Columna 3: Filtro de área -->
        <div class="w-full">
            <select wire:model.live="filterArea" class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-[10px] font-black uppercase px-6 py-3.5 outline-none focus:ring-4 focus:ring-agri-green/10 transition-all cursor-pointer shadow-inner">
                <option value="">CUALQUIER ÁREA</option>
                <option value="0-5">MENOS DE 5 HA</option>
                <option value="5-10">5 - 10 HA</option>
                <option value="10+">MÁS DE 10 HA</option>
            </select>
        </div>

        <!-- Columna 4: Botón Reset -->
        <div class="flex items-center">
            <button wire:click="resetFilters" class="w-full px-8 py-3.5 bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all italic whitespace-nowrap shadow-sm">
                <i class="fa-solid fa-undo-alt mr-2"></i> Reset
            </button>
        </div>
    </div>

    <!-- Estadísticas y Mapa -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="{{ $activeLandId ? 'lg:col-span-3' : 'lg:col-span-1' }} bg-gradient-to-br from-agri-green to-emerald-800 p-6 rounded-[2.5rem] shadow-2xl relative overflow-hidden group flex flex-col transition-all duration-700">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:rotate-12 transition-transform duration-700"><i class="fa-solid fa-chart-area text-9xl text-white"></i></div>

            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    @if($activeLandId)
                        @php $activeL = \App\Models\Terreno::find($activeLandId); @endphp
                        <p class="text-[9px] font-black text-white/60 uppercase tracking-[0.3em] mb-1">Analizando: {{ $activeL->nombre }}</p>
                        <h4 class="text-4xl font-black text-white italic tracking-tighter">{{ number_format($activeL->hectareas, 1) }} <span class="text-lg font-bold opacity-50 uppercase">ha</span></h4>
                    @else
                        <p class="text-[9px] font-black text-white/60 uppercase tracking-[0.3em] mb-1">Superficie Total</p>
                        <h4 class="text-4xl font-black text-white italic tracking-tighter">{{ number_format($totalArea, 1) }} <span class="text-lg font-bold opacity-50 uppercase">ha</span></h4>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if($activeLandId)
                        <button wire:click="$set('activeLandId', null)" class="bg-white/10 hover:bg-white/20 p-2 rounded-xl text-white transition-all">
                            <i class="fa-solid fa-map mr-2"></i> <span class="text-[8px] font-black uppercase">Volver al Mapa</span>
                        </button>
                    @endif

                    <div class="flex bg-black/20 backdrop-blur-md p-1 rounded-xl border border-white/10">
                        <label class="cursor-pointer group">
                            <input type="radio" wire:model.live="chartPeriod" value="mes" class="hidden peer">
                            <div class="px-3 py-1 rounded-lg text-[8px] font-black uppercase transition-all peer-checked:bg-white peer-checked:text-agri-green text-white/60 hover:text-white">Mes</div>
                        </label>
                        <label class="cursor-pointer group ml-1">
                            <input type="radio" wire:model.live="chartPeriod" value="anio" class="hidden peer">
                            <div class="px-3 py-1 rounded-lg text-[8px] font-black uppercase transition-all peer-checked:bg-white peer-checked:text-agri-green text-white/60 hover:text-white">Año</div>
                        </label>
                    </div>
                </div>
            </div>

            @if(!$activeLandId)
                <div class="mt-2 flex items-center text-white/80 text-[10px] font-bold relative z-10"><i class="fa-solid fa-leaf mr-2 text-white/40"></i> {{ $totalCount }} parcelas registradas</div>
            @endif

            <div class="flex-1 mt-6 min-h-[160px] relative" wire:ignore.self>
                <div data-react-component="agro-land-line-chart"
                     data-props="{{ json_encode(['data' => $lineChartData]) }}"
                     wire:key="land-trend-{{ $chartPeriod }}-{{ count($lineChartData['values']) }}-{{ $activeLandId }}"
                     class="w-full h-full"></div>
            </div>
        </div>

        @if(!$activeLandId)
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-2 rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-2xl min-h-[350px]" wire:ignore>
                <div data-react-component="agro-map-terrenos" data-props="{{ json_encode(['terrenos' => $mapData]) }}" class="w-full h-full rounded-[2rem] overflow-hidden"></div>
            </div>
        @endif
    </div>

    <!-- Grid de Terrenos -->
    <div class="animate-in fade-in duration-500">
        <h2 class="text-xl font-black text-slate-800 dark:text-white italic tracking-tighter mb-6 uppercase"><i class="fa-solid fa-list-ul mr-2 text-agri-green"></i> Mis Terrenos</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($terrenos as $terreno)
            <div class="bg-white dark:bg-[#051110] rounded-[1.5rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-slate-100 dark:border-white/5 group relative" x-data="{ menuOpen: false }">
                <div class="h-48 bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
                    <!-- 1. Capa Base: Imagen y Gradiente -->
                    <a href="{{ route('admin.cultivos', ['filterTerrenoId' => $terreno->id]) }}" class="block w-full h-full relative z-0">
                        @if($terreno->foto_path)
                            <img src="{{ Storage::url($terreno->foto_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 bg-slate-50 dark:bg-[#0a1a19] group-hover:bg-slate-100 transition-colors"><i class="fa-solid fa-mountain-sun text-6xl"></i></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                    </a>

                    <!-- 2. Información Visual (Clima y Texto) - SE OPACA EN HOVER -->
                    <div class="group-hover:opacity-30 transition-all duration-500 pointer-events-none">
                        <!-- Clima -->
                        <div class="absolute top-4 left-4 flex items-center space-x-2 bg-white/90 dark:bg-[#051110]/90 backdrop-blur-md px-3 py-1.5 rounded-xl shadow-xl border border-white/20 z-10">
                            <i class="fa-solid fa-cloud-sun text-amber-500 text-xs animate-pulse"></i>
                            <span class="text-[9px] font-black text-slate-800 dark:text-white italic uppercase tracking-tighter leading-none">24°C | 65% HR</span>
                        </div>

                        <!-- Info Inferior -->
                        <div class="absolute bottom-4 left-6 right-6 flex justify-between items-end text-white z-10">
                            <div class="min-w-0 flex-1 pr-4">
                                <div class="flex flex-col space-y-1 mb-2">
                                    <div class="flex flex-col items-start space-y-1">
                                        <span class="bg-agri-green/90 backdrop-blur-md px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-tighter text-white border border-white/20 shadow-sm">
                                            Sembrados: {{ $terreno->sembr_count }}
                                        </span>

                                        <span class="bg-blue-600/90 backdrop-blur-md px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-tighter text-white border border-white/20 shadow-sm">
                                            Planificados: {{ $terreno->plan_count }}
                                        </span>
                                    </div>

                                    @if($terreno->hist_count > 0)
                                        <div class="flex">
                                            <span class="bg-slate-700/80 backdrop-blur-md px-2 py-0.5 rounded-md text-[7px] font-black uppercase tracking-tighter text-white/70 border border-white/10 italic">
                                                Inactivos: {{ $terreno->hist_count }} (C/P)
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <h3 class="text-[20px] font-black italic truncate tracking-tight uppercase leading-none text-white drop-shadow-lg">{{ $terreno->nombre }}</h3>
                                <p class="text-[11px] font-bold uppercase italic opacity-90 truncate mt-1 text-white/80"><i class="fa-solid fa-location-dot mr-1"></i> {{ $terreno->ubicacion }}</p>
                            </div>
                            <div class="flex flex-col items-end shrink-0">
                                <div class="px-3 py-1 bg-agri-green rounded-md text-[11px] font-black italic shadow-lg border border-white/20 uppercase">TOTAL: {{ number_format($terreno->hectareas, 2) }} ha</div>
                                <div class="text-[9px] font-black text-white/70 uppercase italic mt-1 drop-shadow-md text-right">USO: {{ number_format($terreno->area_ocupada, 2) }} HA<br>DISP: {{ number_format($terreno->area_disponible, 2) }} HA</div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Overlay Central (Botón de Gestión) -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 z-20 pointer-events-none bg-black/40 backdrop-blur-[2px]">
                        <a href="{{ route('admin.cultivos', ['filterTerrenoId' => $terreno->id]) }}" class="pointer-events-auto transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <span class="bg-agri-green text-white px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.2em] italic shadow-[0_0_40px_rgba(16,185,129,0.4)] border border-white/20 flex items-center gap-3">
                                <i class="fa-solid fa-seedling text-sm"></i> Gestionar Cultivos
                            </span>
                        </a>
                    </div>

                    <!-- 4. Acciones (Top Right) -->
                    <div class="absolute top-4 right-4 z-30 flex space-x-2">
                        <button @click="$wire.set('activeLandId', null); setTimeout(() => window.dispatchEvent(new CustomEvent('map-fly-to', { detail: { lat: {{ $terreno->latitud }}, lng: {{ $terreno->longitud }} } })), 100)"
                                class="w-9 h-9 bg-white/20 backdrop-blur-md border border-white/30 rounded-xl text-white hover:bg-agri-green transition-all shadow-lg flex items-center justify-center hover:scale-110 active:scale-90">
                            <i class="fa-solid fa-location-dot text-sm"></i>
                        </button>

                        <div class="relative">
                            <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false" class="w-9 h-9 bg-white/20 backdrop-blur-md border border-white/30 rounded-xl text-white hover:bg-white/40 transition-all flex items-center justify-center shadow-lg hover:scale-110 active:scale-90"><i class="fa-solid fa-ellipsis-vertical text-xs"></i></button>
                            <div x-show="menuOpen" x-transition class="absolute right-0 mt-2 w-24 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 dark:border-white/10 overflow-hidden z-40">
                                <button wire:click="edit({{ $terreno->id }})" class="w-full px-3 py-2 text-left text-[10px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-all"><i class="fa-solid fa-edit mr-2 text-blue-500"></i> Editar</button>
                                <button wire:click="delete({{ $terreno->id }})" wire:confirm="¿Borrar?" class="w-full px-3 py-2 text-left text-[10px] font-black uppercase text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/10 transition-all"><i class="fa-solid fa-trash-can mr-2"></i> Borrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN DE INFORMACIÓN TÉCNICA (INTERACTIVA) -->
                <div wire:click="selectActiveLand({{ $terreno->id }})" class="p-2 space-y-4 bg-white dark:bg-[#051110] cursor-pointer hover:bg-slate-50 dark:hover:bg-white/5 transition-all duration-300 active:scale-[0.98] group/info relative overflow-hidden">
                    <!-- Efecto de Brillo al Hover -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-agri-green/0 via-agri-green/5 to-agri-green/0 opacity-0 group-hover/info:opacity-100 transition-opacity"></div>

                    <div class="grid grid-cols-2 gap-4 relative z-10">
                        <!-- Columna Izquierda: Información Técnica -->
                        <div class="space-y-4">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none mb-3">Información Técnica</p>
                                <div class="space-y-2.5">
                                    <div class="flex items-center space-x-2 text-[12px] font-bold text-slate-700 dark:text-slate-300 italic group-hover/info:text-agri-green transition-colors">
                                        <i class="fa-solid fa-mountain text-agri-green w-4 text-center"></i>
                                        <span>{{ ucfirst($terreno->calidad_suelo) }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 text-[12px] font-bold text-slate-700 dark:text-slate-300 italic group-hover/info:text-blue-500 transition-colors">
                                        <i class="fa-solid fa-droplet text-blue-500 w-4 text-center"></i>
                                        <span>{{ $terreno->fuente_agua }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna Derecha: Situación Legal -->
                        <div class="space-y-5 text-right border-l border-slate-100 dark:border-white/5 pl-4">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none mb-3">Situación Legal</p>
                                <span class="inline-block px-3 py-1.5 bg-slate-50 dark:bg-[#0a1a19] border border-slate-200 dark:border-white/10 rounded-lg text-[10px] font-black text-agri-green uppercase tracking-widest italic shadow-sm group-hover/info:bg-agri-green group-hover/info:text-white transition-all">
                                    {{ strtoupper($terreno->tipo_tenencia) }}
                                </span>

                                @if($terreno->organizacion_id)
                                    <div class="mt-2">
                                        <span class="inline-block px-2 py-1 bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 rounded-md text-[8px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-tighter italic">
                                            {{ $terreno->organizacion->nombre }}
                                        </span>
                                    </div>
                                @endif

                                @if($terreno->tipo_tenencia === 'alquilado')
                                    <div class="mt-2 space-y-0.5">
                                        <p class="text-[10px] font-black text-amber-500 italic group-hover/info:scale-110 transition-transform origin-right">S/ {{ number_format($terreno->costo_alquiler_anual, 2) }}</p>
                                        <p class="text-[10px] font-black text-slate-400 uppercase italic">Vence: {{ $terreno->fecha_vencimiento_alquiler ? $terreno->fecha_vencimiento_alquiler->format('d/m/Y') : 'N/A' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-12">{{ $terrenos->links() }}</div>
    </div>

    <!-- MODAL DE AGREGAR/EDITAR -->
    <x-modal name="modal-add-terrain" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg rounded-2xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10"
             x-init="$watch('show', value => { if(value) { setTimeout(() => window.mountAgroReact(), 500) } })"
             x-on:map-polygon-updated.window="
                $wire.set('landLat', $event.detail.center.lat, false);
                $wire.set('landLng', $event.detail.center.lng, false);
                $wire.set('landArea', $event.detail.area, false);
                $wire.landPolygon = JSON.stringify($event.detail.points);
                if($event.detail.address) $wire.set('landLocation', $event.detail.address, false);
             ">

            <div class="bg-[#003a38] px-8 py-4 flex justify-between items-center text-white border-b border-white/5">
                <h3 class="text-xl font-black italic tracking-tighter uppercase">{{ $landId ? 'Editar Parcela' : 'Mapeo Técnico de Parcela' }}</h3>
                <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form wire:submit.prevent="save" class="p-8 space-y-8 max-h-[85vh] overflow-y-auto custom-scrollbar">

                <!-- SECCION MAPA DIBUJO -->
                <div class="space-y-4" wire:ignore>
                    <div class="flex justify-between items-end border-b border-slate-100 dark:border-white/5 pb-2">
                        <h4 class="text-[10px] font-black text-agri-green uppercase tracking-[0.3em] italic">Delimitación Poligonal</h4>
                        <span class="text-[10px] font-black text-slate-400">Puntos marcados: <span class="text-agri-green" x-text="$wire.landPolygon ? JSON.parse($wire.landPolygon).length : 0"></span></span>
                    </div>
                    <div class="w-full h-96 rounded-3xl overflow-hidden border-2 border-agri-green/20 shadow-2xl relative">
                        <div data-react-component="agro-map-terrenos"
                             data-props="{{ json_encode([
                                'drawMode' => true,
                                'editingId' => $landId,
                                'tenure' => $landTenure,
                                'terrenos' => $mapData,
                                'initialPoints' => $landPolygon ? json_decode($landPolygon) : null,
                                'center' => ($landLat && $landLng) ? ['lat' => (float)$landLat, 'lng' => (float)$landLng] : null
                             ]) }}"
                             wire:key="draw-map-{{ $landId ?? 'new' }}"
                             class="w-full h-full"></div>
                        <div class="absolute top-4 right-4 bg-black/60 backdrop-blur-md px-4 py-2 rounded-xl text-[9px] font-black text-white uppercase z-[2] flex flex-col gap-1">
                            <span><i class="fa-solid fa-mouse-pointer mr-2"></i>Clic: Añadir punto</span>
                            <span><i class="fa-solid fa-draw-polygon mr-2"></i>Mínimo: 3 puntos para área</span>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN ORGANIZACIÓN -->
                @if($misOrganizaciones->count() > 0)
                <div class="space-y-4">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] italic border-b border-slate-100 dark:border-white/5 pb-2">Asignación de Organización</h4>
                    <div class="grid grid-cols-1 gap-6">
                        <div class="space-y-1.5">
                            <x-input-label for="selectedOrgId" :value="__('Vincular a Organización')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                            <select wire:model="selectedOrgId" id="selectedOrgId" class="block w-full bg-slate-50 dark:bg-white/5 border-none rounded-xl text-xs font-bold focus:ring-4 focus:ring-agri-green/10">
                                <option value="">NINGUNA (TERRENO PERSONAL)</option>
                                @foreach($misOrganizaciones as $org)
                                    <option value="{{ $org->id }}">{{ strtoupper($org->nombre) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endif

                <!-- SECCION 1: Identificación -->
                <div class="space-y-4">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] italic border-b border-slate-100 dark:border-white/5 pb-2">Información del Predio</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <x-input-label for="landName" :value="__('Nombre de la parcela *')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                            <x-text-input wire:model="landName" id="landName" type="text" class="block w-full" placeholder="Ej: Fundo Los Olivos" required />
                        </div>
                        <div class="space-y-1.5">
                            <x-input-label for="landArea" :value="__('Área (Hectáreas) *')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                            <x-text-input wire:model="landArea" id="landArea" type="number" step="0.1" class="block w-full" placeholder="0.0" required />
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <x-input-label for="landLocation" :value="__('Ubicación Detectada (Auto)')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-agri-green"><i class="fa-solid fa-map-pin"></i></span>
                                <input wire:model="landLocation" type="text" readonly class="w-full pl-12 pr-4 py-3.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 italic outline-none">
                            </div>
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <x-input-label for="landDirRef" :value="__('Dirección de Referencia / Notas')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                            <x-text-input wire:model="landDirRef" id="landDirRef" type="text" class="block w-full" placeholder="Cerca al río, portón verde..." />
                        </div>
                    </div>
                </div>

                <!-- SECCION 2: Tenencia -->
                <div class="space-y-4 pt-2">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] italic border-b border-slate-100 dark:border-white/5 pb-2">Tenencia y Costos</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <x-input-label for="landTenure" :value="__('Tipo de Tenencia')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                            <select wire:model.live="landTenure" id="landTenure" class="block w-full bg-slate-50 dark:bg-white/5 border-none rounded-xl text-xs font-bold focus:ring-4 focus:ring-agri-green/10">
                                <option value="propio">Propio (Título)</option>
                                <option value="alquilado">Alquilado (Contrato)</option>
                                <option value="comunal">Comunal</option>
                            </select>
                        </div>

                        @if($landTenure === 'alquilado')
                        <div class="space-y-1.5 animate-in slide-in-from-top-2">
                            <x-input-label for="landRentCost" :value="__('Costo Alquiler Anual (S/)')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                            <x-text-input wire:model="landRentCost" id="landRentCost" type="number" class="block w-full" />
                        </div>

                        <div class="space-y-1.5 animate-in slide-in-from-top-2">
                            <x-input-label for="landRentMod" :value="__('Modalidad de Alquiler')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                            <select wire:model.live="landRentMod" id="landRentMod" class="block w-full bg-slate-50 dark:bg-white/5 border-none rounded-xl text-xs font-bold">
                                <option value="global">Global</option>
                                <option value="por_campana">Por Campaña</option>
                            </select>
                        </div>

                        <div class="space-y-1.5 animate-in slide-in-from-top-2">
                            <x-input-label for="landRentPeriod" :value="__('Periodo de Alquiler')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                            <select wire:model.live="landRentPeriod" id="landRentPeriod" class="block w-full bg-slate-50 dark:bg-white/5 border-none rounded-xl text-xs font-bold">
                                <option value="fecha">Por Fecha</option>
                                <option value="anual">Anual</option>
                            </select>
                        </div>

                        <div class="space-y-1.5 animate-in slide-in-from-top-2">
                            <x-input-label for="landRentStart" :value="__('Fecha Inicio')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                            <x-text-input wire:model="landRentStart" id="landRentStart" type="date" class="block w-full" />
                        </div>

                        <div class="space-y-1.5 animate-in slide-in-from-top-2">
                            <x-input-label for="landRentEnd" :value="__('Fecha Vencimiento')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                            <x-text-input wire:model="landRentEnd" id="landRentEnd" type="date" class="block w-full" />
                        </div>
                        @endif
                    </div>
                </div>

                <!-- SECCION 3: Datos Técnicos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 dark:border-white/5 pt-6">
                    <div class="space-y-1.5">
                        <x-input-label for="landSoil" :value="__('Tipo de Suelo')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <select wire:model="landSoil" id="landSoil" class="block w-full bg-slate-50 dark:bg-white/5 border-none rounded-xl text-xs font-bold focus:ring-4 focus:ring-agri-green/10">
                            <option value="franco">Franco</option><option value="arenoso">Arenoso</option><option value="arcilloso">Arcilloso</option><option value="limoso">Limoso</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <x-input-label for="landWater" :value="__('Fuente de Agua Principal')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <select wire:model="landWater" id="landWater" class="block w-full bg-slate-50 dark:bg-white/5 border-none rounded-xl text-xs font-bold focus:ring-4 focus:ring-agri-green/10">
                            <option value="Riego por goteo">Riego por goteo</option><option value="Pozo">Pozo tubular</option><option value="Canal">Canal de regadío</option><option value="Lluvia">Temporal (Lluvia)</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <x-input-label for="landStatus" :value="__('Estado del Terreno')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <select wire:model="landStatus" id="landStatus" class="block w-full bg-slate-50 dark:bg-white/5 border-none rounded-xl text-xs font-bold focus:ring-4 focus:ring-agri-green/10">
                            <option value="activo">Activo / Operativo</option>
                            <option value="inactivo">En Descanso</option>
                        </select>
                    </div>
                </div>

                <!-- SECCION 4: Foto -->
                <div class="space-y-4 pt-2">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] italic border-b border-slate-100 dark:border-white/5 pb-2">Evidencia Fotográfica</h4>
                    <div class="flex items-center space-x-6 bg-slate-50 dark:bg-white/5 p-6 rounded-3xl border border-dashed border-slate-200 dark:border-white/10">
                        <div class="w-32 h-24 rounded-2xl overflow-hidden bg-white dark:bg-slate-800 shrink-0 shadow-xl border-2 border-white">
                            @if($landPhoto && method_exists($landPhoto, 'isPreviewable') && $landPhoto->isPreviewable())
                                <img src="{{ $landPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif($landPhoto)
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
                            <input type="file" wire:model="landPhoto" class="text-[10px] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-agri-green file:text-white hover:file:bg-emerald-600 transition-all cursor-pointer"/>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-8 border-t border-slate-100 dark:border-white/5">
                    <button type="button" @click="$dispatch('close')" class="px-10 py-3.5 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-slate-200 transition-all">Cancelar</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-14 py-3.5 bg-agri-green text-white rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-agri-green/30 hover:scale-105 active:scale-95 transition-all italic flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up" wire:loading.remove></i>
                        <i class="fa-solid fa-spinner fa-spin" wire:loading></i>
                        {{ $landId ? 'Actualizar Registro' : 'Confirmar Registro' }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- MODAL DE INFORMACIÓN DETALLADA -->
    <x-modal name="modal-land-details" :show="false" focusable>
        @if($selectedLandForDetail)
        <div class="bg-white dark:bg-agri-d_bg rounded-3xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10 max-w-4xl mx-auto">
            <!-- Header con Imagen de Fondo -->
            <div class="relative h-64 w-full">
                @if($selectedLandForDetail->foto_path)
                    <img src="{{ Storage::url($selectedLandForDetail->foto_path) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-agri-green to-emerald-900 flex items-center justify-center">
                        <i class="fa-solid fa-mountain-sun text-8xl text-white/20"></i>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                <!-- Acciones Header -->
                <div class="absolute top-6 right-6 flex gap-3">
                    <button @click="$dispatch('close')" class="w-10 h-10 bg-black/20 backdrop-blur-md text-white rounded-full hover:bg-black/40 transition-all flex items-center justify-center">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Título e ID -->
                <div class="absolute bottom-8 left-8 right-8">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-agri-green text-white text-[9px] font-black uppercase rounded-lg shadow-lg border border-white/20">
                            Parcela #{{ $selectedLandForDetail->id }}
                        </span>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-white text-[9px] font-black uppercase rounded-lg border border-white/10">
                            {{ $selectedLandForDetail->tipo_tenencia }}
                        </span>
                    </div>
                    <h2 class="text-4xl font-black text-white italic tracking-tighter uppercase leading-none drop-shadow-2xl">
                        {{ $selectedLandForDetail->nombre }}
                    </h2>
                    <p class="text-white/80 text-xs font-bold mt-2 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-agri-green"></i> {{ $selectedLandForDetail->ubicacion }}
                    </p>
                </div>
            </div>

            <!-- Contenido Detallado -->
            <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Columna 1: Especificaciones Técnicas -->
                <div class="space-y-6">
                    <div>
                        <h4 class="text-[10px] font-black text-agri-green uppercase tracking-[0.3em] mb-4 border-b border-slate-100 dark:border-white/5 pb-2">Perfil Técnico</h4>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 group">
                                <div class="w-10 h-10 bg-slate-50 dark:bg-white/5 rounded-xl flex items-center justify-center text-agri-green group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-chart-area text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-1">Superficie</p>
                                    <p class="text-sm font-black text-slate-700 dark:text-slate-300 italic">{{ number_format($selectedLandForDetail->hectareas, 2) }} <span class="text-[10px] opacity-50">HA</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 group">
                                <div class="w-10 h-10 bg-slate-50 dark:bg-white/5 rounded-xl flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-mountain text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-1">Calidad de Suelo</p>
                                    <p class="text-sm font-black text-slate-700 dark:text-slate-300 italic">{{ ucfirst($selectedLandForDetail->calidad_suelo) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 group">
                                <div class="w-10 h-10 bg-slate-50 dark:bg-white/5 rounded-xl flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-droplet text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-1">Suministro de Agua</p>
                                    <p class="text-sm font-black text-slate-700 dark:text-slate-300 italic">{{ $selectedLandForDetail->fuente_agua }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna 2: Estado Legal y Organización -->
                <div class="space-y-6">
                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 border-b border-slate-100 dark:border-white/5 pb-2">Situación Administrativa</h4>
                        <div class="space-y-4">
                            @if($selectedLandForDetail->organizacion)
                                <div class="p-4 bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-100 dark:border-blue-900/30">
                                    <p class="text-[8px] font-black text-blue-400 uppercase mb-2 leading-none">Vinculado a Organización</p>
                                    <p class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-tight italic">{{ $selectedLandForDetail->organizacion->nombre }}</p>
                                </div>
                            @endif

                            @if($selectedLandForDetail->tipo_tenencia === 'alquilado')
                                <div class="p-4 bg-amber-50 dark:bg-amber-950/20 rounded-2xl border border-amber-100 dark:border-amber-900/30">
                                    <p class="text-[8px] font-black text-amber-500 uppercase mb-2 leading-none">Detalles del Alquiler</p>
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-end">
                                            <span class="text-[9px] text-slate-500 font-bold uppercase">Costo Anual:</span>
                                            <span class="text-xs font-black text-slate-700 dark:text-slate-300 italic">S/ {{ number_format($selectedLandForDetail->costo_alquiler_anual, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-end">
                                            <span class="text-[9px] text-slate-500 font-bold uppercase">Vencimiento:</span>
                                            <span class="text-xs font-black text-rose-500 italic">{{ $selectedLandForDetail->fecha_vencimiento_alquiler ? $selectedLandForDetail->fecha_vencimiento_alquiler->format('d M, Y') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/10">
                                    <p class="text-[8px] font-black text-slate-400 uppercase mb-1 leading-none">Responsable del Registro</p>
                                    <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 italic">{{ $selectedLandForDetail->responsable->name }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Columna 3: Cultivos y Disponibilidad -->
                <div class="space-y-6">
                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 border-b border-slate-100 dark:border-white/5 pb-2">Distribución de Área</h4>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-3 text-center">
                                <div class="p-3 bg-slate-50 dark:bg-white/5 rounded-xl">
                                    <p class="text-[7px] font-black text-slate-400 uppercase mb-1 leading-none">Ocupado</p>
                                    <p class="text-xs font-black text-agri-green italic">{{ number_format($selectedLandForDetail->area_ocupada, 2) }} ha</p>
                                </div>
                                <div class="p-3 bg-slate-50 dark:bg-white/5 rounded-xl">
                                    <p class="text-[7px] font-black text-slate-400 uppercase mb-1 leading-none">Disponible</p>
                                    <p class="text-xs font-black text-slate-400 italic">{{ number_format($selectedLandForDetail->area_disponible, 2) }} ha</p>
                                </div>
                            </div>

                            <div class="pt-2">
                                <p class="text-[8px] font-black text-slate-400 uppercase mb-3 leading-none">Cultivos Activos ({{ $selectedLandForDetail->cultivos->where('estado', 'En crecimiento')->count() }})</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($selectedLandForDetail->cultivos->where('estado', 'En crecimiento') as $c)
                                        <span class="px-3 py-1.5 bg-white dark:bg-white/5 rounded-lg border border-slate-100 dark:border-white/10 text-[9px] font-black text-slate-600 dark:text-slate-400 uppercase italic flex items-center gap-2">
                                            <i class="fa-solid fa-leaf text-agri-green"></i> {{ $c->detalleCatalogo->nombre }}
                                        </span>
                                    @endforeach
                                    @if($selectedLandForDetail->cultivos->where('estado', 'En crecimiento')->count() === 0)
                                        <p class="text-[10px] text-slate-400 italic font-bold">Sin cultivos activos en este momento.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer con Notas -->
            @if($selectedLandForDetail->direccion_referencia)
                <div class="px-8 pb-8">
                    <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-2xl border-l-4 border-agri-green">
                        <p class="text-[8px] font-black text-agri-green uppercase mb-1 tracking-widest leading-none">Notas de Referencia</p>
                        <p class="text-xs font-medium text-slate-600 dark:text-slate-400 italic">"{{ $selectedLandForDetail->direccion_referencia }}"</p>
                    </div>
                </div>
            @endif

            <div class="bg-slate-50 dark:bg-white/5 px-8 py-4 flex justify-between items-center border-t border-slate-100 dark:border-white/5">
                <p class="text-[8px] font-black text-slate-400 uppercase italic">Última actualización: {{ $selectedLandForDetail->updated_at->format('d/m/Y H:i') }}</p>
                <button @click="$dispatch('close')" class="px-8 py-2 bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-300 transition-all">Cerrar Detalle</button>
            </div>
        </div>
        @endif
    </x-modal>
</div>

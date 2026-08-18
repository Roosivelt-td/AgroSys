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

    <!-- BARRA DE FILTROS ESTILO IMAGEN (Corregida) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-white dark:bg-agri-d_bg p-4 rounded-3xl border border-slate-100 dark:border-white/5 shadow-xl">

        <!-- Columna 1: Búsqueda -->
        <div class="relative w-full group">
            <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-slate-400">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar parcela..."
                   class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-[11px] font-bold text-slate-600 dark:text-slate-300 placeholder-slate-400 focus:ring-4 focus:ring-agri-green/10 outline-none transition-all italic">
        </div>

        <!-- Columna 2: Filtro de cultivos (Searchable) -->
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
        <div class="bg-gradient-to-br from-agri-green to-emerald-800 p-6 rounded-3xl shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-700"><i class="fa-solid fa-chart-area text-9xl text-white"></i></div>
            <p class="text-[10px] font-black text-white/60 uppercase tracking-[0.3em] mb-1">Superficie Total</p>
            <h4 class="text-4xl font-black text-white italic tracking-tighter">{{ number_format($totalArea, 1) }} <span class="text-lg font-bold opacity-50">ha</span></h4>
            <div class="mt-4 flex items-center text-white/80 text-[10px] font-bold"><i class="fa-solid fa-leaf mr-2"></i> {{ $totalCount }} parcelas registradas</div>
        </div>

        <div class="lg:col-span-2 bg-white dark:bg-agri-d_bg p-2 rounded-3xl border border-slate-100 dark:border-white/5 shadow-2xl min-h-[350px]" wire:ignore>
            <div data-react-component="agro-map-terrenos" data-props="{{ json_encode(['terrenos' => $mapData]) }}" class="w-full h-full rounded-2xl overflow-hidden"></div>
        </div>
    </div>

    <!-- Grid de Parcelas (Texto aumentado 75%) -->
    <div class="animate-in fade-in duration-500">
        <h2 class="text-xl font-black text-slate-800 dark:text-white italic tracking-tighter mb-6 uppercase"><i class="fa-solid fa-list-ul mr-2 text-agri-green"></i> Mis Parcelas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($terrenos as $terreno)
            <div class="bg-white dark:bg-[#051110] rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-slate-100 dark:border-white/5 group relative" x-data="{ menuOpen: false }">
                <div class="h-48 bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
                    <a href="{{ route('admin.cultivos', ['filterTerrenoId' => $terreno->id]) }}" class="block w-full h-full relative group">
                        @if($terreno->foto_path)
                            <img src="{{ Storage::url($terreno->foto_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 bg-slate-50 dark:bg-[#0a1a19] group-hover:bg-slate-100 transition-colors"><i class="fa-solid fa-mountain-sun text-6xl"></i></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20 backdrop-blur-[2px]">
                            <span class="bg-agri-green text-white px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest italic shadow-xl">Gestionar Cultivos</span>
                        </div>
                    </a>

                    <!-- Clima (Superior Izquierda) -->
                    <div class="absolute top-4 left-4 flex items-center space-x-2 bg-white/90 dark:bg-[#051110]/90 backdrop-blur-md px-3 py-1.5 rounded-xl shadow-xl border border-white/20 z-10">
                        <i class="fa-solid fa-cloud-sun text-amber-500 text-xs animate-pulse"></i>
                        <span class="text-[9px] font-black text-slate-800 dark:text-white italic uppercase tracking-tighter leading-none">24°C | 65% HR</span>
                    </div>

                    <div class="absolute top-4 right-4 z-10 flex space-x-2">
                        <button @click="window.dispatchEvent(new CustomEvent('map-center-to', { detail: { lat: {{ $terreno->latitud }}, lng: {{ $terreno->longitud }} } }))"
                                class="w-9 h-9 bg-white/20 backdrop-blur-md border border-white/30 rounded-xl text-white hover:bg-agri-green transition-all shadow-lg flex items-center justify-center"><i class="fa-solid fa-crosshairs text-sm"></i></button>

                        <div class="relative">
                            <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false" class="w-9 h-9 bg-white/20 backdrop-blur-md border border-white/30 rounded-xl text-white hover:bg-white/40 transition-all flex items-center justify-center shadow-lg"><i class="fa-solid fa-ellipsis-vertical text-xs"></i></button>
                            <div x-show="menuOpen" x-transition class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 dark:border-white/10 overflow-hidden z-20">
                                <button wire:click="edit({{ $terreno->id }})" class="w-full px-4 py-3 text-left text-[12px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-all"><i class="fa-solid fa-edit mr-2 text-blue-500"></i> Editar</button>
                                <button wire:click="delete({{ $terreno->id }})" wire:confirm="¿Borrar?" class="w-full px-4 py-3 text-left text-[12px] font-black uppercase text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/10 transition-all"><i class="fa-solid fa-trash-can mr-2"></i> Borrar</button>
                            </div>
                        </div>
                    </div>

                    <div class="absolute bottom-4 left-6 right-6 flex justify-between items-end text-white pointer-events-none">
                        <div class="min-w-0 flex-1 pr-4">
                            <h3 class="text-[20px] font-black italic truncate tracking-tight uppercase leading-none text-white drop-shadow-lg">{{ $terreno->nombre }}</h3>
                            <p class="text-[11px] font-bold uppercase italic opacity-90 truncate mt-1 text-white/80"><i class="fa-solid fa-location-dot mr-1"></i> {{ $terreno->ubicacion }}</p>
                        </div>
                        <div class="flex flex-col items-end shrink-0">
                            <div class="px-3 py-1 bg-agri-green rounded-md text-[11px] font-black italic shadow-lg border border-white/20 uppercase">Disp: {{ number_format($terreno->area_disponible, 2) }} ha</div>
                            <div class="text-[9px] font-black text-white/70 uppercase italic mt-1 drop-shadow-md">Uso: {{ number_format($terreno->area_ocupada, 2) }} ha</div>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4 bg-white dark:bg-[#051110]">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Columna Izquierda: Información Técnica -->
                        <div class="space-y-4">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none mb-3">Información Técnica</p>
                                <div class="space-y-2.5">
                                    <div class="flex items-center space-x-2 text-[12px] font-bold text-slate-700 dark:text-slate-300 italic">
                                        <i class="fa-solid fa-mountain text-agri-green w-4 text-center"></i>
                                        <span>{{ ucfirst($terreno->calidad_suelo) }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 text-[12px] font-bold text-slate-700 dark:text-slate-300 italic">
                                        <i class="fa-solid fa-droplet text-blue-500 w-4 text-center"></i>
                                        <span>{{ $terreno->fuente_agua }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna Derecha: Situación Legal (Subida) -->
                        <div class="space-y-5 text-right border-l border-slate-100 dark:border-white/5 pl-4">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none mb-3">Situación Legal</p>
                                <span class="inline-block px-3 py-1.5 bg-slate-50 dark:bg-[#0a1a19] border border-slate-200 dark:border-white/10 rounded-lg text-[10px] font-black text-agri-green uppercase tracking-widest italic shadow-sm">
                                    {{ strtoupper($terreno->tipo_tenencia) }}
                                </span>

                                @if($terreno->tipo_tenencia === 'alquilado')
                                    <div class="mt-2 space-y-0.5">
                                        <p class="text-[10px] font-black text-amber-500 italic">S/ {{ number_format($terreno->costo_alquiler_anual, 2) }}</p>
                                        <p class="text-[8px] font-black text-slate-400 uppercase italic">Vence: {{ $terreno->fecha_vencimiento_alquiler ? date('d/m/Y', $terreno->fecha_vencimiento_alquiler) : 'N/A' }}</p>
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

    <!-- MODAL REGISTRO (DISEÑO RESTAURADO Y DINÁMICO) -->
    <x-modal name="modal-add-terrain" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg rounded-2xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10"
             x-init="$watch('show', value => { if(value) { setTimeout(() => window.mountAgroReact(), 500) } })"
             x-on:map-location-selected.window="$wire.set('landLat', $event.detail.lat, false); $wire.set('landLng', $event.detail.lng, false); $wire.set('landLocation', $event.detail.address);">

            <div class="bg-[#003a38] px-8 py-4 flex justify-between items-center text-white border-b border-white/5">
                <h3 class="text-xl font-black italic tracking-tighter uppercase">{{ $landId ? 'Editar Parcela AgroSys' : 'Nueva Parcela AgroSys' }}</h3>
                <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form wire:submit.prevent="save" class="p-8 space-y-8 max-h-[85vh] overflow-y-auto custom-scrollbar">

                <!-- SECCION MAPA -->
                <div class="space-y-4" wire:ignore>
                    <h4 class="text-[10px] font-black text-agri-green uppercase tracking-[0.3em] italic border-b border-slate-100 dark:border-white/5 pb-2">Ubicación Geo-Referenciada</h4>
                    <div class="w-full h-80 rounded-3xl overflow-hidden border-2 border-agri-green/10 shadow-2xl relative bg-slate-50">
                        <div data-react-component="agro-map-terrenos" data-props="{{ json_encode(['selectionMode' => true]) }}" class="w-full h-full"></div>
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/60 backdrop-blur-md px-4 py-2 rounded-full text-[8px] font-black text-white uppercase tracking-widest pointer-events-none z-[2]">
                            <i class="fa-solid fa-hand-pointer animate-bounce mr-2 text-agri-green"></i> Haz clic en el mapa para sincronizar datos
                        </div>
                    </div>
                </div>

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

                <!-- SECCION 2: Tenencia (DINÁMICA SEGÚN SELECT) -->
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
</div>

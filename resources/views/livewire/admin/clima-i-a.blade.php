<div class="space-y-6 p-4 md:p-1 transition-all duration-500 animate-in fade-in">
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #10b981; border-radius: 10px; }
        .leaflet-control-zoom { display: block !important; }
    </style>

    <!-- CABECERA PREMIUM (SIEMPRE VISIBLE - UBICACIÓN O TERRENO) -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-md border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div data-react-component="agro-logo-premium"
             data-props="{{ json_encode([
                 'src' => asset('AgroSys_logo.png'),
                 'title' => $current['location_name'],
                 'subtitle' => 'COORD: ' . $current['coords']
             ]) }}"
             class="z-10"></div>

        <!-- Indicadores Climáticos en Cabecera -->
        <div class="flex flex-wrap items-center gap-6 bg-slate-50 dark:bg-white/5 px-8 py-4 rounded-md border border-black/5 dark:border-white/5 shadow-inner">
            <div class="flex items-center gap-3">
                <i class="fa-solid {{ $current['icon'] }} text-amber-500 text-2xl"></i>
                <div class="flex flex-col">
                    <span class="text-[16px] font-black text-slate-800 dark:text-white leading-none">{{ round($current['temp'], 1) }}°C</span>
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-0.5">{{ $current['condicion'] }}</span>
                </div>
            </div>
            <div class="w-px h-8 bg-slate-200 dark:bg-white/10"></div>
            <div class="flex items-center gap-4 text-xs font-bold text-slate-500 dark:text-slate-400">
                <div class="flex flex-col items-center">
                    <i class="fa-solid fa-sun text-amber-400 mb-1"></i>
                    <span>{{ $current['extras']['sunrise'] }}</span>
                </div>
                <div class="flex flex-col items-center">
                    <i class="fa-solid fa-moon text-blue-400 mb-1"></i>
                    <span>{{ $current['extras']['sunset'] }}</span>
                </div>
            </div>
            <div class="w-px h-8 bg-slate-200 dark:bg-white/10"></div>
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-droplet text-blue-500 text-sm"></i>
                <div class="flex flex-col">
                    <span class="text-[13px] font-bold text-slate-700 dark:text-slate-200 leading-none">{{ $current['humedad'] }}%</span>
                    <span class="text-[7px] font-black text-slate-400 uppercase tracking-tighter">Humedad</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-wind text-slate-400 text-sm"></i>
                <div class="flex flex-col">
                    <span class="text-[13px] font-bold text-slate-700 dark:text-slate-200 leading-none">{{ number_format($current['viento'], 1) }} km/h</span>
                    <span class="text-[7px] font-black text-slate-400 uppercase tracking-tighter">Viento</span>
                </div>
            </div>
        </div>

        <div class="bg-blue-500/10 px-5 py-3 rounded-md border border-blue-500/20 flex items-center gap-3 group hover:bg-blue-500/20 transition-all cursor-pointer"
             onclick="navigator.geolocation.getCurrentPosition(pos => { @this.updateGPSLocation(pos.coords.latitude, pos.coords.longitude) })"
             title="Haz clic para usar tu ubicación GPS exacta">
            <i class="fa-solid fa-location-crosshairs text-blue-500 text-sm animate-pulse"></i>
            <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest leading-none">
                {{ $useGPS ? 'Ubicación GPS Exacta' : 'Sincronizar GPS Local' }}
            </span>
        </div>
    </div>

    <!-- BARRA DE FILTROS HORIZONTAL -->
    <div class="bg-white dark:bg-slate-900 px-6 py-1.5 rounded-md border border-slate-100 dark:border-white/5 shadow-2xl animate-in slide-in-from-top-4 duration-700 flex items-center gap-2 overflow-x-auto custom-scrollbar">
        <div class="flex items-center px-2 shrink-0 border-r border-slate-100 dark:border-white/10 mr-2">
            <i class="fa-solid fa-filter text-agri-green text-[11px]"></i>
        </div>

        <div class="flex-1 flex flex-nowrap items-center gap-1 min-w-max relative" wire:key="filters-bar-container">
            <!-- Overlay de carga sutil - Punto 1 -->
            <div wire:loading wire:target="fTerrenoId, fCultivoId, fVariedad, search, fFecha" class="absolute inset-0 z-10 bg-white/20 dark:bg-slate-900/20 backdrop-blur-[1px] flex items-center justify-center rounded-full pointer-events-none">
                <i class="fa-solid fa-circle-notch fa-spin text-agri-green text-[10px]"></i>
            </div>

            <!-- Búsqueda por Texto -->
            <div class="relative w-48">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[11px]"></i>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Buscar lote..." class="w-full pl-9 pr-2 py-1 bg-transparent border-none focus:ring-0 text-[11px] font-bold italic placeholder-slate-300">
            </div>

            <div class="w-px h-6 bg-slate-100 dark:bg-white/10 mx-1"></div>

            <!-- 1. Terreno -->
            <select wire:model.live="fTerrenoId"
                    wire:key="select-terreno-filter"
                    class="w-40 bg-transparent border-none focus:ring-0 text-[11px] font-bold appearance-none italic cursor-pointer truncate hover:text-agri-green transition-colors">
                <option value="">TERRENO: TODOS</option>
                @foreach($terrenosOptions as $t)
                    <option value="{{ $t->id }}">{{ strtoupper($t->nombre) }}</option>
                @endforeach
            </select>

            <div class="w-px h-6 bg-slate-100 dark:bg-white/10 mx-1"></div>

            <!-- 2. Cultivo -->
            <select wire:model.live="fCultivoId"
                    wire:key="select-cultivo-filter"
                    class="w-40 bg-transparent border-none focus:ring-0 text-[11px] font-bold appearance-none italic cursor-pointer truncate hover:text-agri-green transition-colors">
                <option value="">CULTIVO: TODOS</option>
                @foreach($catalogos as $cat)
                    <option value="{{ $cat->id }}">{{ strtoupper($cat->nombre) }}</option>
                @endforeach
            </select>

            <div class="w-px h-6 bg-slate-100 dark:bg-white/10 mx-1"></div>

            <!-- 3. Variedad -->
            <select wire:model.live="fVariedad"
                    wire:key="select-variedad-filter"
                    class="w-40 bg-transparent border-none focus:ring-0 text-[11px] font-bold appearance-none italic cursor-pointer truncate hover:text-agri-green transition-colors">
                <option value="">VARIEDAD: TODAS</option>
                @foreach($variedades as $var)
                    <option value="{{ $var }}">{{ strtoupper($var) }}</option>
                @endforeach
            </select>

            <div class="w-px h-6 bg-slate-100 dark:bg-white/10 mx-1"></div>

            <!-- 4. LOTE (Activa Análisis) - Punto 2 -->
            <select wire:model.live="selectedCropId"
                    wire:change="selectCrop($event.target.value)"
                    wire:key="select-lote-final-filter"
                    class="w-64 bg-transparent border-none focus:ring-0 text-[11px] font-black text-agri-green appearance-none italic cursor-pointer truncate shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                <option value="">SELECCIONAR LOTE...</option>
                @foreach($cultivos as $c_item)
                    <option value="{{ $c_item->id }}">
                        LOTE: {{ strtoupper($c_item->nombre_lote) }} - {{ strtoupper($c_item->variedad ?: 'COMÚN') }}
                    </option>
                @endforeach
            </select>

            <div class="w-px h-6 bg-slate-100 dark:bg-white/10 mx-1"></div>

            <!-- Fecha -->
            <input type="date" wire:model.live="fFecha" class="w-36 bg-transparent border-none focus:ring-0 text-[11px] font-bold italic cursor-pointer">
        </div>

        <div class="flex items-center gap-2 shrink-0 border-l border-slate-100 dark:border-white/10 pl-4 ml-2">
            <button wire:click="$refresh" class="px-5 py-1.5 bg-agri-green text-white rounded-md text-[11px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-md flex items-center gap-2">
                <i class="fa-solid fa-sync-alt" wire:loading.class="fa-spin" wire:target="$refresh"></i>
                ACEPTAR
            </button>
            <button wire:click="resetFilters" class="p-1.5 text-rose-500 hover:bg-rose-500 hover:text-white rounded-md transition-all" title="Limpiar Filtros">
                <i class="fa-solid fa-rotate-left text-[11px]"></i>
            </button>
        </div>
    </div>

    <!-- TENDENCIAS Y PRONÓSTICO (SIEMPRE VISIBLES - PUNTO 3) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 w-full">
        <!-- Gráfico 24h -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-md border border-slate-100 dark:border-white/5 shadow-2xl p-8 relative overflow-hidden min-h-[400px]">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-6 bg-agri-green rounded-md"></div>
                    <h3 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-wider italic">
                        @if($selectedCropId)
                            @php $selC = $cultivos->find($selectedCropId); @endphp
                            {{ $selC ? strtoupper($selC->detalleCatalogo->nombre) . ' (LOTE: ' . $selC->nombre_lote . ')' : '' }} : TENDENCIA PARA ESTE CULTIVO
                        @else
                            TENDENCIA MICROCLIMÁTICA ACTUAL ({{ $current['location_name'] }})
                        @endif
                    </h3>
                </div>
                <div class="flex gap-2">
                    <span class="px-3 py-1 bg-agri-green/10 text-agri-green text-[10px] font-black rounded-md border border-agri-green/20 italic">Satélite Activo</span>
                </div>
            </div>
            <div class="h-64 w-full relative">
                <div wire:loading wire:target="selectedCropId, selectCrop" class="absolute inset-0 z-50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center rounded-md">
                    <div class="flex flex-col items-center gap-3">
                        <i class="fa-solid fa-circle-notch fa-spin text-agri-green text-3xl"></i>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">Sincronizando Lote...</span>
                    </div>
                </div>
                <div wire:ignore class="h-full w-full">
                    <canvas id="forecastHourlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pronóstico -->
        <div class="lg:col-span-1 bg-white dark:bg-slate-900 rounded-md border border-slate-100 dark:border-white/5 shadow-2xl p-4 h-full flex flex-col">
            <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 italic text-center">Pronóstico Extendido</h3>
            <div class="flex-1 space-y-2 overflow-y-auto custom-scrollbar pr-1 max-h-[350px]">
                @if(isset($current['forecast']) && count($current['forecast']) > 0)
                    @foreach($current['forecast'] as $day)
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-white/5 rounded-md border border-slate-100 dark:border-white/5 hover:border-blue-500/30 transition-all group">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 w-7 uppercase">{{ $day['day_name'] }}</span>
                            <i class="fa-solid {{ $day['icon'] }} text-amber-500 text-base group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[8px] font-bold text-slate-400 uppercase whitespace-nowrap">{{ $day['condition'] }}</span>
                            <div class="flex gap-1.5 text-[11px] font-black">
                                <span class="text-slate-800 dark:text-white">{{ $day['temp_max'] }}°</span>
                                <span class="text-slate-400">{{ $day['temp_min'] }}°</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-center text-[9px] text-slate-400 italic">Cargando pronóstico...</p>
                @endif
            </div>
        </div>
    </div>

    <!-- ANÁLISIS TÉCNICO (SÓLO SI HAY LOTE SELECCIONADO) -->
    @if($selectedCropId)
        @php $c_active = $cultivos->find($selectedCropId); @endphp
        @if($c_active)
            <div class="flex flex-col lg:flex-row gap-6 mt-6 items-stretch animate-in fade-in slide-in-from-bottom-4 duration-1000">
                <!-- Lado Izquierdo (Info + Mapa) -->
                <div class="lg:w-[65%] flex flex-col gap-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                        <!-- 1. BLOQUE INFO CULTIVO -->
                        <div class="lg:col-span-1 bg-white dark:bg-slate-900 rounded-md border border-slate-100 dark:border-white/5 shadow-2xl overflow-hidden flex flex-col relative group min-h-[400px]">
                            @if($c_active->foto_path)
                                <img src="{{ Storage::url($c_active->foto_path) }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                            @else
                                <div class="absolute inset-0 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                    <i class="fa-solid fa-mountain-sun text-7xl text-slate-300 opacity-20"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-black/20"></div>

                            <div class="relative z-10 h-full flex flex-col p-6">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex flex-col gap-3">
                                        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-md shadow-2xl">
                                            <i class="fa-solid fa-cloud text-blue-500 text-sm"></i>
                                            <span class="text-[11px] font-black text-slate-800 italic">{{ round($current['temp'], 0) }}°C | {{ $current['humedad'] }}% HR</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-[#ff8a00] px-4 py-2 rounded-md shadow-2xl">
                                            <i class="fa-solid fa-barcode text-white text-sm"></i>
                                            <span class="text-[11px] font-black text-white italic uppercase tracking-widest">LOTE: {{ $c_active->nombre_lote }}</span>
                                        </div>
                                    </div>
                                    <button class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-md border border-white/20 flex items-center justify-center text-white hover:bg-white/40 transition-all shadow-lg">
                                        <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                    </button>
                                </div>
                                <div class="flex-1"></div>
                                <div class="flex items-end justify-between mb-4">
                                    <h4 class="text-4xl font-black italic text-white leading-none tracking-tighter uppercase">
                                        @php
                                            $parts = explode(' ', $c_active->detalleCatalogo->nombre);
                                            $main = $parts[0] ?? '';
                                            $sub = isset($parts[1]) ? implode(' ', array_slice($parts, 1)) : '';
                                        @endphp
                                        {{ $main }} <span class="text-emerald-400">{{ $sub }}</span>
                                    </h4>
                                    <div class="bg-emerald-500 text-white px-4 py-2 rounded-md text-[12px] font-black shadow-xl italic border border-white/10">
                                        {{ number_format($c_active->area_destinada, 2) }} ha
                                    </div>
                                </div>
                                <div class="pt-5 border-t border-white/30 grid grid-cols-3 gap-2">
                                    <div class="text-left">
                                        <span class="text-[8px] font-black text-slate-400 uppercase block mb-1">INVERSIÓN</span>
                                        <p class="text-[12px] font-black text-amber-400 italic leading-none">S/ {{ number_format($investmentStats['total'] ?? 0, 2) }}</p>
                                    </div>
                                    <div class="text-center border-x border-white/10 px-1">
                                        <span class="text-[8px] font-black text-slate-400 uppercase block mb-1">RIEGO</span>
                                        <p class="text-[10px] font-bold text-white italic leading-tight">{{ $c_active->terreno->fuente_agua ?: 'Riego por goteo' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[8px] font-black text-slate-400 uppercase block mb-1">LUGAR</span>
                                        <p class="text-[10px] font-bold text-white italic leading-tight truncate" title="{{ $c_active->terreno->ubicacion ?: $c_active->terreno->nombre }}">{{ $c_active->terreno->ubicacion ?: $c_active->terreno->nombre }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. MAPA -->
                        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-md border border-slate-100 dark:border-white/5 shadow-2xl overflow-hidden relative min-h-[400px]">
                            @php
                                $mapCenter = ['lat' => (float)$c_active->terreno->latitud, 'lng' => (float)$c_active->terreno->longitud];
                                $direccionExacta = $c_active->terreno->ubicacion ?: $c_active->terreno->nombre;
                                $direccionRef = $c_active->terreno->direccion_referencia;
                            @endphp
                            <div data-react-component="agro-map-terrenos"
                                 data-props="{{ json_encode([
                                     'terrenos' => $mapTerrenos,
                                     'center' => $mapCenter,
                                     'zoom' => 16,
                                     'selectedId' => $selectedTerrenoId
                                 ]) }}"
                                 wire:key="map-premium-focus-{{ $selectedCropId }}-{{ $viewTimestamp }}"
                                 wire:ignore
                                 class="w-full h-full absolute inset-0"></div>

                            <!-- BADGES FLOTANTES DE REFERENCIA Y GOOGLE MAPS (ARRIBA A LA DERECHA, EN FILAS APILADAS) -->
                            <div class="absolute top-3 right-3 z-20 flex flex-col items-end gap-1.5">
                                @if($direccionRef)
                                    <div class="flex items-center gap-1.5 bg-slate-900/90 text-amber-300 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/20 shadow-2xl text-[11px] font-bold italic">
                                        <i class="fa-solid fa-map-pin text-amber-400 text-[10px]"></i>
                                        <span>Ref: {{ $direccionRef }}</span>
                                    </div>
                                @endif
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $c_active->terreno->latitud }},{{ $c_active->terreno->longitud }}"
                                   target="_blank"
                                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-black text-[10px] rounded-lg flex items-center gap-1.5 uppercase tracking-wider transition-all shadow-2xl active:scale-95 border border-blue-400/30">
                                    <span>Google Maps</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[8px]"></i>
                                </a>
                            </div>

                            <!-- TARJETA DE EXTENSIÓN ESTIMADA -->
                            <div class="absolute bottom-4 right-16 z-20 bg-slate-900/90 text-white backdrop-blur-md px-5 py-2.5 rounded-xl border border-white/20 shadow-2xl text-right">
                                <span class="text-[9px] font-black text-slate-400 uppercase block mb-0.5 italic tracking-widest leading-none">Extensión Estimada</span>
                                <p class="text-3xl font-black italic text-emerald-400 leading-none">
                                    {{ number_format($c_active->area_destinada, 2) }} <span class="text-base text-slate-400 uppercase font-bold tracking-tighter">HA</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 4. TELEMETRÍA TÉCNICA -->
                    <div class="bg-white dark:bg-slate-900 rounded-md border border-slate-100 dark:border-white/5 shadow-2xl p-8 overflow-hidden relative min-h-[350px] flex flex-col justify-center">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center relative z-10 mb-6">
                            <div class="lg:col-span-3 space-y-4 border-r border-slate-100 dark:border-white/5 pr-4">
                                <div class="text-center space-y-1">
                                    <span class="text-[10px] font-black text-agri-green uppercase tracking-[0.3em] italic text-emerald-500">PROYECCIÓN RENDIMIENTO</span>
                                    <p class="text-5xl font-black text-slate-900 dark:text-white italic leading-none">40.00 <span class="text-xl opacity-30 font-bold">TN/HA</span></p>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-50 dark:border-white/5">
                                    <div class="text-center">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase block leading-none mb-1">ÁREA TERRENO</span>
                                        <p class="text-[11px] font-black text-slate-700 dark:text-slate-200">{{ number_format($c_active->terreno->hectareas, 2) }} HA</p>
                                    </div>
                                    <div class="text-center border-l border-slate-50 dark:border-white/5">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase block leading-none mb-1">ÁREA SIEMBRA</span>
                                        <p class="text-[11px] font-black text-agri-green">{{ number_format($c_active->area_destinada, 2) }} HA</p>
                                    </div>
                                </div>
                            </div>
                            <div class="lg:col-span-3 flex items-center justify-center border-r border-slate-100 dark:border-white/5 px-4">
                                <div class="relative w-40 h-40">
                                    <canvas id="investmentCropChart"></canvas>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                        <span class="text-[8px] font-black text-slate-400 uppercase leading-none">INVERTIDO</span>
                                        <span class="text-[11px] font-black text-slate-800 dark:text-white mt-1">S/{{ number_format($investmentStats['total'] ?? 0, 0) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="lg:col-span-3 space-y-2 border-r border-slate-100 dark:border-white/5 px-4">
                                @php
                                    $costs = [
                                        ['label' => 'Insumos', 'val' => $investmentStats['insumos'] ?? 0, 'bg' => 'bg-agri-green'],
                                        ['label' => 'Mano Obra', 'val' => $investmentStats['personal'] ?? 0, 'bg' => 'bg-blue-500'],
                                        ['label' => 'Maquinaria', 'val' => $investmentStats['maquinaria'] ?? 0, 'bg' => 'bg-amber-500'],
                                        ['label' => 'Alquiler', 'val' => $investmentStats['alquiler'] ?? 0, 'bg' => 'bg-rose-500'],
                                    ];
                                @endphp
                                @foreach($costs as $cost)
                                <div class="flex items-center justify-between bg-slate-50 dark:bg-white/5 p-2 rounded-xl border border-black/5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full {{ $cost['bg'] }}"></div>
                                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter">{{ $cost['label'] }}</span>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-700 dark:text-slate-300 italic">S/{{ number_format($cost['val'], 0) }}</span>
                                </div>
                                @endforeach
                            </div>
                            <div class="lg:col-span-3 space-y-4 pl-4 border-l border-slate-50 dark:border-white/5">
                                <div class="bg-slate-50/50 dark:bg-white/5 p-4 rounded-2xl space-y-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-500 shadow-sm"><i class="fa-solid fa-seedling text-xs"></i></div>
                                        <div>
                                            <span class="text-[8px] font-black text-slate-400 uppercase block leading-none mb-0.5">VARIEDAD / TIPO</span>
                                            <p class="text-[11px] font-black text-slate-700 dark:text-slate-200 italic truncate uppercase">{{ $c_active->variedad ?: 'Común' }} | <span class="text-agri-green">{{ strtoupper($c_active->terreno->tipo_tenencia) }}</span></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-500/10 rounded-full flex items-center justify-center text-blue-500 shadow-sm"><i class="fa-solid fa-calendar-check text-xs"></i></div>
                                        <div>
                                            <span class="text-[8px] font-black text-slate-400 uppercase block leading-none mb-0.5">FECHA SIEMBRA</span>
                                            <p class="text-[11px] font-black text-slate-700 dark:text-slate-200 italic leading-none">{{ $c_active->fecha_siembra ? $c_active->fecha_siembra->format('d/m/Y') : '---' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-amber-500/10 rounded-full flex items-center justify-center text-amber-500 shadow-sm"><i class="fa-solid fa-shield-virus text-xs"></i></div>
                                        <div>
                                            <span class="text-[8px] font-black text-slate-400 uppercase block leading-none mb-0.5">ESTADO ACTUAL</span>
                                            <p class="text-[11px] font-black text-slate-700 dark:text-slate-200 italic leading-none">
                                                @if($c_active->estado === 'Cosecha')
                                                    <span class="text-amber-500 animate-pulse font-black">EN COSECHA 🚜</span>
                                                @else
                                                    {{ $c_active->estado }} (IA)
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="px-6 space-y-4 relative z-10 pt-4 border-t border-slate-100 dark:border-white/5">
                            <div class="flex justify-between items-end">
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-[10px] font-black text-slate-400 uppercase italic tracking-widest leading-none">FENOLOGÍA ACTUAL:</span>
                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-600 text-white px-6 py-2 rounded-md text-[12px] font-black uppercase italic border border-blue-400/20 shadow-md">{{ strtoupper($c_active->estado) }}</div>
                                        <span class="text-[11px] font-black text-slate-700 dark:text-slate-200 italic uppercase">Ciclo de Crecimiento</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-[10px] font-black text-agri-green uppercase italic tracking-widest animate-pulse">CICLO EN PROCESO</span>
                                    <span class="text-[9px] font-bold text-slate-400 italic">Día {{ (int)$investmentStats['dias_cultivo'] }} de {{ (int)$investmentStats['dias_totales'] }} (Aprox. {{ round(($investmentStats['dias_cultivo'] / max(1, $investmentStats['dias_totales'])) * 100) }}%)</span>
                                </div>
                            </div>
                            <div class="h-4 w-full bg-slate-100 dark:bg-white/5 rounded-md overflow-hidden relative shadow-inner">
                                <div class="h-full bg-gradient-to-r from-agri-green to-emerald-400 rounded-md transition-all duration-1000 shadow-[0_0_30px_rgba(16,185,129,0.4)]" style="width: {{ min(100, round(($investmentStats['dias_cultivo'] / max(1, $investmentStats['dias_totales'])) * 100)) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Sidebar IA (Modificada según Imagen) -->
                <div class="lg:w-[35%] flex flex-col">
                    <div class="bg-white dark:bg-slate-900 rounded-md border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col h-full overflow-hidden p-8">
                        <div class="flex items-center gap-3 mb-8 shrink-0">
                            <div class="w-1.5 h-6 bg-[#ff8a00] rounded-full shadow-[0_0_10px_rgba(255,138,0,0.5)]"></div>
                            <h4 class="text-[14px] font-black text-slate-700 dark:text-white uppercase italic tracking-[0.25em]">PLAN DE ACCIÓN IA</h4>
                        </div>

                        <div class="flex-1 space-y-6 overflow-y-auto custom-scrollbar pr-1">

                            <!-- RIESGOS INMEDIATOS -->
                            @if(count($actionPlan['critico']) > 0)
                            <div class="space-y-3">
                                <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest italic border-b border-rose-500/20 pb-1 block">Alerta de Riesgo</span>
                                @foreach($actionPlan['critico'] as $item)
                                    <div class="flex items-start gap-4 p-4 bg-rose-500/5 rounded-xl border border-rose-500/10 animate-pulse">
                                        <div class="w-10 h-10 bg-rose-500/10 rounded-full flex items-center justify-center shrink-0 shadow-inner"><i class="fa-solid {{ $item['icon'] }} text-rose-500 text-sm"></i></div>
                                        <div class="space-y-1">
                                            <h5 class="text-[10px] font-black text-rose-700 dark:text-rose-400 uppercase leading-none">{{ $item['title'] }}</h5>
                                            <p class="text-[10px] font-bold text-slate-600 dark:text-slate-300 leading-tight italic">{{ $item['desc'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- RESTRICCIONES (PREVENCIÓN DE PÉRDIDAS) -->
                            @if(count($actionPlan['restriccion']) > 0)
                            <div class="space-y-3">
                                <span class="text-[9px] font-black text-amber-500 uppercase tracking-widest italic border-b border-amber-500/20 pb-1 block">Protección de Inversión</span>
                                @foreach($actionPlan['restriccion'] as $item)
                                    <div class="flex items-start gap-4 p-4 bg-amber-500/5 rounded-xl border border-amber-500/10">
                                        <div class="w-10 h-10 bg-amber-500/10 rounded-full flex items-center justify-center shrink-0"><i class="fa-solid {{ $item['icon'] }} text-amber-500 text-sm"></i></div>
                                        <div class="space-y-1">
                                            <h5 class="text-[10px] font-black text-amber-700 dark:text-amber-400 uppercase leading-none">{{ $item['title'] }}</h5>
                                            <p class="text-[10px] font-bold text-slate-600 dark:text-slate-300 leading-tight italic">{{ $item['desc'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- ANÁLISIS ESTRATÉGICO DE IA (GEMINI / LOCAL) -->
                            <div class="space-y-3">
                                <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest italic border-b border-emerald-500/20 pb-1 block w-full">Cerebro AgroSys (IA Generativa)</span>
                                <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-xl border border-emerald-500/10 shadow-inner relative overflow-hidden group">
                                    <div class="absolute top-0 right-0 p-2 opacity-5 group-hover:opacity-10 transition-opacity">
                                        <i class="fa-solid fa-brain text-5xl text-emerald-500"></i>
                                    </div>
                                    <div class="relative z-10 ai-markdown-output text-[11px] font-medium text-slate-700 dark:text-slate-200 leading-relaxed space-y-2">
                                        {!! \Illuminate\Support\Str::markdown($aiAnalysis ?? 'Cargando análisis estratégico...') !!}
                                    </div>
                                    <div class="mt-3 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">Análisis en tiempo real ejecutado vía IA</span>
                                    </div>
                                </div>
                            </div>

                            <!-- ESTRATEGIA SIGUIENTE -->
                            @if(count($actionPlan['pendiente']) > 0)
                            <div class="space-y-3">
                                <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest italic border-b border-blue-500/20 pb-1 block">Hoja de Ruta Técnica</span>
                                @foreach($actionPlan['pendiente'] as $item)
                                    <div class="flex items-start gap-4 p-4 bg-blue-500/5 rounded-xl border border-blue-500/10 group hover:bg-blue-500/10 transition-all">
                                        <div class="w-10 h-10 bg-blue-500/10 rounded-full flex items-center justify-center shrink-0 shadow-inner group-hover:scale-110 transition-transform"><i class="fa-solid {{ $item['icon'] }} text-blue-500 text-sm"></i></div>
                                        <div class="space-y-1">
                                            <h5 class="text-[10px] font-black text-blue-700 dark:text-blue-400 uppercase leading-none">{{ $item['title'] }}</h5>
                                            <p class="text-[10px] font-bold text-slate-600 dark:text-slate-300 leading-tight italic">{{ $item['desc'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- INSIGHTS PREDICTIVOS (ACTUACIÓN DE IA) -->
                            @if(count($actionPlan['ia_insights']) > 0)
                            <div class="space-y-3">
                                <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest italic border-b border-emerald-500/20 pb-1 block">Análisis de IA & Finanzas</span>
                                @foreach($actionPlan['ia_insights'] as $item)
                                    <div class="flex items-start gap-4 p-4 bg-emerald-500/5 rounded-xl border border-emerald-500/10 border-dashed">
                                        <div class="w-10 h-10 bg-emerald-500/10 rounded-full flex items-center justify-center shrink-0"><i class="fa-solid {{ $item['icon'] }} text-emerald-500 text-sm"></i></div>
                                        <div class="space-y-1">
                                            <h5 class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase leading-none">{{ $item['title'] }}</h5>
                                            <p class="text-[10px] font-bold text-slate-600 dark:text-slate-300 leading-tight italic">{{ $item['desc'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- ESTADO VACÍO (SIN LOTE) - MUESTRA IA BASADA EN UBICACIÓN -->
        <div class="flex flex-col items-center justify-center py-16 text-center animate-in fade-in zoom-in duration-700">
            <div class="w-24 h-24 bg-slate-100 dark:bg-white/5 rounded-3xl flex items-center justify-center shadow-inner mb-6 relative">
                <div class="absolute inset-0 bg-agri-green/10 rounded-3xl animate-ping"></div>
                <i class="fa-solid fa-robot text-4xl text-agri-green relative z-10"></i>
            </div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-widest italic mb-2">LISTO PARA EL ANÁLISIS</h3>
            <p class="text-[12px] font-bold text-slate-400 leading-relaxed max-w-[380px] italic">
                Selecciona un <span class="text-agri-green">LOTE (Paso 4)</span> para habilitar telemetría técnica e IA específica. Actualmente visualizando microclima en: <span class="text-blue-500">{{ $current['location_name'] }}</span>
            </p>
        </div>
    @endif

    <!-- FILA: TENDENCIAS Y HISTORIAL (SIEMPRE VISIBLES) -->
    <div class="space-y-8 pt-8 border-t border-slate-100 dark:border-white/5">
        <!--
        <div class="bg-white dark:bg-slate-900 p-8 rounded-md border border-slate-100 dark:border-white/5 shadow-2xl h-[450px] flex flex-col relative overflow-hidden">
            <div class="flex items-center justify-between mb-6 relative z-10">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-chart-line text-agri-green text-sm"></i>
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-widest italic leading-none uppercase">Seguimiento Histórico ({{ $selectedCropId ? 'Lote' : 'Ubicación' }})</h4>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-0.5 bg-rose-500"></div>
                        <span class="text-[9px] font-black text-slate-400 uppercase italic">Temp.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-0.5 bg-blue-500"></div>
                        <span class="text-[9px] font-black text-slate-400 uppercase italic">Hum.</span>
                    </div>
                </div>
            </div>
            <div class="flex-1 w-full relative z-10">
                <div data-react-component="agro-climate-trend-chart"
                     data-props="{{ json_encode(['data' => $trendData]) }}"
                     wire:key="clima-trend-chart-{{ $viewTimestamp }}"
                     class="w-full h-full"></div>
            </div>
        </div>
        -->
        <div class="bg-white dark:bg-slate-900 p-8 rounded-md border border-slate-100 dark:border-white/5 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-list-check text-agri-green"></i>
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] italic leading-none uppercase">SEGUIMIENTO HISTÓRICO (LOTE)</h4>
                </div>
                @if($selectedCropId)
                    <span class="text-[10px] font-black text-agri-green bg-agri-green/10 px-3 py-1 rounded-full italic border border-agri-green/20">Sincronizado con MySQL</span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($laboresCultivo as $lab)
                    <div class="bg-white dark:bg-slate-900/50 p-2 rounded-md border border-slate-100 dark:border-white/5 flex items-center justify-between group transition-all hover:shadow-lg relative overflow-hidden min-h-[80px]">
                        <!-- LADO IZQUIERDO: TEXTOS -->
                        <div class="flex flex-col flex-1 pr-4">
                            <div class="flex items-center gap-3 mb-0.5">
                                <h5 class="text-[13px] font-black italic text-slate-800 dark:text-white uppercase tracking-tight leading-none">{{ $lab->detalleCatalogo->nombre ?? 'Labor' }}</h5>
                                <span class="text-[11px] font-bold text-agri-green italic leading-none">{{ $lab->fecha_realizacion ? $lab->fecha_realizacion->format('d/m/Y') : '---' }}</span>
                            </div>
                            <p class="text-[11px] font-bold text-slate-300 dark:text-slate-500 italic leading-tight line-clamp-2">{{ $lab->observaciones ?: 'Sin observaciones técnicas registradas.' }}</p>
                        </div>

                        <!-- LADO DERECHO: COSTO (ESTILO IMAGEN) -->
                        <div class="text-right border-l border-slate-100 dark:border-white/10 pl-6 min-w-[120px] flex flex-col justify-center">
                            <div class="inline-block self-end mb-1">
                                <span class="text-[10px] font-black text-slate-400 border border-slate-200 dark:border-white/10 px-1.5 py-0.5 rounded uppercase tracking-tighter leading-none">COSTO TOTAL</span>
                            </div>
                            <p class="text-[16px] font-black text-agri-green italic leading-none">S/ {{ number_format($lab->costo_total, 2) }}</p>
                            <span class="text-[9px] font-black text-agri-green italic uppercase block mt-1 leading-none tracking-tighter">EJECUTADO</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 flex flex-col items-center justify-center text-center opacity-40">
                        <i class="fa-solid fa-folder-open text-4xl text-slate-300 mb-4"></i>
                        <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest italic">No hay labores registradas para este lote</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-8 rounded-md border border-slate-100 dark:border-white/5 shadow-2xl">
            <div class="flex items-center gap-3 mb-6">
                <i class="fa-solid fa-clock-rotate-left text-agri-green"></i>
                <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] italic leading-none uppercase">Últimos Registros de Telemetría</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead>
                        <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="px-6 py-2">Fecha / Hora</th>
                            <th class="px-6 py-2">Temp.</th>
                            <th class="px-6 py-2">Humedad</th>
                            <th class="px-6 py-2">Viento</th>
                            <th class="px-6 py-2">Condición</th>
                        </tr>
                    </thead>
                    <tbody class="text-[11px] font-bold italic">
                        @foreach($history as $h_reg)
                        <tr class="bg-slate-50 dark:bg-white/5 hover:bg-agri-green/5 transition-colors rounded-md">
                            <td class="px-6 py-4 rounded-l-2xl">{{ \Carbon\Carbon::parse($h_reg->fecha_hora)->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">{{ $h_reg->temperatura }}°C</td>
                            <td class="px-6 py-4">{{ $h_reg->humedad }}%</td>
                            <td class="px-6 py-4">{{ $h_reg->viento_kmh }} km/h</td>
                            <td class="px-6 py-4 rounded-r-2xl">
                                <span class="px-3 py-1 bg-white dark:bg-slate-800 rounded-md border border-slate-100 dark:border-white/10 uppercase text-[9px] tracking-tighter">
                                    {{ $h_reg->condicion }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        window.climaIAChartInstance = null;
        function initClimaIAChart() {
            const ctxForecast = document.getElementById('forecastHourlyChart');
            if (!ctxForecast) return;
            const renderForecastChart = (data) => {
                if (!data || !data.labels || !data.labels.length) return;
                if (window.climaIAChartInstance) window.climaIAChartInstance.destroy();
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#94a3b8' : '#64748b';
                window.climaIAChartInstance = new Chart(ctxForecast.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            { label: 'Temp', data: data.temps, borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.05)', fill: true, tension: 0.4, pointRadius: 0, borderWidth: 3, yAxisID: 'y' },
                            { label: 'Hum', data: data.hums, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.05)', fill: true, tension: 0.4, pointRadius: 0, borderWidth: 3, yAxisID: 'y1' }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        scales: {
                            y: { type: 'linear', display: true, position: 'left', grid: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' }, ticks: { color: '#3b82f6' } },
                            y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { color: '#10b981' }, min: 0, max: 100 },
                            x: { grid: { display: false }, ticks: { color: textColor, maxRotation: 0 } }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
            };
            const renderInvestmentChart = (stats) => {
                const ctxInv = document.getElementById('investmentCropChart');
                if (!ctxInv || !stats) return;
                if (window.investmentChartInstance) window.investmentChartInstance.destroy();
                window.investmentChartInstance = new Chart(ctxInv.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Insumos', 'Personal', 'Maquinaria', 'Alquiler'],
                        datasets: [{
                            data: [stats.insumos||0, stats.personal||0, stats.maquinaria||0, stats.alquiler||0],
                            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#f43f5e'],
                            borderWidth: 0
                        }]
                    },
                    options: { cutout: '75%', plugins: { legend: { display: false } }, responsive: true, maintainAspectRatio: false }
                });
            };
            const initialData = @json($currentWeatherJS['hourly_data'] ?? []);
            if (initialData && initialData.labels) renderForecastChart(initialData);
            const initialInvStats = @json($investmentStats);
            if (initialInvStats) renderInvestmentChart(initialInvStats);
            Livewire.on('refresh-chart-data', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                if (data && data.hourly) renderForecastChart(data.hourly);
                if (data && data.investment) renderInvestmentChart(data.investment);
            });
            Livewire.on('refreshChart', () => {
                @this.get('currentWeatherJS').then(c => { if(c && c.hourly_data) renderForecastChart(c.hourly_data); });
                @this.get('investmentStats').then(s => { if(s) renderInvestmentChart(s); });
            });
        }
        document.addEventListener('livewire:navigated', () => initClimaIAChart());
        document.addEventListener('DOMContentLoaded', () => { if (window.Livewire) initClimaIAChart(); });
        window.addEventListener('map-marker-clicked', (e) => { @this.call('selectTerreno', e.detail.id); });
        window.addEventListener('map-center-to', (e) => {
            const data = Array.isArray(e.detail) ? e.detail[0] : e.detail;
            if (data && data.lat && data.lng) {
                window.dispatchEvent(new CustomEvent('map-fly-to', { detail: { lat: parseFloat(data.lat), lng: parseFloat(data.lng) } }));
            }
        });
    </script>

    <style>
        .ai-markdown-output h3 {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 12px;
            margin-bottom: 6px;
            color: #059669;
            border-bottom: 1px solid rgba(16, 185, 129, 0.15);
            padding-bottom: 3px;
        }
        .ai-markdown-output h3:first-child {
            margin-top: 0;
        }
        .ai-markdown-output ul {
            list-style-type: disc;
            padding-left: 18px;
            margin-top: 4px;
            margin-bottom: 8px;
        }
        .ai-markdown-output li {
            margin-bottom: 5px;
            font-size: 11px;
            line-height: 1.45;
        }
        .ai-markdown-output strong {
            font-weight: 800;
            color: #0f172a;
        }
        .dark .ai-markdown-output strong {
            color: #f8fafc;
        }
        .dark .ai-markdown-output h3 {
            color: #34d399;
            border-bottom-color: rgba(52, 211, 153, 0.15);
        }
    </style>
</div>

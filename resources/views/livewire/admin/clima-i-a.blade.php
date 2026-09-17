<div class="space-y-6 p-4 md:p-1 transition-all duration-500 animate-in fade-in">

    <!-- CABECERA PREMIUM -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div data-react-component="agro-logo-premium"
             data-props="{{ json_encode([
                 'src' => asset('AgroSys_logo.png'),
                 'title' => $current['location_name'],
                 'subtitle' => 'COORD: ' . $current['coords']
             ]) }}"
             class="z-10"></div>

        <!-- Indicadores Climáticos en Cabecera -->
        <div class="flex flex-wrap items-center gap-6 bg-slate-50 dark:bg-white/5 px-8 py-4 rounded-[2rem] border border-black/5 dark:border-white/5 shadow-inner">
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

        <div class="bg-blue-500/10 px-5 py-3 rounded-2xl border border-blue-500/20 flex items-center gap-3 group hover:bg-blue-500/20 transition-all cursor-pointer"
             onclick="navigator.geolocation.getCurrentPosition(pos => { @this.updateGPSLocation(pos.coords.latitude, pos.coords.longitude) })"
             title="Haz clic para usar tu ubicación GPS exacta">
            <i class="fa-solid fa-location-crosshairs text-blue-500 text-sm animate-pulse"></i>
            <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest leading-none">
                {{ $useGPS ? 'Ubicación GPS Exacta' : 'Sincronizar GPS Local' }}
            </span>
        </div>
    </div>

    <!-- BARRA DE FILTROS HORIZONTAL (STRICT 9PT - FIXED SPACING) -->
    <div class="bg-white dark:bg-slate-900 px-6 py-1.5 rounded-full border border-slate-100 dark:border-white/5 shadow-2xl animate-in slide-in-from-top-4 duration-700 flex items-center gap-2 overflow-x-auto custom-scrollbar">
        <div class="flex items-center px-2 shrink-0 border-r border-slate-100 dark:border-white/10 mr-2">
            <i class="fa-solid fa-filter text-agri-green text-[9pt]"></i>
        </div>

        <div class="flex-1 flex flex-nowrap items-center gap-1 min-w-max">
            <!-- Search -->
            <div class="relative w-48">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[9pt]"></i>
                <input type="text" wire:model.live="search" placeholder="Buscar lote..." class="w-full pl-9 pr-2 py-1 bg-transparent border-none focus:ring-0 text-[9pt] font-bold italic placeholder-slate-300">
            </div>

            <div class="w-px h-6 bg-slate-100 dark:bg-white/10 mx-1"></div>

            <!-- Terreno -->
            <select wire:model.live="fTerrenoId" class="w-40 bg-transparent border-none focus:ring-0 text-sm font-bold appearance-none italic cursor-pointer truncate">
                <option value="">TERRENO: TODOS</option>
                @foreach($terrenos as $t) <option value="{{ $t->id }}">{{ strtoupper($t->nombre) }}</option> @endforeach
            </select>

            <div class="w-px h-6 bg-slate-100 dark:bg-white/10 mx-1"></div>

            <!-- Cultivo -->
            <select wire:model.live="fCultivoId" class="w-40 bg-transparent border-none focus:ring-0 text-sm font-bold appearance-none italic cursor-pointer truncate">
                <option value="">CULTIVO: TODOS</option>
                @foreach($catalogos as $cat) <option value="{{ $cat->id }}">{{ strtoupper($cat->nombre) }}</option> @endforeach
            </select>

            <div class="w-px h-6 bg-slate-100 dark:bg-white/10 mx-1"></div>

            <!-- Variedad -->
            <select wire:model.live="fVariedad" class="w-40 bg-transparent border-none focus:ring-0 text-sm font-bold appearance-none italic cursor-pointer truncate">
                <option value="">VARIEDAD: TODAS</option>
                @foreach($variedades as $var) <option value="{{ $var }}">{{ strtoupper($var) }}</option> @endforeach
            </select>

            <div class="w-px h-6 bg-slate-100 dark:bg-white/10 mx-1"></div>

            <!-- Cultivo Seleccionado -->
            <select wire:model.live="selectedCropId" wire:change="selectCrop($event.target.value)" class="w-52 bg-transparent border-none focus:ring-0 text-sm font-black text-agri-green appearance-none italic cursor-pointer truncate">
                <option value="">SELECCIONAR LOTE...</option>
                @foreach($cultivos as $c)
                    <option value="{{ $c->id }}">LOTE: {{ $c->nombre_lote }}</option>
                @endforeach
            </select>

            <div class="w-px h-6 bg-slate-100 dark:bg-white/10 mx-1"></div>

            <!-- Fecha -->
            <input type="date" wire:model.live="fFecha" class="w-36 bg-transparent border-none focus:ring-0 text-sm font-bold italic cursor-pointer">
        </div>

        <div class="flex items-center gap-2 shrink-0 border-l border-slate-100 dark:border-white/10 pl-4 ml-2">
            <button class="px-5 py-1.5 bg-agri-green text-white rounded-full text-[9pt] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-md">
                ACEPTAR
            </button>
            <button wire:click="resetFilters" class="p-1.5 text-rose-500 hover:bg-rose-500 hover:text-white rounded-full transition-all" title="Limpiar Filtros">
                <i class="fa-solid fa-rotate-left text-[9pt]"></i>
            </button>
        </div>
    </div>

    <!-- FILA 1: TENDENCIA 24H Y PRONÓSTICO 7 DÍAS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 w-full">
        <!-- Panel Izquierdo: Gráfico de Tendencia 24h -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-2xl p-8 relative overflow-hidden min-h-[400px]">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-6 bg-agri-green rounded-full"></div>
                    <h3 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-wider italic">
                        @if($selectedCropId)
                            @php $selC = $cultivos->find($selectedCropId); @endphp
                            {{ $selC ? strtoupper($selC->detalleCatalogo->nombre) . ' (LOTE: ' . $selC->nombre_lote . ')' : '' }} : Tendencia para este cultivo
                        @else
                            esto cambiara dependiendo al cultivo selecionado si no se lecciona nada solo se mostrara con la ubicacion actual donde nos encontramos
                        @endif
                    </h3>
                </div>
                <div class="flex gap-2">
                    <span class="px-3 py-1 bg-agri-green/10 text-agri-green text-[10px] font-black rounded-lg border border-agri-green/20">Satélite Activo</span>
                </div>
            </div>

            <div class="h-64 w-full relative">
                <!-- Overlay de carga para el gráfico -->
                <div wire:loading wire:target="selectedCropId, selectedTerrenoId, selectCrop, updateGPSLocation" class="absolute inset-0 z-50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center rounded-3xl">
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

        <!-- Panel Derecho: Pronóstico Extendido -->
        <div class="lg:col-span-1 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-2xl p-4 h-full flex flex-col">
            <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 italic text-center">Pronóstico Extendido</h3>
            <div class="flex-1 space-y-2 overflow-y-auto custom-scrollbar pr-1 max-h-[350px]">
                @if(isset($current['forecast']) && count($current['forecast']) > 0)
                    @foreach($current['forecast'] as $day)
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5 hover:border-blue-500/30 transition-all group">
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

    <!-- FILA 2: DETALLE CULTIVO | PLAN DE ACCIÓN IA -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch mt-6">
        <!-- 1. DATOS DEL CULTIVO (Imagen y Resumen) -->
        <div class="flex flex-col relative">
            <div wire:loading class="absolute inset-0 z-[100] bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm flex items-center justify-center rounded-[3rem]">
                <i class="fa-solid fa-circle-notch fa-spin text-agri-green text-3xl"></i>
            </div>
            @if($selectedCropId)
                @php $c = $cultivos->find($selectedCropId); @endphp
                @if($c)
                    <div class="bg-white dark:bg-slate-900 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl overflow-hidden h-full flex flex-col group animate-in zoom-in-95 duration-300">
                        <div class="relative h-64 shrink-0 overflow-hidden">
                            @if($c->foto_path)
                                <img src="{{ Storage::url($c->foto_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                            @else
                                <div class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-300">
                                    <i class="fa-solid fa-mountain-sun text-7xl opacity-20"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>

                            <div class="absolute top-6 left-6 flex items-center gap-3 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-4 py-2 rounded-2xl shadow-2xl border border-white/20">
                                <i class="fa-solid {{ $current['icon'] }} text-amber-500 text-sm"></i>
                                <span class="text-[12px] font-black text-slate-800 dark:text-white italic">{{ round($current['temp'], 1) }}°C | {{ $current['humedad'] }}% HR</span>
                            </div>

                            <div class="absolute bottom-8 left-8 right-8 flex justify-between items-end text-white z-20">
                                <div>
                                    <span class="bg-amber-500 px-4 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest mb-3 inline-block shadow-lg border border-white/20 italic">LOTE: {{ $c->nombre_lote }}</span>
                                    <h4 class="text-4xl font-black italic uppercase tracking-tighter leading-none uppercase">{{ $c->detalleCatalogo->nombre }}</h4>
                                    <p class="text-[12px] font-bold opacity-80 uppercase tracking-widest mt-2">{{ $c->variedad ?: 'VAR. COMÚN' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-black opacity-60 uppercase block mb-1">Extensión</span>
                                    <p class="text-3xl font-black italic text-agri-green">{{ number_format($c->area_destinada, 2) }} <span class="text-lg opacity-40">HA</span></p>
                                </div>
                            </div>

                            <!-- Botón central de gestión -->
                            <div class="absolute inset-0 z-10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 bg-black/40 backdrop-blur-[1px]">
                                <a href="{{ route('admin.labores', ['filterCropId' => $c->id, 'strict' => 1]) }}" class="px-8 py-3 bg-agri-green text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-2xl border border-white/20 italic flex items-center gap-3">
                                    <i class="fa-solid fa-gears text-sm"></i> Gestionar Labores
                                </a>
                            </div>
                        </div>

                        <div class="p-8 grid grid-cols-3 gap-4 bg-white dark:bg-slate-900 flex-1 items-center border-b border-slate-50 dark:border-white/5">
                            <div class="space-y-1 text-center">
                                <span class="text-[10px] font-black text-agri-green uppercase tracking-tighter block mb-1">RENDIMIENTO</span>
                                <p class="text-lg font-black text-slate-800 dark:text-slate-100 italic">40.00 <span class="text-[9px] opacity-40 uppercase">TN/HA</span></p>
                                <p class="text-[7px] font-black text-slate-400 uppercase italic">DATO REAL VENTA</p>
                            </div>
                            <div class="space-y-1 text-center border-l border-slate-100 dark:border-white/5">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter block mb-1 italic">RIEGO</span>
                                <p class="text-[11px] font-black text-slate-700 dark:text-slate-200 uppercase italic">{{ $c->terreno->fuente_agua ?: 'POR GOTEO' }}</p>
                            </div>
                            <div class="space-y-1 text-center border-l border-slate-100 dark:border-white/5">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter block mb-1 italic">LUGAR</span>
                                <p class="text-[11px] font-black text-slate-700 dark:text-slate-200 uppercase italic truncate">{{ $c->terreno->nombre }}</p>
                            </div>
                        </div>

                        <div class="px-8 py-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">FENOLOGÍA: {{ strtoupper($c->estado) }}</span>
                                <span class="text-[9px] font-black text-agri-green italic">FASE ACTIVA</span>
                            </div>
                            <div class="h-1.5 w-full bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-agri-green rounded-full transition-all duration-1000" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-slate-50 dark:bg-white/5 rounded-[3rem] border border-dashed border-slate-300 dark:border-white/10 flex flex-col items-center justify-center text-center p-12 h-full opacity-60">
                    <div class="w-20 h-20 bg-white dark:bg-slate-800 rounded-3xl flex items-center justify-center shadow-inner mb-6">
                        <i class="fa-solid fa-robot text-4xl text-slate-200"></i>
                    </div>
                    <h5 class="text-sm font-black text-slate-400 uppercase tracking-widest italic mb-2">SELECCIÓN DE LOTE REQUERIDA</h5>
                    <p class="text-[11px] font-bold text-slate-400 leading-relaxed max-w-[280px]">Usa los filtros de la izquierda para seleccionar un cultivo activo y ver su análisis detallado.</p>
                </div>
            @endif
        </div>

        <!-- 3. PLAN DE ACCIÓN IA (SUGERENCIAS) -->
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col space-y-6 relative overflow-hidden h-full">
            <div class="absolute top-0 right-0 p-8 opacity-5"><i class="fa-solid fa-wand-magic-sparkles text-8xl text-agri-green"></i></div>
            <div class="relative z-10 h-full flex flex-col">
                <div class="flex items-center gap-3 mb-8 shrink-0">
                    <i class="fa-solid fa-lightbulb text-amber-500 text-sm"></i>
                    <h4 class="text-[12px] font-black text-slate-700 dark:text-white uppercase tracking-[0.2em] italic">PLAN DE ACCIÓN IA</h4>
                </div>

                <div class="space-y-5 flex-1 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($actionPlan as $action)
                        <div class="flex items-start gap-4 p-5 bg-slate-50 dark:bg-white/5 rounded-[2rem] border-l-4 border-{{ $action['color'] }}-500 transition-all hover:bg-white dark:hover:bg-white/10 group">
                            <div class="w-12 h-12 bg-{{ $action['color'] }}-500/10 rounded-2xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid {{ $action['icon'] }} text-{{ $action['color'] }}-500 text-lg"></i>
                            </div>
                            <div class="space-y-1">
                                <h5 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-tighter">{{ $action['title'] }}</h5>
                                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 leading-relaxed italic">{{ $action['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 pt-6 border-t border-slate-50 dark:border-white/5 shrink-0">
                    <div class="flex items-center gap-3 mb-3">
                        <i class="fa-solid fa-satellite-dish text-blue-500 text-[10px]"></i>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">IA Basada en Fenología & Pronóstico</span>
                    </div>
                    <p class="text-[9px] font-medium text-slate-400 leading-relaxed italic">
                        Recomendaciones técnicas (riego, fumigación, cosecha) calculadas según el estado actual del lote y el **pronóstico registrado diariamente**.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- FILA 3: MAPA Y TENDENCIAS (ÚLTIMOS 7 DÍAS) -->
    <div class="space-y-4 pt-6">
        <div class="bg-white dark:bg-slate-900 px-8 py-4 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl flex items-center justify-between">
            <div class="flex items-center gap-4 border-l-4 border-rose-500 pl-6">
                <h3 class="text-lg font-black italic tracking-tighter uppercase text-slate-800 dark:text-white">Análisis Geográfico y Tendencias Sensores</h3>
            </div>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-0.5 bg-rose-500"></div>
                    <span class="text-[9px] font-black text-slate-400 uppercase italic">Temperatura (°C)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-0.5 bg-blue-500"></div>
                    <span class="text-[9px] font-black text-slate-400 uppercase italic">Humedad (%)</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Mapa -->
            <div class="lg:col-span-5 bg-white dark:bg-slate-900 p-6 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col h-[650px]">
                <div class="flex-1 w-full rounded-[2rem] overflow-hidden shadow-inner bg-slate-50 dark:bg-slate-800 relative">
                    <div data-react-component="agro-map-terrenos"
                         data-props="{{ json_encode(['terrenos' => $mapTerrenos]) }}"
                         wire:key="map-clima-sync-{{ $selectedTerrenoId }}"
                         wire:ignore
                         class="w-full h-full absolute inset-0"></div>
                </div>
            </div>

            <!-- Gráfico Tendencias -->
            <div class="lg:col-span-7 bg-white dark:bg-slate-900 p-8 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl h-[650px] flex flex-col relative overflow-hidden">
                <div class="flex items-center gap-3 mb-6 relative z-10">
                    <i class="fa-solid fa-chart-line text-agri-green text-sm"></i>
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Seguimiento Histórico de Microclima</h4>
                </div>
                <div class="flex-1 w-full relative z-10">
                    <div data-react-component="agro-climate-trend-chart"
                         data-props="{{ json_encode(['data' => $trendData]) }}"
                         wire:key="clima-trend-chart-{{ $viewTimestamp }}"
                         class="w-full h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCION: HISTORIAL DE TELEMETRÍA (TABLA) -->
    <div class="bg-white dark:bg-slate-900 p-8 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl mt-8">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-clock-rotate-left text-agri-green"></i>
                <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] italic leading-none uppercase">HISTORIAL DE TELEMETRÍA (ÚLTIMOS REGISTROS)</h4>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="px-6 py-2">Fecha / Hora</th>
                        <th class="px-6 py-2">Temp.</th>
                        <th class="px-6 py-2">Humedad</th>
                        <th class="px-6 py-2">Viento</th>
                        <th class="px-6 py-2">Presión</th>
                        <th class="px-6 py-2">Condición</th>
                    </tr>
                </thead>
                <tbody class="text-[11px] font-bold italic">
                    @foreach($history as $h)
                    <tr class="bg-slate-50 dark:bg-white/5 hover:bg-agri-green/5 transition-colors rounded-2xl">
                        <td class="px-6 py-4 rounded-l-2xl">{{ \Carbon\Carbon::parse($h->fecha_hora)->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">{{ $h->temperatura }}°C</td>
                        <td class="px-6 py-4">{{ $h->humedad }}%</td>
                        <td class="px-6 py-4">{{ $h->viento_kmh }} km/h</td>
                        <td class="px-6 py-4">{{ $h->presion_hpa }} hPa</td>
                        <td class="px-6 py-4 rounded-r-2xl">
                            <span class="px-3 py-1 bg-white dark:bg-slate-800 rounded-full border border-slate-100 dark:border-white/10 uppercase text-[9px] tracking-tighter">
                                {{ $h->condicion }}
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
    // Variable global para persistencia entre navegaciones
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
                        {
                            label: 'Temperatura (°C)',
                            data: data.temps,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.05)',
                            fill: true, tension: 0.4, pointRadius: 0, borderWidth: 3, yAxisID: 'y',
                        },
                        {
                            label: 'Humedad (%)',
                            data: data.hums,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.05)',
                            fill: true, tension: 0.4, pointRadius: 0, borderWidth: 3, yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    scales: {
                        y: {
                            type: 'linear', display: true, position: 'left',
                            grid: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
                            ticks: { color: '#3b82f6', font: { weight: 'bold' } },
                            title: { display: true, text: 'Temp °C', color: '#3b82f6' }
                        },
                        y1: {
                            type: 'linear', display: true, position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { color: '#10b981', font: { weight: 'bold' } },
                            title: { display: true, text: 'Hum %', color: '#10b981' },
                            min: 0, max: 100
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: textColor, font: { weight: 'bold' }, maxRotation: 0 }
                        }
                    },
                    plugins: {
                        legend: { display: true, position: 'top', labels: { color: textColor, font: { weight: 'bold', size: 10 } } },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#fff',
                            titleColor: isDark ? '#fff' : '#000',
                            bodyColor: isDark ? '#cbd5e1' : '#475569',
                            borderColor: 'rgba(59,130,246,0.2)',
                            borderWidth: 1
                        }
                    }
                }
            });
        };

        // Cargar datos iniciales de PHP
        const initialData = @json($currentWeatherJS['hourly_data'] ?? []);
        if (initialData && initialData.labels && initialData.labels.length) {
            renderForecastChart(initialData);
        }

        // Registrar listeners de Livewire
        Livewire.on('refresh-chart-data', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            if (data && data.hourly) renderForecastChart(data.hourly);
            setTimeout(() => { if (window.mountAgroReact) window.mountAgroReact(); }, 100);
        });

        Livewire.on('refreshChart', () => {
            @this.get('currentWeatherJS').then(current => {
                if (current && current.hourly_data) renderForecastChart(current.hourly_data);
            });
        });
    }

    // Inicialización para carga normal y navegación SPA (wire:navigate)
    document.addEventListener('livewire:navigated', () => {
        initClimaIAChart();
    });

    // Fallback para la primera carga
    document.addEventListener('DOMContentLoaded', () => {
        if (window.Livewire) initClimaIAChart();
    });

    // Eventos globales de mapa
    window.addEventListener('map-marker-clicked', (e) => {
        @this.call('selectTerreno', e.detail.id);
    });

    window.addEventListener('map-center-to', (e) => {
        const data = Array.isArray(e.detail) ? e.detail[0] : e.detail;
        if (data && data.lat && data.lng) {
            window.dispatchEvent(new CustomEvent('map-fly-to', {
                detail: { lat: parseFloat(data.lat), lng: parseFloat(data.lng) }
            }));
        }
    });
</script>

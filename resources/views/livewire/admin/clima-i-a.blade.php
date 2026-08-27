<div class="space-y-6 p-4 md:p-1 transition-all duration-500 animate-in fade-in">

    <!-- CABECERA PREMIUM (Punto 2: Datos Clima Integrados) -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="flex items-center gap-5 relative z-10">
            <div class="w-14 h-14 bg-[#003a38] rounded-2xl flex items-center justify-center shadow-lg border border-white/10">
                <i class="fa-solid fa-cloud-showers-water text-2xl text-agri-green"></i>
            </div>
            <div>
                <h2 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">CLIMA IA</h2>
                <p class="text-[9px] text-agri-green font-black uppercase tracking-[0.3em] mt-1.5 italic opacity-80">INTELIGENCIA PREDICTIVA</p>
            </div>
        </div>

        <!-- Indicadores Climáticos en Cabecera -->
        <div class="flex flex-wrap items-center gap-6 bg-slate-50 dark:bg-white/5 px-8 py-3 rounded-3xl border border-black/5 dark:border-white/5 shadow-inner">
            <div class="flex items-center gap-3">
                <i class="fa-solid {{ $current['icon'] }} text-amber-500 text-xl"></i>
                <div class="flex flex-col">
                    <span class="text-[14px] font-black text-slate-800 dark:text-white leading-none">{{ $current['temp'] }}°C</span>
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-0.5">{{ $current['condicion'] }}</span>
                </div>
            </div>
            <div class="w-px h-8 bg-slate-200 dark:bg-white/10"></div>
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-droplet text-blue-500 text-sm"></i>
                <div class="flex flex-col">
                    <span class="text-[14px] font-black text-slate-800 dark:text-white leading-none">{{ $current['humedad'] }}%</span>
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-0.5">HUMEDAD</span>
                </div>
            </div>
            <div class="w-px h-8 bg-slate-200 dark:bg-white/10"></div>
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-wind text-slate-400 text-sm"></i>
                <div class="flex flex-col">
                    <span class="text-[14px] font-black text-slate-800 dark:text-white leading-none">{{ $current['viento'] }} km/h</span>
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-0.5">VIENTO</span>
                </div>
            </div>
        </div>

        <div class="bg-agri-green/10 px-5 py-3 rounded-2xl border border-agri-green/20 flex items-center gap-3">
            <i class="fa-solid fa-satellite-dish text-agri-green text-sm animate-pulse"></i>
            <span class="text-[9px] font-black text-agri-green uppercase tracking-widest leading-none">Estación: Conectada</span>
        </div>
    </div>

    <!-- CUERPO PRINCIPAL (Grilla Rediseñada) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Bloque Izquierdo (Punto 1: Mapa Leaflet con Filtros) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white dark:bg-slate-900 p-3 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl relative">

                <!-- Selectores sobre el mapa -->
                <div class="absolute top-6 left-6 right-6 z-[1000] flex flex-col gap-2">
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-agri-green text-xs"></i>
                        <select wire:model.live="selectedTerrenoId" class="w-full pl-10 pr-8 py-3 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md border-none rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl focus:ring-2 focus:ring-agri-green appearance-none italic">
                            @foreach($terrenos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option> @endforeach
                        </select>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-leaf absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 text-xs"></i>
                        <select wire:model.live="selectedCropId" class="w-full pl-10 pr-8 py-3 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md border-none rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl focus:ring-2 focus:ring-blue-500 appearance-none italic">
                            <option value="">TODOS LOS CULTIVOS ACTIVOS</option>
                            @foreach($cultivos as $c)
                                <option value="{{ $c->id }}">{{ $c->detalleCatalogo->nombre }} - {{ $c->variedad }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Contenedor del Mapa -->
                <div class="w-full h-[580px] rounded-[2.5rem] overflow-hidden shadow-inner bg-slate-50 dark:bg-slate-800" wire:ignore>
                    <div data-react-component="agro-map-terrenos"
                         data-props="{{ json_encode(['terrenos' => $mapTerrenos]) }}"
                         wire:key="map-clima-{{ $selectedTerrenoId }}-{{ $viewTimestamp }}"
                         class="w-full h-full"></div>
                </div>
            </div>
        </div>

        <!-- Bloque Derecho: Gráfico y Pronóstico -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Gráfico de Tendencia (Punto 3: Línea) -->
            <div class="bg-white dark:bg-slate-900 p-10 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col h-[400px]">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-chart-line text-agri-green"></i>
                        <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] italic leading-none">TENDENCIAS CLIMÁTICAS (ÚLTIMOS 7 DÍAS)</h4>
                    </div>
                </div>
                <div class="flex-1 w-full">
                    <div data-react-component="agro-climate-trend-chart"
                         data-props="{{ json_encode(['data' => $trendData]) }}"
                         wire:key="clima-trend-chart-{{ $viewTimestamp }}"
                         class="w-full h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCION: DETALLE TÉCNICO Y SUGERENCIAS IA (Punto de Deslizamiento) -->
    @if($selectedTerrenoId)
    <div id="seccion-detalles" class="space-y-6 pt-10 border-t border-slate-100 dark:border-white/5 animate-in slide-in-from-bottom-10 duration-700">
        <div class="flex items-center gap-4 border-l-4 border-agri-green pl-6">
            <div class="w-12 h-12 bg-agri-green/10 rounded-2xl flex items-center justify-center">
                <i class="fa-solid fa-microchip text-agri-green text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black italic tracking-tighter uppercase text-slate-800 dark:text-white">Análisis de Cultivo & Sugerencias IA</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">Diagnóstico basado en condiciones climáticas reales</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Detalles de Cultivos Activos -->
            <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($cultivos as $c)
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-xl hover:shadow-2xl transition-all group overflow-hidden relative">
                        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:rotate-12 transition-transform">
                            <i class="fa-solid fa-leaf text-6xl"></i>
                        </div>
                        <div class="flex flex-col h-full justify-between gap-6">
                            <div class="space-y-2">
                                <span class="px-3 py-1 bg-agri-green/10 text-agri-green rounded-lg text-[9px] font-black uppercase italic tracking-widest">{{ $c->estado }}</span>
                                <h4 class="text-xl font-black italic uppercase tracking-tight text-slate-800 dark:text-white leading-none">{{ $c->detalleCatalogo->nombre }}</h4>
                                <p class="text-[10px] font-black text-slate-400 uppercase italic">{{ $c->variedad ?: 'Var. Genérica' }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-50 dark:border-white/5">
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Área Lote</span>
                                    <span class="text-[14px] font-black italic text-slate-700 dark:text-slate-200">{{ number_format($c->area_destinada, 2) }} Ha</span>
                                </div>
                                <div class="flex flex-col text-right">
                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Siembra</span>
                                    <span class="text-[14px] font-black italic text-slate-700 dark:text-slate-200">{{ $c->fecha_siembra->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 bg-slate-50 dark:bg-white/5 rounded-[2.5rem] p-12 flex flex-col items-center justify-center text-center gap-4 border border-dashed border-slate-300">
                        <i class="fa-solid fa-seedling text-4xl text-slate-300 opacity-50"></i>
                        <p class="text-[11px] font-black text-slate-400 uppercase italic tracking-widest">No hay cultivos activos registrados en esta parcela</p>
                    </div>
                @endforelse
            </div>

            <!-- Sugerencias de la IA DUALES -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Bloque 1: Alertas Generales del Terreno -->
                <div class="bg-[#003a38] p-8 rounded-[3rem] text-white shadow-2xl relative overflow-hidden">
                    <div class="absolute right-0 bottom-0 p-6 opacity-10"><i class="fa-solid fa-cloud-sun-rain text-[100px]"></i></div>
                    <div class="relative z-10 space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-6 bg-agri-green"></div>
                            <h4 class="text-sm font-black uppercase tracking-[0.2em] italic">ALERTAS DEL TERRENO</h4>
                        </div>
                        <div class="space-y-4">
                            @foreach($generalRecs as $rec)
                                <div class="flex gap-4 p-4 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 transition-colors">
                                    <div class="w-10 h-10 bg-{{ $rec['color'] }}-500 rounded-xl flex items-center justify-center shadow-lg shrink-0">
                                        <i class="fa-solid fa-circle-exclamation text-white"></i>
                                    </div>
                                    <div class="flex-1 space-y-1">
                                        <p class="text-[11px] font-bold italic leading-relaxed">{{ $rec['msg'] }}</p>
                                        <span class="text-[8px] font-black text-agri-green uppercase tracking-widest">Impacto: {{ $rec['priority'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Bloque 2: Estrategia por Cultivo (Solo si hay cultivo seleccionado) -->
                @if($selectedCropId)
                <div class="bg-white dark:bg-slate-900 p-8 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl relative overflow-hidden animate-in zoom-in-95 duration-500">
                    <div class="absolute right-0 bottom-0 p-6 opacity-5"><i class="fa-solid fa-robot text-[100px] text-agri-green"></i></div>
                    <div class="relative z-10 space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-6 bg-blue-500"></div>
                            <h4 class="text-sm font-black uppercase tracking-[0.2em] italic text-slate-800 dark:text-white">ESTRATEGIA IA: {{ strtoupper($cultivos->find($selectedCropId)->detalleCatalogo->nombre) }}</h4>
                        </div>
                        <div class="space-y-4">
                            @foreach($cropRecs as $rec)
                                <div class="flex gap-4 p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/5 hover:border-agri-green/30 transition-colors">
                                    <div class="w-10 h-10 bg-{{ $rec['color'] }}-500 rounded-xl flex items-center justify-center shadow-lg shrink-0">
                                        <i class="fa-solid fa-robot text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1 space-y-1">
                                        <p class="text-[11px] font-bold italic text-slate-700 dark:text-slate-300 leading-relaxed">{{ $rec['msg'] }}</p>
                                        <div class="flex justify-between items-center mt-2">
                                            <span class="text-[8px] font-black text-{{ $rec['color'] }}-600 uppercase tracking-widest">{{ $rec['type'] }}</span>
                                            <button class="text-[8px] font-black text-blue-500 uppercase underline">Ver Guía Técnica</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-slate-50 dark:bg-white/5 p-8 rounded-[3rem] border border-dashed border-slate-300 dark:border-white/10 flex flex-col items-center justify-center text-center gap-3 opacity-60">
                    <i class="fa-solid fa-robot text-3xl text-slate-300"></i>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Selecciona un cultivo sobre el mapa para generar su estrategia IA individual</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    window.addEventListener('map-marker-clicked', (e) => {
        const id = e.detail.id;
        @this.call('selectTerreno', id);

        // Desplazamiento suave a la sección de detalles
        setTimeout(() => {
            const element = document.getElementById('seccion-detalles');
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 300);
    });
</script>

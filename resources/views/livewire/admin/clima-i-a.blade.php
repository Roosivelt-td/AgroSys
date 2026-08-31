<div class="space-y-6 p-4 md:p-1 transition-all duration-500 animate-in fade-in">

    <!-- CABECERA PREMIUM -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-6 relative overflow-hidden">

        <div data-react-component="agro-logo-premium"
             data-props="{{ json_encode([
                 'src' => asset('AgroSys_logo.png'),
                 'title' => 'CLIMA IA',
                 'subtitle' => 'INTELIGENCIA PREDICTIVA'
             ]) }}"
             class="z-10"></div>

        <!-- Indicadores Climáticos en Cabecera (Línea Recta) -->
        <div class="flex flex-wrap items-center gap-6 bg-slate-50 dark:bg-white/5 px-8 py-4 rounded-[2rem] border border-black/5 dark:border-white/5 shadow-inner">
            <div class="flex items-center gap-3">
                <i class="fa-solid {{ $current['icon'] }} text-amber-500 text-2xl"></i>
                <div class="flex flex-col">
                    <span class="text-[16px] font-black text-slate-800 dark:text-white leading-none">{{ $current['temp'] }}°C</span>
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-0.5">{{ $current['condicion'] }}</span>
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
                    <span class="text-[13px] font-bold text-slate-700 dark:text-slate-200 leading-none">{{ $current['viento'] }} km/h</span>
                    <span class="text-[7px] font-black text-slate-400 uppercase tracking-tighter">Viento</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <i class="fa-solid fa-gauge-high text-emerald-500 text-sm"></i>
                <div class="flex flex-col">
                    <span class="text-[13px] font-bold text-slate-700 dark:text-slate-200 leading-none">{{ $current['presion'] }} hPa</span>
                    <span class="text-[7px] font-black text-slate-400 uppercase tracking-tighter">Presión</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <i class="fa-solid fa-cloud-rain text-blue-400 text-sm"></i>
                <div class="flex flex-col">
                    <span class="text-[13px] font-bold text-slate-700 dark:text-slate-200 leading-none">23%</span>
                    <span class="text-[7px] font-black text-slate-400 uppercase tracking-tighter">Prob. Lluvia</span>
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

        <!-- Bloque Izquierdo (Mapa con Selectores en Cabecera) -->
        <div class="lg:col-span-5">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col h-[450px]">

                <!-- Selectores integrados en cabecera de tarjeta -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-location-dot absolute left-3 top-1/2 -translate-y-1/2 text-agri-green text-[10px]"></i>
                        <select wire:model.live="selectedTerrenoId" class="w-full pl-8 pr-6 py-2 bg-slate-50 dark:bg-white/5 border-none rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-agri-green appearance-none italic">
                            <option value="">VER TODOS LOS TERRENOS</option>
                            @foreach($terrenos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option> @endforeach
                        </select>
                    </div>
                    <div class="relative flex-1">
                        <i class="fa-solid fa-leaf absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 text-[10px]"></i>
                        <select wire:model.live="selectedCropId" class="w-full pl-8 pr-6 py-2 bg-slate-50 dark:bg-white/5 border-none rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-blue-500 appearance-none italic">
                            <option value="">CULTIVOS ACTIVOS</option>
                            @foreach($cultivos as $c)
                                <option value="{{ $c->id }}">{{ $c->detalleCatalogo->nombre }} - {{ $c->variedad }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Contenedor del Mapa -->
                <div class="flex-1 w-full rounded-[2rem] overflow-hidden shadow-inner bg-slate-50 dark:bg-slate-800 relative">
                    <div data-react-component="agro-map-terrenos"
                         data-props="{{ json_encode(['terrenos' => $mapTerrenos]) }}"
                         wire:key="map-clima-estatico"
                         wire:ignore
                         class="w-full h-full absolute inset-0"></div>
                </div>
            </div>
        </div>

        <!-- Bloque Derecho: Gráfico de Tendencias -->
        <div class="lg:col-span-7">
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col h-[450px]">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-chart-line text-agri-green"></i>
                        <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] italic leading-none">TENDENCIAS CLIMÁTICAS (ÚLTIMOS 7 DÍAS)</h4>
                    </div>
                </div>
                <div class="flex-1 w-full relative">
                    <div data-react-component="agro-climate-trend-chart"
                         data-props="{{ json_encode(['data' => $trendData]) }}"
                         wire:key="clima-trend-chart-{{ $viewTimestamp }}"
                         class="w-full h-full absolute inset-0"></div>
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
            <!-- Detalles de Cultivos Activos e Inteligencia de Producción -->
            <div class="lg:col-span-7 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($cultivos as $c)
                        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-xl hover:shadow-2xl transition-all group overflow-hidden relative">
                            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:rotate-12 transition-transform">
                                <i class="fa-solid fa-leaf text-6xl"></i>
                            </div>
                            <div class="flex flex-col h-full justify-between gap-6">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-start">
                                        <span class="px-3 py-1 bg-agri-green/10 text-agri-green rounded-lg text-[9px] font-black uppercase italic tracking-widest">{{ $c->estado }}</span>
                                        <!-- IA: Pronóstico de Producción -->
                                        <div class="flex flex-col items-end">
                                            <span class="text-[8px] font-black text-blue-500 uppercase tracking-widest">IA Estimación</span>
                                            <span class="text-[12px] font-black italic text-blue-600 dark:text-blue-400">~{{ number_format($c->rendimiento_esperado_tn_ha * $c->area_destinada, 1) }} TN</span>
                                        </div>
                                    </div>
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

                <!-- IA: Recomendador de Siembra (Siguiente Campaña) -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-950 p-8 rounded-[3rem] text-white shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10"><i class="fa-solid fa-wand-magic-sparkles text-6xl"></i></div>
                    <div class="relative z-10 space-y-6">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-calendar-check text-agri-green"></i>
                            <h4 class="text-sm font-black uppercase tracking-[0.2em] italic">IA: SUGERENCIA DE SIEMBRA PRÓXIMA</h4>
                        </div>
                        <p class="text-[11px] font-medium text-slate-400 max-w-md">Basado en el historial de rendimiento de este terreno, tipo de suelo ({{ $terrenos->find($selectedTerrenoId)->calidad_suelo }}) y pronóstico estacional.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 bg-white/5 rounded-2xl border border-white/10 flex items-center justify-between">
                                <div>
                                    <p class="text-[9px] font-black text-agri-green uppercase">Prioridad 1</p>
                                    <p class="text-lg font-black italic">PAPA CANCHÁN</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-slate-400 uppercase">Éxito</p>
                                    <p class="text-xl font-black text-emerald-400">92%</p>
                                </div>
                            </div>
                            <div class="p-4 bg-white/5 rounded-2xl border border-white/10 flex items-center justify-between opacity-60">
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase">Alternativa</p>
                                    <p class="text-lg font-black italic">MAÍZ AMARILLO</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-slate-400 uppercase">Éxito</p>
                                    <p class="text-xl font-black text-amber-400">78%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sugerencias de la IA DUALES -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Bloque 1: Alertas Generales del Terreno e IA Plagas -->
                <div class="bg-[#003a38] p-8 rounded-[3rem] text-white shadow-2xl relative overflow-hidden">
                    <div class="absolute right-0 bottom-0 p-6 opacity-10"><i class="fa-solid fa-bug text-[100px]"></i></div>
                    <div class="relative z-10 space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-6 bg-agri-green"></div>
                                <h4 class="text-sm font-black uppercase tracking-[0.2em] italic">RIESGO DE PLAGAS IA</h4>
                            </div>
                            <span class="text-[9px] font-black bg-rose-500 px-3 py-1 rounded-full animate-pulse">ALTO RIESGO</span>
                        </div>

                        <!-- Barras de Probabilidad de Plagas -->
                        <div class="space-y-4 py-4">
                            <div class="space-y-2">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                    <span>Rancha (Phytophthora)</span>
                                    <span class="text-rose-400">88%</span>
                                </div>
                                <div class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-rose-500 rounded-full" style="width: 88%"></div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                    <span>Gusano de tierra</span>
                                    <span class="text-amber-400">34%</span>
                                </div>
                                <div class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-500 rounded-full" style="width: 34%"></div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                    <span>Mosca Blanca</span>
                                    <span class="text-emerald-400">12%</span>
                                </div>
                                <div class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: 12%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-white/10">
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

        <!-- SECCION: HISTORIAL CLIMÁTICO (Tabla Premium) -->
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-2xl mt-8">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-clock-rotate-left text-agri-green"></i>
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] italic leading-none">HISTORIAL DE TELEMETRÍA (ÚLTIMOS REGISTROS)</h4>
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
                    <tbody class="text-[11px] font-bold">
                        @foreach($history as $h)
                        <tr class="bg-slate-50 dark:bg-white/5 hover:bg-agri-green/5 transition-colors rounded-2xl">
                            <td class="px-6 py-4 rounded-l-2xl border-l border-t border-b border-transparent">{{ \Carbon\Carbon::parse($h->fecha_hora)->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">{{ $h->temperatura }}°C</td>
                            <td class="px-6 py-4">{{ $h->humedad }}%</td>
                            <td class="px-6 py-4">{{ $h->viento_kmh }} km/h</td>
                            <td class="px-6 py-4">{{ $h->presion_hpa }} hPa</td>
                            <td class="px-6 py-4 rounded-r-2xl border-r border-t border-b border-transparent">
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

    // Bridge para capturar el evento de Livewire y mover el mapa
    window.addEventListener('map-center-to', (e) => {
        // Livewire 3 envía los datos en e.detail. Los desglosamos para el CustomEvent
        const data = Array.isArray(e.detail) ? e.detail[0] : e.detail;

        if (data && data.lat && data.lng) {
            console.log('[AgroBridge] Moviendo mapa a:', data.lat, data.lng);
            window.dispatchEvent(new CustomEvent('map-fly-to', {
                detail: { lat: parseFloat(data.lat), lng: parseFloat(data.lng) }
            }));
        }
    });
</script>

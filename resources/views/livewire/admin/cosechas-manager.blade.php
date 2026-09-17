<div class="space-y-8 pb-20">
    @if(!$hasLaboresCosecha)
        <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-900 rounded-[3rem] border border-dashed border-slate-300 dark:border-white/10 shadow-inner">
            <div class="w-32 h-32 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-8 relative">
                <i class="fa-solid fa-basket-shopping text-6xl text-slate-200 dark:text-white/10"></i>
                <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center shadow-lg animate-bounce">
                    <i class="fa-solid fa-triangle-exclamation text-white"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic tracking-tighter mb-4 text-center">Falta Registro de Producción</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium max-w-md text-center italic leading-relaxed px-6">
                No se han registrado labores de recolección o cosecha. Por favor, <span class="text-agri-green font-black">finalice una labor de cosecha</span> en la sección 'Mis Labores' para ver los resultados consolidados aquí.
            </p>
            <a href="{{ route('admin.labores') }}" class="mt-10 px-12 py-4 bg-agri-green text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-2xl shadow-agri-green/30 hover:scale-110 transition-all italic flex items-center gap-3">
                <i class="fa-solid fa-tractor"></i> Ir a Mis Labores
            </a>
        </div>
    @else
        <!-- Header con Estadísticas Generales -->
    <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-100 dark:border-white/5 p-4 md:p-6">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-agri-green/5 rounded-full blur-3xl"></div>

        <div class="relative grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div class="space-y-5">
                <div>
                    <span class="px-3 py-1 bg-agri-green/10 text-agri-green text-[8px] font-black uppercase tracking-widest rounded border border-agri-green/20 italic">Análisis de Producción</span>
                    <h1 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white italic tracking-tighter mt-1.5 leading-tight uppercase">Mis<br><span class="text-agri-green">Cosechas</span></h1>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-medium italic max-w-xs leading-relaxed">
                    Historial consolidado de rendimiento y balance económico de campañas finalizadas.
                </p>
                <div class="flex flex-wrap gap-3">
                    <div class="bg-slate-50 dark:bg-white/5 px-5 py-2.5 rounded-xl border border-slate-100 dark:border-white/5 shadow-sm">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Total Campañas</p>
                        <p class="text-lg font-black text-slate-800 dark:text-white italic">{{ $cosechas->total() }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-white/5 px-5 py-2.5 rounded-xl border border-slate-100 dark:border-white/5 shadow-sm">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Producción (1ra)</p>
                        <p class="text-lg font-black text-agri-green italic">{{ number_format(array_sum($stats['produccion']), 0) }} KG</p>
                    </div>
                </div>
            </div>

            <!-- Gráfico General -->
            <div class="relative bg-white dark:bg-slate-800/20 p-6 rounded-xl shadow-lg border border-slate-100 dark:border-white/5" wire:ignore>
                <div class="h-48 sm:h-56">
                    <canvas id="mainCosechaChart"></canvas>
                </div>
                <script>
                    document.addEventListener('livewire:initialized', () => {
                        const ctx = document.getElementById('mainCosechaChart');
                        const labels = @js($stats['labels']);
                        const data = @js($stats['ganancias']);

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Balance Neto (S/)',
                                    data: data,
                                    backgroundColor: data.map(v => v >= 0 ? '#00ba2e' : '#f43f5e'),
                                    borderRadius: 6,
                                    barThickness: 20,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(0,0,0,0.9)',
                                        padding: 10,
                                        titleFont: { size: 9 },
                                        bodyFont: { size: 11, weight: '900' }
                                    }
                                },
                                scales: {
                                    y: { grid: { color: 'rgba(0,0,0,0.03)' }, ticks: { font: { weight: 'bold', size: 8 } } },
                                    x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 8 } } }
                                }
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-white/5 shadow-md">
        <div class="w-full md:max-w-xs">
            <div class="relative group">
                <input type="text" wire:model.live="searchCultivo" placeholder="Buscar por producto..."
                       class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg py-2.5 flex pl-10 text-[11px] font-bold shadow-inner focus:ring-1 focus:ring-agri-green/30 transition-all uppercase">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>
        </div>
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Resultados: {{ $cosechas->total() }}</p>
    </div>

    <!-- Grid de Cosechas -->
    @if($cosechas->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center space-y-4">
            <div class="w-20 h-20 bg-slate-50 dark:bg-white/5 rounded-xl flex items-center justify-center text-slate-200">
                <i class="fa-solid fa-box-open text-3xl"></i>
            </div>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Sin registros finalizados</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($cosechas as $c)
                <div class="group relative bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 border border-slate-100 dark:border-white/5">
                    <!-- Contenedor de Imagen y Acciones Centrales -->
                    <div class="relative h-72 overflow-hidden">
                        @if($c->foto_path)
                            <img src="{{ Storage::url($c->foto_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                        @else
                            <div class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-300">
                                <i class="fa-solid fa-leaf text-4xl opacity-10"></i>
                            </div>
                        @endif

                        <!-- Capa de Degradado -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/40 to-transparent"></div>

                        <!-- Badge Estado, Lote y Menú (Top) -->
                        <div class="absolute top-4 right-4 flex items-center gap-2 z-50" x-data="{ openMenu: false }">
                            <span class="px-4 py-1.5 bg-amber-500 text-white text-[11px] font-black uppercase rounded-xl tracking-widest shadow-2xl border border-white/20 italic flex items-center gap-2 animate-in slide-in-from-right-2">
                                <i class="fa-solid fa-barcode text-xs"></i> LOTE: {{ $c->nombre_lote }}
                            </span>
                            <span class="px-2.5 py-1.5 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-[8px] font-black uppercase rounded-lg tracking-widest shadow-xl border border-slate-100 dark:border-white/10">FINALIZADO</span>
                            <div class="relative">
                                <button @click.stop="openMenu = !openMenu" @click.away="openMenu = false" class="w-8 h-8 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-lg flex items-center justify-center shadow-xl hover:bg-agri-green hover:text-white transition-all border border-slate-100 dark:border-white/10">
                                    <i class="fa-solid fa-ellipsis-vertical text-[11px]"></i>
                                </button>
                                <div x-show="openMenu"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 dark:border-white/10 z-50 overflow-hidden"
                                     x-cloak>
                                    <button wire:click="editHarvest({{ $c->id }})" class="w-full px-4 py-3 text-left text-[10px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 flex items-center gap-2 italic transition-colors">
                                        <i class="fa-solid fa-basket-shopping text-agri-green"></i> Editar Cosecha
                                    </button>
                                    <button wire:click="edit({{ $c->id }})" class="w-full px-4 py-3 text-left text-[10px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 flex items-center gap-2 italic transition-colors border-t border-slate-50 dark:border-white/5">
                                        <i class="fa-solid fa-pen-to-square text-blue-500"></i> Editar Siembra
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Botón Central (Aparece en Hover) -->
                        <div wire:click="showCropReport({{ $c->id }})" class="absolute inset-0 z-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 bg-black/60 backdrop-blur-[2px] cursor-pointer">
                            <div class="px-6 py-3 bg-agri-green text-white rounded-2xl font-black text-[9px] uppercase tracking-[0.2em] shadow-2xl border border-white/20 italic flex items-center justify-center gap-3 transform scale-75 group-hover:scale-100 transition-all duration-300">
                                <i class="fa-solid fa-chart-pie text-sm"></i> Ver Informe Detallado
                            </div>
                        </div>

                        <!-- Información Detallada Overlay (Visible por defecto) -->
                        <div class="absolute bottom-0 left-0 right-0 p-6 space-y-3 transition-all duration-300 group-hover:opacity-0 group-hover:invisible">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-2xl font-black text-white italic leading-none uppercase tracking-tighter">{{ $c->detalleCatalogo->nombre }}</h3>
                                    <p class="text-[11px] font-bold text-white/60 uppercase tracking-widest mt-1">{{ $c->variedad ?: 'Genérica' }}</p>
                                </div>
                                <div class="text-right">
                                    @php
                                        // Agrupamos normalizando la calidad a minúsculas para asegurar sumas homogéneas
                                        $cosechasAgrupadas = $c->labores->flatMap->cosechas->groupBy(fn($item) => strtolower($item->calidad));

                                        // Buscamos la calidad principal para el indicador destacado
                                        $calidadPrincipal = $cosechasAgrupadas->has('primera') ? 'primera' : $cosechasAgrupadas->keys()->first();
                                        $grupoPrincipal = $cosechasAgrupadas->get($calidadPrincipal);
                                        $totalPrincipal = $grupoPrincipal ? $grupoPrincipal->sum('cantidad_kg') : 0;
                                    @endphp
                                    <p class="text-[11px] font-black text-white/40 uppercase tracking-widest leading-none mb-1">Cosecha {{ strtoupper($calidadPrincipal ?: 'Lote') }}</p>
                                    <p class="text-m font-black text-agri-green italic leading-none">
                                        {{ number_format($totalPrincipal, 2) }} {{ strtoupper($grupoPrincipal ? $grupoPrincipal->first()->unidad_medida : 'KG') }}
                                    </p>
                                    @php
                                        // Cálculo de Precio Real Promedio (Dato de Venta)
                                        $ventasRelacionadas = $grupoPrincipal ? $grupoPrincipal->flatMap->ventas : collect();
                                        $totalIngreso = $ventasRelacionadas->sum(fn($v) => $v->cantidad_vendida_kg * $v->precio_por_kg);
                                        $totalVendido = $ventasRelacionadas->sum('cantidad_vendida_kg');
                                        $precioReal = $totalVendido > 0 ? ($totalIngreso / $totalVendido) : null;
                                    @endphp
                                    @if($precioReal)
                                        <p class="text-[10px] font-black text-amber-400 italic mt-1 uppercase tracking-tighter">
                                            S/ {{ number_format($precioReal, 2) }} <span class="text-[8px] text-white/40">REAL</span>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Listado de Cosechas Agrupadas por Calidad -->
                            <div class="flex flex-wrap gap-1.5 py-2 border-y border-white/10 bg-white-800/50">
                                @foreach($cosechasAgrupadas as $calidad => $items)
                                    @if($calidad !== $calidadPrincipal)
                                        <div class="px-2 py-0.5 bg-white/5 border border-white/10 rounded flex items-center gap-1.5 shadow-sm">
                                            <span class="text-[11px] font-black text-white italic">
                                                {{ number_format($items->sum('cantidad_kg'), 1) }} {{ strtoupper($items->first()->unidad_medida) }}
                                            </span>
                                            <span class="text-[11px] font-bold text-white/40 uppercase">{{ $calidad }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="grid grid-cols-3 gap-2 pt-1">
                                <div class="flex flex-col">
                                    <span class="text-[12px] font-black text-white/40 uppercase tracking-wider">Siembra</span>
                                    <span class="text-[12px] font-black text-white italic">{{ $c->fecha_siembra ? $c->fecha_siembra->format('d/m/Y') : '---' }}</span>
                                </div>
                                <div class="flex flex-col border-l border-white/10 pl-2">
                                    <span class="text-[12px] font-black text-white/40 uppercase tracking-wider">Cosecha</span>
                                    <span class="text-[12px] font-black text-blue-400 italic">{{ $c->fecha_cosecha_finalizada ? $c->fecha_cosecha_finalizada->format('d/m/Y') : date('d/m/Y') }}</span>
                                </div>
                                <div class="flex flex-col border-l border-white/10 pl-2 text-right">
                                    <span class="text-[12px] font-black text-white/40 uppercase tracking-wider">Terreno</span>
                                    <span class="text-[10px] font-black text-white uppercase truncate">{{ $c->terreno->nombre }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    <div class="mt-8">
        {{ $cosechas->links() }}
    </div>
    @endif

    <!-- MODAL DE REPORTE -->
    <x-modal name="modal-crop-report" :show="false" focusable>
        @if($selectedCropForReport)
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-2xl border border-slate-100 max-w-3xl mx-auto"
             x-data="{
                 initReportChart() {
                     const data = @js($reportData['chart']);
                     if (!data || !data.values || data.values.length === 0) return;
                     const canvas = document.getElementById('cosechaReportChartCanvas');
                     if (!canvas) return;
                     const ctx = canvas.getContext('2d');
                     if (window.myCosechaReportChart) window.myCosechaReportChart.destroy();
                     window.myCosechaReportChart = new Chart(ctx, {
                         type: 'pie',
                         data: {
                             labels: data.labels,
                             datasets: [{
                                 data: data.values,
                                 backgroundColor: ['#3b82f6', '#f59e0b', '#8b5cf6'],
                                 borderWidth: 2,
                                 borderColor: '#ffffff'
                             }]
                         },
                         options: {
                             responsive: true,
                             maintainAspectRatio: false,
                             plugins: {
                                 legend: {
                                     display: true,
                                     position: 'top',
                                     labels: {
                                         usePointStyle: true,
                                         pointStyle: 'circle',
                                         padding: 15,
                                         font: { size: 9, weight: 'bold' }
                                     }
                                 },
                                 tooltip: {
                                     enabled: true,
                                     backgroundColor: 'rgba(0,0,0,0.9)',
                                     padding: 8,
                                     titleFont: { size: 9 },
                                     bodyFont: { size: 10, weight: 'bold' }
                                 }
                             }
                         }
                     });
                 }
             }"
             x-init="setTimeout(() => initReportChart(), 600)"
             x-on:open-modal.window="$event.detail == 'modal-crop-report' ? setTimeout(() => initReportChart(), 700) : null">

            <!-- Header Bar -->
            <div class="bg-[#003a38] px-8 py-3 flex justify-between items-center border-b border-white/10">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 text-white/70">
                        @php
                            $clima = $selectedCropForReport->terreno->latestClima;
                            $icon = 'fa-sun';
                            if($clima) {
                                $cond = strtolower($clima->condicion);
                                if(str_contains($cond, 'lluvia') || str_contains($cond, 'llovizna')) $icon = 'fa-cloud-showers-heavy';
                                elseif(str_contains($cond, 'nublado')) $icon = 'fa-cloud';
                                elseif(str_contains($cond, 'tormenta')) $icon = 'fa-cloud-bolt';
                            }
                        @endphp
                        <i class="fa-solid {{ $icon }} text-amber-400 text-xs"></i>
                        <span class="text-[12px] font-black uppercase tracking-widest italic">
                            @if($clima)
                                {{ round($clima->temperatura) }}°C | {{ $clima->humedad }}% HR
                            @else
                                S/D
                            @endif
                        </span>
                    </div>
                    <div class="h-4 w-px bg-white/10"></div>
                    <span class="text-[12px] font-black text-agri-green uppercase tracking-[0.3em] italic">Análisis de Resultados</span>
                </div>
                <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center text-white/40 hover:text-white transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <div class="relative h-64 w-full group/hero">
                @if($selectedCropForReport->foto_path)<img src="{{ Storage::url($selectedCropForReport->foto_path) }}" class="w-full h-full object-cover">@else<div class="w-full h-full bg-slate-800 flex items-center justify-center"><i class="fa-solid fa-leaf text-5xl text-white/5"></i></div>@endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/20 to-transparent"></div>

                <!-- BADGE DE LOTE POSICIONADO (Top Right HUD) -->
                <div class="absolute top-6 right-8 z-50">
                    <div class="bg-amber-500 text-white px-4 py-2 rounded-xl shadow-2xl border border-white/20 tracking-widest italic flex items-center gap-2 animate-in slide-in-from-right-4 duration-700">
                        <i class="fa-solid fa-barcode text-sm"></i>
                        <span class="text-[11px] font-black uppercase">LOTE: {{ $selectedCropForReport->nombre_lote }}</span>
                    </div>
                </div>

                <div class="absolute bottom-6 left-8 right-8 z-30 space-y-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2 py-0.5 bg-agri-green text-white text-[12px] font-black uppercase rounded tracking-widest">ID #{{ $selectedCropForReport->id }}</span>
                        </div>
                        <h2 class="text-4xl font-black text-white italic tracking-tighter uppercase leading-none">{{ $selectedCropForReport->detalleCatalogo->nombre }}  <span class="text-xl font-bold text-white/50 ml-4 lowercase"> {{ $selectedCropForReport->variedad ?: 'genérica' }}</span></h2>
                    </div>

                    <!-- Información Adicional en Overlay -->
                    <div class="flex flex-wrap items-center gap-6 pt-4 border-t border-white/10">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 bg-agri-green rounded flex items-center justify-center text-white text-xs shadow-lg"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <p class="text-[12px] font-black text-white/40 uppercase tracking-widest">Terreno</p>
                                <p class="text-[12px] font-black text-white uppercase italic leading-none">{{ $selectedCropForReport->terreno->nombre }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 bg-blue-500 rounded flex items-center justify-center text-white text-xs shadow-lg"><i class="fa-solid fa-calendar-check"></i></div>
                            <div>
                                <p class="text-[12px] font-black text-white/40 uppercase tracking-widest">Siembra</p>
                                <p class="text-[12px] font-black text-white uppercase italic leading-none">{{ ($selectedCropForReport->fecha_siembra ?: $selectedCropForReport->fecha_planificada)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 bg-amber-500 rounded flex items-center justify-center text-white text-xs shadow-lg"><i class="fa-solid fa-calendar-plus"></i></div>
                            <div>
                                <p class="text-[12px] font-black text-white/40 uppercase tracking-widest">Cosecha</p>
                                <p class="text-[12px] font-black text-white uppercase italic leading-none">{{ $selectedCropForReport->fecha_cosecha_finalizada ? $selectedCropForReport->fecha_cosecha_finalizada->format('d/m/Y') : 'Finalizada' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-8 max-h-[65vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                     <!-- Columna 1: Distribución de Costos -->
                     <div class="space-y-4">
                        <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] text-center border-b pb-2 italic">Distribución de Gasto</h4>
                        <div class="h-48 relative" wire:ignore>
                            <canvas id="cosechaReportChartCanvas" class="w-full h-full"></canvas>
                        </div>
                     </div>

                     <!-- Columna 2: Inversión Final y Desglose -->
                     <div class="bg-slate-50 dark:bg-slate-800/40 p-6 rounded-2xl border border-slate-100 dark:border-white/5 space-y-6 flex flex-col items-center">
                        <div class="text-center w-full pb-4 border-b border-slate-200 dark:border-white/5">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Inversión Final Acumulada</span>
                            <span class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tighter">
                                S/ {{ number_format($reportData['costoInsumos'] + $reportData['costoManoObra'] + $reportData['costoMaquinaria'], 2) }}
                            </span>
                        </div>
                        <div class="w-full space-y-4">
                            <div class="flex justify-between items-center"><span class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Total Insumos</span><span class="text-xs font-black text-blue-600">S/ {{ number_format($reportData['costoInsumos'], 2) }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Total Mano Obra</span><span class="text-xs font-black text-amber-600">S/ {{ number_format($reportData['costoManoObra'], 2) }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-[10px] font-black text-violet-500 uppercase tracking-widest">Total Maquinaria</span><span class="text-xs font-black text-violet-600">S/ {{ number_format($reportData['costoMaquinaria'], 2) }}</span></div>
                        </div>
                     </div>

                     <!-- Columna 3: Balance y Producción -->
                     <div class="space-y-6">
                        <div class="p-6 {{ $reportData['balance'] >= 0 ? 'bg-emerald-500/10' : 'bg-rose-500/10' }} rounded-2xl border {{ $reportData['balance'] >= 0 ? 'border-emerald-500/20' : 'border-rose-500/20' }} flex flex-col items-center justify-center text-center space-y-2">
                            <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-none">
                                {{ $reportData['esReal'] ? 'Ganancia Neta Real' : 'Ganancia / Balance Proyectado' }}
                            </p>
                            <h3 class="text-3xl font-black {{ $reportData['balance'] >= 0 ? 'text-agri-green' : 'text-rose-500' }} italic tracking-tighter leading-none">
                                S/ {{ number_format($reportData['balance'], 2) }}
                            </h3>
                            <p class="text-[8px] font-bold text-slate-400 uppercase italic">
                                {{ $reportData['esReal'] ? 'Lote liquidado al 100%' : 'Basado en ventas actuales vs costos totales' }}
                            </p>
                        </div>

                        <div class="p-5 bg-slate-900 rounded-2xl border border-white/5 space-y-3">
                            <div class="border-b border-white/5 pb-2 mb-2">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-3">Producción Detallada</p>
                                <div class="space-y-2.5">
                                    @foreach($reportData['produccionAgrupada'] as $item)
                                        <div class="flex justify-between items-center">
                                            <span class="text-[10px] font-bold text-white/50 uppercase italic">{{ $item['calidad'] }}</span>
                                            <span class="text-xs font-black text-agri-green italic tracking-tighter">
                                                @if(strtolower($item['unidad']) === 'kg' && $item['cantidad'] >= 1000)
                                                    {{ number_format($item['cantidad'] / 1000, 2) }} TN
                                                @else
                                                    {{ number_format($item['cantidad'], 2) }} {{ strtoupper($item['unidad']) }}
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex justify-between items-center pt-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ventas Totales</span>
                                <span class="text-xs font-black text-blue-400 italic tracking-tighter">S/ {{ number_format($reportData['ingresosTotales'], 2) }}</span>
                            </div>
                            @if($reportData['costoFletes'] > 0)
                                <div class="flex justify-between items-center pt-1 border-t border-white/5">
                                    <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Total Fletes</span>
                                    <span class="text-xs font-black text-amber-500 italic tracking-tighter">S/ {{ number_format($reportData['costoFletes'], 2) }}</span>
                                </div>
                            @endif
                        </div>
                     </div>
                </div>

                <!-- SECCIÓN DETALLE DE COSECHAS (TABLES PEGADAS) -->
                <div class="space-y-4 pt-6 border-t border-slate-100 dark:border-white/5">
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] italic border-l-4 border-agri-green pl-3">Desglose de Lotes Cosechados y Liquidación</h4>

                    <div class="grid grid-cols-1 gap-3">
                        <div class="overflow-hidden rounded-xl border border-slate-100 dark:border-white/5 shadow-sm">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-white/5">
                                        <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Fecha</th>
                                        <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Calidad</th>
                                        <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Cosechado</th>
                                        <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Vendido</th>
                                        <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Costo Est.</th>
                                        <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Ingreso</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                    @php
                                        $totalInversion = $reportData['costoInsumos'] + $reportData['costoManoObra'] + $reportData['costoMaquinaria'] + $reportData['costoFletes'];
                                        $totalCantKgTn = 0;
                                        foreach($reportData['cosechasDetalladas'] as $i) {
                                            if(strtolower($i['unidad']) === 'tn') $totalCantKgTn += $i['cantidad'] * 1000;
                                            else $totalCantKgTn += $i['cantidad'];
                                        }
                                    @endphp
                                    @foreach($reportData['cosechasDetalladas'] as $item)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                            <td class="px-4 py-3 text-[11px] font-bold text-slate-600 dark:text-slate-300 italic">{{ $item['fecha'] }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $item['calidad'] == 'primera' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : ($item['calidad'] == 'segunda' ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20') }}">
                                                    {{ $item['calidad'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-[11px] font-black text-slate-700 dark:text-white uppercase">{{ number_format($item['cantidad'], 2) }} {{ $item['unidad'] }}</td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-black {{ $item['completado'] ? 'text-blue-500' : 'text-slate-400' }}">{{ number_format($item['vendido'], 2) }}</span>
                                                    @if($item['completado'])
                                                        <i class="fa-solid fa-circle-check text-[10px] text-blue-500"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-[11px] font-black text-slate-400 text-right italic">
                                                @php
                                                    $pesoLote = strtolower($item['unidad']) === 'tn' ? $item['cantidad'] * 1000 : $item['cantidad'];
                                                    $costoRealLote = $totalCantKgTn > 0 ? ($totalInversion / $totalCantKgTn) * $pesoLote : 0;
                                                @endphp
                                                S/ {{ number_format($costoRealLote, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-[11px] font-black text-blue-500 text-right">S/ {{ number_format($item['ingreso'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-900 text-white">
                                        <td colspan="4" class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-right">Totales del Ciclo (Reales)</td>
                                        <td class="px-4 py-3 text-xs font-black text-slate-400 text-right italic">S/ {{ number_format($totalInversion, 2) }}</td>
                                        <td class="px-4 py-3 text-xs font-black text-agri-green text-right">S/ {{ number_format($reportData['ingresosTotales'], 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase italic text-center">
                        <i class="fa-solid fa-circle-info mr-1"></i> El costo est. se calcula proporcionalmente al volumen cosechado sobre la inversión total.
                    </p>
                </div>
            </div>

            <div class="bg-slate-100 dark:bg-black/60 px-8 py-4 flex justify-end items-center border-t border-slate-200 dark:border-white/5">
                <button @click="$dispatch('close')" class="px-8 py-2.5 bg-slate-900 text-white rounded-lg font-black text-[9px] uppercase tracking-widest hover:bg-black transition-all">Cerrar</button>
            </div>
        </div>
        @endif
    </x-modal>

    <!-- MODAL DE EDICIÓN DE RESULTADO DE COSECHA -->
    <x-modal name="modal-edit-harvest-result" :show="false" focusable>
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <div class="bg-[#003a38] px-6 py-4 flex justify-between items-center text-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-agri-green rounded-lg flex items-center justify-center shadow-lg"><i class="fa-solid fa-basket-shopping text-white text-sm"></i></div>
                    <div>
                        <h3 class="text-lg font-black italic tracking-tighter uppercase">Actualizar Resultados de Cosecha</h3>
                        <p class="text-[8px] font-bold text-agri-green uppercase tracking-widest leading-none">Campaña: {{ $cropNombreSelected }}</p>
                    </div>
                </div>
                <button @click="$dispatch('close')" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white/10"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form wire:submit.prevent="saveHarvest" class="p-6 space-y-5 max-h-[80vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400">Fecha de Realización</label>
                        <input wire:model="fecha_cosecha_edit" type="date" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-xs font-bold p-3 uppercase focus:ring-1 focus:ring-agri-green/30" />
                    </div>
                </div>

                <!-- SECCIÓN PRODUCCIÓN -->
                <div class="space-y-4 pt-2">
                    <div class="flex justify-between items-center border-l-4 border-emerald-500 pl-3">
                        <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">DETALLES DE COSECHA (PRODUCCIÓN)</h4>
                        <button type="button" wire:click="addItemCosecha" class="px-5 py-1.5 bg-emerald-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg active:scale-95">
                            <i class="fa-solid fa-plus"></i> AGREGAR LOTE
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach($itemsCosecha as $idx => $item)
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-emerald-500/5 p-4 rounded-xl border border-emerald-500/20 items-end" wire:key="harvest-edit-{{ $idx }}">
                                <div class="md:col-span-3 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase flex items-center gap-2"><i class="fa-solid fa-weight-hanging text-emerald-500"></i> Cantidad</label>
                                    <input type="number" step="0.01" wire:model="itemsCosecha.{{ $idx }}.cantidad" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm">
                                </div>
                                <div class="md:col-span-3 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase flex items-center gap-2"><i class="fa-solid fa-ruler-combined text-emerald-500"></i> Unidad</label>
                                    <select wire:model="itemsCosecha.{{ $idx }}.unidad" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm">
                                        <option value="kg">KILOGRAMOS (KG)</option>
                                        <option value="tn">TONELADAS (TN)</option>
                                        <option value="sacos">SACOS (U)</option>
                                        <option value="und">UNIDADES (UND)</option>
                                        <option value="jabas">JABAS</option>
                                    </select>
                                </div>
                                <div class="md:col-span-3 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase flex items-center gap-2"><i class="fa-solid fa-star text-emerald-500"></i> Calidad</label>
                                    <select wire:model="itemsCosecha.{{ $idx }}.calidad" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm">
                                        <option value="primera">PRIMERA (A)</option>
                                        <option value="segunda">SEGUNDA (B)</option>
                                        <option value="descarte">DESCARTE / MERMA</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase flex items-center gap-2"><i class="fa-solid fa-money-bill-trend-up text-emerald-500"></i> Costo Op.</label>
                                    <input type="number" step="0.01" wire:model="itemsCosecha.{{ $idx }}.costo_operativo" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm" placeholder="0.00">
                                </div>
                                <div class="md:col-span-1 flex justify-center">
                                    @if(count($itemsCosecha) > 1)
                                        <button type="button" wire:click="removeItemCosecha({{ $idx }})" class="w-9 h-9 bg-rose-50 text-rose-500 rounded-lg flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm"><i class="fa-solid fa-trash-can text-xs"></i></button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- SECCIÓN INSUMOS -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-white/5">
                    <div class="flex justify-between items-center border-l-4 border-blue-500 pl-3">
                        <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">INSUMOS UTILIZADOS</h4>
                        <button type="button" wire:click="addItemInsumo" class="px-5 py-1.5 bg-blue-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg">
                            <i class="fa-solid fa-plus"></i> AGREGAR INSUMO
                        </button>
                    </div>
                    <div class="space-y-3">
                        @foreach($itemsInsumos as $idx => $item)
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-blue-500/5 p-4 rounded-xl border border-blue-500/20 items-end" wire:key="insumo-{{ $idx }}">
                                <div class="md:col-span-4 space-y-1 relative">
                                    <label class="text-[9px] font-black text-slate-500 uppercase flex items-center gap-2"><i class="fa-solid fa-flask text-blue-500"></i> Nombre Insumo</label>
                                    <input type="text" wire:model.live="itemsInsumos.{{ $idx }}.insumo_nombre" wire:input="searchInsumo({{ $idx }}, $event.target.value)" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm" placeholder="Buscar...">
                                    @if($showIns && $activeIdx === $idx)
                                        <div class="absolute w-full mt-1 bg-white dark:bg-slate-900 rounded-lg shadow-2xl border border-slate-100 z-50 overflow-hidden max-h-40 overflow-y-auto">
                                            @foreach($resultsIns as $ri)
                                                <div wire:click="selectInsumoItem({{ $idx }}, {{ $ri->id }}, '{{ $ri->nombre }}')" class="p-3 hover:bg-blue-50 cursor-pointer text-[10px] font-black border-b last:border-0 border-slate-50 uppercase tracking-widest">{{ $ri->nombre }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase">Cantidad</label>
                                    <input type="number" wire:model.live="itemsInsumos.{{ $idx }}.cantidad" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm">
                                </div>
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase">Precio Un.</label>
                                    <input type="number" step="0.01" wire:model.live="itemsInsumos.{{ $idx }}.costo_unitario" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm">
                                </div>
                                <div class="md:col-span-3 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase">Flete/Envío</label>
                                    <input type="number" step="0.01" wire:model.live="itemsInsumos.{{ $idx }}.costo_flete" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm" placeholder="0.00">
                                </div>
                                <div class="md:col-span-1 flex justify-center">
                                    <button type="button" wire:click="removeItem('insumo', {{ $idx }})" class="w-9 h-9 bg-rose-50 text-rose-500 rounded-lg flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm"><i class="fa-solid fa-trash-can text-xs"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- SECCIÓN MANO DE OBRA -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-white/5">
                    <div class="flex justify-between items-center border-l-4 border-amber-500 pl-3">
                        <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">PERSONAL / MANO DE OBRA</h4>
                        <button type="button" wire:click="addItemManoObra" class="px-5 py-1.5 bg-amber-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg">
                            <i class="fa-solid fa-plus"></i> AGREGAR PERSONAL
                        </button>
                    </div>
                    <div class="space-y-3">
                        @foreach($itemsManoObra as $idx => $item)
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-amber-500/5 p-4 rounded-xl border border-amber-500/20 items-end" wire:key="mano-{{ $idx }}">
                                <div class="md:col-span-4 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase">Tipo de Personal</label>
                                    <select wire:model="itemsManoObra.{{ $idx }}.tipo_id" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm">
                                        <option value="">Elegir...</option>
                                        @foreach($manoObraTipos as $mot) <option value="{{ $mot->id }}">{{ $mot->nombre }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase">Cantidad</label>
                                    <input type="number" wire:model.live="itemsManoObra.{{ $idx }}.cantidad" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm">
                                </div>
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase">Días</label>
                                    <input type="number" wire:model.live="itemsManoObra.{{ $idx }}.dias" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm">
                                </div>
                                <div class="md:col-span-3 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase">Costo x Día</label>
                                    <input type="number" step="0.01" wire:model.live="itemsManoObra.{{ $idx }}.costo_dia" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm" placeholder="0.00">
                                </div>
                                <div class="md:col-span-1 flex justify-center">
                                    <button type="button" wire:click="removeItem('mano', {{ $idx }})" class="w-9 h-9 bg-rose-50 text-rose-500 rounded-lg flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm"><i class="fa-solid fa-trash-can text-xs"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- SECCIÓN MAQUINARIA -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-white/5">
                    <div class="flex justify-between items-center border-l-4 border-violet-500 pl-3">
                        <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">MAQUINARIA UTILIZADA</h4>
                        <button type="button" wire:click="addItemMaquinaria" class="px-5 py-1.5 bg-violet-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg">
                            <i class="fa-solid fa-plus"></i> AGREGAR MAQUINA
                        </button>
                    </div>
                    <div class="space-y-3">
                        @foreach($itemsMaquinaria as $idx => $item)
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-violet-500/5 p-4 rounded-xl border border-violet-500/20 items-end" wire:key="maq-{{ $idx }}">
                                <div class="md:col-span-4 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase">Nombre / Equipo</label>
                                    <input type="text" wire:model="itemsMaquinaria.{{ $idx }}.nombre" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm" placeholder="Ej: Tractor John Deere">
                                </div>
                                <div class="md:col-span-4 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase">Labor Realizada</label>
                                    <input type="text" wire:model="itemsMaquinaria.{{ $idx }}.labor" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm" placeholder="Cosecha">
                                </div>
                                <div class="md:col-span-3 space-y-1">
                                    <label class="text-[9px] font-black text-slate-500 uppercase">Costo Total</label>
                                    <input type="number" step="0.01" wire:model.live="itemsMaquinaria.{{ $idx }}.costo_total" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-xs font-black shadow-sm" placeholder="0.00">
                                </div>
                                <div class="md:col-span-1 flex justify-center">
                                    <button type="button" wire:click="removeItem('maq', {{ $idx }})" class="w-9 h-9 bg-rose-50 text-rose-500 rounded-lg flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm"><i class="fa-solid fa-trash-can text-xs"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- RESUMEN DE COSTOS -->
                <div class="bg-slate-900 rounded-xl p-6 flex flex-col md:flex-row justify-between items-center gap-6 border border-white/10 shadow-2xl">
                    <div class="grid grid-cols-3 gap-8">
                        <div class="text-center md:text-left"><p class="text-[8px] font-black text-slate-500 uppercase">Insumos</p><p class="text-xs font-black text-white italic">S/ {{ number_format($costo_insumos_total, 2) }}</p></div>
                        <div class="text-center md:text-left"><p class="text-[8px] font-black text-slate-500 uppercase">Mano Obra</p><p class="text-xs font-black text-white italic">S/ {{ number_format($costo_mano_obra_total, 2) }}</p></div>
                        <div class="text-center md:text-left"><p class="text-[8px] font-black text-slate-500 uppercase">Maquinaria</p><p class="text-xs font-black text-white italic">S/ {{ number_format($costo_maquinaria_total, 2) }}</p></div>
                    </div>
                    <div class="text-center md:text-right border-t md:border-t-0 md:border-l border-white/10 pt-4 md:pt-0 md:pl-8">
                        <p class="text-[10px] font-black text-agri-green uppercase tracking-widest leading-none mb-1">TOTAL INVERSIÓN COSECHA</p>
                        <p class="text-3xl font-black text-white italic tracking-tighter">S/ {{ number_format($costo_total, 2) }}</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400">Observaciones Generales de la Labor</label>
                    <textarea wire:model="observaciones_cosecha" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-xs font-bold p-3 min-h-[80px]" placeholder="Notas adicionales sobre la recolección..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 dark:border-white/5">
                    <button type="button" @click="$dispatch('close')" class="px-6 py-3 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-lg font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Cancelar</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-10 py-3 bg-agri-green text-white rounded-lg font-black text-[10px] uppercase tracking-widest shadow-lg shadow-agri-green/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up" wire:loading.remove></i>
                        <i class="fa-solid fa-spinner fa-spin" wire:loading></i>
                        Guardar Resultados
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- MODAL DE REGISTRO (Siembra) -->
    <x-modal name="modal-crop-manager" :show="false" focusable>
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10" x-data="{ showTerrenos: false, showCultivos: false }">
            <div class="bg-[#003a38] px-6 py-4 flex justify-between items-center text-white">
                <h3 class="text-lg font-black italic tracking-tighter uppercase">Actualizar Ficha de Siembra</h3>
                <button @click="$dispatch('close')" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white/10"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form wire:submit.prevent="save" class="p-6 space-y-5 max-h-[80vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Terreno (Solo lectura en edición desde cosechas) -->
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase text-agri-green tracking-widest">1. Terreno Asignado</label>
                        <div class="flex items-center justify-between bg-emerald-50 dark:bg-white/5 border-2 border-emerald-500/30 rounded-xl p-3">
                            <div class="flex items-center space-x-3 overflow-hidden">
                                <div class="w-10 h-10 rounded-lg bg-agri-green flex items-center justify-center text-white"><i class="fa-solid fa-mountain-sun"></i></div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $terrenoNombreSelected }}</p>
                                    <p class="text-[8px] font-bold text-emerald-600 uppercase">Campaña finalizada</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Cultivo (Solo lectura en edición desde cosechas) -->
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase text-agri-green tracking-widest">2. Producto</label>
                        <div class="flex items-center justify-between bg-blue-50 dark:bg-white/5 border-2 border-blue-500/30 rounded-xl p-3">
                            <div class="flex items-center space-x-3 overflow-hidden">
                                <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center text-white"><i class="fa-solid fa-leaf"></i></div>
                                <p class="text-[10px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $cultivoNombreSelected }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-50 dark:border-white/5">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400">Variedad del Producto</label>
                        <input wire:model.live="variedad" type="text" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-xs font-bold p-3 uppercase focus:ring-1 focus:ring-agri-green/30" placeholder="Ej: Morada, Canchan..." />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400">Nombre de Lote (Auto)</label>
                        <input wire:model="nombre_lote" type="text" readonly class="block w-full bg-slate-100 dark:bg-slate-800/50 border-none rounded-lg text-xs font-black text-agri-green italic p-3" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Área (ha) *</label>
                        <input wire:model.live="area_destinada" type="number" step="0.01" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-xs font-bold p-3" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Plantas Est.</label>
                        <input wire:model="plantas_estimadas" type="number" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-xs font-bold p-3" placeholder="Cant. aprox." />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Rend. Esp. (tn/ha)</label>
                        <input wire:model="rendimiento_esperado_tn_ha" type="number" step="0.1" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-xs font-bold p-3" placeholder="0.0" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Fecha Planificada</label>
                        <input wire:model="fecha_planificada" type="date" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-xs font-bold p-3 uppercase" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Fecha Siembra Real</label>
                        <input wire:model="fecha_siembra" type="date" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-xs font-bold p-3 uppercase" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-slate-400 uppercase">Observaciones Generales</label>
                    <textarea wire:model="observaciones" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-xs font-bold p-3 min-h-[80px]" placeholder="Notas sobre el cultivo..."></textarea>
                </div>

                <div class="space-y-4 pt-2">
                    <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic border-b border-slate-100 dark:border-white/5 pb-2">Evidencia Fotográfica</h4>
                    <div class="flex items-center space-x-6 bg-slate-50 dark:bg-white/5 p-4 rounded-xl border border-dashed border-slate-200 dark:border-white/10">
                        <div class="w-24 h-20 rounded-lg overflow-hidden bg-white dark:bg-slate-800 shrink-0 shadow-sm border border-slate-100">
                            @if($cropPhoto && method_exists($cropPhoto, 'isPreviewable') && $cropPhoto->isPreviewable())
                                <img src="{{ $cropPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif($currentPhotoPath)
                                <img src="{{ Storage::url($currentPhotoPath) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fa-solid fa-camera text-2xl"></i></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" wire:model="cropPhoto" class="text-[9px] file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-[9px] file:font-black file:uppercase file:bg-agri-green file:text-white hover:file:bg-emerald-600 transition-all cursor-pointer"/>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 dark:border-white/5">
                    <button type="button" @click="$dispatch('close')" class="px-6 py-3 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-lg font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Cancelar</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-10 py-3 bg-agri-green text-white rounded-lg font-black text-[10px] uppercase tracking-widest shadow-lg shadow-agri-green/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up" wire:loading.remove></i>
                        <i class="fa-solid fa-spinner fa-spin" wire:loading></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
    @endif
</div>

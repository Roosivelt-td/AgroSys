<div class="space-y-4">
    @if(auth()->user()->rol_id == 1)
        <livewire:dashboard.super-admin />
    @elseif(!$hasOrg && empty($stats))
        <!-- Vista Bienvenida -->
        <div class="bg-white dark:bg-slate-900 p-10 rounded-xl shadow-xl text-center max-w-4xl mx-auto border border-slate-100">
            <div class="w-14 h-14 bg-agri-mint rounded-xl flex items-center justify-center mx-auto mb-6 text-agri-green"><i class="fa-solid fa-tractor text-2xl"></i></div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-4 uppercase italic">Bienvenido</h2>
            <p class="text-slate-500 mb-8 text-xs uppercase font-bold tracking-widest">Inicie registrando su organización o lote agrícola.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <button x-on:click.prevent="$dispatch('open-modal', 'create-organization')" class="px-6 py-2.5 bg-agri-green text-white rounded-lg font-black shadow-md transition-all hover:scale-105 uppercase text-[10px] tracking-widest">SOLICITAR ORG</button>
                <button x-on:click.prevent="$dispatch('open-modal', 'join-organization')" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg font-black transition-all hover:scale-105 uppercase text-[10px] tracking-widest">UNIRSE</button>
            </div>
        </div>
    @else
        <!-- HEADER REFINADO (COMPACTO) -->
        <div class="flex flex-col lg:flex-row justify-between items-center gap-4 mb-1">
            <div class="flex flex-col">
                <h2 class="text-3xl font-black text-slate-900 dark:text-white italic tracking-tighter uppercase leading-none">Panel de Control</h2>
                <p class="text-slate-400 font-bold text-[8px] uppercase mt-0.5 tracking-[0.3em]">{{ $organizacion->nombre }}</p>
            </div>
            <!-- FILTRO CON ANCHURA DINÁMICA MÍNIMA -->
            <div class="relative w-fit">
                <select wire:model.live="cultivoFiltroId" class="w-full min-w-[200px] max-w-[500px] rounded-lg border-2 border-slate-100 bg-white dark:bg-slate-800 text-slate-700 dark:text-white text-[10px] font-black py-1.5 pl-4 pr-10 shadow-sm focus:ring-2 focus:ring-agri-green transition-all uppercase italic appearance-none cursor-pointer">
                    <option value="">VER TODOS LOS CULTIVOS REGISTRADOS</option>
                    @foreach($listaCultivosFiltro as $cf)
                        <option value="{{ $cf->id }}">{{ $cf->nombre_lote }} - {{ $cf->detalleCatalogo->nombre }} ({{ $cf->variedad }}) · {{ $cf->fecha_siembra->format('d/m/Y') }}</option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down absolute right-3 top-[10px] text-agri-green pointer-events-none text-[9px]"></i>
            </div>
        </div>

        <!-- KPIs EN UNA SOLA LÍNEA (ULTRA COMPACTOS) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            @php
                $kpis = [
                    ['label' => 'Terrenos', 'value' => $stats['terrenos'], 'color' => 'bg-agri-green', 'icon' => 'fa-map', 'iconColor' => 'text-emerald-600', 'bgIcon' => 'bg-emerald-500/5'],
                    ['label' => 'Cultivos', 'value' => $stats['cultivos'], 'color' => 'bg-blue-500', 'icon' => 'fa-seedling', 'iconColor' => 'text-blue-500', 'bgIcon' => 'bg-blue-500/5'],
                    ['label' => 'Ventas', 'value' => 'S/ '.number_format($stats['ventas'], 0), 'color' => 'bg-blue-600', 'icon' => 'fa-dollar-sign', 'iconColor' => 'text-blue-600', 'bgIcon' => 'bg-blue-600/5'],
                    ['label' => 'Inversión', 'value' => 'S/ '.number_format($stats['inversiones'], 0), 'color' => 'bg-rose-500', 'icon' => 'fa-receipt', 'iconColor' => 'text-rose-600', 'bgIcon' => 'bg-rose-500/5'],
                    ['label' => 'ROI %', 'value' => $stats['roi'].'%', 'color' => 'bg-emerald-500', 'icon' => 'fa-chart-pie', 'iconColor' => 'text-emerald-600', 'bgIcon' => 'bg-emerald-500/5'],
                    ['label' => 'Neto/HA', 'value' => 'S/ '.number_format($stats['margen_ha'], 0), 'color' => 'bg-amber-500', 'icon' => 'fa-expand', 'iconColor' => 'text-amber-600', 'bgIcon' => 'bg-amber-500/5'],
                ];
            @endphp
            @foreach($kpis as $kpi)
                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm relative overflow-hidden border border-slate-50 dark:border-white/5 flex items-center justify-between group">
                    <div class="absolute left-0 top-[35%] bottom-[35%] w-1 {{ $kpi['color'] }} rounded-r-full"></div>
                    <div class="pl-2">
                        <p class="text-slate-400 text-[7px] font-black uppercase tracking-wider mb-0.5">{{ $kpi['label'] }}</p>
                        <p class="text-lg font-black text-slate-800 dark:text-white italic leading-none">{{ $kpi['value'] }}</p>
                    </div>
                    <div class="w-8 h-8 {{ $kpi['bgIcon'] }} rounded-md flex items-center justify-center {{ $kpi['iconColor'] }} shadow-inner">
                        <i class="fa-solid {{ $kpi['icon'] }} text-sm"></i>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- SECCIÓN SUPERIOR DE ESTRATEGIA (CIRCULAR + CLIMA/CRÍTICOS) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-2">
            <!-- INVERSIÓN CIRCULAR -->
            <div class="lg:col-span-1 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[350px]"
                 x-data="{
                    chart: null,
                    init() {
                        this.$nextTick(() => {
                            const ctx = document.getElementById('investmentPieChartHeader');
                            if (!ctx) return;
                            this.chart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Insumos', 'Mano Obra', 'Maquinaria', 'Cosecha', 'Flete'],
                                    datasets: [{
                                        data: [
                                            {{ $stats['invest_breakdown']['insumos'] }},
                                            {{ $stats['invest_breakdown']['mano_obra'] }},
                                            {{ $stats['invest_breakdown']['maquinaria'] }},
                                            {{ $stats['invest_breakdown']['cosecha'] }},
                                            {{ $stats['invest_breakdown']['flete'] }}
                                        ],
                                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#e11d48'],
                                        borderWidth: 0
                                    }]
                                },
                                options: {
                                    responsive: true, maintainAspectRatio: false, cutout: '75%',
                                    plugins: { legend: { position: 'bottom', labels: { font: { size: 9, weight: 'bold' }, padding: 10, boxWidth: 8 } } }
                                }
                            });
                        });
                    }
                 }"
                 wire:key="invest-pie-header-{{ $cultivoFiltroId }}"
            >
                <h4 class="text-[9px] font-black text-slate-700 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-2"><i class="fa-solid fa-chart-pie text-indigo-500"></i> Distribución de Inversión</h4>
                <div class="flex-1 relative">
                    <canvas id="investmentPieChartHeader"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pb-6">
                        <p class="text-[8px] font-black text-slate-400 uppercase">Total</p>
                        <p class="text-sm font-black text-slate-800 dark:text-white">S/ {{ number_format($stats['inversiones'], 0) }}</p>
                    </div>
                </div>
            </div>

            <!-- CLIMA OPERATIVO Y PROGRESO -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-gradient-to-br from-blue-50/50 to-indigo-50/50 dark:from-slate-800 dark:to-slate-900 p-6 rounded-xl shadow-md border border-blue-100/20 flex items-center justify-between group h-[165px]">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="text-[10px] font-black text-slate-700 dark:text-white uppercase tracking-widest italic leading-none">Ventana Operativa</h4>
                            <span class="px-2 py-0.5 bg-emerald-500 text-white rounded-[4px] text-[7px] font-black uppercase">Óptimo</span>
                        </div>
                        <div class="flex items-end gap-6">
                            <div class="text-5xl font-mono font-black text-slate-800 dark:text-white tracking-tighter">24°C</div>
                            <div class="pb-1 space-y-1">
                                <p class="text-[9px] font-bold text-slate-500 uppercase">Humedad: <span class="text-blue-600">65%</span></p>
                                <p class="text-[9px] font-bold text-slate-500 uppercase">Viento: <span class="text-amber-600">5km/h</span></p>
                            </div>
                        </div>
                        @if($cultivoSeleccionado)
                            <div class="mt-4 w-full">
                                <div class="flex justify-between items-center mb-1"><p class="text-[8px] font-black text-slate-400 uppercase">Progreso del Cultivo</p><p class="text-[8px] font-black text-agri-green uppercase">{{ $stats['progreso'] }}%</p></div>
                                <div class="w-full bg-slate-200 rounded-full h-1.5 dark:bg-slate-700"><div class="bg-agri-green h-1.5 rounded-full transition-all duration-1000" style="width: {{ $stats['progreso'] }}%"></div></div>
                            </div>
                        @endif
                    </div>
                    <div class="text-amber-500 text-7xl animate-pulse ml-4"><i class="fa-solid fa-sun"></i></div>
                </div>

                <!-- TAREAS CRÍTICAS URGENTES -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-md border border-slate-100 flex flex-col justify-center h-[165px]">
                    <h4 class="text-[9px] font-black text-slate-700 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-3"><i class="fa-solid fa-bolt-lightning text-amber-500"></i> Próximas 48h Críticas</h4>
                    <div class="grid grid-cols-3 gap-3">
                        @forelse($laboresCriticas as $lc)
                            <div class="bg-rose-50/50 p-2 rounded-lg border-l-4 border-rose-500 shadow-sm">
                                <p class="text-[9px] font-black text-slate-800 uppercase leading-none truncate">{{ $lc->detalleCatalogo->nombre }}</p>
                                <p class="text-[7px] text-slate-500 font-bold uppercase mt-1 truncate">{{ $lc->cultivo->nombre_lote }}</p>
                                <div class="mt-2 flex justify-between items-center"><span class="text-[7px] font-black text-rose-600 uppercase">{{ $lc->fecha_realizacion->diffForHumans(null, true) }}</span></div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-4"><p class="text-[9px] text-slate-400 italic uppercase tracking-widest font-bold">Todo al día</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>



        <!-- SECCIÓN MAESTRA: GRÁFICOS (16) + LABORES (8) -->
        <div class="grid grid-cols-1 lg:grid-cols-24 gap-6 mt-4"
             style="display: grid; grid-template-columns: repeat(24, minmax(0, 1fr));"
             x-data="{
                charts: { c1: null, c2: null, c3: null, c4: null },
                initCharts() {
                    this.$nextTick(() => { this.renderC1(); this.renderC2(); this.renderC3(); this.renderC4(); });
                },
                renderC1() {
                    const ctx = document.getElementById('lineActChartMaster');
                    if (!ctx) return;
                    if (this.charts.c1) this.charts.c1.destroy();
                    this.charts.c1 = new Chart(ctx, {
                        type: 'line',
                        data: { labels: @js($chartData['meses']), datasets: [{ label: 'Actividad', data: @js($chartData['actividad']), borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.02)', tension: 0.4, fill: true, pointRadius: 3, borderWidth: 3 }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
                    });
                },
                renderC2() {
                    const ctx = document.getElementById('barBalanceTotal');
                    if (!ctx) return;
                    if (this.charts.c2) this.charts.c2.destroy();
                    this.charts.c2 = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['BALANCE TOTAL'],
                            datasets: [
                                { label: 'Inversión', data: [{{ $stats['inversiones'] }}], backgroundColor: '#e11d48', borderRadius: 5 },
                                { label: 'Venta Bruta', data: [{{ $stats['ventas'] }}], backgroundColor: '#2563eb', borderRadius: 5 },
                                { label: 'Ganancia Neta', data: [{{ $stats['ganancia'] }}], backgroundColor: '#10b981', borderRadius: 5 }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { font: { size: 10, weight: 'bold' }, boxWidth: 12 } },
                                tooltip: { callbacks: { label: (c) => c.dataset.label + ': S/ ' + c.raw.toLocaleString() } }
                            },
                            scales: { y: { grid: { color: 'rgba(0,0,0,0.03)' }, ticks: { font: { size: 9 }, callback: (v) => 'S/ ' + v.toLocaleString() } } }
                        }
                    });
                },
                renderC3() {
                    const ctx = document.getElementById('barRentMonthly');
                    if (!ctx) return;
                    if (this.charts.c3) this.charts.c3.destroy();
                    this.charts.c3 = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @js($chartData['meses']),
                            datasets: [
                                { type: 'bar', label: 'Inv', data: @js($chartData['inversiones']), backgroundColor: '#e11d48', yAxisID: 'y' },
                                { type: 'bar', label: 'Ven', data: @js($chartData['ventas']), backgroundColor: '#2563eb', yAxisID: 'y' },
                                { type: 'line', label: 'ROI %', data: @js($chartData['roi']), borderColor: '#8b5cf6', backgroundColor: '#8b5cf6', borderWidth: 2, fill: false, yAxisID: 'y1', tension: 0.4, pointRadius: 2 }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 9 } } } },
                            scales: {
                                y: { type: 'linear', position: 'left', grid: { display: false } },
                                y1: { type: 'linear', position: 'right', grid: { display: false }, ticks: { callback: (v) => v + '%' } }
                            }
                        }
                    });
                },
                renderC4() {
                    const ctx = document.getElementById('barOpChartMaster');
                    if (!ctx) return;
                    if (this.charts.c4) this.charts.c4.destroy();
                    this.charts.c4 = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Insumos', 'Mano Obra', 'Maquinaria', 'Cosecha', 'Flete'],
                            datasets: [{
                                data: [
                                    {{ $stats['invest_breakdown']['insumos'] }},
                                    {{ $stats['invest_breakdown']['mano_obra'] }},
                                    {{ $stats['invest_breakdown']['maquinaria'] }},
                                    {{ $stats['invest_breakdown']['cosecha'] }},
                                    {{ $stats['invest_breakdown']['flete'] }}
                                ],
                                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#e11d48'],
                                borderRadius: 10
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { x: { grid: { color: 'rgba(0,0,0,0.03)' }, ticks: { font: { size: 9 }, callback: (v) => 'S/ ' + v.toLocaleString() } } }
                        }
                    });
                }
             }"
             x-init="initCharts()"
             wire:key="master-dash-sync-{{ $cultivoFiltroId }}"
        >
            <!-- COLUMNA IZQUIERDA: GRÁFICOS (16 COLUMNAS) -->
            <div style="grid-column: span 16 / span 16;" class="space-y-4">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[450px]" wire:ignore>
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-3">
                            <h4 class="text-[9px] font-black text-slate-700 dark:text-white uppercase tracking-widest italic flex items-center gap-2"><i class="fa-solid fa-chart-simple text-blue-600"></i> Balance de Rentabilidad</h4>
                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 rounded text-[8px] font-black uppercase shadow-inner">ROI: {{ $stats['roi'] }}%</span>
                        </div>
                        <span class="text-[8px] font-black text-agri-green uppercase bg-agri-green/10 px-2 py-0.5 rounded italic">FILTRO ACTIVO</span>
                    </div>
                    <div class="flex-1 relative"><canvas id="barBalanceTotal"></canvas></div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[450px]" wire:ignore>
                    <h4 class="text-[9px] font-black text-slate-700 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-4"><i class="fa-solid fa-calendar-days text-indigo-500"></i> Rentabilidad Mensual</h4>
                    <div class="flex-1 relative"><canvas id="barRentMonthly"></canvas></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[350px]" wire:ignore>
                        <h4 class="text-[9px] font-black text-slate-700 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-4"><i class="fa-solid fa-chart-line text-emerald-500"></i> Evolución de Actividad</h4>
                        <div class="flex-1 relative"><canvas id="lineActChartMaster"></canvas></div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[350px]" wire:ignore>
                        <h4 class="text-[9px] font-black text-slate-700 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-4"><i class="fa-solid fa-gears text-slate-500"></i> Estructura de Costos</h4>
                        <div class="flex-1 relative"><canvas id="barOpChartMaster"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: OPERACIÓN Y RENDIMIENTO (8 COLUMNAS) -->
            <div style="grid-column: span 8 / span 8;" class="flex flex-col gap-4">
                <!-- LABORES RECIENTES -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-md border border-slate-50 flex flex-col h-[600px] sticky top-20">
                    <div class="flex justify-between items-center border-b pb-3 mb-5">
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-2 italic">
                            <i class="fa-solid fa-list-check text-amber-500"></i> Labores Recientes
                        </h4>
                        <a href="/labores" class="text-[8px] font-black text-agri-green hover:underline uppercase tracking-widest" wire:navigate>VER TODOS</a>
                    </div>

                    <div class="flex-1 overflow-y-auto space-y-2 custom-scrollbar pr-2">
                        @forelse($ultimas5Labores as $labor)
                            @php
                                $icon = match(true) {
                                    str_contains(strtolower($labor->detalleCatalogo->nombre), 'riego') => 'fa-droplet text-blue-500',
                                    str_contains(strtolower($labor->detalleCatalogo->nombre), 'abono') || str_contains(strtolower($labor->detalleCatalogo->nombre), 'fertiliza') => 'fa-flask text-emerald-500',
                                    str_contains(strtolower($labor->detalleCatalogo->nombre), 'fumiga') || str_contains(strtolower($labor->detalleCatalogo->nombre), 'aplica') => 'fa-spray-can text-rose-500',
                                    str_contains(strtolower($labor->detalleCatalogo->nombre), 'cosecha') => 'fa-wheat-awn text-amber-600',
                                    str_contains(strtolower($labor->detalleCatalogo->nombre), 'siembra') => 'fa-seedling text-agri-green',
                                    default => 'fa-wrench text-slate-400'
                                };
                            @endphp
                            <div wire:click="mostrarDetalleLabor({{ $labor->id }})" class="p-2.5 bg-slate-50 dark:bg-white/5 rounded-lg border border-slate-100 hover:border-agri-green transition-all shadow-sm group cursor-pointer">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-lg bg-white dark:bg-slate-800 flex items-center justify-center shadow-sm">
                                        <i class="fa-solid {{ $icon }} text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] font-black text-slate-800 dark:text-white uppercase leading-none truncate">{{ $labor->detalleCatalogo->nombre }}</p>
                                        <p class="text-[9px] font-bold text-agri-green mt-1 uppercase italic truncate">{{ $labor->cultivo->detalleCatalogo->nombre }} ({{ $labor->cultivo->variedad }})</p>
                                        <div class="flex justify-between items-center mt-1.5 opacity-60">
                                            <p class="text-[8px] font-black text-slate-500 uppercase tracking-tighter">{{ number_format($labor->cultivo->area_destinada, 1) }} ha</p>
                                            <p class="text-[8px] font-bold text-slate-500 uppercase tracking-tighter">{{ $labor->fecha_realizacion->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-20 text-slate-200"><i class="fa-solid fa-ghost text-4xl mb-4"></i><p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Sin registros</p></div>
                        @endforelse
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-50 text-center">
                        <p class="text-[8px] font-black text-agri-green uppercase tracking-[0.2em] flex items-center justify-center gap-1">
                            <span class="w-1.5 h-1.5 bg-agri-green rounded-full animate-ping"></span> Auditoría IA Activa
                        </p>
                    </div>
                </div>

                <!-- SECCIÓN DE RENDIMIENTO (DENTRO DEL SIDEBAR) -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[280px]"
                     x-data="{
                        chart: null,
                        init() {
                            this.$nextTick(() => {
                                const ctx = document.getElementById('yieldCompareChartSidebar');
                                if (!ctx) return;
                                if (this.chart) this.chart.destroy();
                                this.chart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: ['RENDIMIENTO (TN/HA)'],
                                        datasets: [
                                            { label: 'Real', data: [{{ $stats['yield']['real'] }}], backgroundColor: '#10b981', borderRadius: 6 },
                                            { label: 'Estimado', data: [{{ $stats['yield']['estimado'] }}], backgroundColor: '#cbd5e1', borderRadius: 6 }
                                        ]
                                    },
                                    options: {
                                        indexAxis: 'y',
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: { legend: { position: 'top', labels: { font: { weight: 'bold', size: 8 }, boxWidth: 10 } } },
                                        scales: { x: { ticks: { font: { size: 8 } } } }
                                    }
                                });
                            });
                        }
                     }"
                     x-init="init()"
                     wire:key="yield-sync-sidebar-v2-{{ $cultivoFiltroId }}"
                >
                    <h4 class="text-[9px] font-black text-slate-700 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-2"><i class="fa-solid fa-chart-line text-emerald-500"></i> Productividad Real vs Estimada</h4>
                    <div class="flex-1 relative"><canvas id="yieldCompareChartSidebar"></canvas></div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL DETALLE LABOR -->
    <x-modal name="labor-detail-modal" focusable>
        @if($selectedLabor)
            <div class="p-6">
                <div class="flex justify-between items-start border-b pb-4 mb-4">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase italic tracking-tighter">{{ $selectedLabor->detalleCatalogo->nombre }}</h3>
                        <p class="text-xs font-bold text-agri-green uppercase tracking-widest mt-1">{{ $selectedLabor->cultivo->detalleCatalogo->nombre }} ({{ $selectedLabor->cultivo->variedad }})</p>
                    </div>
                    <button x-on:click="$dispatch('close')" class="text-slate-400 hover:text-rose-500 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50 p-3 rounded-lg"><p class="text-[9px] font-black text-slate-400 uppercase mb-1">Fecha Realizada</p><p class="text-xs font-black text-slate-800 uppercase italic">{{ $selectedLabor->fecha_realizacion->format('d/m/Y') }}</p></div>
                            <div class="bg-rose-50 p-3 rounded-lg"><p class="text-[9px] font-black text-rose-400 uppercase mb-1">Costo Total</p><p class="text-xs font-black text-rose-600 uppercase italic">S/ {{ number_format($selectedLabor->costo_total, 2) }}</p></div>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg"><p class="text-[9px] font-black text-slate-400 uppercase mb-1">Observaciones</p><p class="text-[11px] text-slate-600 italic leading-relaxed">{{ $selectedLabor->observaciones ?: 'Sin notas adicionales.' }}</p></div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest border-b border-blue-100 pb-2 mb-2">Recursos Utilizados</p>
                            <div class="space-y-2">
                                @if($selectedLabor->insumos->count() > 0)
                                    <div>
                                        <p class="text-[9px] font-black text-slate-500 uppercase mb-1">Insumos</p>
                                        @foreach($selectedLabor->insumos as $insumo)
                                            <div class="flex justify-between text-[10px] font-bold uppercase py-1 border-b border-blue-100/50"><span>{{ $insumo->producto->nombre }}</span> <span class="text-blue-700">{{ $insumo->cantidad }} {{ $insumo->producto->unidad_medida }}</span></div>
                                        @endforeach
                                    </div>
                                @endif
                                @if($selectedLabor->manoDeObra->count() > 0)
                                    <div class="mt-2 flex justify-between items-center"><p class="text-[9px] font-black text-slate-500 uppercase">Personal involucrado</p><span class="text-[10px] font-black text-emerald-700 bg-emerald-100 px-2 rounded-full">{{ $selectedLabor->manoDeObra->count() }} Operarios</span></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </x-modal>
</div>

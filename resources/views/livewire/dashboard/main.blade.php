<div class="space-y-6">
    @if(auth()->user()->rol_id == 1)
        <livewire:dashboard.super-admin />
    @elseif(!$hasOrg && empty($stats))
        <!-- Vista Bienvenida -->
        <div class="bg-white dark:bg-slate-900 p-10 rounded-2xl shadow-2xl text-center max-w-4xl mx-auto border border-slate-100 dark:border-white/10">
            <div class="w-16 h-16 bg-agri-green/10 rounded-2xl flex items-center justify-center mx-auto mb-6 text-agri-green">
                <i class="fa-solid fa-tractor text-3xl"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-4 uppercase italic">Bienvenido a AgroSys</h2>
            <p class="text-slate-500 dark:text-slate-400 mb-8 text-sm uppercase font-bold tracking-widest">Inicie registrando su organización o lote agrícola para activar su panel de control.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <button x-on:click.prevent="$dispatch('open-modal', 'create-organization')" class="px-8 py-3.5 bg-agri-green hover:bg-emerald-600 text-white rounded-xl font-black shadow-lg transition-all hover:scale-105 uppercase text-xs tracking-widest">
                    Solicitar Organización
                </button>
                <button x-on:click.prevent="$dispatch('open-modal', 'join-organization')" class="px-8 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white rounded-xl font-black transition-all hover:scale-105 uppercase text-xs tracking-widest">
                    Unirse a Empresa
                </button>
            </div>
        </div>
    @else
        <!-- HEADER PRINCIPAL DEL USUARIO (TIPO DE TEXTO Y COLORES POTENCIADOS) -->
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4 border-b border-slate-200 dark:border-white/10 pb-4">
            <div class="space-y-1">
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white italic tracking-tighter uppercase leading-none">
                    Panel de <span class="text-agri-green italic">Control</span>
                </h2>
                <p class="text-agri-green font-black text-xs uppercase tracking-[0.3em] italic">
                    {{ $organizacion->nombre }}
                </p>
            </div>

            <!-- FILTRO DE CULTIVO -->
            <div class="relative w-full lg:w-auto">
                <select wire:model.live="cultivoFiltroId" class="w-full lg:min-w-[320px] rounded-2xl border-2 border-agri-green/30 bg-white dark:bg-slate-900 text-slate-800 dark:text-white text-xs font-black py-3 pl-4 pr-10 shadow-md focus:ring-2 focus:ring-agri-green transition-all uppercase italic cursor-pointer">
                    <option value="">VER TODOS LOS CULTIVOS REGISTRADOS</option>
                    @foreach($listaCultivosFiltro as $cf)
                        <option value="{{ $cf->id }}">{{ $cf->nombre_lote }} - {{ $cf->detalleCatalogo->nombre }} ({{ $cf->variedad ?: 'Genérica' }}) · {{ $cf->fecha_siembra ? $cf->fecha_siembra->format('d/m/Y') : '---' }}</option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-agri-green pointer-events-none text-xs"></i>
            </div>
        </div>

        <!-- KPIs PRINCIPALES (NÚMEROS Y TEXTOS MÁS GRANDES Y COLORES VIBRANTES) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
                $kpis = [
                    ['label' => 'Terrenos', 'value' => $stats['terrenos'], 'color' => 'bg-emerald-500', 'border' => 'border-emerald-500/30', 'textColor' => 'text-emerald-600 dark:text-emerald-400', 'icon' => 'fa-map-location-dot', 'bgIcon' => 'bg-emerald-500/10'],
                    ['label' => 'Cultivos', 'value' => $stats['cultivos'], 'color' => 'bg-blue-500', 'border' => 'border-blue-500/30', 'textColor' => 'text-blue-600 dark:text-blue-400', 'icon' => 'fa-seedling', 'bgIcon' => 'bg-blue-500/10'],
                    ['label' => 'Ventas', 'value' => 'S/ '.number_format($stats['ventas'], 0), 'color' => 'bg-indigo-600', 'border' => 'border-indigo-500/30', 'textColor' => 'text-indigo-600 dark:text-indigo-400', 'icon' => 'fa-sack-dollar', 'bgIcon' => 'bg-indigo-600/10'],
                    ['label' => 'Inversión', 'value' => 'S/ '.number_format($stats['inversiones'], 0), 'color' => 'bg-rose-500', 'border' => 'border-rose-500/30', 'textColor' => 'text-rose-600 dark:text-rose-400', 'icon' => 'fa-receipt', 'bgIcon' => 'bg-rose-500/10'],
                    ['label' => 'ROI %', 'value' => $stats['roi'].'%', 'color' => 'bg-emerald-600', 'border' => 'border-emerald-600/30', 'textColor' => 'text-emerald-600 dark:text-emerald-400', 'icon' => 'fa-chart-pie', 'bgIcon' => 'bg-emerald-600/10'],
                    ['label' => 'Neto / HA', 'value' => 'S/ '.number_format($stats['margen_ha'], 0), 'color' => 'bg-amber-500', 'border' => 'border-amber-500/30', 'textColor' => 'text-amber-600 dark:text-amber-400', 'icon' => 'fa-chart-line-up', 'bgIcon' => 'bg-amber-500/10'],
                ];
            @endphp
            @foreach($kpis as $kpi)
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-md relative overflow-hidden border {{ $kpi['border'] }} flex items-center justify-between group hover:shadow-xl transition-all">
                    <div class="absolute left-0 top-[20%] bottom-[20%] w-1.5 {{ $kpi['color'] }} rounded-r-full"></div>
                    <div class="pl-2.5 space-y-1">
                        <p class="text-slate-400 dark:text-slate-400 text-xs font-black uppercase tracking-wider">{{ $kpi['label'] }}</p>
                        <p class="text-2xl md:text-3xl font-black {{ $kpi['textColor'] }} italic leading-none">{{ $kpi['value'] }}</p>
                    </div>
                    <div class="w-11 h-11 {{ $kpi['bgIcon'] }} rounded-xl flex items-center justify-center {{ $kpi['textColor'] }} shadow-inner shrink-0">
                        <i class="fa-solid {{ $kpi['icon'] }} text-lg"></i>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- SECCIÓN DE ESTRATEGIA (CIRCULAR + CLIMA OPERATIVO + TAREAS CRÍTICAS) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-2">
            <!-- INVERSIÓN CIRCULAR -->
            <div class="lg:col-span-1 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-xl border border-slate-100 dark:border-white/10 flex flex-col h-[380px]"
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
                                    responsive: true, maintainAspectRatio: false, cutout: '72%',
                                    plugins: { legend: { position: 'bottom', labels: { font: { size: 10, weight: 'bold' }, padding: 12, boxWidth: 10 } } }
                                }
                            });
                        });
                    }
                 }"
                 wire:key="invest-pie-header-{{ $cultivoFiltroId }}"
            >
                <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-3">
                    <i class="fa-solid fa-chart-pie text-indigo-500 text-sm"></i>
                    <span>Distribución de Inversión</span>
                </h4>
                <div class="flex-1 relative">
                    <canvas id="investmentPieChartHeader"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pb-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Inversión</p>
                        <p class="text-xl font-black text-slate-900 dark:text-white">S/ {{ number_format($stats['inversiones'], 0) }}</p>
                    </div>
                </div>
            </div>

            <!-- CLIMA OPERATIVO Y PROGRESO DE CAMPAÑA -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-gradient-to-br from-emerald-500/10 via-blue-500/10 to-indigo-600/10 dark:from-slate-900 dark:to-slate-800 p-6 rounded-2xl shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row items-center justify-between gap-6 h-[180px]">
                    <div class="flex-1 w-full space-y-2">
                        <div class="flex items-center gap-3">
                            <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic leading-none">Ventana Climática Operativa</h4>
                            <span class="px-3 py-1 bg-emerald-500 text-white rounded-full text-[9px] font-black uppercase tracking-wider shadow-sm">Óptimo Campo</span>
                        </div>
                        <div class="flex items-end gap-6 pt-1">
                            <div class="text-5xl md:text-6xl font-black text-slate-900 dark:text-white italic tracking-tighter">24°C</div>
                            <div class="pb-1 space-y-1">
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Humedad: <span class="text-blue-600 dark:text-blue-400 font-black">65% HR</span></p>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Viento: <span class="text-amber-600 dark:text-amber-400 font-black">5 km/h</span></p>
                            </div>
                        </div>
                        @if($cultivoSeleccionado)
                            <div class="pt-2 w-full">
                                <div class="flex justify-between items-center mb-1">
                                    <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase">Progreso del Cultivo</p>
                                    <p class="text-[10px] font-black text-agri-green uppercase italic">{{ $stats['progreso'] }}% COMPLETO</p>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden p-0.5 shadow-inner">
                                    <div class="bg-agri-green h-full rounded-full transition-all duration-1000" style="width: {{ $stats['progreso'] }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="text-amber-400 text-7xl animate-pulse shrink-0 hidden sm:block">
                        <i class="fa-solid fa-sun-plant-wilt"></i>
                    </div>
                </div>

                <!-- TAREAS CRÍTICAS URGENTES -->
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-xl border border-slate-100 dark:border-white/10 flex flex-col justify-center h-[180px]">
                    <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-bolt-lightning text-amber-500 text-sm"></i>
                        <span>Próximas Labores Críticas (48h)</span>
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @forelse($laboresCriticas as $lc)
                            <div class="bg-rose-50 dark:bg-rose-950/30 p-3 rounded-xl border-l-4 border-rose-500 shadow-sm space-y-1">
                                <p class="text-xs font-black text-slate-800 dark:text-white uppercase leading-none truncate">{{ $lc->detalleCatalogo->nombre }}</p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase truncate">{{ $lc->cultivo->nombre_lote }}</p>
                                <div class="pt-1 flex justify-between items-center">
                                    <span class="text-[9px] font-black text-rose-600 dark:text-rose-400 uppercase italic">{{ $lc->fecha_realizacion->diffForHumans(null, true) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-4">
                                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-black uppercase tracking-widest italic">
                                    ✓ Todas las labores al día en campo
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN MAESTRA: GRÁFICOS DE RENTABILIDAD & LABORES RECIENTES (GRID RESPONSIVA) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4"
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
                        data: { labels: @js($chartData['meses']), datasets: [{ label: 'Actividad', data: @js($chartData['actividad']), borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.05)', tension: 0.4, fill: true, pointRadius: 3, borderWidth: 3 }] },
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
                            labels: ['BALANCE RENTABILIDAD GLOBAL'],
                            datasets: [
                                { label: 'Inversión Total', data: [{{ $stats['inversiones'] }}], backgroundColor: '#e11d48', borderRadius: 6 },
                                { label: 'Venta Bruta', data: [{{ $stats['ventas'] }}], backgroundColor: '#2563eb', borderRadius: 6 },
                                { label: 'Ganancia Neta', data: [{{ $stats['ganancia'] }}], backgroundColor: '#10b981', borderRadius: 6 }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { font: { size: 11, weight: 'bold' }, boxWidth: 14 } },
                                tooltip: { callbacks: { label: (c) => c.dataset.label + ': S/ ' + c.raw.toLocaleString() } }
                            },
                            scales: { y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 10 }, callback: (v) => 'S/ ' + v.toLocaleString() } } }
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
                                { type: 'bar', label: 'Inversión', data: @js($chartData['inversiones']), backgroundColor: '#e11d48', yAxisID: 'y' },
                                { type: 'bar', label: 'Venta', data: @js($chartData['ventas']), backgroundColor: '#2563eb', yAxisID: 'y' },
                                { type: 'line', label: 'ROI %', data: @js($chartData['roi']), borderColor: '#8b5cf6', backgroundColor: '#8b5cf6', borderWidth: 3, fill: false, yAxisID: 'y1', tension: 0.4, pointRadius: 3 }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 10, weight: 'bold' } } } },
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
                                borderRadius: 8
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { x: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 10 }, callback: (v) => 'S/ ' + v.toLocaleString() } } }
                        }
                    });
                }
             }"
             x-init="initCharts()"
             wire:key="master-dash-sync-{{ $cultivoFiltroId }}"
        >
            <!-- COLUMNA IZQUIERDA: GRÁFICOS PRINCIPALES (2 COLUMNAS) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- BALANCE RENTABILIDAD GLOBAL -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-xl border border-slate-100 dark:border-white/10 flex flex-col h-[420px]" wire:ignore>
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-3">
                            <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic flex items-center gap-2">
                                <i class="fa-solid fa-chart-simple text-blue-600 text-sm"></i>
                                <span>Balance de Rentabilidad Global</span>
                            </h4>
                            <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-black uppercase shadow-inner">
                                ROI: {{ $stats['roi'] }}%
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 relative"><canvas id="barBalanceTotal"></canvas></div>
                </div>

                <!-- RENTABILIDAD MENSUAL -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-xl border border-slate-100 dark:border-white/10 flex flex-col h-[420px]" wire:ignore>
                    <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-calendar-days text-indigo-500 text-sm"></i>
                        <span>Rentabilidad Mensual Consolidada</span>
                    </h4>
                    <div class="flex-1 relative"><canvas id="barRentMonthly"></canvas></div>
                </div>

                <!-- EVOLUCIÓN DE ACTIVIDAD + ESTRUCTURA DE COSTOS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-xl border border-slate-100 dark:border-white/10 flex flex-col h-[350px]" wire:ignore>
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-4">
                            <i class="fa-solid fa-chart-line text-emerald-500 text-sm"></i>
                            <span>Evolución de Actividad</span>
                        </h4>
                        <div class="flex-1 relative"><canvas id="lineActChartMaster"></canvas></div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-xl border border-slate-100 dark:border-white/10 flex flex-col h-[350px]" wire:ignore>
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-4">
                            <i class="fa-solid fa-gears text-slate-500 text-sm"></i>
                            <span>Estructura de Costos</span>
                        </h4>
                        <div class="flex-1 relative"><canvas id="barOpChartMaster"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: LABORES RECIENTES & PRODUCTIVIDAD (1 COLUMNA) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- LABORES RECIENTES -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-xl border border-slate-100 dark:border-white/10 flex flex-col h-[580px]">
                    <div class="flex justify-between items-center border-b border-slate-100 dark:border-white/10 pb-4 mb-4">
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-2 italic">
                            <i class="fa-solid fa-list-check text-amber-500 text-sm"></i>
                            <span>Labores Recientes</span>
                        </h4>
                        <a href="/admin/labores" class="text-[10px] font-black text-agri-green hover:underline uppercase tracking-widest" wire:navigate>VER TODOS →</a>
                    </div>

                    <div class="flex-1 overflow-y-auto space-y-3 custom-scrollbar pr-1">
                        @forelse($ultimas5Labores as $labor)
                            @php
                                $icon = match(true) {
                                    str_contains(strtolower($labor->detalleCatalogo->nombre), 'riego') => 'fa-droplet text-blue-500',
                                    str_contains(strtolower($labor->detalleCatalogo->nombre), 'abono') || str_contains(strtolower($labor->detalleCatalogo->nombre), 'fertiliza') => 'fa-flask text-emerald-500',
                                    str_contains(strtolower($labor->detalleCatalogo->nombre), 'fumiga') || str_contains(strtolower($labor->detalleCatalogo->nombre), 'aplica') => 'fa-spray-can text-rose-500',
                                    str_contains(strtolower($labor->detalleCatalogo->nombre), 'cosecha') => 'fa-wheat-awn text-amber-600',
                                    str_contains(strtolower($labor->detalleCatalogo->nombre), 'siembra') => 'fa-seedling text-agri-green',
                                    default => 'fa-screwdriver-wrench text-slate-400'
                                };
                            @endphp
                            <div wire:click="mostrarDetalleLabor({{ $labor->id }})" class="p-3 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5 hover:border-agri-green transition-all shadow-xs group cursor-pointer">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 flex items-center justify-center shadow-xs shrink-0">
                                        <i class="fa-solid {{ $icon }} text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-black text-slate-800 dark:text-white uppercase leading-tight truncate">{{ $labor->detalleCatalogo->nombre }}</p>
                                        <p class="text-[10px] font-bold text-agri-green mt-0.5 uppercase italic truncate">{{ $labor->cultivo->detalleCatalogo->nombre }} ({{ $labor->cultivo->variedad ?: 'Genérica' }})</p>
                                        <div class="flex justify-between items-center mt-1 text-[9px] font-bold text-slate-400">
                                            <span>{{ number_format($labor->cultivo->area_destinada, 1) }} Ha</span>
                                            <span>{{ $labor->fecha_realizacion->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-20 text-slate-300">
                                <i class="fa-solid fa-ghost text-4xl mb-3"></i>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Sin labores registradas</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- SECCIÓN DE PRODUCTIVIDAD REAL VS ESTIMADA -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-xl border border-slate-100 dark:border-white/10 flex flex-col h-[280px]"
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
                                        labels: ['PRODUCTIVIDAD (TN/HA)'],
                                        datasets: [
                                            { label: 'Real', data: [{{ $stats['yield']['real'] }}], backgroundColor: '#10b981', borderRadius: 6 },
                                            { label: 'Estimado', data: [{{ $stats['yield']['estimado'] }}], backgroundColor: '#94a3b8', borderRadius: 6 }
                                        ]
                                    },
                                    options: {
                                        indexAxis: 'y',
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: { legend: { position: 'top', labels: { font: { weight: 'bold', size: 10 }, boxWidth: 12 } } },
                                        scales: { x: { ticks: { font: { size: 9 } } } }
                                    }
                                });
                            });
                        }
                     }"
                     x-init="init()"
                     wire:key="yield-sync-sidebar-v2-{{ $cultivoFiltroId }}"
                >
                    <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-chart-line text-emerald-500 text-sm"></i>
                        <span>Productividad Real vs Estimada</span>
                    </h4>
                    <div class="flex-1 relative"><canvas id="yieldCompareChartSidebar"></canvas></div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL DETALLE LABOR -->
    <x-modal name="labor-detail-modal" focusable maxWidth="xl">
        @if($selectedLabor)
            <div class="p-8 bg-white dark:bg-slate-900 rounded-2xl">
                <div class="flex justify-between items-start border-b border-slate-100 dark:border-white/10 pb-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic tracking-tight">{{ $selectedLabor->detalleCatalogo->nombre }}</h3>
                        <p class="text-xs font-black text-agri-green uppercase tracking-widest mt-1">{{ $selectedLabor->cultivo->detalleCatalogo->nombre }} ({{ $selectedLabor->cultivo->variedad ?: 'Genérica' }})</p>
                    </div>
                    <button x-on:click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-white/10 text-slate-400 hover:text-rose-500 transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-xl space-y-1">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha Realizada</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white uppercase italic">{{ $selectedLabor->fecha_realizacion->format('d/m/Y') }}</p>
                            </div>
                            <div class="bg-rose-50 dark:bg-rose-950/30 p-4 rounded-xl space-y-1">
                                <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Costo Total</p>
                                <p class="text-sm font-black text-rose-600 dark:text-rose-400 uppercase italic">S/ {{ number_format($selectedLabor->costo_total, 2) }}</p>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-xl space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Observaciones</p>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 italic leading-relaxed">"{{ $selectedLabor->observaciones ?: 'Sin notas adicionales.' }}"</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-blue-50 dark:bg-blue-950/30 p-5 rounded-2xl border border-blue-100 dark:border-blue-900/30 space-y-3">
                            <p class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest border-b border-blue-100 dark:border-blue-900/50 pb-2">Recursos Utilizados</p>
                            <div class="space-y-2">
                                @if($selectedLabor->insumos->count() > 0)
                                    <div>
                                        <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Insumos Aplicados</p>
                                        @foreach($selectedLabor->insumos as $insumo)
                                            <div class="flex justify-between text-xs font-bold uppercase py-1 border-b border-blue-100/50">
                                                <span class="text-slate-700 dark:text-slate-300">{{ $insumo->producto->nombre }}</span>
                                                <span class="text-blue-700 dark:text-blue-400 font-black">{{ $insumo->cantidad }} {{ $insumo->producto->unidad_medida }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @if($selectedLabor->manoDeObra->count() > 0)
                                    <div class="pt-1 flex justify-between items-center">
                                        <p class="text-[10px] font-black text-slate-500 uppercase">Personal involucrado</p>
                                        <span class="text-xs font-black text-emerald-700 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 px-3 py-1 rounded-full">
                                            {{ $selectedLabor->manoDeObra->count() }} Operarios
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </x-modal>
</div>

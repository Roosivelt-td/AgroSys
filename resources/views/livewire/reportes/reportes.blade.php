<div class="max-w-[1600px] mx-auto space-y-6 pb-24 px-4 sm:px-6">
    <style>
        @media print {
            @page { size: {{ $cultivoId ? 'portrait' : 'landscape' }}; margin: 10mm; }
            html, body { height: auto !important; overflow: visible !important; }
            .no-print, aside, header, nav, .sidebar-transition, .filters-area { display: none !important; }
            div.flex.h-screen, div.flex-1.flex.flex-col { display: block !important; height: auto !important; }
            main { display: block !important; padding: 0 !important; margin: 0 !important; background: white !important; }
            .dashboard-view { display: none !important; } .print-view { display: block !important; }
            .print-table { width: 100% !important; border-collapse: collapse !important; margin-top: 10px; }
            .print-table th, .print-table td { border: 1px solid #aaa !important; padding: 4px 6px !important; text-align: left !important; font-size: 8px; }
            .print-table th { background-color: #f1f5f9 !important; font-weight: bold; text-transform: uppercase; }
            .print-header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #2D6A4F; padding-bottom: 10px; }
            .print-summary-box { border: 1px solid #ccc; padding: 10px; border-radius: 5px; background: #fafafa; margin-bottom: 15px; }
            .grid-print { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 15px !important; }
            .kpi-grid-print { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 8px !important; }
            .kpi-box-print { border: 1px solid #ddd; padding: 8px; border-radius: 6px; text-align: center; background: #fff; }
        }
        .print-view { display: none; }
    </style>

    <!-- DASHBOARD PANTALLA -->
    <div class="dashboard-view space-y-6 no-print">
        <!-- Modern Header & Filters -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white flex items-center gap-4">
                        <div class="w-12 h-12 bg-agri-green/10 rounded-2xl flex items-center justify-center text-agri-green shadow-inner">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        Centro de Reportes <span class="text-agri-green italic">Inteligente</span>
                    </h2>
                    <p class="text-slate-400 text-sm font-medium italic mt-2 ml-1">Auditoría financiera y operativa de alta precisión en tiempo real.</p>
                </div>

                <div class="flex">
                    <button onclick="window.print()" class="group px-6 py-3 bg-white dark:bg-slate-900 border-2 border-rose-500 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all duration-300 flex items-center gap-3 shadow-lg shadow-rose-500/10 font-black text-[11px] uppercase tracking-widest italic active:scale-95 whitespace-nowrap self-start">
                        <i class="fa-solid fa-file-pdf text-lg group-hover:rotate-12 transition-transform"></i>
                        <span>Exportar Informe</span>
                    </button>
                </div>
            </div>

            <!-- Filtros Modernos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 pt-8 border-t border-gray-100 dark:border-gray-700">
                <!-- 1. BUSCADOR RÁPIDO -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Buscador Rápido</label>
                    <div class="relative group">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar lote o cultivo..."
                               class="w-full rounded-2xl border-none bg-slate-50 dark:bg-slate-900/50 dark:text-white text-xs font-bold py-3 pl-4 pr-10 shadow-inner focus:ring-2 focus:ring-agri-green/30 transition-all uppercase">
                        <i class="fa-solid fa-magnifying-glass absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-agri-green transition-colors"></i>
                    </div>
                </div>

                <!-- 2. LOTE / CAMPAÑA -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-500 uppercase tracking-widest ml-1">Lote / Campaña</label>
                    <select wire:model.live="cultivoId" class="w-full rounded-2xl border-2 border-blue-100 dark:border-blue-900/30 bg-blue-50/50 dark:bg-blue-900/10 dark:text-white text-[11px] font-black py-3 px-4 shadow-sm focus:ring-2 focus:ring-blue-500 transition-all italic cursor-pointer">
                        <option value="">RESUMEN GENERAL</option>@foreach($listaCultivos as $c) <option value="{{ $c->id }}">{{ strtoupper($c->nombre_lote) }}</option> @endforeach
                    </select>
                </div>

                <!-- 3. TIPO DE CULTIVO -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tipo de Cultivo</label>
                    <select wire:model.live="catalogoCultivoId" class="w-full rounded-2xl border-none bg-slate-50 dark:bg-slate-900/50 dark:text-white text-xs font-bold py-3 shadow-inner focus:ring-2 focus:ring-blue-500/30 cursor-pointer">
                        <option value="">TODOS LOS TIPOS</option>@foreach($listaCatalogo as $cat) <option value="{{ $cat->id }}">{{ strtoupper($cat->nombre) }}</option> @endforeach
                    </select>
                </div>

                <!-- 4. PERIODO -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Periodo</label>
                    <select wire:model.live="filtro" class="w-full rounded-2xl border-none bg-slate-50 dark:bg-slate-900/50 dark:text-white text-xs font-bold py-3 shadow-inner focus:ring-2 focus:ring-blue-500/30 appearance-none cursor-pointer" @if($cultivoId) disabled @endif>
                        <option value="todo">TODO</option><option value="anio">ESTE AÑO</option><option value="mes">ESTE MES</option><option value="semana">ESTA SEMANA</option><option value="personalizado">PERSONALIZADO</option>
                    </select>
                </div>

                <!-- 5. HASTA -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Hasta</label>
                    <input type="date" wire:model.live="fechaFin" class="w-full rounded-2xl border-none bg-slate-50 dark:bg-slate-900/50 dark:text-white text-xs font-bold py-3 shadow-inner focus:ring-2 focus:ring-blue-500/30" @if($cultivoId) disabled @endif>
                </div>
            </div>
        </div>

        @if(!$cultivoId)
            <!-- CABECERA RESUMEN GENERAL (KPIs + Vertical Breakdown) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-xl border-b-4 border-blue-500 flex flex-col justify-center items-center text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">VENTA BRUTA</p>
                        <h3 class="text-3xl font-black text-slate-800 dark:text-white font-mono italic">S/ {{ number_format($this->datos['ventas_brutas'] ?? 0, 2) }}</h3>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-xl border-b-4 border-slate-700 flex flex-col justify-center items-center text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">INVERSIÓN</p>
                        <h3 class="text-3xl font-black text-slate-800 dark:text-white font-mono italic">S/ {{ number_format($this->datos['total_inversion'] ?? 0, 2) }}</h3>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-xl border-b-4 border-emerald-500 flex flex-col justify-center items-center text-center">
                        <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">UTILIDAD</p>
                        <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono italic">S/ {{ number_format($this->datos['utilidad_neta'] ?? 0, 2) }}</h3>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-xl border-b-4 border-purple-500 flex flex-col justify-center items-center text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">EFICIENCIA (ROI)</p>
                        <h3 class="text-3xl font-black text-slate-800 dark:text-white font-mono italic">{{ round($this->datos['roi'] ?? 0, 1) }}%</h3>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-xl border border-gray-100 dark:border-white/5 flex flex-col">
                    <h4 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-[0.2em] border-b border-gray-100 dark:border-white/10 pb-3 mb-4 italic">Desglose Financiero</h4>
                    <div class="space-y-4 flex-1 flex flex-col justify-center">
                        <div class="flex justify-between items-center px-4">
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Mano Obra:</span>
                            <span class="text-sm font-black text-slate-700 dark:text-white font-mono">S/ {{ number_format($this->datos['mo_total'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center px-4">
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Maquinaria:</span>
                            <span class="text-sm font-black text-slate-700 dark:text-white font-mono">S/ {{ number_format($this->datos['maq_total'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center px-4">
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Insumos:</span>
                            <span class="text-sm font-black text-slate-700 dark:text-white font-mono">S/ {{ number_format($this->datos['ins_total'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center px-4">
                            <span class="text-[10px] font-bold text-gray-400 uppercase border-l-2 border-yellow-500 pl-2">Cosecha:</span>
                            <span class="text-sm font-black text-yellow-600 font-mono">S/ {{ number_format($this->datos['cos_lab_total'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- KPIs Pantalla Individual (No tocar) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] shadow-xl border-b-4 border-emerald-500 transform hover:-translate-y-1 transition-all"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Producción</p><h3 class="text-3xl font-black text-slate-800 dark:text-white font-mono italic">{{ number_format($this->datos['cosecha'] ?? 0, 2) }} <span class="text-xs">Kg</span></h3></div>
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] shadow-xl border-b-4 border-blue-500 transform hover:-translate-y-1 transition-all"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ventas Brutas</p><h3 class="text-3xl font-black text-blue-600 dark:text-blue-400 font-mono italic">S/ {{ number_format($this->datos['ventas_brutas'] ?? 0, 2) }}</h3></div>
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] shadow-xl border-b-4 border-rose-500 transform hover:-translate-y-1 transition-all"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Inversión Total</p><h3 class="text-3xl font-black text-rose-600 dark:text-rose-400 font-mono italic">S/ {{ number_format($this->datos['total_inversion'] ?? 0, 2) }}</h3></div>
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] shadow-xl border-b-4 border-violet-500 transform hover:-translate-y-1 transition-all"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Utilidad Neta</p><h3 class="text-3xl font-black {{ ($this->datos['utilidad_neta'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-mono italic">S/ {{ number_format($this->datos['utilidad_neta'] ?? 0, 2) }}</h3></div>
            </div>
        @endif

        <!-- 📊 SECCIÓN DE GRÁFICOS INTERACTIVOS (DSS) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 my-8">
            <!-- Gráfico 1: Balance General -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-white/5">
                <h4 class="text-xs font-black text-slate-700 dark:text-white uppercase tracking-[0.2em] mb-6 flex items-center gap-2 italic">
                    <div class="w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-500 shadow-inner">
                        <i class="fa-solid fa-chart-bar"></i>
                    </div>
                    Balance Financiero General
                </h4>
                <div class="h-64 relative" wire:ignore>
                    <canvas id="canvasRentabilidad"></canvas>
                </div>
            </div>

            <!-- Gráfico 2: Desglose de Inversiones -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-white/5">
                <h4 class="text-xs font-black text-slate-700 dark:text-white uppercase tracking-[0.2em] mb-6 flex items-center gap-2 italic">
                    <div class="w-8 h-8 bg-purple-500/10 rounded-lg flex items-center justify-center text-purple-500 shadow-inner">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    Distribución Detallada de Inversiones
                </h4>
                <div class="h-64 relative" wire:ignore>
                    <canvas id="canvasCostos"></canvas>
                </div>
            </div>
        </div>

        <script>
            (function() {
                const initCharts = () => {
                    const ctxRent = document.getElementById('canvasRentabilidad');
                    const ctxCost = document.getElementById('canvasCostos');
                    if (!ctxRent || !ctxCost) return;

                    if (window.chartRent) window.chartRent.destroy();
                    if (window.chartCost) window.chartCost.destroy();

                    const isDark = document.documentElement.classList.contains('dark');
                    const textColor = isDark ? '#94a3b8' : '#64748b';
                    const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

                    window.chartRent = new Chart(ctxRent.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['Ventas Brutas', 'Inversión Total', 'Utilidad Neta'],
                            datasets: [{
                                data: [
                                    {{ $this->datos['ventas_brutas'] ?? 0 }},
                                    {{ $this->datos['total_inversion'] ?? 0 }},
                                    {{ $this->datos['utilidad_neta'] ?? 0 }}
                                ],
                                backgroundColor: [
                                    'rgba(59, 130, 246, 0.85)',
                                    'rgba(71, 85, 105, 0.85)',
                                    'rgba(16, 185, 129, 0.85)'
                                ],
                                borderColor: ['#3b82f6', '#475569', '#10b981'],
                                borderWidth: 1,
                                borderRadius: 12,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    grid: { color: gridColor },
                                    ticks: { color: textColor, font: { weight: 'bold' } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: textColor, font: { weight: 'bold' } }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return ' S/ ' + new Intl.NumberFormat('es-PE').format(context.raw);
                                        }
                                    }
                                }
                            }
                        }
                    });

                    window.chartCost = new Chart(ctxCost.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Mano Obra', 'Maquinaria', 'Insumos', 'Cosecha Labor'],
                            datasets: [{
                                data: [
                                    {{ $this->datos['mo_total'] ?? 0 }},
                                    {{ $this->datos['maq_total'] ?? 0 }},
                                    {{ $this->datos['ins_total'] ?? 0 }},
                                    {{ $this->datos['cos_lab_total'] ?? 0 }}
                                ],
                                backgroundColor: ['#6366f1', '#3b82f6', '#ec4899', '#eab308'],
                                borderStrokeColor: isDark ? '#1e293b' : '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: textColor, font: { weight: 'bold', size: 11 } }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return ' S/ ' + new Intl.NumberFormat('es-PE').format(context.raw);
                                        }
                                    }
                                }
                            },
                            cutout: '70%'
                        }
                    });
                };

                setTimeout(initCharts, 50);
            })();
        </script>

        @if($cultivoId && isset($detalleInversiones['info']))
            <!-- SECCIÓN DETALLE DE UN SOLO CULTIVO (No tocar) -->
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-white/5">
                <div class="bg-agri-green/10 p-8 flex justify-between items-center border-b border-agri-green/20">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-agri-green rounded-2xl flex items-center justify-center text-white text-3xl shadow-xl shadow-agri-green/20 ring-8 ring-agri-green/5"><i class="fa-solid fa-leaf"></i></div>
                        <div><h4 class="text-[10px] font-black text-agri-green uppercase tracking-widest">Detalle Analítico de Campaña</h4><h3 class="text-2xl font-black text-gray-800 dark:text-white uppercase italic tracking-tighter">{{ $detalleInversiones['info']->lote }} • {{ $detalleInversiones['info']->cultivo }}</h3></div>
                    </div>
                    <div class="text-right flex gap-8 italic">
                        <div class="text-center border-r border-agri-green/20 pr-8"><p class="text-[9px] font-black text-gray-400 uppercase">Siembra</p><p class="font-black text-sm uppercase">{{ $detalleInversiones['info']->fecha_siembra->format('d/m/Y') }}</p></div>
                        <div class="text-center border-r border-agri-green/20 pr-8"><p class="text-[9px] font-black text-gray-400 uppercase">Cosecha</p><p class="font-black text-sm text-blue-500 uppercase">{{ $detalleInversiones['info']->fecha_cosecha ? $detalleInversiones['info']->fecha_cosecha->format('d/m/Y') : 'EN PROCESO' }}</p></div>
                        <div class="text-center"><p class="text-[9px] font-black text-gray-400 uppercase">Área</p><p class="font-black text-sm uppercase">{{ $detalleInversiones['info']->hectareas }} HA</p></div>
                    </div>
                </div>
                <div class="bg-slate-900 p-8 flex justify-end"><button wire:click="$set('cultivoId', '')" class="px-10 py-3 bg-white text-slate-900 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all italic">Regresar al Resumen Global</button></div>
            </div>
        @else
            <!-- SECCIÓN RESUMEN GLOBAL (Vista Multi-lote) -->
            <div class="space-y-12">

                <!-- TABLA 1: CUADRO DETALLADO DE PRODUCCIÓN Y COMERCIALIZACIÓN -->
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl overflow-hidden border border-gray-300 dark:border-white/10">
                    <div class="p-6 flex items-center gap-4 bg-slate-50 dark:bg-white/5 border-b border-gray-300 dark:border-white/5">
                        <div class="w-10 h-10 bg-agri-green/10 rounded-xl flex items-center justify-center text-agri-green shadow-sm">
                            <i class="fa-solid fa-list-check text-lg"></i>
                        </div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider italic">
                            Cuadro Detallado de Producción y Comercialización
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[1400px]">
                            <thead class="bg-slate-50/50 dark:bg-white/5 text-slate-400 font-bold uppercase text-[9px] tracking-widest border-b border-gray-300 dark:border-white/5">
                                <tr>
                                    <th class="py-4 px-6">Lote / Campaña</th>
                                    <th class="py-4 px-4 text-center">Fecha (S+C)</th>
                                    <th class="py-4 px-4 text-right">Alquiler</th>
                                    <th class="py-4 px-4 text-right">Labores (Mant.)</th>
                                    <th class="py-4 px-4 text-right text-yellow-600 dark:text-yellow-500">Cosecha</th>
                                    <th class="py-4 px-4 text-center text-blue-600 dark:text-blue-400">Cant. Cosechada</th>
                                    <th class="py-4 px-4 text-right">Precio Venta</th>
                                    <th class="py-4 px-4 text-right">Venta por Cos.</th>
                                    <th class="py-4 px-4 text-right text-rose-600 dark:text-rose-400">Flete (Suma)</th>
                                    <th class="py-4 px-4 text-right text-blue-700 dark:text-blue-300">Venta Bruta</th>
                                    <th class="py-4 px-6 text-right">Ganancia Final</th>
                                </tr>
                            </thead>
                            <tbody class="text-slate-600 dark:text-slate-300 font-medium">
                                @php $tAl=0; $tMa=0; $tCo=0; $tFl=0; $tBr=0; $tGa=0; @endphp
                                @foreach($this->datos['tabla_resumen_v3'] ?? [] as $lote)
                                    @php $tAl+=$lote->alquiler; $tMa+=$lote->labores; $tCo+=$lote->cosecha; $tFl+=$lote->flete; $tGa+=$lote->ganancia; @endphp
                                    @foreach($lote->filas as $idx => $f)
                                        @php $tBr+=$f->bruto; @endphp
                                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border-b border-gray-300 dark:border-white/5">
                                            @if($idx === 0)
                                                <td rowspan="{{ $lote->span }}" class="py-5 px-6 font-black text-slate-800 dark:text-white border-r border-gray-300 dark:border-white/5 align-middle">
                                                    <div class="flex flex-col">
                                                        <span class="text-[11px] tracking-tight italic uppercase opacity-60">{{ $lote->lote }}</span>
                                                        <span class="text-xs text-agri-green mt-0.5 font-black uppercase">{{ $lote->cultivo }}</span>
                                                    </div>
                                                </td>
                                                <td rowspan="{{ $lote->span }}" class="py-5 px-4 text-center text-[10px] font-mono border-r border-gray-300 dark:border-white/5 align-middle opacity-50">
                                                    {{ $lote->fechas }}
                                                </td>
                                                <td rowspan="{{ $lote->span }}" class="py-5 px-4 text-right text-[11px] border-r border-gray-300 dark:border-white/5 align-middle font-mono italic">
                                                    S/ {{ number_format($lote->alquiler, 0) }}
                                                </td>
                                                <td rowspan="{{ $lote->span }}" class="py-5 px-4 text-right text-[11px] text-slate-400 border-r border-gray-300 dark:border-white/5 align-middle font-mono">
                                                    S/ {{ number_format($lote->labores, 0) }}
                                                </td>
                                                <td rowspan="{{ $lote->span }}" class="py-5 px-4 text-right text-[11px] text-yellow-600 dark:text-yellow-500 font-black border-r border-gray-300 dark:border-white/5 align-middle font-mono">
                                                    S/ {{ number_format($lote->cosecha, 0) }}
                                                </td>
                                            @endif

                                            <td class="py-4 px-4 text-center border-r border-gray-300 dark:border-white/5 text-blue-600 dark:text-blue-400 font-black italic text-[10px] uppercase">
                                                {{ strtoupper($f->produccion) }}
                                            </td>
                                            <td class="py-4 px-4 text-right border-r border-gray-300 dark:border-white/5 text-[10px] font-mono opacity-70 italic">
                                                S/ {{ number_format($f->precio, 2) }}
                                            </td>
                                            <td class="py-4 px-4 text-right border-r border-gray-300 dark:border-white/5 text-blue-600 dark:text-blue-500 font-black text-[11px] font-mono">
                                                S/ {{ number_format($f->bruto, 0) }}
                                            </td>

                                            @if($idx === 0)
                                                <td rowspan="{{ $lote->span }}" class="py-5 px-4 text-right text-rose-600 dark:text-rose-500 font-black border-r border-gray-300 dark:border-white/5 align-middle font-mono">
                                                    S/ {{ number_format($lote->flete, 0) }}
                                                </td>
                                                <td rowspan="{{ $lote->span }}" class="py-5 px-4 text-right text-blue-800 dark:text-blue-300 font-black border-r border-gray-300 dark:border-white/5 align-middle font-mono text-[11px]">
                                                    S/ {{ number_format($lote->venta_bruta, 0) }}
                                                </td>
                                                <td rowspan="{{ $lote->span }}" class="py-5 px-6 text-right align-middle">
                                                    <div class="px-4 py-2 rounded-xl border {{ $lote->ganancia >= 0 ? 'bg-emerald-500/5 text-emerald-600 border-emerald-500/20' : 'bg-rose-500/5 text-rose-600 border-rose-500/20' }}">
                                                        <span class="text-xs font-black italic tracking-tighter font-mono">
                                                            S/ {{ number_format($lote->ganancia, 0) }}
                                                        </span>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white font-black uppercase italic text-[10px] border-t-2 border-gray-300 dark:border-slate-800">
                                <tr>
                                    <td colspan="2" class="py-5 px-6 text-center tracking-[0.4em] text-slate-400">Totales Acumulados</td>
                                    <td class="py-5 px-4 text-right font-mono">S/ {{ number_format($tAl, 0) }}</td>
                                    <td class="py-5 px-4 text-right font-mono">S/ {{ number_format($tMa, 0) }}</td>
                                    <td class="py-5 px-4 text-right text-yellow-600 dark:text-yellow-500 font-mono">S/ {{ number_format($tCo, 0) }}</td>
                                    <td colspan="3"></td>
                                    <td class="py-5 px-4 text-right text-rose-600 font-mono">S/ {{ number_format($tFl, 0) }}</td>
                                    <td class="py-6 px-4 text-right text-blue-700 dark:text-blue-300 font-mono">S/ {{ number_format($tBr, 0) }}</td>
                                    <td class="py-5 px-6 text-right text-base text-emerald-600 dark:text-emerald-400 font-black tracking-tighter">
                                        <div class="inline-block px-5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/30">
                                            S/ {{ number_format($tGa, 0) }}
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- TABLA 2: RESUMEN EJECUTIVO DE RENTABILIDAD FINAL DETALLADO -->
                <div class="bg-slate-900 rounded-[1.5rem] shadow-2xl border border-white/5 overflow-hidden">
                    <div class="bg-gradient-to-r from-slate-900 to-slate-800 p-6 text-white border-b border-white/5">
                        <h3 class="text-xs font-black uppercase tracking-[0.3em] italic flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg"><i class="fa-solid fa-calculator"></i></div>
                            RESUMEN EJECUTIVO DE RENTABILIDAD FINAL DETALLADO
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[11px] text-left border-collapse min-w-[1000px]">
                <!-- TABLA 2: RESUMEN EJECUTIVO DE RENTABILIDAD FINAL DETALLADO -->
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl border border-gray-300 dark:border-white/10 overflow-hidden">
                    <div class="p-6 flex items-center gap-4 bg-slate-50 dark:bg-white/5 border-b border-gray-300 dark:border-white/5">
                        <div class="w-10 h-10 bg-blue-600/10 rounded-xl flex items-center justify-center text-blue-600 shadow-sm">
                            <i class="fa-solid fa-calculator text-lg"></i>
                        </div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider italic">
                            Resumen Ejecutivo de Rentabilidad Final Detallado
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[11px] text-left border-collapse min-w-[1000px]">
                            <thead class="bg-slate-50/50 dark:bg-white/5 text-slate-400 font-bold uppercase text-[9px] tracking-widest border-b border-gray-300 dark:border-white/5">
                                <tr><th class="py-4 px-6">LOTE / CAMPAÑA</th><th class="py-4 px-4">FECHA (S+C)</th><th class="py-4 px-4 text-right">ALQUILER</th><th class="py-4 px-4 text-right text-gray-400">MANTENIMIENTO</th><th class="py-4 px-4 text-right text-yellow-600 dark:text-yellow-500">COSECHA</th><th class="py-4 px-4 text-right text-rose-600 dark:text-rose-400">FLETE</th><th class="py-4 px-4 text-right font-black">VENTA BRUTA</th><th class="py-4 px-6 text-right">GANANCIA FINAL</th></tr>
                            </thead>
                            <tbody class="text-slate-600 dark:text-slate-300 font-medium">
                                @foreach($this->datos['resumen_ejecutivo'] ?? [] as $re)
                                    <tr class="group hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border-b border-gray-200 dark:border-white/5">
                                        <td class="py-5 px-6 font-black text-slate-800 dark:text-white">{{ $re->lote }} • <span class="text-agri-green">{{ $re->cultivo }}</span></td>
                                        <td class="py-5 px-4 font-mono text-[10px] text-slate-400">{{ $re->fechas }}</td>
                                        <td class="py-5 px-4 text-right italic font-mono">S/ {{ number_format($re->alquiler, 2) }}</td>
                                        <td class="py-5 px-4 text-right text-slate-400 font-mono">S/ {{ number_format($re->labores, 2) }}</td>
                                        <td class="py-5 px-4 text-right text-yellow-600 dark:text-yellow-500 font-black font-mono">S/ {{ number_format($re->cosecha, 2) }}</td>
                                        <td class="py-5 px-4 text-right text-rose-600 dark:text-rose-400 font-mono">S/ {{ number_format($re->flete, 2) }}</td>
                                        <td class="py-5 px-4 text-right text-blue-700 dark:text-blue-300 font-black tracking-tighter font-mono">S/ {{ number_format($re->venta_bruta, 2) }}</td>
                                        <td class="py-5 px-6 text-right">
                                            <div class="inline-block px-4 py-2 rounded-xl border {{ $re->ganancia >= 0 ? 'bg-emerald-500/5 text-emerald-600 border-emerald-500/20' : 'bg-rose-500/5 text-rose-600 border-rose-500/20' }}">
                                                <span class="text-sm font-black italic tracking-tighter font-mono">
                                                    S/ {{ number_format($re->ganancia, 2) }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TABLA 3: RESUMEN EJECUTIVO DE RENTABILIDAD FINAL -->
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl border border-gray-300 dark:border-white/10 overflow-hidden">
                    <div class="p-6 flex items-center gap-4 bg-emerald-600 border-b border-emerald-700 shadow-lg">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white shadow-inner">
                            <i class="fa-solid fa-flag-checkered text-lg"></i>
                        </div>
                        <h3 class="text-sm font-black text-white uppercase tracking-wider italic">
                            Resumen Ejecutivo de Rentabilidad Final
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[11px] text-left border-collapse min-w-[800px]">
                            <thead class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 font-bold uppercase text-[9px] tracking-widest border-b border-emerald-200 dark:border-emerald-900/30">
                                <tr><th class="py-5 px-8">LOTE / CAMPAÑA</th><th class="py-5 px-6">FECHA (S+C)</th><th class="py-5 px-6 text-right text-rose-600 dark:text-rose-400">INVERSIÓN TOTAL</th><th class="py-5 px-6 text-right text-blue-600 dark:text-blue-400">VENTA BRUTA</th><th class="py-5 px-8 text-right">GANANCIA FINAL</th></tr>
                            </thead>
                            <tbody class="text-slate-600 dark:text-slate-300 font-medium">
                                @foreach($this->datos['resumen_ejecutivo'] ?? [] as $re)
                                    <tr class="group hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 transition-colors border-b border-emerald-100 dark:border-emerald-900/20">
                                        <td class="py-6 px-8 font-black text-slate-800 dark:text-white uppercase tracking-tighter">{{ $re->lote }} • <span class="text-emerald-600">{{ $re->cultivo }}</span></td>
                                        <td class="py-6 px-6 font-mono text-[10px] text-slate-400">{{ $re->fechas }}</td>
                                        <td class="py-6 px-6 text-right text-rose-600 dark:text-rose-400 tracking-tighter font-mono">S/ {{ number_format($re->inversion_total, 2) }}</td>
                                        <td class="py-6 px-6 text-right text-blue-700 dark:text-blue-300 font-black tracking-tighter font-mono">S/ {{ number_format($re->venta_bruta, 2) }}</td>
                                        <td class="py-6 px-8 text-right font-black text-lg {{ $re->ganancia >= 0 ? 'text-emerald-600' : 'text-rose-600' }} tracking-tighter font-mono">S/ {{ number_format($re->ganancia, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white font-black uppercase italic text-[10px] border-t-2 border-gray-300 dark:border-slate-800">
                                <tr>
                                    <td colspan="2" class="py-8 px-8 text-center tracking-[0.6em] text-slate-400 italic">Consolidado Total de Campaña</td>
                                    <td class="py-8 px-6 text-right font-mono text-rose-600 dark:text-rose-400">S/ {{ number_format($this->datos['total_inversion'], 2) }}</td>
                                    <td class="py-8 px-6 text-right font-mono text-blue-700 dark:text-blue-300">S/ {{ number_format($this->datos['ventas_brutas'], 2) }}</td>
                                    <td class="py-8 px-8 text-right text-2xl text-emerald-600 dark:text-emerald-400 font-mono tracking-tighter">
                                        <div class="inline-block px-6 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/30">
                                            S/ {{ number_format($this->datos['utilidad_neta'], 2) }}
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        @endif
    </div>

    <!-- VISTA PDF (ESTILOS COMPATIBLES) -->
    <div class="print-view">
        <div class="print-header">
            <h1 style="font-size: 20px; font-weight: 900; margin: 0; color: #0f172a; text-transform: uppercase; letter-spacing: 2px;">AgroSys - Informe Contable DSS</h1>
            <p style="font-size: 8px; color: #64748b; margin-top: 5px; font-style: italic;">Reporte de Auditoría Interna • Generado: {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        @if($cultivoId && isset($detalleInversiones['info']))
            <div class="print-summary-box" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                <div style="display: flex; justify-content: space-between;">
                    <div><h2 style="font-size: 12px; font-weight: 900; margin: 0; color: #166534;">LOTE: {{ strtoupper($detalleInversiones['info']->lote) }}</h2><p style="font-size: 10px; margin-top: 2px;">CULTIVO: {{ $detalleInversiones['info']->cultivo }} ({{ $detalleInversiones['info']->variedad }})</p></div>
                    <div style="text-align: right; font-size: 8px;"><p><strong>INICIO:</strong> {{ $detalleInversiones['info']->fecha_siembra->format('d/m/Y') }}</p><p><strong>EXTENSIÓN:</strong> {{ $detalleInversiones['info']->hectareas }} HA</p></div>
                </div>
            </div>
            <div class="grid-print">
                <div class="kpi-grid-print">
                    <div class="kpi-box-print"><p style="font-size: 6px; color: #64748b;">VENTA BRUTA</p><p style="font-size: 11px; font-weight: 900;">S/ {{ number_format($this->datos['ventas_brutas'], 2) }}</p></div>
                    <div class="kpi-box-print"><p style="font-size: 6px; color: #64748b;">INVERSIÓN</p><p style="font-size: 11px; font-weight: 900;">S/ {{ number_format($this->datos['total_inversion'], 2) }}</p></div>
                    <div class="kpi-box-print" style="background: #f0fdf4;"><p style="font-size: 6px; color: #16a34a;">UTILIDAD</p><p style="font-size: 11px; font-weight: 900; color: #15803d;">S/ {{ number_format($this->datos['utilidad_neta'], 2) }}</p></div>
                    <div class="kpi-box-print"><p style="font-size: 6px; color: #64748b;">EFICIENCIA (ROI)</p><p style="font-size: 11px; font-weight: 900;">{{ round($this->datos['roi'] ?? 0, 1) }}%</p></div>
                </div>
                <div class="print-summary-box">
                    <h3 style="font-size: 7px; font-weight: 900; margin-bottom: 5px; color: #64748b; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;">DESGLOSE FINANCIERO</h3>
                    <div style="font-size: 7px; margin-bottom: 2px; display: flex; justify-content: space-between;"><span>Mano Obra:</span> <strong>S/ {{ number_format($this->datos['mo_total'] ?? 0, 2) }}</strong></div>
                    <div style="font-size: 7px; margin-bottom: 2px; display: flex; justify-content: space-between;"><span>Maquinaria:</span> <strong>S/ {{ number_format($this->datos['maq_total'] ?? 0, 2) }}</strong></div>
                    <div style="font-size: 7px; margin-bottom: 2px; display: flex; justify-content: space-between;"><span>Insumos:</span> <strong>S/ {{ number_format($this->datos['ins_total'] ?? 0, 2) }}</strong></div>
                    <div style="font-size: 7px; margin-bottom: 2px; display: flex; justify-content: space-between;"><span>Cosecha:</span> <strong>S/ {{ number_format($this->datos['cos_lab_total'] ?? 0, 2) }}</strong></div>
                </div>
            </div>
            <h3 style="font-size: 8px; font-weight: 900; background: #0f172a; color: white; padding: 4px; margin-top: 10px; border-radius: 4px;">DESGLOSE TÉCNICO DE INVERSIONES</h3>
            <table class="print-table">
                <thead><tr><th>FECHA</th><th>CATEGORÍA / ACTIVIDAD</th><th style="text-align: right;">INVERSIÓN</th></tr></thead>
                <tbody><tr><td>---</td><td style="font-weight: 900;">COSTO FIJO: ALQUILER</td><td style="text-align: right; font-weight: 900;">S/ {{ number_format($detalleInversiones['alquiler'], 2) }}</td></tr>@php $pM = $detalleInversiones['alquiler']; @endphp @foreach($detalleInversiones['labores'] as $l) @php $pM += $l->costo_total; @endphp <tr><td>{{ $l->fecha_realizacion->format('d/m/Y') }}</td><td style="font-weight: 900;">{{ strtoupper($l->detalleCatalogo->nombre) }}</td><td style="text-align: right; font-weight: 900;">S/ {{ number_format($l->costo_total, 2) }}</td></tr>@endforeach </tbody>
                <tfoot><tr style="background: #f8fafc; font-weight: 900;"><td colspan="2" style="font-size: 9px;">SUBTOTAL MANTENIMIENTO</td><td style="text-align: right; font-size: 9px;">S/ {{ number_format($pM, 2) }}</td></tr></tfoot>
            </table>
            <table class="print-table" style="margin-top: 8px;">
                <thead><tr><th style="background: #ca8a04 !important; color: white;">FECHA</th><th style="background: #ca8a04 !important; color: white;">CATEGORÍA: RECOLECCIÓN</th><th style="background: #ca8a04 !important; color: white; text-align: right;">COSTO</th></tr></thead>
                <tbody>@php $pC = 0; @endphp @foreach($detalleInversiones['labores_cosecha'] as $l) @php $pC += $l->costo_total; @endphp <tr><td>{{ $l->fecha_realizacion->format('d/m/Y') }}</td><td style="font-weight: 900;">{{ strtoupper($l->detalleCatalogo->nombre) }}</td><td style="text-align: right; font-weight: 900;">S/ {{ number_format($l->costo_total, 2) }}</td></tr>@endforeach </tbody>
                <tfoot><tr style="background: #fefce8; font-weight: 900;"><td colspan="2" style="font-size: 9px; color: #854d0e;">SUBTOTAL COSECHA</td><td style="text-align: right; font-size: 9px; color: #854d0e;">S/ {{ number_format($pC, 2) }}</td></tr></tfoot>
            </table>
            <h3 style="font-size: 8px; font-weight: 900; background: #1d4ed8; color: white; padding: 4px; margin-top: 10px; border-radius: 4px;">ANÁLISIS DE COMERCIALIZACIÓN</h3>
            <table class="print-table">
                <thead><tr><th>FECHA V.</th><th>FECHA C.</th><th>COMPRADOR</th><th style="text-align: right;">CANT.</th><th style="text-align: right;">P.U.</th><th style="text-align: right;">FLETE</th><th style="text-align: right;">TOTAL</th></tr></thead>
                <tbody>@php $vCa = 0; $vFl = 0; $vBr = 0; @endphp @foreach($detalleInversiones['ventas'] as $v) @php $vCa += $v->cantidad_vendida_kg; $vFl += $v->costo_flete; $vBr += ($v->cantidad_vendida_kg * $v->precio_por_kg); @endphp <tr>@if($v->flete_span > 0) <td rowspan="{{ $v->flete_span }}" style="vertical-align: middle;">{{ $v->fecha_venta->format('d/m/Y') }}</td> @endif <td>{{ $v->cosecha->fecha_cosecha->format('d/m/Y') }}</td>@if($v->flete_span > 0) <td rowspan="{{ $v->flete_span }}" style="vertical-align: middle; font-weight: 900;">{{ strtoupper($v->comprador->nombre) }}</td> @endif <td style="text-align: right;">{{ (float)$v->cantidad_vendida_kg }} {{ $v->cosecha->unidad_medida }}</td><td style="text-align: right;">{{ number_format($v->precio_por_kg, 2) }}</td>@if($v->flete_span > 0) <td rowspan="{{ $v->flete_span }}" style="text-align: right; color: #dc2626; vertical-align: middle;">- S/ {{ number_format($v->costo_flete, 2) }}</td> @endif <td style="text-align: right; font-weight: 900; color: #1e40af;">S/ {{ number_format($v->cantidad_vendida_kg * $v->precio_por_kg, 2) }}</td></tr>@endforeach </tbody>
                <tfoot><tr style="background: #eff6ff; font-weight: 900;"><td colspan="3" style="font-size: 8px;">TOTALES COMERCIALIZACIÓN</td><td style="text-align: right;">{{ number_format($vCa, 2) }}</td><td></td><td style="text-align: right; color: #dc2626;">S/ {{ number_format($vFl, 2) }}</td><td style="text-align: right; color: #1e40af;">S/ {{ number_format($vBr, 2) }}</td></tr></tfoot>
            </table>
        @else
            <!-- PDF GLOBAL -->
            <div class="grid-print">
                <div class="kpi-grid-print">
                    <div class="kpi-box-print"><p style="font-size: 6px; color: #64748b;">VENTA BRUTA</p><p style="font-size: 11px; font-weight: 900;">S/ {{ number_format($this->datos['ventas_brutas'], 2) }}</p></div>
                    <div class="kpi-box-print"><p style="font-size: 6px; color: #64748b;">INVERSIÓN</p><p style="font-size: 11px; font-weight: 900;">S/ {{ number_format($this->datos['total_inversion'], 2) }}</p></div>
                    <div class="kpi-box-print" style="background: #f0fdf4;"><p style="font-size: 6px; color: #16a34a;">UTILIDAD</p><p style="font-size: 11px; font-weight: 900; color: #15803d;">S/ {{ number_format($this->datos['utilidad_neta'], 2) }}</p></div>
                    <div class="kpi-box-print"><p style="font-size: 6px; color: #64748b;">EFICIENCIA (ROI)</p><p style="font-size: 11px; font-weight: 900;">{{ round($this->datos['roi'] ?? 0, 1) }}%</p></div>
                </div>
                <div class="print-summary-box">
                    <h3 style="font-size: 7px; font-weight: 900; margin-bottom: 5px; color: #64748b; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;">DESGLOSE FINANCIERO</h3>
                    <div style="font-size: 7px; margin-bottom: 2px; display: flex; justify-content: space-between;"><span>Mano Obra:</span> <strong>S/ {{ number_format($this->datos['mo_total'] ?? 0, 2) }}</strong></div>
                    <div style="font-size: 7px; margin-bottom: 2px; display: flex; justify-content: space-between;"><span>Maquinaria:</span> <strong>S/ {{ number_format($this->datos['maq_total'] ?? 0, 2) }}</strong></div>
                    <div style="font-size: 7px; margin-bottom: 2px; display: flex; justify-content: space-between;"><span>Insumos:</span> <strong>S/ {{ number_format($this->datos['ins_total'] ?? 0, 2) }}</strong></div>
                    <div style="font-size: 7px; margin-bottom: 2px; display: flex; justify-content: space-between;"><span>Cosecha:</span> <strong>S/ {{ number_format($this->datos['cos_lab_total'] ?? 0, 2) }}</strong></div>
                </div>
            </div>

            <h3 style="font-size: 9px; font-weight: 900; margin-bottom: 5px; text-transform: uppercase;">CUADRO DETALLADO DE PRODUCCIÓN Y COMERCIALIZACIÓN</h3>
            <table class="print-table">
                <thead><tr><th>LOTE / CAMPAÑA</th><th>FECHA (S+C)</th><th>ALQUILER</th><th>LABORES (MANT.)</th><th>COSECHA</th><th>PROD. COSECHADA</th><th>P. VENTA</th><th>VENTA COS.</th><th>FLETE</th><th>V. BRUTA</th><th>GANANCIA</th></tr></thead>
                <tbody>
                    @php $pA=0; $pL=0; $pC=0; $pF=0; $pB=0; $pG=0; @endphp
                    @foreach($this->datos['tabla_resumen_v3'] ?? [] as $lote)
                        @php $pA+=$lote->alquiler; $pL+=$lote->labores; $pC+=$lote->cosecha; $pF+=$lote->flete; $pG+=$lote->ganancia; @endphp
                        @foreach($lote->filas as $idx => $v)
                            @php $pB+=$v->bruto; @endphp
                            <tr>
                                @if($idx === 0)
                                    <td rowspan="{{ $lote->span }}" style="font-weight: 900;">{{ $lote->lote }} • {{ $lote->cultivo }}</td>
                                    <td rowspan="{{ $lote->span }}" style="font-mono text-[7px]">{{ $lote->fechas }}</td>
                                    <td rowspan="{{ $lote->span }}" style="text-align: right;">{{ number_format($lote->alquiler, 0) }}</td>
                                    <td rowspan="{{ $lote->span }}" style="text-align: right;">{{ number_format($lote->labores, 0) }}</td>
                                    <td rowspan="{{ $lote->span }}" style="text-align: right;">{{ number_format($lote->cosecha, 0) }}</td>
                                @endif
                                <td style="text-align: center; color: #1e40af;">{{ $v->produccion }}</td>
                                <td style="text-align: right;">{{ number_format($v->precio, 2) }}</td>
                                <td style="text-align: right; color: #1e40af;">{{ number_format($v->bruto, 0) }}</td>
                                @if($idx === 0)
                                    <td rowspan="{{ $lote->span }}" style="text-align: right; color: #dc2626;">{{ number_format($lote->flete, 0) }}</td>
                                    <td rowspan="{{ $lote->span }}" style="text-align: right; font-weight: 900;">{{ number_format($lote->venta_bruta, 0) }}</td>
                                    <td rowspan="{{ $lote->span }}" style="vertical-align: middle; text-align: right; font-weight: 900; color: #15803d; background: #f0fdf4;">S/ {{ number_format($lote->ganancia, 0) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot><tr style="background: #0f172a; color: white; font-weight: 900;"><td colspan="2">TOTALES</td><td style="text-align: right;">{{ number_format($pA, 0) }}</td><td style="text-align: right;">{{ number_format($pL, 0) }}</td><td style="text-align: right;">{{ number_format($pC, 0) }}</td><td colspan="3"></td><td style="text-align: right;">{{ number_format($pF, 0) }}</td><td style="text-align: right;">{{ number_format($pB, 0) }}</td><td style="text-align: right;">S/ {{ number_format($pG, 0) }}</td></tr></tfoot>
            </table>

            <h3 style="font-size: 9px; font-weight: 900; margin-top: 15px; margin-bottom: 5px;">RESUMEN EJECUTIVO DE RENTABILIDAD FINAL DETALLADO</h3>
            <table class="print-table">
                <thead><tr><th>LOTE / CAMPAÑA</th><th>FECHA (S+C)</th><th>ALQUILER</th><th>MANTENIMIENTO</th><th>COSECHA</th><th>FLETE</th><th>VENTA BRUTA</th><th>GANANCIA</th></tr></thead>
                <tbody>
                    @foreach($this->datos['resumen_ejecutivo'] ?? [] as $re)
                        <tr><td><strong>{{ $re->lote }} • {{ $re->cultivo }}</strong></td><td style="font-mono text-[7px]">{{ $re->fechas }}</td><td style="text-align: right;">{{ number_format($re->alquiler, 2) }}</td><td style="text-align: right;">{{ number_format($re->labores, 2) }}</td><td style="text-align: right;">{{ number_format($re->cosecha, 2) }}</td><td style="text-align: right;">{{ number_format($re->flete, 2) }}</td><td style="text-align: right;">{{ number_format($re->venta_bruta, 2) }}</td><td style="text-align: right; font-weight: 900;">S/ {{ number_format($re->ganancia, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>

            <h3 style="font-size: 9px; font-weight: 900; margin-top: 15px; margin-bottom: 5px; color: #166534;">RESUMEN EJECUTIVO DE RENTABILIDAD FINAL</h3>
            <table class="print-table">
                <thead><tr><th>LOTE / CAMPAÑA</th><th>FECHA (S+C)</th><th>INVERSIÓN TOTAL</th><th>VENTA BRUTA</th><th>GANANCIA FINAL</th></tr></thead>
                <tbody>
                    @foreach($this->datos['resumen_ejecutivo'] ?? [] as $re)
                        <tr style="background: #f0fdf4;"><td style="font-weight: 900;">{{ $re->lote }} • {{ $re->cultivo }}</td><td>{{ $re->fechas }}</td><td style="text-align: right; color: #dc2626;">S/ {{ number_format($re->inversion_total, 2) }}</td><td style="text-align: right; color: #1e40af;">S/ {{ number_format($re->venta_bruta, 2) }}</td><td style="text-align: right; font-weight: 900; font-size: 10px;">S/ {{ number_format($re->ganancia, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <div style="margin-top: 20px; text-align: center; font-size: 5px; color: #94a3b8; border-top: 1px dashed #cbd5e1; padding-top: 10px;">Documento oficial generado por AgroSys • DSS Inteligencia Agrícola</div>
    </div>
</div>

<div class="space-y-6">
    <!-- HEADER GLOBAL -->
    <div class="flex flex-col lg:flex-row justify-between items-center gap-4 mb-2">
        <div>
            <h2 class="text-4xl font-black text-slate-900 dark:text-white italic tracking-tighter uppercase leading-none">Control <span class="text-indigo-600 italic">Ecosistema</span></h2>
            <p class="text-slate-400 font-bold text-[10px] uppercase mt-1 tracking-[0.4em]">Administración Maestro AgroSys SaaS</p>
        </div>
        <div class="bg-indigo-50 dark:bg-indigo-900/20 px-6 py-3 rounded-xl border border-indigo-100 dark:border-indigo-500/10 flex items-center gap-4 shadow-sm">
            <i class="fa-solid fa-server text-indigo-600 animate-pulse text-xl"></i>
            <div>
                <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest">Estado del Sistema</p>
                <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase italic">Sincronización v2.4.5 OK</p>
            </div>
        </div>
    </div>

    <!-- KPIs GLOBALES INTERACTIVOS (6 TARJETAS EN UNA LÍNEA) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
            $kpis = [
                ['id' => 'modal-orgs', 'label' => 'Empresas', 'value' => $stats['orgs'], 'color' => 'bg-indigo-600', 'icon' => 'fa-building', 'iconColor' => 'text-indigo-600', 'bgIcon' => 'bg-indigo-50'],
                ['id' => 'modal-users', 'label' => 'Usuarios', 'value' => $stats['users'], 'color' => 'bg-blue-500', 'icon' => 'fa-users', 'iconColor' => 'text-blue-500', 'bgIcon' => 'bg-blue-50'],
                ['id' => 'modal-hectareas', 'label' => 'Hectareas', 'value' => number_format($stats['hectareas'], 1), 'color' => 'bg-emerald-500', 'icon' => 'fa-map-location-dot', 'iconColor' => 'text-emerald-500', 'bgIcon' => 'bg-emerald-50'],
                ['id' => 'modal-activos', 'label' => 'Siembras Rango', 'value' => $stats['cultivos'], 'color' => 'bg-agri-green', 'icon' => 'fa-seedling', 'iconColor' => 'text-emerald-600', 'bgIcon' => 'bg-emerald-50'],
                ['id' => 'modal-cosechados', 'label' => 'Cosechas Rango', 'value' => $stats['cosechados'], 'color' => 'bg-amber-500', 'icon' => 'fa-basket-shopping', 'iconColor' => 'text-amber-600', 'bgIcon' => 'bg-amber-50'],
                ['id' => 'modal-pendientes', 'label' => 'Pendientes', 'value' => $stats['solicitudes'], 'color' => 'bg-rose-500', 'icon' => 'fa-clock-rotate-left', 'iconColor' => 'text-rose-600', 'bgIcon' => 'bg-rose-50'],
            ];
        @endphp
        @foreach($kpis as $kpi)
            <div x-on:click="$dispatch('open-modal', '{{ $kpi['id'] }}')" class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm relative overflow-hidden border border-slate-50 dark:border-white/5 flex items-center justify-between transition-all hover:shadow-md hover:-translate-y-1 cursor-pointer group">
                <div class="absolute left-0 top-[35%] bottom-[35%] w-1 {{ $kpi['color'] }} rounded-r-full"></div>
                <div class="pl-2">
                    <p class="text-slate-400 text-[8px] font-black uppercase tracking-wider mb-0.5">{{ $kpi['label'] }}</p>
                    <p class="text-xl font-black text-slate-800 dark:text-white italic leading-none">{{ $kpi['value'] }}</p>
                </div>
                <div class="w-10 h-10 {{ $kpi['bgIcon'] }} rounded-lg flex items-center justify-center {{ $kpi['iconColor'] }} shadow-inner group-hover:scale-110 transition-transform">
                    <i class="fa-solid {{ $kpi['icon'] }} text-base"></i>
                </div>
            </div>
        @endforeach
    </div>

    <!-- SECCIÓN DE TENDENCIAS DE CRECIMIENTO -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-2">
        <!-- TENDENCIA EMPRESAS -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[400px]"
             x-data="{
                init() {
                    const ctx = document.getElementById('orgGrowthChartIndependent');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @js($labelsOrgs),
                            datasets: [{ label: 'Empresas', data: @js($dataOrgs), borderColor: '#4f46e5', backgroundColor: 'rgba(79, 70, 229, 0.08)', fill: true, tension: 0.4, pointRadius: 2, borderWidth: 3 }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } } }
                    });
                }
             }" x-init="init()"
             wire:key="org-trend-sync-{{ $fDesdeOrgs }}-{{ $fHastaOrgs }}"
        >
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h4 class="text-[10px] font-black text-slate-700 dark:text-white uppercase tracking-[0.2em] italic flex items-center gap-3">
                    <span class="w-1.5 h-4 bg-indigo-600 rounded-full"></span> Tendencia de Empresas
                </h4>
                <div class="flex items-center gap-1 bg-slate-50 dark:bg-slate-900/50 p-1 rounded-lg border border-slate-100 shadow-sm">
                    <div class="flex items-center px-2 border-r border-slate-200"><span class="text-[8px] font-black text-slate-400 uppercase mr-1 italic">De</span><input type="date" wire:model.live="fDesdeOrgs" class="bg-transparent border-none text-[9px] font-black text-slate-700 dark:text-white focus:ring-0 p-0 w-24"></div>
                    <div class="flex items-center px-2"><span class="text-[8px] font-black text-slate-400 uppercase mr-1 italic">A</span><input type="date" wire:model.live="fHastaOrgs" class="bg-transparent border-none text-[9px] font-black text-slate-700 dark:text-white focus:ring-0 p-0 w-24"></div>
                </div>
            </div>
            <div class="flex-1 relative" wire:ignore><canvas id="orgGrowthChartIndependent"></canvas></div>
        </div>

        <!-- TENDENCIA USUARIOS -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[400px]"
             x-data="{
                init() {
                    const ctx = document.getElementById('userGrowthChartIndependent');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @js($labelsUsers),
                            datasets: [{ label: 'Usuarios', data: @js($dataUsers), borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.08)', fill: true, tension: 0.4, pointRadius: 2, borderWidth: 3 }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } } }
                    });
                }
             }" x-init="init()"
             wire:key="user-trend-sync-{{ $fDesdeUsers }}-{{ $fHastaUsers }}"
        >
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h4 class="text-[10px] font-black text-slate-700 dark:text-white uppercase tracking-[0.2em] italic flex items-center gap-3">
                    <span class="w-1.5 h-4 bg-agri-green rounded-full"></span> Tendencia de Usuarios
                </h4>
                <div class="flex items-center gap-1 bg-slate-50 dark:bg-slate-900/50 p-1 rounded-lg border border-slate-100 shadow-sm">
                    <div class="flex items-center px-2 border-r border-slate-200"><span class="text-[8px] font-black text-slate-400 uppercase mr-1 italic">De</span><input type="date" wire:model.live="fDesdeUsers" class="bg-transparent border-none text-[9px] font-black text-slate-700 dark:text-white focus:ring-0 p-0 w-24"></div>
                    <div class="flex items-center px-2"><span class="text-[8px] font-black text-slate-400 uppercase mr-1 italic">A</span><input type="date" wire:model.live="fHastaUsers" class="bg-transparent border-none text-[9px] font-black text-slate-700 dark:text-white focus:ring-0 p-0 w-24"></div>
                </div>
            </div>
            <div class="flex-1 relative" wire:ignore><canvas id="userGrowthChartIndependent"></canvas></div>
        </div>
    </div>

    <!-- SECCIÓN MAESTRA: ANÁLISIS DE PRODUCCIÓN Y BALANCE FINANCIERO GLOBAL -->
    <div class="space-y-6 mt-4">
        <!-- GRÁFICO SIEMBRAS VS COSECHAS -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[450px]"
             x-data="{
                init() {
                    const ctx = document.getElementById('siembrasCosechasChartMaster');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @js($labelsProd),
                            datasets: [
                                { label: 'Siembras', data: @js($dataSiembras), borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.05)', fill: true, tension: 0.4, pointRadius: 3, borderWidth: 3 },
                                { label: 'Cosechas', data: @js($dataCosechas), borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.05)', fill: true, tension: 0.4, pointRadius: 3, borderWidth: 3 }
                            ]
                        },
                        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } } }
                    });
                }
             }" x-init="init()"
             wire:key="main-prod-full-{{ $fechaDesde }}-{{ $fechaHasta }}"
        >
            <div class="flex flex-col lg:flex-row justify-between items-center mb-6 gap-4">
                <h4 class="text-sm font-black text-slate-700 dark:text-white uppercase tracking-widest italic flex items-center gap-3">
                    <span class="w-1.5 h-5 bg-blue-600 rounded-full"></span> Análisis de Producción Global
                </h4>
                <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-900/50 p-1.5 rounded-xl border border-slate-100 shadow-sm">
                    <div class="flex items-center px-3 border-r border-slate-200"><span class="text-[9px] font-black text-slate-400 uppercase mr-2 italic">Desde</span><input type="date" wire:model.live="fechaDesde" class="bg-transparent border-none text-[10px] font-black text-slate-700 dark:text-white focus:ring-0 py-0 cursor-pointer"></div>
                    <div class="flex items-center px-3"><span class="text-[9px] font-black text-slate-400 uppercase mr-2 italic">Hasta</span><input type="date" wire:model.live="fechaHasta" class="bg-transparent border-none text-[10px] font-black text-slate-700 dark:text-white focus:ring-0 py-0 cursor-pointer"></div>
                </div>
            </div>
            <div class="flex-1 relative" wire:ignore><canvas id="siembrasCosechasChartMaster"></canvas></div>
        </div>

        <!-- BALANCE FINANCIERO GLOBAL -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[450px]"
             x-data="{
                init() {
                    const ctx = document.getElementById('globalFinanceBarChartMaster');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['BALANCE ECONÓMICO GLOBAL'],
                            datasets: [
                                { label: 'Inversión', data: [{{ $stats['global_inversion'] }}], backgroundColor: '#e11d48', borderRadius: 8 },
                                { label: 'Ventas', data: [{{ $stats['global_ventas'] }}], backgroundColor: '#2563eb', borderRadius: 8 },
                                { label: 'Ganancia', data: [{{ $stats['global_ganancia'] }}], backgroundColor: '#10b981', borderRadius: 8 }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true, ticks: { callback: (v) => 'S/ ' + v.toLocaleString() } } }
                        }
                    });
                }
             }" x-init="init()"
             wire:key="global-finance-bar-full-{{ $fechaDesde }}-{{ $fechaHasta }}"
        >
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-sm font-black text-slate-700 dark:text-white uppercase tracking-widest italic flex items-center gap-3">
                    <i class="fa-solid fa-chart-bar text-indigo-600"></i> Balance Financiero Global
                </h4>
                <div class="px-4 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-black uppercase italic border border-indigo-100">
                    FLUJO TOTAL: S/ {{ number_format($stats['global_ventas'], 0) }}
                </div>
            </div>
            <div class="flex-1 relative" wire:ignore><canvas id="globalFinanceBarChartMaster"></canvas></div>
        </div>
    </div>

    <!-- BLOQUE SECUNDARIO -->
    <div class="grid grid-cols-1 lg:grid-cols-24 gap-6 mt-4">
        <div style="grid-column: span 16 / span 16;" class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <!-- TOP SIEMBRA -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[350px]">
                    <h4 class="text-[10px] font-black text-slate-700 dark:text-white uppercase tracking-widest italic mb-6">Top Siembra Global</h4>
                    <div class="space-y-3">
                        @foreach($topCultivos as $index => $cultivo)
                            <div class="flex items-center justify-between p-3 bg-slate-50/50 rounded-lg border border-slate-100">
                                <span class="w-6 h-6 flex items-center justify-center bg-indigo-600 text-white rounded text-[9px] font-black italic">{{ $index + 1 }}</span>
                                <p class="text-[10px] font-black uppercase text-slate-700 dark:text-white flex-1 ml-4 leading-none">{{ $cultivo->nombre }}</p>
                                <span class="text-[9px] font-bold text-slate-400 uppercase">{{ $cultivo->total }} UNID.</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- COSECHA POR MODALIDAD -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-lg border border-slate-50 flex flex-col h-[350px]">
                    <h4 class="text-[10px] font-black text-slate-700 dark:text-white uppercase tracking-widest italic mb-6">Cosecha por Modalidad</h4>
                    <div class="space-y-3">
                        @foreach($cosechaPorProducto as $index => $cp)
                            <div class="flex items-center justify-between p-3 bg-slate-50/50 rounded-lg border border-slate-100">
                                <p class="text-[10px] font-black uppercase text-slate-700 dark:text-white">{{ $cp->nombre }}</p>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-emerald-600 leading-none">{{ number_format($cp->total_cantidad, 0) }}</p>
                                    <p class="text-[7px] font-black text-slate-400 uppercase mt-1">{{ $cp->unidad_medida }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- LOG GLOBAL -->
        <div style="grid-column: span 8 / span 8;">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl border border-slate-50 h-full flex flex-col sticky top-20">
                <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-3 italic border-b pb-6 mb-6">
                    <i class="fa-solid fa-fingerprint text-indigo-600"></i> Últimas 5 Actividades
                </h4>
                <div class="flex-1 overflow-y-auto space-y-4 custom-scrollbar pr-2">
                    @foreach($actividadGlobal as $log)
                        <div class="p-4 bg-slate-50/50 dark:bg-white/5 rounded-xl border border-slate-100 group transition-all hover:border-indigo-300">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-[10px] font-black text-slate-800 dark:text-white uppercase truncate mr-2">{{ $log->usuario->nombres }}</p>
                                <span class="text-[7px] font-black text-slate-300 uppercase italic flex-shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[8px] font-bold text-indigo-600 uppercase tracking-tighter">{{ $log->accion }} -> {{ $log->tabla_afectada }}</p>
                            <p class="text-[9px] text-slate-400 mt-2 font-medium leading-relaxed">{{ $log->descripcion }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- MODALES DETALLE KPIs -->
    <x-modal name="modal-orgs" focusable>
        <div class="p-8">
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase italic mb-6 border-b pb-4">Directorio de Empresas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($lists['orgs'] as $org)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-black text-slate-800 uppercase">{{ $org->nombre }}</p>
                        <p class="text-[10px] text-slate-500 font-bold mt-1">RUC: {{ $org->ruc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </x-modal>

    <x-modal name="modal-users" focusable>
        <div class="p-8">
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase italic mb-6 border-b pb-4">Censo de Usuarios</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($lists['users'] as $user)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                        <div>
                            <p class="text-xs font-black text-slate-800 uppercase">{{ $user->nombres }} {{ $user->apellidos }}</p>
                            <p class="text-[9px] text-slate-400 font-bold mt-1 italic">{{ $user->email }}</p>
                        </div>
                        <span class="text-[8px] px-2 py-1 bg-indigo-100 text-indigo-700 rounded font-black uppercase">{{ $user->rol->nombre ?? 'Usuario' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </x-modal>

    <x-modal name="modal-hectareas" focusable>
        <div class="p-8">
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase italic mb-6 border-b pb-4">Activos de Tierra</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($lists['terrenos'] as $terreno)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-end">
                        <div>
                            <p class="text-xs font-black text-slate-800 uppercase">{{ $terreno->nombre }}</p>
                            <p class="text-[9px] text-slate-500 font-bold mt-1 uppercase italic tracking-tighter">Propietario: {{ $terreno->responsable->nombres ?? 'N/A' }}</p>
                        </div>
                        <p class="text-sm font-black text-emerald-600 italic">{{ $terreno->hectareas }} ha</p>
                    </div>
                @endforeach
            </div>
        </div>
    </x-modal>

    <x-modal name="modal-activos" focusable>
        <div class="p-8">
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase italic mb-6 border-b pb-4">Lotes Sembrados en Rango</h3>
            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($lists['cultivos_activos'] as $cultivo)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <i class="fa-solid fa-seedling text-emerald-500"></i>
                            <div>
                                <p class="text-xs font-black text-slate-800 uppercase">{{ $cultivo->nombre_lote }}</p>
                                <p class="text-[9px] text-slate-500 font-bold uppercase">{{ $cultivo->detalleCatalogo->nombre }} ({{ $cultivo->variedad }})</p>
                            </div>
                        </div>
                        <span class="text-[8px] px-2 py-1 bg-emerald-100 text-emerald-700 rounded font-black uppercase italic">{{ $cultivo->fecha_siembra->format('d/m/Y') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </x-modal>

    <x-modal name="modal-cosechados" focusable>
        <div class="p-8">
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase italic mb-6 border-b pb-4">Historial de Lotes Cosechados</h3>
            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($lists['cultivos_cosechados'] as $cultivo)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <i class="fa-solid fa-basket-shopping text-amber-500"></i>
                            <div>
                                <p class="text-xs font-black text-slate-800 uppercase">{{ $cultivo->nombre_lote }}</p>
                                <p class="text-[9px] text-slate-500 font-bold uppercase">{{ $cultivo->detalleCatalogo->nombre }} · Final: {{ $cultivo->fecha_cosecha_finalizada ? $cultivo->fecha_cosecha_finalizada->format('d/m/Y') : '---' }}</p>
                            </div>
                        </div>
                        <p class="text-[10px] font-black text-amber-600 uppercase italic">Cosechado</p>
                    </div>
                @endforeach
            </div>
        </div>
    </x-modal>

    <x-modal name="modal-pendientes" focusable>
        <div class="p-8">
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase italic mb-6 border-b pb-4">Solicitudes de Organizaciones</h3>
            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($lists['solicitudes'] as $sol)
                    <div class="p-4 bg-slate-50 rounded-xl border border-rose-100 flex justify-between items-center">
                        <div>
                            <p class="text-xs font-black text-slate-800 uppercase">{{ $sol->solicitante->nombres }} {{ $sol->solicitante->apellidos }}</p>
                            <p class="text-[9px] text-rose-500 font-bold uppercase italic tracking-tighter">Petición: {{ $sol->datos_extra['nombre'] ?? 'N/A' }}</p>
                        </div>
                        <a href="/admin/solicitudes" class="px-3 py-1 bg-rose-600 text-white rounded text-[8px] font-black uppercase hover:bg-rose-700 transition-all shadow-sm">Atender</a>
                    </div>
                @endforeach
            </div>
        </div>
    </x-modal>
</div>

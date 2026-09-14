<div class="space-y-8 p-4 md:p-1 transition-colors duration-500">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-100 dark:border-white/5 pb-6">
        <div class="flex items-center space-x-5">
            <div class="w-16 h-16 bg-[#003a38] rounded-2xl flex items-center justify-center shadow-2xl border border-white/10">
                <i class="fa-solid fa-seedling text-agri-green text-3xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">Gestión de Cultivos</h2>
                <p class="text-[9px] text-agri-green font-black uppercase tracking-[0.3em] mt-1.5 italic">Control de Campañas y Lotes</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center bg-white dark:bg-slate-900 px-4 py-2 rounded-2xl border border-slate-100 dark:border-white/5 shadow-sm space-x-2">
                <span class="text-[9px] font-black text-slate-400 uppercase italic">Desde:</span>
                <input type="date" wire:model.live="filterDateStart" class="bg-transparent border-none p-0 text-[10px] font-black text-slate-600 dark:text-slate-300 focus:ring-0 outline-none w-28 uppercase">
                <div class="w-px h-4 bg-slate-100 dark:bg-white/10"></div>
                <span class="text-[9px] font-black text-slate-400 uppercase italic">Hasta:</span>
                <input type="date" wire:model.live="filterDateEnd" class="bg-transparent border-none p-0 text-[10px] font-black text-slate-600 dark:text-slate-300 focus:ring-0 outline-none w-28 uppercase">
            </div>
            <button wire:click="openCreateModal" class="px-10 py-3 bg-agri-green text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all italic"><i class="fa-solid fa-plus mr-2"></i> Nuevo Registro</button>
        </div>
    </div>

    <!-- Filtros Searchables -->
    <div class="space-y-4">
        @if($filterTerrenoId && $filteredTerreno)
            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-white/5 px-6 py-4 rounded-xl animate-in fade-in shadow-xl">
                <div class="flex items-center gap-6">
                    <div class="w-14 h-14 rounded-2xl overflow-hidden shadow-2xl border-2 border-white dark:border-white/10 shrink-0"><img src="{{ $filteredTerreno->foto_path ? Storage::url($filteredTerreno->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($filteredTerreno->nombre).'&background=003a38&color=fff' }}" class="w-full h-full object-cover"></div>
                    <div class="flex flex-col gap-1.5">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] leading-none">Filtrando por Terreno</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase italic tracking-tighter leading-none">{{ number_format($filteredTerreno->hectareas, 2) }} HA DEL TERRENO {{ $filteredTerreno->nombre }}<span class="text-agri-green text-sm ml-2">({{ number_format($filteredTerreno->area_disponible, 2) }} Ha. disponibles)</span></h4>
                            @php
                                $tenureClass = $filteredTerreno->tipo_tenencia === 'alquilado' ? ($filteredTerreno->is_alquiler_vencido ? 'bg-rose-500 animate-pulse' : 'bg-amber-500') : 'bg-emerald-500';
                            @endphp
                            <span class="{{ $tenureClass }} text-white px-3 py-1 rounded-lg text-[9px] font-black uppercase shadow-lg">{{ strtoupper($filteredTerreno->tipo_tenencia) }}</span>
                        </div>
                    </div>
                </div>
                <button wire:click="$set('filterTerrenoId', null)" class="px-6 py-2.5 bg-white dark:bg-slate-800 text-rose-500 rounded-xl font-black text-[10px] uppercase hover:bg-rose-50 transition-all shadow-sm border border-rose-100 flex items-center gap-2"><i class="fa-solid fa-circle-xmark"></i> Quitar Filtro</button>
            </div>
        @endif
        <div class="flex flex-wrap items-center gap-2 p-1.5 bg-white/40 dark:bg-slate-900/40 rounded-2xl border border-slate-100 shadow-lg backdrop-blur-md">
            <div class="relative min-w-[140px] flex-1"><span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-mountain-sun text-[9px]"></i></span><input list="terrenos-list" wire:model.live.debounce.300ms="searchTerreno" placeholder="TERRENO..." class="w-full pl-8 pr-2 py-2 bg-slate-50 dark:bg-white/5 border-none rounded-xl text-[9px] font-black focus:ring-1 focus:ring-agri-green/20 shadow-inner italic uppercase"><datalist id="terrenos-list">@foreach($misTerrenos as $t) <option value="{{ $t->nombre }}"> @endforeach</datalist></div>
            <div class="relative min-w-[140px] flex-1"><span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-leaf text-[9px]"></i></span><input list="crops-catalog-list" wire:model.live.debounce.300ms="searchCultivo" placeholder="CULTIVO..." class="w-full pl-8 pr-2 py-2 bg-slate-50 dark:bg-white/5 border-none rounded-xl text-[9px] font-black focus:ring-1 focus:ring-agri-green/20 shadow-inner italic uppercase"><datalist id="crops-catalog-list">@foreach($catalogo as $cat) <option value="{{ $cat->nombre }}"> @endforeach</datalist></div>
            <div class="relative min-w-[100px] flex-1"><span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-tags text-[9px]"></i></span><input wire:model.live.debounce.300ms="searchVariedad" placeholder="VARIEDAD..." class="w-full pl-8 pr-2 py-2 bg-slate-50 dark:bg-white/5 border-none rounded-xl text-[9px] font-black focus:ring-1 focus:ring-agri-green/20 shadow-inner italic uppercase"></div>
            <div class="relative min-w-[150px] flex-1"><span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-circle-info text-[9px]"></i></span><select wire:model.live="filterStatus" class="w-full pl-8 pr-6 py-2 bg-slate-50 dark:bg-white/5 border-none rounded-xl text-[9px] font-black focus:ring-1 focus:ring-agri-green/20 shadow-inner italic appearance-none uppercase"><option value="">TODOS LOS ESTADOS</option><option value="Planificado">PLANIFICADO</option><option value="En crecimiento">EN CRECIMIENTO</option><option value="Cosechado">COSECHADO</option><option value="Perdido">PERDIDO</option></select></div>
            <button wire:click="resetFilters" class="px-5 py-2 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-xl font-black text-[9px] uppercase hover:bg-slate-200 transition-all italic flex items-center gap-2 shadow-sm"><i class="fa-solid fa-undo-alt"></i> RESET</button>
        </div>
    </div>

    @if(!$hasTerrenos)
        <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-900 rounded-[3rem] border border-dashed border-slate-300 dark:border-white/10 shadow-inner">
            <div class="w-32 h-32 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-8 relative">
                <i class="fa-solid fa-mountain-sun text-6xl text-slate-200 dark:text-white/10"></i>
                <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center shadow-lg animate-bounce">
                    <i class="fa-solid fa-triangle-exclamation text-white"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic tracking-tighter mb-4 text-center">Falta Información Base</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium max-w-md text-center italic leading-relaxed px-6">
                No se encontraron parcelas registradas. Por favor, <span class="text-agri-green font-black">registre primero un terreno</span> en la sección correspondiente para poder gestionar sus campañas agrícolas.
            </p>
            <a href="{{ route('admin.terrenos') }}" class="mt-10 px-12 py-4 bg-agri-green text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-2xl shadow-agri-green/30 hover:scale-110 transition-all italic flex items-center gap-3">
                <i class="fa-solid fa-map-location-dot"></i> Ir a Mis Terrenos
            </a>
        </div>
    @else
        <!-- Chart de Rendimiento -->
        <div class="animate-in fade-in duration-700">
        <div class="bg-white/90 dark:bg-slate-900/80 p-6 rounded-xl border border-slate-100 shadow-2xl flex flex-col">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-4 px-2">
                <div class="relative w-full lg:w-[30%] min-w-[200px]"><div class="absolute inset-y-0 left-0 pl-5 flex items-center text-agri-green"><i class="fa-solid fa-chart-simple text-sm"></i></div><select wire:model.live="chartType" class="w-full pl-12 pr-12 py-2.5 bg-white dark:bg-slate-800 border-2 border-agri-green/20 rounded-2xl text-[12px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-agri-green/10 outline-none cursor-pointer shadow-sm hover:shadow-md appearance-none"><option value="hist_tn">📊 REND. HISTÓRICO (TN/HA)</option><option value="hist_inv">💰 REND. HISTÓRICO (INV/GAN)</option><option value="real_inv">📈 REND. REAL (INV/GAN)</option><option value="real_tn">🌱 REND. REAL (TN/HA)</option></select></div>
                <div class="text-right flex flex-col items-end border-r-4 border-agri-green pr-5 py-0.5"><span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.3em] mb-1">Indicador Seleccionado</span><span class="text-[11px] font-black text-agri-green uppercase italic tracking-tighter leading-none">{{ $chartData['title'] }}</span></div>
            </div>
            <div class="relative w-full h-[260px]"><div data-react-component="agro-bar-chart" data-props="{{ json_encode(['data' => $chartData]) }}" wire:key="chart-{{ $chartType }}-{{ count($chartData['values']) }}" class="w-full h-full" wire:ignore.self></div></div>
        </div>
    </div>

    <!-- Grid de Cultivos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
        @foreach($cultivos as $cultivo)
        @php
            $temp = rand(18, 26); $hmd = rand(55, 75);
            $siembra = \Carbon\Carbon::parse($cultivo->fecha_siembra);
            $cosecha = $cultivo->fecha_cosecha_estimada ? \Carbon\Carbon::parse($cultivo->fecha_cosecha_estimada) : $siembra->copy()->addMonths(4);
            $totalDias = $siembra->diffInDays($cosecha);
            $diasTranscurridos = $siembra->diffInDays(now(), false);
            $progreso = $totalDias > 0 ? max(0, min(100, ($diasTranscurridos / $totalDias) * 100)) : 0;
            $colorProgreso = $progreso < 30 ? 'bg-emerald-300' : ($progreso < 70 ? 'bg-amber-400' : 'bg-agri-green');
        @endphp
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-2xl border border-slate-100 group relative transition-all duration-700 hover:-translate-y-2" x-data="{ menuOpen: false }">
            <div class="h-72 bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
                @if($cultivo->foto_path)<img src="{{ Storage::url($cultivo->foto_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">@else<div class="w-full h-full flex items-center justify-center text-slate-200 bg-slate-50 dark:bg-white/5"><i class="fa-solid fa-leaf text-8xl"></i></div>@endif

                <!-- 1. Capa de Degradado Base -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/30 to-transparent"></div>

                <!-- 2. Overlay de Oscurecimiento SOLO al pasar el mouse por la imagen -->
                <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-all duration-300 z-10"></div>

                <!-- 3. BLOQUE DE FECHAS DINÁMICAS (Nítidas, sin fondo y siempre visibles) -->
                <div class="absolute top-16 left-7 z-[100] flex flex-col space-y-1 pointer-events-none transition-all duration-500 group-hover:opacity-40">
                    <div class="flex items-center gap-2 drop-shadow-[0_2px_3px_rgba(0,0,0,1)]">
                        <i class="fa-solid fa-calendar-check text-agri-green text-[12px]"></i>
                        <p class="text-[11px] font-black text-white uppercase italic leading-none">
                            @if($cultivo->estado === 'Planificado')
                                siembra planificada - {{ $cultivo->fecha_planificada ? $cultivo->fecha_planificada->format('d/m/Y') : '---' }}
                            @else
                                sembrado el {{ $cultivo->fecha_siembra ? $cultivo->fecha_siembra->format('d/m/Y') : ($cultivo->fecha_planificada ? $cultivo->fecha_planificada->format('d/m/Y') : '---') }}
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-2 drop-shadow-[0_2px_3px_rgba(0,0,0,1)]">
                        <i class="fa-solid fa-calendar-plus text-blue-400 text-[12px]"></i>
                        <p class="text-[11px] font-black text-white uppercase italic leading-none">
                            @if($cultivo->estado === 'Cosechado')
                                cosechado el {{ $cultivo->fecha_cosecha_finalizada ? $cultivo->fecha_cosecha_finalizada->format('d/m/Y') : '---' }}
                            @else
                                cosechar aprox el {{ $cultivo->fecha_cosecha_estimada ? $cultivo->fecha_cosecha_estimada->format('d/m/Y') : '---' }}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- 1. Clima y Menú (Z-50: Siempre arriba y nítidos) -->
                <div class="absolute top-5 left-5 z-50 flex flex-col gap-2">
                    <div class="flex items-center space-x-2 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md px-3.5 py-1.5 rounded-xl shadow-2xl border border-white/20 w-fit">
                        <i class="fa-solid fa-cloud-sun text-amber-500 text-xs"></i>
                        <span class="text-[10px] font-black text-slate-800 dark:text-white italic uppercase">{{ $temp }}°C | {{ $hmd }}% HR</span>
                    </div>
                    <!-- IDENTIFICADOR DE LOTE (Agrosys Premium) -->
                    <div class="bg-amber-500 text-white px-4 py-1.5 rounded-xl shadow-2xl border border-white/20 w-fit animate-in slide-in-from-left-2 duration-500">
                        <p class="text-[11px] font-black tracking-widest uppercase italic leading-none flex items-center gap-2">
                            <i class="fa-solid fa-barcode text-[12px]"></i>
                            LOTE: {{ $cultivo->nombre_lote }}
                        </p>
                    </div>
                </div>

                <div class="absolute top-5 right-5 z-50">
                    <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false" class="w-8 h-8 bg-white/10 backdrop-blur-md border border-white/20 rounded-lg text-white flex items-center justify-center shadow-lg hover:bg-white/30 transition-all"><i class="fa-solid fa-ellipsis-vertical text-xs"></i></button>
                    <div x-show="menuOpen" x-transition class="absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden" x-cloak><button wire:click="edit({{ $cultivo->id }})" class="w-full px-6 py-4 text-left text-[11px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition-all italic">EDITAR</button><button wire:click="delete({{ $cultivo->id }})" wire:confirm="¿Borrar?" class="w-full px-6 py-4 text-left text-[11px] font-black uppercase text-rose-500 hover:bg-rose-50 transition-all italic">BORRAR</button></div>
                </div>

                <!-- 3. Botón Central de Gestión (Z-40) -->
                <a href="{{ route('admin.labores', ['filterCropId' => $cultivo->id, 'strict' => 1]) }}" class="absolute inset-0 z-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                    <div class="px-6 py-3 bg-agri-green text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-2xl border border-white/20 italic flex items-center justify-center gap-3 transform scale-75 group-hover:scale-100 transition-all duration-300">
                        <i class="fa-solid fa-gears text-sm animate-spin-slow"></i> Gestionar Labores
                    </div>
                </a>

                <!-- 4. Información Principal del Cultivo (Z-30: Se opaca en hover) -->
                <div class="absolute bottom-5 left-7 right-7 text-white space-y-4 z-30 transition-all duration-300 group-hover:opacity-30">
                    <div class="flex justify-between items-end">
                        <div class="min-w-0 flex-1 pr-2">
                            <h3 class="text-2xl font-black italic tracking-tighter uppercase leading-none flex items-baseline gap-2">
                                <span>{{ $cultivo->detalleCatalogo->nombre }}</span>
                                <span class="text-[12px] font-black text-emerald-400 uppercase tracking-widest italic truncate">{{ $cultivo->variedad ?: 'VAR. GENERICA' }}</span>
                            </h3>
                        </div>
                        <div class="px-3 py-1.5 bg-agri-green rounded-lg text-[11px] font-black italic shadow-lg shrink-0 border border-white/20">{{ number_format($cultivo->area_destinada, 2) }} ha</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 pt-3 border-t border-white/20">
                        @php
                            $esCosechado = $cultivo->estado === 'Cosechado';
                        @endphp
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none mb-1">
                                @if($esCosechado) RENDIMIENTO @else INVERSIÓN @endif
                            </span>
                            @if($esCosechado)
                                <span class="text-[12px] font-black italic text-emerald-400">
                                    {{ number_format($cultivo->rendimiento_esperado_tn_ha, 2) }} <span class="text-[8px] opacity-70">TN/HA</span>
                                </span>
                                <span class="text-[7px] font-black text-white/40 uppercase tracking-tighter">DATO REAL VENTA</span>
                            @else
                                <span class="text-[12px] font-black italic text-amber-400">S/ {{ number_format($cultivo->total_inversion, 2) }}</span>
                            @endif
                        </div>
                        <div class="flex flex-col border-l border-white/10 pl-2">
                            <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none mb-1">Riego</span>
                            <span class="text-[10px] font-bold italic opacity-90 truncate">{{ $cultivo->terreno->fuente_agua }}</span>
                        </div>
                        <div class="flex flex-col border-l border-white/10 pl-2 text-right">
                            <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none mb-1">Lugar</span>
                            <a href="{{ route('admin.terrenos') }}" class="text-[10px] font-bold italic opacity-90 hover:text-agri-green transition-colors truncate block">{{ $cultivo->terreno->nombre }}</a>
                        </div>
                    </div>
                </div>

                <!-- 3. Botón Central de Gestión (Z-40) -->
                <a href="{{ route('admin.labores', ['filterCropId' => $cultivo->id, 'strict' => 1]) }}" class="absolute inset-0 z-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                    <div class="px-6 py-3 bg-agri-green text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-2xl border border-white/20 italic flex items-center justify-center gap-3 transform scale-75 group-hover:scale-100 transition-all duration-300">
                        <i class="fa-solid fa-gears text-sm animate-spin-slow"></i> Gestionar Labores
                    </div>
                </a>

                <!-- 4. Información Principal del Cultivo (Z-30: Se opaca en hover) -->
                <div class="absolute bottom-5 left-7 right-7 text-white space-y-4 z-30 transition-all duration-300 group-hover:opacity-30">
                    <div class="flex justify-between items-end">
                        <div class="min-w-0 flex-1 pr-2">
                            <h3 class="text-2xl font-black italic tracking-tighter uppercase leading-none flex flex-wrap items-baseline gap-2">
                                <span>{{ $cultivo->detalleCatalogo->nombre }}</span>
                                <span class="text-[12px] font-black text-emerald-400 uppercase tracking-widest italic truncate">{{ $cultivo->variedad ?: 'VAR. GENERICA' }}</span>
                            </h3>
                        </div>
                        <div class="px-3 py-1.5 bg-agri-green rounded-lg text-[11px] font-black italic shadow-lg shrink-0 border border-white/20">{{ number_format($cultivo->area_destinada, 2) }} ha</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 pt-3 border-t border-white/20">
                        @php
                            $esCosechado = $cultivo->estado === 'Cosechado';
                        @endphp
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none mb-1">
                                @if($esCosechado) RENDIMIENTO @else INVERSIÓN @endif
                            </span>
                            @if($esCosechado)
                                <span class="text-[12px] font-black italic text-emerald-400">
                                    {{ number_format($cultivo->rendimiento_esperado_tn_ha, 2) }} <span class="text-[8px] opacity-70">TN/HA</span>
                                </span>
                                <span class="text-[7px] font-black text-white/40 uppercase tracking-tighter">DATO REAL VENTA</span>
                            @else
                                <span class="text-[12px] font-black italic text-amber-400">S/ {{ number_format($cultivo->total_inversion, 2) }}</span>
                            @endif
                        </div>
                        <div class="flex flex-col border-l border-white/10 pl-2">
                            <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none mb-1">Riego</span>
                            <span class="text-[10px] font-bold italic opacity-90 truncate">{{ $cultivo->terreno->fuente_agua }}</span>
                        </div>
                        <div class="flex flex-col border-l border-white/10 pl-2 text-right">
                            <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none mb-1">Lugar</span>
                            <a href="{{ route('admin.terrenos') }}" class="text-[10px] font-bold italic opacity-90 hover:text-agri-green transition-colors truncate block">{{ $cultivo->terreno->nombre }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. Barra de Progreso Inferior (Sin efectos de opacación al pasar el mouse por arriba) -->
            <div wire:click="showCropReport({{ $cultivo->id }})" class="px-4 py-3 space-y-3 bg-white dark:bg-slate-900 cursor-pointer hover:bg-slate-50 dark:hover:bg-white/5 transition-all duration-300">
                <div class="flex justify-between items-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Fenología: {{ strtoupper($cultivo->estado) }}</p>
                    <span class="text-[11px] font-black text-agri-green uppercase tracking-tighter">{{ round($progreso) }}% completo</span>
                </div>
                <div class="w-full h-3 bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden shadow-inner p-0.5 border border-slate-200/50">
                    <div class="h-full {{ $colorProgreso }} transition-all duration-1000 ease-out rounded-full shadow-[0_0_15px_rgba(0,186,46,0.3)]" style="width: {{ $progreso }}%"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Paginación -->
    <div class="mt-12">{{ $cultivos->links() }}</div>
    @endif

    <!-- MODAL DE REPORTE DE CULTIVO -->
    <x-modal name="modal-crop-report" :show="false" focusable>
        @if($selectedCropForReport)
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-2xl border border-slate-100 max-w-5xl mx-auto"
             x-data="{
                 initReportChart() {
                     const data = @js($reportData['chart']);
                     if (!data || !data.values || data.values.length === 0) return;
                     const canvas = document.getElementById('cultivoReportChartCanvas');
                     if (!canvas) return;
                     const ctx = canvas.getContext('2d');
                     if (window.myCultivoReportChart) window.myCultivoReportChart.destroy();
                     window.myCultivoReportChart = new Chart(ctx, {
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
                                     bodyFont: { size: 10, weight: 'bold' },
                                     callbacks: {
                                         label: (context) => ` ${context.label}: S/ ${context.parsed.toLocaleString()}`
                                     }
                                 }
                             }
                         }
                     });
                 }
             }"
             x-init="setTimeout(() => initReportChart(), 600)"
             x-on:open-modal.window="$event.detail == 'modal-crop-report' ? setTimeout(() => initReportChart(), 700) : null"
            <!-- Header Bar -->
            <div class="bg-[#003a38] px-8 py-3 flex justify-between items-center border-b border-white/10">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 text-white/70">
                        <i class="fa-solid fa-cloud-sun text-amber-400 text-xs"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest italic">{{ rand(18,26) }}°C | {{ rand(55,75) }}% HR</span>
                    </div>
                    <div class="h-4 w-px bg-white/10"></div>
                    <span class="text-[9px] font-black text-agri-green uppercase tracking-[0.3em] italic">Informe Técnico de Campaña</span>
                </div>
                <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center text-white/40 hover:text-white transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <!-- Header Hero -->
            <div class="relative h-64 w-full group/hero">
                @if($selectedCropForReport->foto_path)<img src="{{ Storage::url($selectedCropForReport->foto_path) }}" class="w-full h-full object-cover">@else<div class="w-full h-full bg-gradient-to-br from-[#003a38] to-emerald-900 flex items-center justify-center"><i class="fa-solid fa-leaf text-6xl text-white/10"></i></div>@endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/40 to-transparent"></div>

                <!-- BADGE DE LOTE POSICIONADO (Top Right HUD) -->
                <div class="absolute top-6 right-8 z-50">
                    <div class="bg-amber-500 text-white px-4 py-2 rounded-xl shadow-2xl border border-white/20 tracking-widest italic flex items-center gap-2 animate-in slide-in-from-right-4 duration-700">
                        <i class="fa-solid fa-barcode text-sm"></i>
                        <span class="text-[11px] font-black uppercase">LOTE: {{ $selectedCropForReport->nombre_lote }}</span>
                    </div>
                </div>

                <div class="absolute bottom-6 left-8 right-8 z-30 space-y-4">
                    <div class="flex justify-between items-end">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-3 py-1 bg-agri-green text-white text-[11px] font-black uppercase rounded shadow-xl border border-white/20 tracking-widest leading-none">CAMPAÑA #{{ $selectedCropForReport->id }}</span>
                                <span class="px-3 py-1 bg-white/10 backdrop-blur-md text-white text-[11px] font-black uppercase rounded border border-white/10 tracking-widest leading-none">{{ strtoupper($selectedCropForReport->estado) }}</span>
                            </div>
                            <h2 class="text-4xl font-black text-white italic tracking-tighter uppercase leading-none drop-shadow-2xl mb-2">{{ $selectedCropForReport->detalleCatalogo->nombre }}<span class="text-xl font-bold text-white/50 ml-4 lowercase">{{ $selectedCropForReport->variedad ?: 'genérica' }}</span></h2>
                        </div>
                    </div>

                    <!-- Ubicación y Fechas (Ahora dentro de la imagen) -->
                    <div class="flex flex-wrap items-center gap-6 pt-4 border-t border-white/10">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 bg-agri-green rounded flex items-center justify-center text-white text-xs shadow-lg"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <p class="text-[11px] font-black text-white/40 uppercase tracking-widest">Terreno</p>
                                <p class="text-[12px] font-black text-white uppercase italic leading-none">{{ $selectedCropForReport->terreno->nombre }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 bg-blue-500 rounded flex items-center justify-center text-white text-xs shadow-lg"><i class="fa-solid fa-calendar-check"></i></div>
                            <div>
                                <p class="text-[11px] font-black text-white/40 uppercase tracking-widest">Siembra</p>
                                <p class="text-[12px] font-black text-white uppercase italic leading-none">{{ ($selectedCropForReport->fecha_siembra ?: $selectedCropForReport->fecha_planificada)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 bg-amber-500 rounded flex items-center justify-center text-white text-xs shadow-lg"><i class="fa-solid fa-calendar-plus"></i></div>
                            <div>
                                <p class="text-[11px] font-black text-white/40 uppercase tracking-widest">Cosecha</p>
                                <p class="text-[12px] font-black text-white uppercase italic leading-none">{{ $selectedCropForReport->fecha_cosecha_estimada ? $selectedCropForReport->fecha_cosecha_estimada->format('d/m/Y') : '---' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cuerpo Informe -->
            <div class="p-6 space-y-8 max-h-[65vh] overflow-y-auto custom-scrollbar bg-slate-50/30 dark:bg-transparent">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna 1: Perfil y Chart -->
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-[11px] font-black text-agri-green uppercase tracking-[0.4em] mb-4 border-b border-slate-200 dark:border-white/10 pb-2 italic">Perfil Operativo</h4>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4 group"><div class="w-12 h-12 bg-white dark:bg-white/5 rounded-xl flex items-center justify-center text-agri-green shadow-xl border border-slate-100 dark:border-white/5 transition-transform group-hover:scale-110"><i class="fa-solid fa-chart-area text-xl"></i></div><div><p class="text-[9px] font-black text-slate-400 uppercase mb-0.5 tracking-widest">Área Destinada</p><p class="text-base font-black text-slate-800 dark:text-slate-200 italic">{{ number_format($selectedCropForReport->area_destinada, 2) }} HA</p></div></div>
                                <div class="flex items-center gap-4 group"><div class="w-12 h-12 bg-white dark:bg-white/5 rounded-xl flex items-center justify-center text-blue-500 shadow-xl border border-slate-100 dark:border-white/5 transition-transform group-hover:scale-110"><i class="fa-solid fa-mountain text-xl"></i></div><div><p class="text-[9px] font-black text-slate-400 uppercase mb-0.5 tracking-widest">Suelo del Terreno</p><p class="text-base font-black text-slate-800 dark:text-slate-200 italic uppercase">{{ $selectedCropForReport->terreno->calidad_suelo }}</p></div></div>
                                <div class="flex items-center gap-4 group"><div class="w-12 h-12 bg-white dark:bg-white/5 rounded-xl flex items-center justify-center text-emerald-500 shadow-xl border border-slate-100 dark:border-white/5 transition-transform group-hover:scale-110"><i class="fa-solid fa-droplet text-xl"></i></div><div><p class="text-[9px] font-black text-slate-400 uppercase mb-0.5 tracking-widest">Sistema de Riego</p><p class="text-base font-black text-slate-800 dark:text-slate-200 italic uppercase">{{ $selectedCropForReport->terreno->fuente_agua }}</p></div></div>
                            </div>
                        </div>
                        <div class="pt-6 border-t border-slate-200 dark:border-white/10">
                            <h4 class="text-[11px] font-black text-blue-500 uppercase tracking-[0.4em] mb-4 text-center italic">Distribución Visual</h4>
                            <div class="h-60 relative bg-white dark:bg-white/5 rounded-xl p-4 shadow-2xl border border-slate-100 dark:border-white/5" wire:ignore>
                                <canvas id="cultivoReportChartCanvas" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Columna 2: Distribución de Gastos -->
                    <div class="space-y-6">
                        <h4 class="text-[11px] font-black text-blue-600 uppercase tracking-[0.4em] mb-4 border-b border-slate-200 dark:border-white/10 pb-2 italic text-center">Distribución de Gastos</h4>
                        <div class="bg-white dark:bg-slate-800/40 p-6 rounded-xl border border-slate-100 dark:border-white/5 shadow-xl space-y-6 flex flex-col items-center">
                            <!-- Inversión Total -->
                            <div class="text-center w-full pb-4 border-b border-slate-100 dark:border-white/5">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] italic block mb-1">Inversión Final:</span>
                                <span class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tighter leading-none">
                                    S/ {{ number_format($reportData['costoInsumos'] + $reportData['costoManoObra'] + $reportData['costoMaquinaria'], 2) }}
                                </span>
                            </div>

                            <div class="w-full space-y-4">
                                <div class="flex flex-col items-center group">
                                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] mb-0.5 group-hover:scale-110 transition-transform">Insumos:</span>
                                    <span class="text-xl font-black text-blue-600 italic tracking-tight">S/ {{ number_format($reportData['costoInsumos'], 2) }}</span>
                                </div>
                                <div class="flex flex-col items-center group">
                                    <span class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-0.5 group-hover:scale-110 transition-transform">Mano de Obra:</span>
                                    <span class="text-xl font-black text-amber-600 italic tracking-tight">S/ {{ number_format($reportData['costoManoObra'], 2) }}</span>
                                </div>
                                <div class="flex flex-col items-center group">
                                    <span class="text-[10px] font-black text-violet-500 uppercase tracking-[0.2em] mb-0.5 group-hover:scale-110 transition-transform">Maquinaria:</span>
                                    <span class="text-xl font-black text-violet-600 italic tracking-tight">S/ {{ number_format($reportData['costoMaquinaria'], 2) }}</span>
                                </div>
                                @if(isset($reportData['costoFlete']) && $reportData['costoFlete'] > 0)
                                <div class="flex flex-col items-center group animate-in slide-in-from-top-2">
                                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-0.5 group-hover:scale-110 transition-transform">Fletes (Venta):</span>
                                    <span class="text-xl font-black text-emerald-600 italic tracking-tight">S/ {{ number_format($reportData['costoFlete'], 2) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Columna 3: Balance -->
                    <div class="space-y-6">
                        <h4 class="text-[11px] font-black text-violet-600 uppercase tracking-[0.4em] mb-4 border-b border-slate-200 dark:border-white/10 pb-2 italic text-right">Balance Económico</h4>
                        <div class="space-y-6">
                            <!-- Resumen de Producción -->
                            <div class="p-6 bg-white dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5 space-y-6 shadow-xl flex flex-col">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] text-center italic border-b border-slate-100 dark:border-white/5 pb-2">Resumen de Producción</p>

                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-slate-500 uppercase italic tracking-wider mb-0.5">Total Cosechado:</span>
                                    <span class="text-xl font-black text-agri-green italic tracking-tighter">{{ number_format($reportData['cantidadCosechada'], 2) }} {{ strtoupper($reportData['unidadCosecha']) }}</span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-slate-500 uppercase italic tracking-wider mb-0.5">Precio Promedio:</span>
                                    <span class="text-xl font-black text-blue-500 italic tracking-tighter">S/ {{ $reportData['cantidadCosechada'] > 0 ? number_format($reportData['ingresosTotales'] / $reportData['cantidadCosechada'], 2) : '0.00' }}</span>
                                </div>

                                <div class="flex flex-col pt-4 border-t border-dashed border-slate-200 dark:border-white/10">
                                    <span class="text-[9px] font-black text-slate-400 uppercase italic tracking-wider mb-0.5">Rendimiento Real:</span>
                                    <span class="text-xl font-black text-amber-500 italic tracking-tighter">
                                        @php
                                            $cant = $reportData['cantidadCosechada'];
                                            $area = $selectedCropForReport->area_destinada;
                                            $esKg = strtolower($reportData['unidadCosecha']) === 'kg';

                                            if($area > 0) {
                                                $rendimiento = $cant / $area;
                                                if($esKg && $rendimiento >= 1000) {
                                                    echo number_format($rendimiento / 1000, 1) . ' TN/HA';
                                                } else {
                                                    echo number_format($rendimiento, 2) . ' ' . strtoupper($reportData['unidadCosecha']) . '/HA';
                                                }
                                            } else {
                                                echo '0.00';
                                            }
                                        @endphp
                                    </span>
                                </div>
                            </div>

                            <!-- Ganancia Final -->
                            <div class="mt-2 flex justify-center">
                                <div class="px-4 py-2 bg-slate-900 dark:bg-black rounded-xl border border-white/10 shadow-xl relative overflow-hidden group">
                                    <div class="relative z-10 flex flex-col items-center">
                                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 italic">Ganancia Final</p>
                                        <h3 class="text-xl  font-black {{ $reportData['balance'] >= 0 ? 'text-agri-green' : 'text-rose-500' }} italic tracking-tighter leading-none drop-shadow-lg">
                                            {{ $reportData['balance'] < 0 ? '-' : '' }} S/ {{ number_format(abs($reportData['balance']), 2) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($selectedCropForReport->observaciones)
                <div class="bg-white dark:bg-white/5 p-6 rounded-xl border-l-4 border-agri-green shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5"><i class="fa-solid fa-quote-right text-4xl"></i></div>
                    <p class="text-[9px] font-black text-agri-green uppercase mb-2 tracking-[0.4em] leading-none italic">Notas Técnicas de Campaña</p>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400 italic leading-relaxed">"{{ $selectedCropForReport->observaciones }}"</p>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="bg-slate-100 dark:bg-black/40 px-8 py-6 flex flex-col md:flex-row justify-between items-center border-t border-slate-200 dark:border-white/5 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-agri-green rounded-lg flex items-center justify-center text-white shadow-lg shadow-agri-green/20 text-xs"><i class="fa-solid fa-shield-halved"></i></div>
                    <p class="text-[9px] font-black text-slate-400 uppercase italic tracking-widest">Informe certificado generado por AgroSys Cloud Enterprise</p>
                </div>
                <button @click="$dispatch('close')" class="px-4 py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-black text-[11px] uppercase tracking-[0.2em] transition-all italic shadow-2xl hover:scale-105 active:scale-95">Cerrar Informe</button>
            </div>
        </div>
        @endif
    </x-modal>

    <!-- MODAL DE REGISTRO -->
    <x-modal name="modal-crop-manager" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10" x-data="{ showTerrenos: false, showCultivos: false }">
            <div class="bg-[#003a38] px-6 py-4 flex justify-between items-center text-white">
                <h3 class="text-lg font-black italic tracking-tighter uppercase">{{ $cropId ? 'Actualizar Ficha Técnica' : 'Nueva Campaña AgroSys' }}</h3>
                <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form wire:submit.prevent="save" class="p-6 space-y-5 max-h-[85vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <x-input-label :value="__('1. Seleccionar Terreno *')" class="text-[9px] font-black uppercase text-agri-green tracking-widest" />
                        @if($terreno_id && $selectedTerrenoModel)
                            <div class="flex items-center justify-between bg-emerald-50 dark:bg-white/5 border-2 border-emerald-500/30 rounded-xl p-3.5 animate-in zoom-in-95">
                                <div class="flex items-center space-x-3.5 overflow-hidden">
                                    <img src="{{ $selectedTerrenoModel->foto_path ? Storage::url($selectedTerrenoModel->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($selectedTerrenoModel->nombre).'&background=003a38&color=fff' }}" class="w-10 h-10 rounded-lg object-cover shadow-md">
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $selectedTerrenoModel->nombre }}</p>
                                        <p class="text-[8px] font-bold text-emerald-600 uppercase tracking-tighter">Disp: {{ number_format($areaDisponible, 2) }} ha</p>
                                    </div>
                                </div>
                                @if(!$filterTerrenoId)<button type="button" wire:click="$set('terreno_id', null)" class="text-slate-400 hover:text-rose-500 transition-colors"><i class="fa-solid fa-rotate-right text-xs"></i></button>@else<div class="text-agri-green opacity-40"><i class="fa-solid fa-lock text-[10px]"></i></div>@endif
                            </div>
                        @else
                            <div class="relative">
                                <input type="text" wire:model.live="queryTerreno" @focus="showTerrenos = true" @click.away="showTerrenos = false" class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner focus:ring-1 focus:ring-agri-green/30" placeholder="Escribe el terreno...">
                                <div x-show="showTerrenos" class="absolute w-full mt-1.5 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 z-50 overflow-hidden max-h-48 overflow-y-auto custom-scrollbar" x-cloak>
                                    @foreach($resultsTerrenos as $t)
                                        <div wire:click="selectTerreno({{ $t->id }}, '{{ $t->nombre }}', {{ $t->disponible }})" @click="showTerrenos = false" class="flex items-center space-x-3 p-3 hover:bg-emerald-50 dark:hover:bg-white/5 cursor-pointer border-b last:border-0 border-slate-50 dark:border-white/5">
                                            <img src="{{ $t->foto_path ? Storage::url($t->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($t->nombre) }}" class="w-9 h-9 rounded-lg object-cover shadow-sm">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[11px] font-black uppercase truncate text-slate-800 dark:text-white">{{ $t->nombre }}</p>
                                                <p class="text-[8px] font-bold text-emerald-600 uppercase">Disp: {{ number_format($t->disponible, 2) }} ha</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="space-y-1.5">
                        <x-input-label :value="__('2. Tipo de Cultivo *')" class="text-[9px] font-black uppercase text-agri-green tracking-widest" />
                        @if($catalogo_cultivo_id && $selectedCultivoModel)
                            <div class="flex items-center justify-between bg-blue-50 dark:bg-white/5 border-2 border-blue-500/30 rounded-xl p-3.5 animate-in zoom-in-95">
                                <div class="flex items-center space-x-3.5 overflow-hidden">
                                    <img src="{{ $selectedCultivoModel->foto_path ? Storage::url($selectedCultivoModel->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($selectedCultivoModel->nombre).'&background=00ba2e&color=fff' }}" class="w-10 h-10 rounded-lg object-cover shadow-md">
                                    <p class="text-[10px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $selectedCultivoModel->nombre }}</p>
                                </div>
                                <button type="button" wire:click="$set('catalogo_cultivo_id', null)" class="text-slate-400 hover:text-rose-500 transition-colors"><i class="fa-solid fa-rotate-right text-xs"></i></button>
                            </div>
                        @else
                            <div class="relative">
                                <input type="text" wire:model.live="queryCultivo" @focus="showCultivos = true" @click.away="showCultivos = false" class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner focus:ring-1 focus:ring-agri-green/30" placeholder="Busca en el catálogo...">
                                <div x-show="showCultivos" class="absolute w-full mt-1.5 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 z-50 overflow-hidden max-h-48 overflow-y-auto custom-scrollbar" x-cloak>
                                    @foreach($resultsCatalogo as $cat)
                                        <div wire:click="selectCultivo({{ $cat->id }}, '{{ $cat->nombre }}')" @click="showCultivos = false" class="flex items-center space-x-3 p-3 hover:bg-blue-50 dark:hover:bg-white/5 cursor-pointer border-b last:border-0 border-slate-100 dark:border-white/5">
                                            <img src="{{ $cat->foto_path ? Storage::url($cat->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($cat->nombre).'&background=00ba2e&color=fff' }}" class="w-8 h-8 rounded-lg object-cover shadow-sm">
                                            <p class="text-[11px] font-black uppercase text-slate-800 dark:text-white italic">{{ $cat->nombre }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-50 dark:border-white/5">
                    <div class="space-y-1">
                        <x-input-label :value="__('Variedad del Producto')" class="text-[9px] font-black uppercase text-slate-400" />
                        <x-text-input wire:model.live="variedad" type="text" class="block w-full uppercase p-3 text-xs" placeholder="Ej: Morada, Canchan..." />
                    </div>
                    <div class="space-y-1">
                        <x-input-label :value="__('Nombre de Lote (Auto)')" class="text-[9px] font-black uppercase text-slate-400" />
                        <x-text-input wire:model="nombre_lote" type="text" readonly class="block w-full bg-slate-50 dark:bg-white/5 font-black text-agri-green italic p-3 text-xs" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="space-y-1">
                        <x-input-label :value="__('Área a Sembrar (ha) *')" class="text-[9px] font-black text-slate-400" />
                        <x-text-input wire:model.live="area_destinada" type="number" step="0.01" class="block w-full p-3 text-xs {{ !$terreno_id ? 'bg-slate-100 opacity-50' : '' }}" :disabled="!$terreno_id" />
                    </div>

                    @if($selectedCultivoModel && $selectedCultivoModel->tipo_ciclo === 'perenne')
                        <div class="space-y-1 animate-in slide-in-from-left-4">
                            <x-input-label :value="__('Plantas Estimadas')" class="text-[9px] font-black text-slate-400" />
                            <x-text-input wire:model="plantas_estimadas" type="number" class="block w-full p-3 text-xs" placeholder="Cant. aprox." />
                        </div>
                        <div class="space-y-1 animate-in slide-in-from-right-4">
                            <div class="flex items-center justify-between mb-1">
                                <x-input-label :value="__('Rendimiento Esp. (tn/ha)')" class="text-[9px] font-black text-slate-400" />
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:model.live="showRendimientoPerenne" class="sr-only peer">
                                    <div class="w-7 h-4 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all dark:border-gray-600 peer-checked:bg-agri-green relative transition-colors"></div>
                                </label>
                            </div>
                            @if($showRendimientoPerenne)
                                <x-text-input wire:model="rendimiento_esperado_tn_ha" type="number" step="0.1" class="block w-full p-3 text-xs animate-in zoom-in-95" placeholder="0.0" />
                            @else
                                <div class="p-3 bg-slate-50 dark:bg-white/5 rounded-xl border border-dashed border-slate-200 dark:border-white/10 text-center">
                                    <span class="text-[8px] font-black text-slate-400 uppercase italic">Opcional en Perennes</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($selectedCultivoModel && $selectedCultivoModel->tipo_ciclo === 'ciclo_corto')
                        <div class="space-y-1 animate-in slide-in-from-right-4">
                            <x-input-label :value="__('Rendimiento Esp. (tn/ha)')" class="text-[9px] font-black text-slate-400" />
                            <x-text-input wire:model="rendimiento_esperado_tn_ha" type="number" step="0.1" class="block w-full p-3 text-xs" placeholder="0.0" />
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1">
                        <x-input-label :value="__('Fecha Planificada')" class="text-[9px] font-black text-slate-400" />
                        <x-text-input wire:model.live="fecha_planificada" type="date" class="block w-full p-3 text-xs" />
                        <x-input-error :messages="$errors->get('fecha_planificada')" class="mt-2" />
                    </div>
                    <div class="space-y-1">
                        <x-input-label :value="__('Cosecha Estimada')" class="text-[9px] font-black text-slate-400" />
                        <x-text-input wire:model="fecha_cosecha_estimada" type="date" class="block w-full p-3 text-xs" />
                        <x-input-error :messages="$errors->get('fecha_cosecha_estimada')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @if($cropId)
                    <div class="space-y-1">
                        <x-input-label :value="__('Estado Fenológico')" class="text-[9px] font-black text-slate-400" />
                        <select wire:model="estado" class="block w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl text-xs font-bold p-3.5 focus:ring-1 focus:ring-agri-green/30 shadow-inner appearance-none">
                            <option value="Planificado">Planificado</option>
                            <option value="En crecimiento">En crecimiento</option>
                            <option value="Cosechado">Cosechado</option>
                            <option value="Perdido">Perdido</option>
                        </select>
                    </div>
                    @endif
                    <div class="space-y-1 {{ !$cropId ? 'md:col-span-2' : '' }}">
                        <x-input-label :value="__('Observaciones Generales')" class="text-[9px] font-black text-slate-400" />
                        <textarea wire:model="observaciones" class="block w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl text-xs font-bold p-3.5 min-h-[90px] focus:ring-1 focus:ring-agri-green/30 shadow-inner" placeholder="Notas sobre el cultivo..."></textarea>
                    </div>
                </div>

                <div class="space-y-3.5 pt-2">
                    <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] italic border-b border-slate-100 dark:border-white/5 pb-2">Evidencia Fotográfica</h4>
                    <div class="flex items-center space-x-5 bg-slate-50 dark:bg-white/5 p-5 rounded-2xl border border-dashed border-slate-200 dark:border-white/10 shadow-inner">
                        <div class="w-24 h-18 rounded-lg overflow-hidden bg-white dark:bg-slate-800 shrink-0 shadow-lg border-2 border-white">
                            @if($cropPhoto && method_exists($cropPhoto, 'isPreviewable') && $cropPhoto->isPreviewable())
                                <img src="{{ $cropPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif($currentPhotoPath)
                                <img src="{{ Storage::url($currentPhotoPath) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fa-solid fa-camera text-2xl"></i></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" wire:model="cropPhoto" class="text-[9px] file:mr-4 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-[9px] file:font-black file:uppercase file:bg-agri-green file:text-white hover:file:bg-emerald-600 transition-all cursor-pointer"/>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-5 pt-8 border-t border-slate-100 dark:border-white/5">
                    <button type="button" @click="$dispatch('close')" class="px-8 py-3 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Cancelar</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-14 py-3 bg-agri-green text-white rounded-xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2.5">
                        <i class="fa-solid fa-cloud-arrow-up" wire:loading.remove></i>
                        <i class="fa-solid fa-spinner fa-spin" wire:loading></i>
                        {{ $cropId ? 'Guardar Cambios' : 'Confirmar Siembra' }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>

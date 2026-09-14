<div class="space-y-8 pb-20">
    <!-- Header -->
    <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-100 dark:border-white/5 p-4 md:p-6">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-agri-green/5 rounded-full blur-3xl"></div>

        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-4">
                <div>
                    <span class="px-3 py-1 bg-blue-500/10 text-blue-500 text-[8px] font-black uppercase tracking-widest rounded border border-blue-500/20 italic">Comercialización</span>
                    <h1 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white italic tracking-tighter mt-1.5 leading-tight uppercase">Mis<br><span class="text-agri-green">Ventas</span></h1>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-medium italic max-w-xs leading-relaxed">
                    Registro y seguimiento de transacciones comerciales de su producción.
                </p>
            </div>

            <button wire:click="openCreateModal" class="px-10 py-3 bg-agri-green text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all italic">
                <i class="fa-solid fa-plus mr-2"></i> Nueva Venta
            </button>
        </div>
    </div>

    @if(!$hasCosechas)
        <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-900 rounded-[3rem] border border-dashed border-slate-300 dark:border-white/10 shadow-inner">
            <div class="w-32 h-32 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-8 relative">
                <i class="fa-solid fa-cart-shopping text-6xl text-slate-200 dark:text-white/10"></i>
                <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center shadow-lg animate-bounce">
                    <i class="fa-solid fa-triangle-exclamation text-white"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic tracking-tighter mb-4 text-center">Sin Stock Disponible</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium max-w-md text-center italic leading-relaxed px-6">
                No hay productos cosechados disponibles para la venta. Por favor, <span class="text-agri-green font-black">registre una labor de cosecha</span> y finalice su recolección para poder procesar transacciones comerciales.
            </p>
            <a href="{{ route('admin.cosechas') }}" class="mt-10 px-12 py-4 bg-agri-green text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-2xl shadow-agri-green/30 hover:scale-110 transition-all italic flex items-center gap-3">
                <i class="fa-solid fa-basket-shopping"></i> Ir a Mis Cosechas
            </a>
        </div>
    @else
        <!-- Filtros -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-white/5 shadow-md">
        <div class="w-full md:max-w-xs">
            <div class="relative group">
                <input type="text" wire:model.live="search" placeholder="Buscar por cliente o producto..."
                       class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg py-2.5 flex pl-10 text-[11px] font-bold shadow-inner focus:ring-1 focus:ring-agri-green/30 transition-all uppercase">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>
        </div>
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Resultados: {{ $ventas->total() }}</p>
    </div>

    <!-- Grid de Ventas Consolidadas por Cultivo -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($ventas as $v)
            @php
                $cultivo = $v->cosecha->labor->cultivo;
                $gananciaConsolidada = $v->total_venta_bruta_cultivo - $v->total_inversion_cultivo - $v->total_fletes_cultivo;
            @endphp
            <div class="group relative bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-slate-100 dark:border-white/5 flex flex-col">
                <!-- Imagen del Cultivo con HUD Consolidado -->
                <div class="relative h-56 w-full overflow-hidden bg-slate-100 dark:bg-slate-800 border-b border-slate-50 dark:border-white/5">
                    @if($cultivo->foto_path)
                        <img src="{{ Storage::url($cultivo->foto_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 grayscale-[20%] group-hover:grayscale-0">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-200 dark:text-slate-700 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900">
                            <i class="fa-solid fa-leaf text-5xl opacity-20"></i>
                        </div>
                    @endif

                    <!-- Capa de Sombra Dinámica -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-slate-900/10 opacity-90 transition-opacity"></div>

                    <!-- BARRA SUPERIOR HUD: Primera Siembra, Primera Cosecha y Primera Venta -->
                    <div class="absolute top-0 left-0 w-full p-4 flex justify-between items-start z-10">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2 bg-black/40 backdrop-blur-xl px-2.5 py-1 rounded-lg border border-white/10 shadow-lg">
                                <i class="fa-solid fa-seedling text-[11px] text-agri-green"></i>
                                <span class="text-white text-[11px] font-black uppercase tracking-widest">SIEM. {{ $v->primera_siembra_date?->format('d/m/y') ?: '---' }}</span>
                            </div>
                            <div class="flex items-center gap-2 bg-black/40 backdrop-blur-xl px-2.5 py-1 rounded-lg border border-white/10 shadow-lg">
                                <i class="fa-solid fa-wheat-awn text-[11px] text-yellow-500"></i>
                                <span class="text-white text-[11px] font-black uppercase tracking-widest">COSE. {{ $v->primera_cosecha_date ? \Carbon\Carbon::parse($v->primera_cosecha_date)->format('d/m/y') : '---' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-blue-600/90 backdrop-blur-lg px-3 py-1.5 rounded-xl border border-white/20 shadow-xl ring-4 ring-blue-600/10">
                            <i class="fa-solid fa-cart-shopping text-[11px] text-white"></i>
                            <span class="text-white text-[11px] font-black uppercase italic tracking-tighter">VENTA: {{ $v->primera_venta_date ? \Carbon\Carbon::parse($v->primera_venta_date)->format('d/m/y') : '---' }}</span>
                        </div>
                    </div>

                    <!-- BARRA INFERIOR HUD: Identificación y Conteo de Ventas -->
                    <div class="absolute bottom-0 left-0 w-full p-4 flex justify-between items-end z-10">
                        <div class="space-y-1 px-3 py-2 bg-black/40 backdrop-blur-xl rounded-xl border border-white/10 shadow-xl inline-block max-w-[60%]">
                            <h4 class="text-white font-black text-base uppercase leading-none truncate">{{ $cultivo->detalleCatalogo->nombre }}</h4>
                            <span class="text-agri-green font-black text-[12px] uppercase italic tracking-widest block">{{ $cultivo->variedad ?: 'Generica' }}</span>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                             <!-- Badge de Cantidad de Ventas Reales -->
                            <div class="bg-amber-500 text-white px-3 py-1.5 rounded-xl font-black text-[11px] uppercase italic shadow-lg border border-white/20 flex items-center gap-2">
                                <i class="fa-solid fa-receipt"></i>
                                <span>{{ $v->conteo_ventas_reales }} {{ $v->conteo_ventas_reales == 1 ? 'VENTA' : 'VENTAS' }}</span>
                            </div>

                            <button wire:click="showVentaReport({{ $v->id }})" class="p-2.5 bg-agri-green hover:bg-emerald-600 backdrop-blur-2xl border border-white/10 rounded-lg flex items-center justify-center gap-2 group/btn transition-all shadow-lg shadow-agri-green/20">
                                 <span class="text-[10px] font-black text-white uppercase tracking-widest italic">DETALLES</span>
                                 <i class="fa-solid fa-chart-line text-[12px] text-white group-hover/btn:scale-110 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer: Métricas Consolidadas de la Campaña -->
                <div class="p-4 bg-slate-50 dark:bg-white/[0.02] space-y-4 border-t border-slate-100 dark:border-white/5 flex flex-col">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="flex flex-col items-center justify-center bg-white dark:bg-slate-800 rounded-xl py-2 px-1 border border-slate-200 dark:border-white/5 shadow-sm">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">INVERSIÓN</span>
                            <span class="text-[12px] font-black text-slate-700 dark:text-white italic tracking-tighter">S/{{ number_format($v->total_inversion_cultivo + $v->total_fletes_cultivo, 0) }}</span>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-emerald-50 dark:bg-agri-green/10 rounded-xl py-2 px-1 border border-emerald-100 dark:border-agri-green/20">
                            <span class="text-[10px] font-black text-agri-green uppercase tracking-widest leading-none mb-1">VENTA BRUTA</span>
                            <span class="text-[12px] font-black text-slate-800 dark:text-white italic tracking-tighter">S/{{ number_format($v->total_venta_bruta_cultivo, 0) }}</span>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-blue-50 dark:bg-blue-500/10 rounded-xl py-2 px-1 border border-blue-100 dark:border-blue-500/20">
                            <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest leading-none mb-1">GANANCIA</span>
                            <span class="text-[12px] font-black text-slate-800 dark:text-white italic tracking-tighter">S/{{ number_format($gananciaConsolidada, 0) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3.5 pt-2 border-t border-slate-100 dark:border-white/5">
                        <div class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 shrink-0 border border-blue-500/20 shadow-inner">
                            <i class="fa-solid fa-location-dot text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1 flex justify-between items-center">
                            <div>
                                <p class="text-[12px] font-black text-slate-800 dark:text-white uppercase truncate tracking-tight">{{ $cultivo->terreno->nombre }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mt-1">LUGAR DE PRODUCCIÓN</p>
                            </div>
                            <div class="bg-amber-500 text-white px-4 py-2 rounded-xl shadow-2xl border border-white/20 italic animate-in zoom-in-95 flex items-center gap-2">
                                <i class="fa-solid fa-barcode text-xs"></i>
                                <p class="text-[11px] font-black uppercase leading-none tracking-widest">LOTE: {{ $cultivo->nombre_lote }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-24 text-center space-y-5 bg-slate-50 dark:bg-white/[0.02] rounded-3xl border border-dashed border-slate-200 dark:border-white/10">
                <div class="w-20 h-20 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-slate-200 shadow-inner">
                    <i class="fa-solid fa-hand-holding-dollar text-4xl"></i>
                </div>
                <div class="space-y-1">
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Sin transacciones registradas</p>
                    <p class="text-slate-300 text-[8px] font-bold uppercase italic">Comience registrando una nueva venta desde el botón superior</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $ventas->links() }}
    </div>
    @endif

    <!-- MODAL DE REGISTRO DE VENTA -->
    <x-modal name="modal-venta-manager" :show="false" focusable>
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <div class="bg-[#003a38] px-8 py-4 flex justify-between items-center text-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-agri-green rounded-lg flex items-center justify-center shadow-lg"><i class="fa-solid fa-hand-holding-dollar text-white"></i></div>
                    <h3 class="text-lg font-black italic tracking-tighter uppercase">Registrar Nueva Venta</h3>
                </div>
                <button @click="$dispatch('close')" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white/10"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form wire:submit.prevent="save" class="p-8 space-y-6 max-h-[85vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Selección de Cultivo -->
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-agri-green tracking-widest italic">1. Seleccionar Cultivo (Campaña) *</label>
                        @if($cultivoSeleccionadoId)
                            <div class="flex items-center justify-between bg-emerald-50 dark:bg-white/5 border-2 border-emerald-500/30 rounded-xl p-2 animate-in zoom-in-95 relative overflow-hidden group">
                                <div class="flex items-center gap-3 overflow-hidden relative z-10">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-agri-green shrink-0 shadow-sm border border-white/20">
                                        @if($cultivoInfo['foto'])
                                            <img src="{{ Storage::url($cultivoInfo['foto']) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-white/50"><i class="fa-solid fa-leaf text-xs"></i></div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <p class="text-[12px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $cultivoSeleccionadoLabel }}</p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black text-agri-green uppercase tracking-tighter">{{ $cultivoInfo['area'] }}</span>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">• COSECHA: {{ $cultivoInfo['fecha_cosecha'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" wire:click="clearCultivoSelection" class="relative z-10 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all"><i class="fa-solid fa-rotate-right text-xs"></i></button>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: false }" wire:key="container-query-cultivo">
                                <input type="text" wire:model.live.debounce.300ms="queryCultivo"
                                       @focus="open = true"
                                       @click.away="open = false"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner focus:ring-1 focus:ring-agri-green/30" placeholder="Escribe el nombre del cultivo...">
                                <div x-show="open" class="absolute w-full mt-1 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 z-50 overflow-hidden max-h-48 overflow-y-auto custom-scrollbar" x-cloak>
                                    @forelse($resultsCultivos as $c)
                                        <div wire:key="sel-crop-{{ $c->id }}" wire:click="selectCultivo({{ $c->id }}, '{{ $c->display_name }}')" @click="open = false" class="p-3 hover:bg-emerald-50 dark:hover:bg-white/5 cursor-pointer border-b last:border-0 border-slate-50 dark:border-white/5 transition-colors flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 shrink-0 overflow-hidden border border-slate-200 dark:border-white/5">
                                                @if($c->foto_path)
                                                    <img src="{{ Storage::url($c->foto_path) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fa-solid fa-leaf text-xs"></i></div>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[12px] font-black uppercase text-slate-800 dark:text-white truncate">{{ $c->display_name }}</p>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-[10px] font-bold text-agri-green uppercase">{{ $c->area_destinada }} HA</p>
                                                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-tighter">• COSECHA: {{ $c->display_harvest_date }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 text-center">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase italic">No hay cultivos cosechados disponibles</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Selección de Cosecha Específica -->
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-agri-green tracking-widest italic">2. Calidad de Cosecha / Lote *</label>
                        @if($cosecha_id)
                            <div class="flex items-center justify-between bg-blue-50 dark:bg-white/5 border-2 border-blue-500/30 rounded-xl p-3.5 animate-in zoom-in-95">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="w-8 h-8 rounded bg-blue-500 flex items-center justify-center text-white"><i class="fa-solid fa-wheat-awn text-xs"></i></div>
                                    <p class="text-[10px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $cosechaSeleccionadaLabel }}</p>
                                </div>
                                <button type="button" wire:click="$set('cosecha_id', null)" class="text-slate-400 hover:text-rose-500 transition-colors"><i class="fa-solid fa-rotate-right text-xs"></i></button>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: false }" wire:key="container-query-cosecha">
                                <input type="text" wire:model.live.debounce.300ms="queryCosecha"
                                       @focus="open = true"
                                       @click.away="open = false"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner focus:ring-1 focus:ring-blue-500/30 {{ !$cultivoSeleccionadoId ? 'opacity-50 cursor-not-allowed' : '' }}"
                                       placeholder="{{ $cultivoSeleccionadoId ? 'Selecciona la calidad...' : 'Selecciona un cultivo primero...' }}"
                                       {{ !$cultivoSeleccionadoId ? 'readonly' : '' }}>

                                @if($cultivoSeleccionadoId)
                                <div x-show="open" class="absolute w-full mt-1 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 z-50 overflow-hidden max-h-48 overflow-y-auto custom-scrollbar" x-cloak>
                                    @forelse($resultsCosechas as $cos)
                                        <div wire:key="sel-cos-{{ $cos->id }}" wire:click="selectCosecha({{ $cos->id }}, '{{ $cos->display_label }}', {{ $cos->available_stock }}, '{{ $cos->unidad_medida }}')" @click="open = false" class="p-3 hover:bg-blue-50 dark:hover:bg-white/5 cursor-pointer border-b last:border-0 border-slate-50 dark:border-white/5 transition-colors">
                                            <p class="text-[10px] font-black uppercase text-slate-800 dark:text-white">{{ $cos->display_label }}</p>
                                            <p class="text-[8px] font-bold text-blue-500 uppercase">Disponible: {{ number_format($cos->available_stock, 2) }} {{ strtoupper($cos->unidad_medida) }}</p>
                                        </div>
                                    @empty
                                        <div class="p-4 text-center">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase italic">Sin stock en este cultivo</p>
                                        </div>
                                    @endforelse
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-50 dark:border-white/5">
                    <!-- Selección de Comprador -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <label class="text-[9px] font-black uppercase text-agri-green tracking-widest italic">3. Comprador / Cliente *</label>
                            <button type="button" wire:click="openAddComprador" class="text-[12px] font-black text-blue-500 uppercase hover:underline italic flex items-center gap-1">
                                <i class="fa-solid fa-plus-circle"></i> Nuevo Cliente
                            </button>
                        </div>
                        @if($comprador_id)
                            <div class="flex items-center justify-between bg-blue-50 dark:bg-white/5 border-2 border-blue-500/30 rounded-xl p-3.5 animate-in zoom-in-95">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="w-8 h-8 rounded bg-blue-500 flex items-center justify-center text-white"><i class="fa-solid fa-user-tie text-xs"></i></div>
                                    <p class="text-[10px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $compradorSeleccionadoNombre }}</p>
                                </div>
                                <button type="button" wire:click="$set('comprador_id', null)" class="text-slate-400 hover:text-rose-500 transition-colors"><i class="fa-solid fa-rotate-right text-xs"></i></button>
                            </div>
                        @else
                            <div class="relative" x-data="{ open: false }" wire:key="container-query-comprador">
                                <input type="text" wire:model.live.debounce.300ms="queryComprador"
                                       @focus="open = true"
                                       @click.away="open = false"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner focus:ring-1 focus:ring-agri-green/30" placeholder="Escribe el nombre del cliente...">
                                <div x-show="open" class="absolute w-full mt-1 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 z-50 overflow-hidden max-h-48 overflow-y-auto custom-scrollbar" x-cloak>
                                    @foreach($resultsCompradores as $comp)
                                        <div wire:key="sel-comp-{{ $comp->id }}" wire:click="selectComprador({{ $comp->id }}, '{{ $comp->nombre }}')" @click="open = false" class="p-3 hover:bg-blue-50 dark:hover:bg-white/5 cursor-pointer border-b last:border-0 border-slate-50 dark:border-white/5 transition-colors">
                                            <p class="text-[10px] font-black uppercase text-slate-800 dark:text-white">{{ $comp->nombre }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Fecha de Venta *</label>
                        <input wire:model="fecha_venta" type="date" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Tipo Comprobante *</label>
                        <select wire:model.live="comprobante_tipo" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3 shadow-inner uppercase appearance-none">
                            <option value="boleta">BOLETA DE VENTA</option>
                            <option value="factura">FACTURA COMERCIAL</option>
                            <option value="ticket">TICKET / RECIBO</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Número de Serie / Folio (Automático)</label>
                        <input wire:model="comprobante_numero" type="text" readonly class="w-full bg-slate-100 dark:bg-slate-900/50 border-none rounded-xl text-xs font-black p-3.5 shadow-inner uppercase text-agri-green cursor-not-allowed" placeholder="GENERANDO...">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-4 border-t border-slate-50 dark:border-white/5">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Unidad de Venta *</label>
                        <select wire:model.live="unidad_venta" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3 shadow-inner uppercase appearance-none">
                            <option value="kg">KILOGRAMOS (KG)</option>
                            <option value="tn">TONELADAS (TN)</option>
                            <option value="sacos">SACOS (U)</option>
                            <option value="und">UNIDADES (UND)</option>
                            <option value="jabas">JABAS</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Cantidad ({{ strtoupper($unidad_venta) }}) *</label>
                        <input wire:model.live="cantidad_vendida_kg" type="number" step="0.01" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3 shadow-inner">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Precio x {{ strtoupper($unidad_venta) }} (S/) *</label>
                        <input wire:model.live="precio_por_kg" type="number" step="0.01" class="w-full bg-white dark:bg-slate-800 border-2 border-agri-green/20 rounded-xl text-xs font-black p-3 shadow-sm text-agri-green">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Fecha de Venta *</label>
                        <input wire:model="fecha_venta" type="date" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3 shadow-inner uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5 md:col-span-2 lg:col-span-1">
                         <label class="text-[9px] font-black text-slate-400 uppercase">Gastos Adicionales (Flete/Imp)</label>
                         <div class="flex gap-2">
                            <input wire:model.live="costo_flete" type="number" step="0.01" class="w-1/2 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3 shadow-inner" placeholder="Flete">
                            <input wire:model.live="impuestos" type="number" step="0.01" class="w-1/2 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3 shadow-inner" placeholder="Impuestos">
                         </div>
                    </div>
                </div>

                <div class="space-y-4 pt-2">
                    <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic border-b border-slate-100 dark:border-white/5 pb-2">Evidencia Fotográfica de la Transacción</h4>
                    <div class="flex items-center space-x-6 bg-slate-50 dark:bg-white/5 p-4 rounded-xl border border-dashed border-slate-200 dark:border-white/10">
                        <div class="w-24 h-20 rounded-lg overflow-hidden bg-white dark:bg-slate-800 shrink-0 shadow-sm border border-slate-100">
                            @if($ventaPhoto && method_exists($ventaPhoto, 'isPreviewable') && $ventaPhoto->isPreviewable())
                                <img src="{{ $ventaPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif($currentPhotoPath)
                                <img src="{{ Storage::url($currentPhotoPath) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fa-solid fa-camera text-2xl"></i></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" wire:model="ventaPhoto" class="text-[9px] file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-[9px] file:font-black file:uppercase file:bg-agri-green file:text-white hover:file:bg-emerald-600 transition-all cursor-pointer"/>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-xl p-6 border border-white/10 shadow-2xl flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-center md:text-left">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Bruto</p>
                        <p class="text-3xl font-black text-white italic tracking-tighter">S/ {{ number_format(($cantidad_vendida_kg ?: 0) * ($precio_por_kg ?: 0), 2) }}</p>
                    </div>
                    <div class="text-center md:text-right border-t md:border-t-0 md:border-l border-white/10 pt-4 md:pt-0 md:pl-8">
                        <p class="text-[10px] font-black text-agri-green uppercase tracking-widest leading-none mb-1">Neto Estimado</p>
                        <p class="text-3xl font-black text-agri-green italic tracking-tighter">S/ {{ number_format((($cantidad_vendida_kg ?: 0) * ($precio_por_kg ?: 0)) - ($costo_flete ?: 0) - ($impuestos ?: 0), 2) }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 dark:border-white/5">
                    <button type="button" @click="$dispatch('close')" class="px-8 py-3 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all italic">Cancelar</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-14 py-4 bg-agri-green text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-agri-green/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-3">
                        <i class="fa-solid fa-cloud-arrow-up" wire:loading.remove></i>
                        <i class="fa-solid fa-spinner fa-spin" wire:loading></i>
                        {{ $ventaId ? 'Actualizar Transacción' : 'Registrar Venta' }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- MODAL REGISTRO RÁPIDO COMPRADOR -->
    <x-modal name="modal-add-comprador" :show="false">
        <div class="bg-white dark:bg-agri-d_bg rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <div class="bg-[#003a38] px-6 py-4 flex justify-between items-center text-white border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center border border-white/10 shadow-inner">
                        <i class="fa-solid fa-user-plus text-base"></i>
                    </div>
                    <h3 class="text-lg font-black tracking-tighter uppercase leading-none italic">Nuevo Cliente</h3>
                </div>
                <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 transition-all border border-white/10"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form wire:submit.prevent="saveQuickComprador" class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2 space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest italic ml-1">Nombre Completo / Razón Social *</label>
                        <input type="text" wire:model="newCompNombre" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner uppercase focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white" placeholder="EJ: JUAN PEREZ O AGROEXPORT SAC">
                        @error('newCompNombre') <span class="text-[8px] text-rose-500 font-bold uppercase ml-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest italic ml-1">RUC / DNI</label>
                        <input type="text" wire:model="newCompRucDni" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white" placeholder="10123456789">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest italic ml-1">Teléfono</label>
                        <input type="text" wire:model="newCompTelf" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white" placeholder="987 654 321">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest italic ml-1">Correo Electrónico</label>
                        <input type="email" wire:model="newCompEmail" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white" placeholder="cliente@correo.com">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest italic ml-1">Dirección Fiscal/Entrega</label>
                        <input type="text" wire:model="newCompDir" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white" placeholder="AV. LAS CULTIVAS 123">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-blue-600 text-white rounded-xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center justify-center gap-3 active:scale-95 italic">
                        <i class="fa-solid fa-save text-base"></i>
                        <span>REGISTRAR Y SELECCIONAR</span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- MODAL DE CONFIRMACIÓN DE SEGURIDAD (PASSWORD) -->
    <x-modal name="modal-confirm-secure-action" :show="false" focusable>
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <div class="bg-rose-600 px-6 py-4 flex justify-between items-center text-white">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center"><i class="fa-solid fa-shield-halved"></i></div>
                    <h3 class="text-sm font-black italic tracking-tighter uppercase">Protocolo de Seguridad</h3>
                </div>
                <button @click="$dispatch('close')" class="text-white/60 hover:text-white transition-all"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="p-8 space-y-6">
                <div class="bg-rose-50 dark:bg-rose-500/5 border-l-4 border-rose-500 p-4 rounded-r-xl">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 mt-1"></i>
                        <div class="space-y-1">
                            <p class="text-[11px] font-black text-rose-800 dark:text-rose-400 uppercase tracking-widest">Advertencia de Seguridad</p>
                            <p class="text-[10px] font-bold text-rose-600/70 dark:text-rose-400/50 leading-relaxed italic">
                                Está intentando {{ $actionToPerform === 'edit' ? 'MODIFICAR' : 'ELIMINAR' }} una transacción comercial finalizada. Esta acción afecta los inventarios de cosecha y los estados financieros históricos.
                            </p>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="verifyPasswordAndPerform" class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Confirmar Contraseña de Administrador</label>
                        <div class="relative">
                            <input type="password" wire:model="adminPassword"
                                   class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-white/5 rounded-xl p-3.5 text-xs font-black shadow-inner focus:ring-rose-500 focus:border-rose-500 transition-all uppercase tracking-widest"
                                   placeholder="••••••••">
                            <i class="fa-solid fa-key absolute right-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        </div>
                        @error('adminPassword') <p class="text-[8px] font-black text-rose-500 uppercase tracking-widest mt-1 ml-1 animate-pulse">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4 pt-2">
                        <button type="button" @click="$dispatch('close')" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Cancelar</button>
                        <button type="submit" class="flex-[2] px-4 py-3 bg-rose-600 text-white rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-rose-600/20 hover:scale-105 active:scale-95 transition-all">
                            Autorizar Acción
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-modal>

    <!-- MODAL DE INFORME DETALLADO DE VENTA -->
    <x-modal name="modal-venta-report" :show="false" maxWidth="2xl">
        @if($selectedVentaReport)
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <!-- Header Informe -->
            <div class="bg-[#003a38] px-8 py-6 flex justify-between items-start text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-agri-green/20 rounded-full blur-3xl"></div>
                <div class="relative z-10 space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 bg-agri-green text-white text-[8px] font-black uppercase rounded tracking-[0.2em]">Auditoría Comercial</span>
                        <div class="bg-amber-500 text-white px-3 py-1 rounded-lg shadow-xl border border-white/10 italic flex items-center gap-1.5 animate-in slide-in-from-left-2 duration-500">
                            <i class="fa-solid fa-barcode text-[10px]"></i>
                            <span class="text-[11px] font-black uppercase">LOTE: {{ $selectedVentaReport->cosecha->labor->cultivo->nombre_lote }}</span>
                        </div>
                        <span class="text-[10px] font-bold text-white/50 uppercase tracking-widest">• REF: {{ $selectedVentaReport->comprobante_numero }}</span>
                    </div>
                    <h3 class="text-2xl font-black italic tracking-tighter uppercase leading-tight">Informe de<br><span class="text-agri-green">Transacción Comercial</span></h3>
                </div>
                <button @click="$dispatch('close')" class="relative z-10 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition-all border border-white/10"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="p-8 space-y-6">
                <!-- Grid de Métricas Principales (Organizado: 2 Arriba, 3 Abajo) -->
                <div class="space-y-4">
                    <!-- Fila 1: KPIs Principales (Totales Campaña) -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-emerald-50/50 dark:bg-agri-green/5 p-5 rounded-2xl border border-emerald-100 dark:border-white/5 space-y-1 transform hover:scale-[1.02] transition-all">
                            <p class="text-[9px] font-black text-agri-green uppercase tracking-[0.2em] leading-none mb-1">Ganancia Total</p>
                            <p class="text-2xl font-black {{ $reportVentaData['ganancia_total_real_campana'] >= 0 ? 'text-agri-green' : 'text-rose-500' }} italic tracking-tighter">S/ {{ number_format($reportVentaData['ganancia_total_real_campana'], 2) }}</p>
                            <p class="text-[8px] font-bold text-slate-400 italic leading-none">Balance Neto Campaña</p>
                        </div>

                        <div class="bg-blue-50/50 dark:bg-blue-500/5 p-5 rounded-2xl border border-blue-100 dark:border-white/5 space-y-1 transform hover:scale-[1.02] transition-all">
                            <p class="text-[9px] font-black text-blue-500 uppercase tracking-[0.2em] leading-none mb-1">Venta Bruta</p>
                            <p class="text-2xl font-black text-blue-600 dark:text-blue-400 italic tracking-tighter">S/ {{ number_format($reportVentaData['monto_bruto_campana'], 2) }}</p>
                            <p class="text-[8px] font-bold text-slate-400 italic leading-none">Total Ingresos Campaña</p>
                        </div>
                    </div>

                    <!-- Fila 2: Desglose Técnico (Totales Campaña) -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-amber-50/50 dark:bg-amber-500/5 p-4 rounded-2xl border border-amber-100 dark:border-white/5 space-y-1 transform hover:scale-[1.02] transition-all">
                            <p class="text-[8px] font-black text-amber-500 uppercase tracking-widest leading-none mb-1">Flete</p>
                            <p class="text-lg font-black text-amber-600 dark:text-amber-400 italic tracking-tighter">S/ {{ number_format($reportVentaData['flete_total_campana'], 2) }}</p>
                            <p class="text-[7px] font-bold text-slate-400 italic leading-none">Gasto Logístico Total</p>
                        </div>

                        <div class="bg-slate-50 dark:bg-white/5 p-4 rounded-2xl border border-slate-100 dark:border-white/5 space-y-1 transform hover:scale-[1.02] transition-all">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Inversión Global</p>
                            <p class="text-lg font-black text-slate-800 dark:text-white italic tracking-tighter">S/ {{ number_format($reportVentaData['inversion_global'], 2) }}</p>
                            <p class="text-[7px] font-bold text-slate-400 italic leading-none">Costo Producción</p>
                        </div>

                        <div class="bg-violet-50/50 dark:bg-violet-500/5 p-4 rounded-2xl border border-violet-100 dark:border-white/5 space-y-1 transform hover:scale-[1.02] transition-all">
                            <p class="text-[8px] font-black text-violet-500 uppercase tracking-widest leading-none">Ganancia Neta Est.</p>
                            <p class="text-lg font-black text-violet-600 dark:text-violet-400 italic tracking-tighter">S/ {{ number_format($reportVentaData['ganancia_neta_estable_campana'], 2) }}</p>
                            <p class="text-[7px] font-bold text-slate-400 italic leading-none">Utilidad (Venta - Flete)</p>
                        </div>
                    </div>
                </div>

                @php
                    $numGrupos = count($reportVentaData['ventas_agrupadas']);
                @endphp

                <div class="grid grid-cols-1 {{ $numGrupos > 1 ? 'md:grid-cols-1' : 'md:grid-cols-2' }} gap-8">
                    <!-- Historial de Ventas del Cultivo (Agrupado) -->
                    <div class="space-y-6">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] border-b border-slate-100 dark:border-white/5 pb-2 italic">Desglose de Ventas por Cliente</h4>

                        <div class="grid grid-cols-1 {{ $numGrupos > 1 ? 'md:grid-cols-2 xl:grid-cols-3' : 'md:grid-cols-1' }} gap-6 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($reportVentaData['ventas_agrupadas'] as $grupo)
                                <div class="relative rounded-3xl overflow-hidden border border-slate-100 dark:border-white/10 shadow-2xl group/block">
                                    <!-- Fondo de Imagen de Venta (Claridad absoluta) -->
                                    <div class="absolute inset-0 z-0">
                                        @if($grupo['foto'])
                                            <img src="{{ Storage::url($grupo['foto']) }}" class="w-full h-full object-cover group-hover/block:scale-110 transition-transform duration-1000">
                                            <!-- Gradiente protector para asegurar lectura de textos blancos -->
                                            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-black/80"></div>
                                        @else
                                            <div class="w-full h-full bg-slate-900"></div>
                                        @endif
                                    </div>

                                    <div class="relative z-10 p-5 space-y-5">
                                        <!-- Header: Comprador -->
                                        <div class="flex justify-between items-center bg-black/30 backdrop-blur-md p-3 rounded-2xl border border-white/10 shadow-xl">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-agri-green text-white flex items-center justify-center shadow-lg"><i class="fa-solid fa-user-tie text-sm"></i></div>
                                                <div>
                                                    <p class="text-[11px] font-black text-white uppercase leading-none">{{ $grupo['comprador'] }}</p>
                                                    <p class="text-[8px] font-bold text-white/60 uppercase tracking-widest mt-1">{{ $grupo['fecha'] }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[12px] font-black text-agri-green drop-shadow-md">S/ {{ number_format($grupo['monto_total'], 2) }}</p>
                                                <p class="text-[7px] font-black text-white/40 uppercase tracking-[0.2em] mt-0.5 leading-none">TOTAL OP.</p>
                                            </div>
                                        </div>

                                        <!-- Listado de Lotes en Cuadros Premium -->
                                        <div class="space-y-3">
                                            @foreach($grupo['detalles'] as $det)
                                                <div class="bg-black/40 backdrop-blur-xl p-4 rounded-2xl border border-white/10 shadow-2xl space-y-3 relative overflow-hidden group/item">
                                                    <!-- Efecto de luz al pasar el mouse -->
                                                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-transparent opacity-0 group-hover/item:opacity-100 transition-opacity"></div>

                                                    <div class="relative z-10">
                                                        <!-- 1. CALIDAD -->
                                                        <span class="text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic mb-2 block">{{ $det['calidad'] }}</span>

                                                        <!-- 2. DATOS PRINCIPALES -->
                                                        <div class="flex justify-between items-end border-b border-white/5 pb-2 mb-2">
                                                            <div class="flex flex-col">
                                                                <p class="text-[16px] font-black text-white leading-none tracking-tight">
                                                                    {{ number_format($det['cantidad'], 0) }} <span class="text-[10px] text-white/50 uppercase ml-0.5">{{ strtoupper($det['unidad']) }}</span>
                                                                </p>
                                                                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase italic">Precio Pactado</p>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-[16px] font-black text-agri-green leading-none italic drop-shadow-sm">S/ {{ number_format($det['precio'], 2) }}</p>
                                                                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase italic">X Unidad</p>
                                                            </div>
                                                        </div>

                                                        <!-- 3. DESGLOSE OPERACIÓN EN CUADROS -->
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <div class="bg-blue-600/20 px-3 py-1.5 rounded-xl border border-blue-500/20 flex flex-col items-center">
                                                                <span class="text-[7px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Venta Bruta</span>
                                                                <span class="text-[11px] font-black text-white italic">S/ {{ number_format($det['cantidad'] * $det['precio'], 2) }}</span>
                                                            </div>
                                                            @if($det['flete'] > 0)
                                                                <div class="bg-amber-600/20 px-3 py-1.5 rounded-xl border border-amber-500/20 flex flex-col items-center">
                                                                    <span class="text-[7px] font-black text-amber-400 uppercase tracking-widest leading-none mb-1">Flete Aplicado</span>
                                                                    <span class="text-[11px] font-black text-white italic">S/ {{ number_format($det['flete'], 2) }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="p-4 bg-slate-900 rounded-2xl border border-white/5 space-y-3">
                            <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                                <span class="text-slate-500 italic">Desempeño de Campaña</span>
                                <span class="text-agri-green">Ventas Totales: S/ {{ number_format($reportVentaData['monto_bruto_campana'], 2) }}</span>
                            </div>
                            <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                                @php
                                    $progress = ($reportVentaData['monto_bruto_campana'] / max(1, $reportVentaData['inversion_global'])) * 100;
                                @endphp
                                <div class="bg-agri-green h-full rounded-full transition-all duration-1000" style="width: {{ min(100, $progress) }}%"></div>
                            </div>
                            <p class="text-[8px] font-bold text-slate-400 italic">Retorno de inversión actual: {{ number_format($progress, 1) }}%</p>
                        </div>
                    </div>

                    @if($numGrupos <= 1)
                        <!-- Evidencia Fotográfica (Solo se muestra si hay un solo bloque) -->
                        <div class="space-y-4 animate-in fade-in duration-500">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] border-b border-slate-100 dark:border-white/5 pb-2 italic">Evidencia de Transacción</h4>
                            <div class="aspect-square w-full rounded-2xl overflow-hidden bg-slate-100 dark:bg-white/5 border-2 border-slate-200 dark:border-white/10 shadow-inner group relative">
                                @if($selectedVentaReport->foto_path)
                                    <img src="{{ Storage::url($selectedVentaReport->foto_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <a href="{{ Storage::url($selectedVentaReport->foto_path) }}" target="_blank" class="px-6 py-2.5 bg-white text-slate-900 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl">Ver Original</a>
                                    </div>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 space-y-3">
                                        <i class="fa-solid fa-camera-retro text-5xl opacity-20"></i>
                                        <p class="text-[9px] font-black uppercase tracking-widest italic opacity-40">Sin evidencia digital</p>
                                    </div>
                                @endif
                            </div>
                            <div class="flex justify-between items-center px-2">
                                <div class="flex flex-col">
                                    <p class="text-[8px] font-black text-slate-400 uppercase leading-none">Fecha de Auditoría</p>
                                    <p class="text-[10px] font-black text-slate-600 dark:text-slate-300 italic">{{ $selectedVentaReport->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <button onclick="window.print()" class="text-[9px] font-black text-blue-500 uppercase hover:underline italic flex items-center gap-1.5">
                                    <i class="fa-solid fa-print"></i> Imprimir Reporte
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-white/5 px-8 py-5 border-t border-slate-100 dark:border-white/5 flex justify-end">
                <button @click="$dispatch('close')" class="px-10 py-3 bg-slate-800 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-700 transition-all shadow-xl shadow-slate-900/20 italic">Cerrar Informe</button>
            </div>
        </div>
        @endif
    </x-modal>
</div>

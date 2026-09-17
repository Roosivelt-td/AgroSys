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
                <!-- KPIs ... -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-emerald-50/50 dark:bg-agri-green/5 p-5 rounded-2xl border border-emerald-100 dark:border-white/5 space-y-1 transform hover:scale-[1.02] transition-all">
                        <p class="text-[9px] font-black text-agri-green uppercase tracking-[0.2em] leading-none mb-1">Ganancia Total</p>
                        <p class="text-2xl font-black {{ $reportVentaData['ganancia_total_real_campana'] >= 0 ? 'text-agri-green' : 'text-rose-500' }} italic tracking-tighter">S/ {{ number_format($reportVentaData['ganancia_total_real_campana'], 2) }}</p>
                    </div>
                    <div class="bg-blue-50/50 dark:bg-blue-500/5 p-5 rounded-2xl border border-blue-100 dark:border-white/5 space-y-1 transform hover:scale-[1.02] transition-all">
                        <p class="text-[9px] font-black text-blue-500 uppercase tracking-[0.2em] leading-none mb-1">Venta Bruta</p>
                        <p class="text-2xl font-black text-blue-600 dark:text-blue-400 italic tracking-tighter">S/ {{ number_format($reportVentaData['monto_bruto_campana'], 2) }}</p>
                    </div>
                </div>

                @php $numGrupos = count($reportVentaData['ventas_agrupadas']); @endphp

                <div class="grid grid-cols-1 {{ $numGrupos > 1 ? 'md:grid-cols-1' : 'md:grid-cols-2' }} gap-8">
                    <div class="space-y-6">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] border-b border-slate-100 dark:border-white/5 pb-2 italic">Desglose de Ventas por Cliente</h4>

                        <div class="grid grid-cols-1 {{ $numGrupos > 1 ? 'md:grid-cols-2 xl:grid-cols-3' : 'md:grid-cols-1' }} gap-6 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($reportVentaData['ventas_agrupadas'] as $grupo)
                                <div class="relative rounded-3xl overflow-hidden border border-slate-100 dark:border-white/10 shadow-2xl group/block">
                                    <div class="absolute inset-0 z-0">
                                        @if($grupo['foto'])
                                            <img src="{{ Storage::url($grupo['foto']) }}" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-black/80"></div>
                                        @else
                                            <div class="w-full h-full bg-slate-900"></div>
                                        @endif
                                    </div>

                                    <div class="relative z-10 p-5 space-y-5">
                                        <!-- Menú Grupal -->
                                        <div class="absolute top-4 right-4 z-20">
                                            <x-dropdown align="right" width="48">
                                                <x-slot name="trigger">
                                                    <button class="w-8 h-8 flex items-center justify-center bg-black/40 backdrop-blur-xl hover:bg-black/60 rounded-xl transition-all border border-white/20 text-white shadow-2xl">
                                                        <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                                    </button>
                                                </x-slot>
                                                <x-slot name="content">
                                                    <div class="p-1">
                                                        @php $grupoIds = collect($grupo['detalles'])->pluck('id')->toArray(); @endphp
                                                        <button wire:click="requestSecureGroupAction({{ json_encode($grupoIds) }}, 'bulk_edit')" class="w-full flex items-center gap-2 px-3 py-2 text-[10px] font-black uppercase text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 rounded-lg transition-colors">
                                                            <i class="fa-solid fa-layer-group text-agri-green"></i> Editar Grupo
                                                        </button>
                                                        <button wire:click="requestSecureGroupAction({{ json_encode($grupoIds) }}, 'bulk_delete')" class="w-full flex items-center gap-2 px-3 py-2 text-[10px] font-black uppercase text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors">
                                                            <i class="fa-solid fa-trash-can"></i> Eliminar Grupo
                                                        </button>
                                                    </div>
                                                </x-slot>
                                            </x-dropdown>
                                        </div>

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
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            @foreach($grupo['detalles'] as $det)
                                                <div class="bg-black/40 backdrop-blur-xl p-4 rounded-2xl border border-white/10 shadow-2xl space-y-3 relative overflow-hidden group/item">
                                                    <div class="absolute top-3 right-3 z-20">
                                                        <x-dropdown align="right" width="32">
                                                            <x-slot name="trigger">
                                                                <button class="w-7 h-7 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg transition-all border border-white/10 text-white/50 hover:text-white">
                                                                    <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                                                </button>
                                                            </x-slot>
                                                            <x-slot name="content">
                                                                <div class="p-1">
                                                                    <button wire:click="requestSecureAction({{ $det['id'] }}, 'edit')" class="w-full flex items-center gap-2 px-3 py-2 text-[10px] font-black uppercase text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 rounded-lg transition-colors">
                                                                        <i class="fa-solid fa-pen-to-square text-blue-500"></i> Editar
                                                                    </button>
                                                                    <button wire:click="requestSecureAction({{ $det['id'] }}, 'delete')" class="w-full flex items-center gap-2 px-3 py-2 text-[10px] font-black uppercase text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors">
                                                                        <i class="fa-solid fa-trash"></i> Eliminar
                                                                    </button>
                                                                </div>
                                                            </x-slot>
                                                        </x-dropdown>
                                                    </div>

                                                    <div class="relative z-10">
                                                        <span class="text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic mb-2 block">{{ $det['calidad'] }}</span>
                                                        <div class="flex justify-between items-end border-b border-white/5 pb-2 mb-2">
                                                            <p class="text-[16px] font-black text-white leading-none tracking-tight">
                                                                {{ number_format($det['cantidad'], 0) }} <span class="text-[10px] text-white/50 uppercase ml-0.5">{{ strtoupper($det['unidad']) }}</span>
                                                            </p>
                                                            <p class="text-[16px] font-black text-agri-green leading-none italic drop-shadow-sm">S/ {{ number_format($det['precio'], 2) }}</p>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <div class="bg-blue-600/20 px-3 py-1.5 rounded-xl border border-blue-500/20 flex flex-col items-center">
                                                                <span class="text-[7px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Venta Bruta</span>
                                                                <span class="text-[11px] font-black text-white italic">S/ {{ number_format($det['cantidad'] * $det['precio'], 2) }}</span>
                                                            </div>
                                                            @if($det['flete'] > 0)
                                                                <div class="bg-amber-600/20 px-3 py-1.5 rounded-xl border border-amber-500/20 flex flex-col items-center">
                                                                    <span class="text-[7px] font-black text-amber-400 uppercase tracking-widest leading-none mb-1">Flete</span>
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
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-white/5 px-8 py-5 border-t border-slate-100 dark:border-white/5 flex justify-end">
                <button @click=\"$dispatch('close')\" class=\"px-10 py-3 bg-slate-800 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-700 transition-all italic\">Cerrar Informe</button>
            </div>
        </div>
        @endif
    </x-modal>

    <!-- MODALES DE ACCIÓN (AL FINAL PARA Z-INDEX) -->

    <!-- 1. MODAL REGISTRO RÁPIDO COMPRADOR -->
    <x-modal name="modal-add-comprador" :show="false">
        <div class="bg-white dark:bg-agri-d_bg rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <div class="bg-[#003a38] px-6 py-4 flex justify-between items-center text-white">
                <h3 class="text-lg font-black tracking-tighter uppercase italic">Nuevo Cliente</h3>
                <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form wire:submit.prevent="saveQuickComprador" class="p-6 space-y-5">
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400">Nombre Completo *</label>
                    <input type="text" wire:model="newCompNombre" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black uppercase">
                </div>
                <button type="submit" class="w-full py-3.5 bg-blue-600 text-white rounded-xl font-black text-[11px] uppercase tracking-[0.2em] italic">REGISTRAR Y SELECCIONAR</button>
            </form>
        </div>
    </x-modal>

    <!-- 2. MODAL DE EDICIÓN INDIVIDUAL -->
    <x-modal name="modal-venta-manager" :show="false" focusable>
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <div class="bg-[#003a38] px-8 py-4 flex justify-between items-center text-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-agri-green rounded-lg flex items-center justify-center"><i class="fa-solid fa-hand-holding-dollar text-white"></i></div>
                    <h3 class="text-lg font-black italic tracking-tighter uppercase">{{ $ventaId ? 'Editar Venta' : 'Registrar Venta' }}</h3>
                </div>
                <button @click="$dispatch('close')" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white/10"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form wire:submit.prevent="save" class="p-8 space-y-6 max-h-[85vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Cultivo -->
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-agri-green italic tracking-widest">1. Cultivo / Campaña</label>
                        <div class="bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 rounded-xl p-3 text-xs font-black uppercase text-slate-500 italic">
                             {{ $cultivoSeleccionadoLabel ?: 'Seleccione un cultivo' }}
                        </div>
                    </div>
                    <!-- Lote / Calidad -->
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-agri-green italic tracking-widest">2. Lote Cosechado</label>
                        <div class="bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 rounded-xl p-3 text-xs font-black uppercase text-slate-500 italic">
                             {{ $cosechaSeleccionadaLabel ?: 'Seleccione un lote' }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-50 dark:border-white/5">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400">Comprador / Cliente *</label>
                        <select wire:model="comprador_id" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner uppercase">
                            <option value="">SELECCIONAR COMPRADOR</option>
                            @foreach(\App\Models\Comprador::orderBy('nombre')->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400">Fecha de Venta *</label>
                        <input wire:model="fecha_venta" type="date" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3.5 shadow-inner uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 pt-4 border-t border-slate-50 dark:border-white/5">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Unidad *</label>
                        <select wire:model.live="unidad_venta" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3 shadow-inner uppercase">
                            <option value="kg">KG</option><option value="tn">TN</option><option value="sacos">SACOS</option><option value="und">UND</option><option value="jabas">JABAS</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Cantidad *</label>
                        <input wire:model.live="cantidad_vendida_kg" type="number" step="0.01" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3 shadow-inner">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Precio Unitario (S/)</label>
                        <input wire:model.live="precio_por_kg" type="number" step="0.01" class="w-full bg-white dark:bg-slate-800 border-2 border-agri-green/20 rounded-xl text-xs font-black p-3 text-agri-green shadow-sm">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Flete (S/)</label>
                        <input wire:model.live="costo_flete" type="number" step="0.01" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold p-3 shadow-inner">
                    </div>
                </div>

                <div class="bg-slate-900 rounded-xl p-6 border border-white/10 shadow-2xl flex justify-between items-center">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Bruto</p>
                        <p class="text-3xl font-black text-white italic tracking-tighter">S/ {{ number_format(($cantidad_vendida_kg ?: 0) * ($precio_por_kg ?: 0), 2) }}</p>
                    </div>
                    <button type="submit" class="px-12 py-4 bg-agri-green text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-agri-green/20 hover:scale-105 transition-all italic flex items-center gap-3">
                        <i class="fa-solid fa-cloud-arrow-up"></i> {{ $ventaId ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- 3. MODAL DE EDICIÓN EN BLOQUE -->
    <x-modal name="modal-bulk-edit-ventas" :show="false" maxWidth="2xl" focusable>
        <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <div class="bg-[#003a38] px-8 py-4 flex justify-between items-center text-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-agri-green rounded-lg flex items-center justify-center shadow-lg"><i class="fa-solid fa-layer-group text-white"></i></div>
                    <h3 class="text-lg font-black italic tracking-tighter uppercase">Editar Grupo de Ventas</h3>
                </div>
                <button @click="$dispatch('close')" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white/10"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form wire:submit.prevent="saveBulk" class="p-8 space-y-6 max-h-[85vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 dark:bg-white/5 p-5 rounded-2xl border border-slate-100 dark:border-white/5">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic ml-1">Comprador *</label>
                        <select wire:model="bulkCompradorId" class="w-full bg-white dark:bg-slate-800 border-none rounded-xl text-xs font-black p-3.5 shadow-sm uppercase text-agri-green">
                            @foreach(\App\Models\Comprador::orderBy('nombre')->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic ml-1">Fecha de Venta *</label>
                        <input wire:model="bulkFechaVenta" type="date" class="w-full bg-white dark:bg-slate-800 border-none rounded-xl text-xs font-black p-3.5 shadow-sm uppercase italic">
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b border-slate-100 dark:border-white/5 pb-2">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] italic">Desglose de Grupo</h4>

                        <!-- Botón para Agregar Nueva Calidad al Grupo -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" type="button" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg font-black text-[9px] uppercase tracking-widest flex items-center gap-2 hover:bg-blue-700 transition-all">
                                <i class="fa-solid fa-plus"></i> Agregar Calidad
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-100 dark:border-white/10 z-50 overflow-hidden" x-cloak>
                                <div class="p-2 space-y-1">
                                    @forelse($bulkAvailableCosechas as $ac)
                                        <button type="button" wire:click="addBulkItem({{ $ac['id'] }})" @click="open = false" class="w-full text-left p-2 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-colors">
                                            <p class="text-[9px] font-black uppercase text-slate-700 dark:text-white leading-tight">{{ $ac['label'] }}</p>
                                        </button>
                                    @empty
                                        <p class="p-2 text-[8px] font-bold text-slate-400 uppercase text-center italic">No hay más calidades disponibles</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($bulkItems)
                        @foreach($bulkItems as $index => $item)
                            <div class="bg-white dark:bg-slate-800/50 p-5 rounded-2xl border border-slate-100 dark:border-white/5 space-y-4 relative">
                                <!-- Botón para quitar item (Solo si es nuevo/id null) -->
                                @if($item['id'] === null)
                                    <button type="button" wire:click="removeBulkItem({{ $index }})" class="absolute top-2 right-2 w-6 h-6 flex items-center justify-center bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-all shadow-lg"><i class="fa-solid fa-xmark text-[10px]"></i></button>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[8px] font-black text-slate-400 uppercase">Calidad</label>
                                        <div class="bg-slate-100 dark:bg-slate-900 px-3 py-2 rounded-xl text-[10px] font-black text-blue-500 uppercase italic">
                                            {{ $item['calidad'] }}
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[8px] font-black text-slate-400 uppercase">Tipo Unidad</label>
                                        <select wire:model.live="bulkItems.{{ $index }}.unidad" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl text-[10px] font-black p-2 shadow-inner uppercase">
                                            <option value="kg">KG</option><option value="tn">TN</option><option value="sacos">SACOS</option><option value="und">UND</option><option value="jabas">JABAS</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[8px] font-black text-slate-400 uppercase">Flete Item (S/)</label>
                                        <input type="number" step="0.01" wire:model.live="bulkItems.{{ $index }}.flete" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl text-[10px] font-black p-2 shadow-inner text-amber-600">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[8px] font-black text-slate-400 uppercase">Cantidad</label>
                                        <input type="number" step="0.01" wire:model.live="bulkItems.{{ $index }}.cantidad" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl text-xs font-black p-3 shadow-inner text-blue-600">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[8px] font-black text-slate-400 uppercase">Precio Unitario (S/)</label>
                                        <input type="number" step="0.01" wire:model.live="bulkItems.{{ $index }}.precio" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl text-xs font-black p-3 shadow-inner text-agri-green">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="bg-slate-900 rounded-2xl p-6 border border-white/10 flex justify-between items-center shadow-2xl">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Grupo</p>
                        <p class="text-3xl font-black text-white italic tracking-tighter">S/ {{ number_format(collect($bulkItems)->sum(fn($i) => $i['cantidad'] * $i['precio']), 2) }}</p>
                    </div>
                    <button type="submit" class="px-12 py-4 bg-agri-green text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-agri-green/20 hover:scale-105 transition-all flex items-center gap-3 italic">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Actualizar Todo
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- 4. MODAL DE SEGURIDAD (PASSWORD) - SIEMPRE AL FINAL -->
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
                                @if($actionToPerform === 'update' || $actionToPerform === 'bulk_update')
                                    Está intentando GUARDAR CAMBIOS en transacciones finalizadas.
                                    @if(isset($reportVentaData['resumen_edicion']))
                                        <br><span class="text-agri-green font-black uppercase mt-2 block">Resumen de Cambios:</span>
                                        <span class="text-blue-500 font-bold block text-[9px] mt-1">{{ $reportVentaData['resumen_edicion']['anterior'] }}</span>
                                        <span class="text-agri-green font-black block text-[11px]">➡ {{ $reportVentaData['resumen_edicion']['nuevo'] }}</span>
                                    @endif
                                @elseif($actionToPerform === 'delete' || $actionToPerform === 'bulk_delete')
                                    Está intentando ELIMINAR permanentemente esta transacción. Esta acción no se puede deshacer.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="verifyPasswordAndPerform" class="space-y-5">
                    @if(in_array($actionToPerform, ['delete', 'bulk_delete', 'update', 'bulk_update']))
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                Confirmación de Texto (Escriba <span class="text-rose-500 italic">"{{ in_array($actionToPerform, ['delete', 'bulk_delete']) ? 'si eliminar' : 'acepto actualizar' }}"</span>)
                            </label>
                            <input type="text" wire:model="confirmText"
                                   class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-white/5 rounded-xl p-3.5 text-xs font-black shadow-inner focus:ring-rose-500 focus:border-rose-500 transition-all uppercase italic"
                                   placeholder="Escriba aquí...">
                            @error('confirmText') <p class="text-[8px] font-black text-rose-500 uppercase tracking-widest mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Contraseña de Administrador</label>
                        <div class="relative">
                            <input type="password" wire:model="adminPassword"
                                   class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-white/5 rounded-xl p-3.5 text-xs font-black shadow-inner focus:ring-rose-500 focus:border-rose-500 transition-all uppercase tracking-widest"
                                   placeholder="••••••••">
                            <i class="fa-solid fa-key absolute right-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        </div>
                        @error('adminPassword') <p class="text-[8px] font-black text-rose-500 uppercase tracking-widest mt-1 ml-1 animate-pulse">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4 pt-2">
                        <button type="button" @click="$dispatch('close')" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all italic">Cancelar</button>
                        <button type="submit" class="flex-[2] px-4 py-3 bg-rose-600 text-white rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-rose-600/20 hover:scale-105 active:scale-95 transition-all italic">
                            {{ in_array($actionToPerform, ['update', 'bulk_update']) ? 'AUTORIZAR CAMBIOS' : 'AUTORIZAR ELIMINACIÓN' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-modal>
</div>

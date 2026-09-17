<div class="space-y-6 p-2 md:p-1 transition-colors duration-500">
    <!-- ALERTAS DE SISTEMA -->
    @if(session()->has('status') || session()->has('warning') || session()->has('error'))
        <div class="fixed top-24 right-6 z-[100] space-y-3 animate-in slide-in-from-right-10 duration-500">
            @if(session('status'))
                <div class="bg-emerald-500 text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-white/20 backdrop-blur-md">
                    <i class="fa-solid fa-circle-check"></i>
                    <span class="text-[11px] font-black uppercase italic">{{ session('status') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div class="bg-amber-500 text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-white/20 backdrop-blur-md">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span class="text-[11px] font-black uppercase italic">{{ session('warning') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-500 text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-white/20 backdrop-blur-md">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span class="text-[11px] font-black uppercase italic">{{ session('error') }}</span>
                </div>
            @endif
        </div>
    @endif

    <!-- CABECERA Y FILTROS PREMIUM -->
    <div class="space-y-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 bg-[#fdf8f8] p-6 rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-sm">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-[#003a38] rounded-2xl flex items-center justify-center shadow-lg transform rotate-3 hover:rotate-0 transition-transform duration-500">
                    <i class="fa-solid fa-tractor text-2xl text-agri-green"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-[#1e293b] tracking-tighter uppercase italic leading-none">GESTIÓN DE LABORES</h2>
                    <p class="text-[10px] font-black text-agri-green uppercase tracking-[0.3em] mt-1 opacity-80">CONTROL DE PROCESOS OPERATIVOS</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-2.5 space-x-4 shadow-sm hover:shadow-md transition-all duration-500 group">
                    <div class="flex items-center space-x-3">
                        <span class="text-[9px] font-black text-slate-400 group-hover:text-agri-green transition-colors uppercase italic">DESDE:</span>
                        <input type="date" wire:model.live="filterDateStart" class="bg-transparent border-none p-0 text-[11px] font-bold text-slate-600 dark:text-white focus:ring-0 w-28 uppercase">
                    </div>
                    <div class="w-px h-5 bg-slate-200 dark:bg-white/10"></div>
                    <div class="flex items-center space-x-3">
                        <span class="text-[9px] font-black text-slate-400 group-hover:text-agri-green transition-colors uppercase italic">HASTA:</span>
                        <input type="date" wire:model.live="filterDateEnd" class="bg-transparent border-none p-0 text-[11px] font-bold text-slate-600 dark:text-white focus:ring-0 w-28 uppercase">
                    </div>
                </div>

                <button wire:click="openCreateModal"
                        class="px-10 py-4 bg-agri-green hover:bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/20 transition-all flex items-center gap-3 active:scale-95 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                    <i class="fa-solid fa-plus text-sm group-hover:rotate-90 transition-transform"></i> NUEVO REGISTRO
                </button>
            </div>
        </div>

        <!-- Banner de Filtro de Cultivo (Heredado) -->
        @if($filterCropId && $fExactCrop)
            <div class="flex items-center justify-between bg-blue-500/10 border border-blue-500/20 px-6 py-3 rounded-2xl animate-in fade-in slide-in-from-top-4 duration-500">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-500 shadow-lg border border-white/20">
                        <i class="fa-solid fa-leaf text-lg"></i>
                    </div>
                    <div class="flex flex-col gap-1">
                        <p class="text-[8px] font-black text-blue-500 uppercase tracking-[0.2em] leading-none mb-1">Filtrando por Cultivo Específico</p>
                        <div class="flex items-center gap-3">
                            <h4 class="text-sm font-black text-slate-700 dark:text-white uppercase italic tracking-tighter">{{ $fExactCrop }}</h4>
                            <span class="px-4 py-1.5 bg-amber-500 text-white text-[11px] font-black uppercase rounded-xl shadow-2xl border border-white/10 italic flex items-center gap-2">
                                <i class="fa-solid fa-barcode text-xs"></i> LOTE: {{ $filterCropId ? \App\Models\Cultivo::find($filterCropId)?->nombre_lote : '---' }}
                            </span>
                        </div>
                    </div>
                </div>
                <button wire:click="$set('filterCropId', null)" class="px-4 py-2 bg-white dark:bg-slate-800 text-rose-500 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-rose-50 transition-all shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark"></i> Quitar Filtro
                </button>
            </div>
        @endif

        <!-- Fila de Selectores Técnicos -->
        <div class="flex flex-wrap items-center gap-2 p-1.5 bg-white/40 dark:bg-slate-900/40 rounded-2xl border border-slate-100 dark:border-white/5 shadow-lg backdrop-blur-md">

            <div class="relative min-w-[150px] flex-1">
                <i class="fa-solid fa-circle-info absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                <select wire:model.live="fStatus" class="w-full pl-8 pr-6 py-2 bg-white dark:bg-slate-800 border-none rounded-xl text-[9px] font-black uppercase focus:ring-1 focus:ring-agri-green shadow-inner appearance-none cursor-pointer italic">
                    <option value="">TODOS LOS ESTADOS</option>
                    <option value="Completada">COMPLETADA</option>
                    <option value="Pendiente">PENDIENTE</option>
                    <option value="En progreso">EN PROGRESO</option>
                </select>
            </div>

            <div class="relative min-w-[130px] flex-1">
                <i class="fa-solid fa-location-dot absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                <input list="lands-list" type="text" wire:model.live.debounce.300ms="fLand" placeholder="TERRENO..." class="w-full pl-8 pr-2 py-2 bg-white dark:bg-slate-800 border-none rounded-xl text-[9px] font-black uppercase focus:ring-1 focus:ring-agri-green shadow-inner italic">
                <datalist id="lands-list">@foreach($terrenosBarra as $t) <option value="{{ $t->nombre }}"> @endforeach</datalist>
            </div>

            <div class="relative min-w-[130px] flex-1">
                <i class="fa-solid fa-seedling absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                <input list="cats-list" type="text" wire:model.live.debounce.300ms="fCat" placeholder="TIPO CULTIVO..." class="w-full pl-8 pr-2 py-2 bg-white dark:bg-slate-800 border-none rounded-xl text-[9px] font-black uppercase focus:ring-1 focus:ring-agri-green shadow-inner italic">
                <datalist id="cats-list">@foreach($catalogosBarra as $c) <option value="{{ $c->nombre }}"> @endforeach</datalist>
            </div>

            <div class="relative min-w-[120px] flex-1">
                <i class="fa-solid fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                <input list="vars-list" type="text" wire:model.live.debounce.300ms="fVariety" placeholder="VARIEDAD..." class="w-full pl-8 pr-2 py-2 bg-white dark:bg-slate-800 border-none rounded-xl text-[9px] font-black uppercase focus:ring-1 focus:ring-agri-green shadow-inner italic">
                <datalist id="vars-list">@foreach($variedadesBarra as $v) <option value="{{ $v }}"> @endforeach</datalist>
            </div>

            <div class="relative min-w-[200px] flex-[2]">
                <i class="fa-solid fa-leaf absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                <input list="exact-crops-list" type="text" wire:model.live.debounce.300ms="fExactCrop" placeholder="CULTIVO EXACTO..." class="w-full pl-8 pr-2 py-2 bg-white dark:bg-slate-800 border-none rounded-xl text-[9px] font-black uppercase focus:ring-1 focus:ring-agri-green shadow-inner italic">
                <datalist id="exact-crops-list">@foreach($cultivosExactosBarra as $ce) <option value="{{ $ce->nombre_lote }}">{{ $ce->label_exacto }}</option> @endforeach</datalist>
            </div>

            <div class="relative min-w-[140px] flex-1">
                <i class="fa-solid fa-gears absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                <select wire:model.live="fLaborId" class="w-full pl-8 pr-6 py-2 bg-white dark:bg-slate-800 border-none rounded-xl text-[9px] font-black uppercase focus:ring-1 focus:ring-agri-green shadow-inner appearance-none cursor-pointer italic">
                    <option value="">TIPO LABOR...</option>
                    @foreach($catalogoLabores as $l) <option value="{{ $l->id }}">{{ strtoupper($l->nombre) }}</option> @endforeach
                </select>
            </div>

            <button wire:click="resetFilters" class="px-5 py-2 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-xl font-black text-[9px] uppercase hover:bg-slate-200 transition-all italic flex items-center gap-2 shadow-sm border border-slate-200 dark:border-white/5">
                <i class="fa-solid fa-rotate-left"></i> RESET
            </button>
        </div>
    </div>

    @if(!$hasCultivos)
        <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-900 rounded-[3rem] border border-dashed border-slate-300 dark:border-white/10 shadow-inner">
            <div class="w-32 h-32 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-8 relative">
                <i class="fa-solid fa-leaf text-6xl text-slate-200 dark:text-white/10"></i>
                <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center shadow-lg animate-bounce">
                    <i class="fa-solid fa-triangle-exclamation text-white"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic tracking-tighter mb-4 text-center">Falta Información de Campaña</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium max-w-md text-center italic leading-relaxed px-6">
                No se encontraron campañas o cultivos registrados. Por favor, <span class="text-agri-green font-black">registre primero un cultivo</span> en la sección 'Mis Cultivos' para poder gestionar sus labores operativas.
            </p>
            <a href="{{ route('admin.cultivos') }}" class="mt-10 px-12 py-4 bg-agri-green text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-2xl shadow-agri-green/30 hover:scale-110 transition-all italic flex items-center gap-3">
                <i class="fa-solid fa-seedling"></i> Ir a Mis Cultivos
            </a>
        </div>
    @else
        <!-- TARJETAS DE ESTADÍSTICAS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $statCards = [
                ['label' => 'Total Labores', 'value' => $stats['total'], 'icon' => 'fa-clipboard-list', 'color' => 'slate'],
                ['label' => 'Costo Total', 'value' => 'S/ '.number_format($stats['costo_total'], 2), 'icon' => 'fa-dollar-sign', 'color' => 'blue'],
                ['label' => 'Labores Pendientes', 'value' => $stats['pendientes'], 'icon' => 'fa-hourglass-half', 'color' => 'amber'],
                ['label' => 'Costo Promedio', 'value' => 'S/ '.number_format($stats['avg_cost'], 2), 'icon' => 'fa-chart-line', 'color' => 'purple']
            ];
        @endphp
        @foreach($statCards as $card)
            <div class="bg-white dark:bg-slate-900 p-6 rounded-[1.8rem] shadow-sm border-l-4 border-{{ $card['color'] }}-500 relative overflow-hidden group">
                <div class="flex justify-between items-center relative z-10">
                    <div><p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1 leading-none">{{ $card['label'] }}</p><h3 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tighter">{{ $card['value'] }}</h3></div>
                    <div class="w-10 h-10 bg-{{ $card['color'] }}-500/10 text-{{ $card['color'] }}-500 rounded-xl flex items-center justify-center shadow-inner"><i class="fa-solid {{ $card['icon'] }} text-lg"></i></div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- LISTADO DE TARJETAS PREMIUM (CRUD + ALTURA COMPACTA) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @php
            $icons = [
                'Preparar' => 'fa-tractor',
                'Siembra' => 'fa-seedling',
                'Riego' => 'fa-droplet',
                'Fumigar' => 'fa-spray-can-sparkles',
                'Aporque' => 'fa-mountain',
                'Desierbe' => 'fa-scissors',
                'Deshierbe' => 'fa-scissors',
                'Abonar' => 'fa-flask-vial',
                'Cosechar' => 'fa-basket-shopping',
                'Otros' => 'fa-ellipsis'
            ];
        @endphp

        @foreach($labores as $l)
        @php
            $isAtrasada = $l->estado === 'Pendiente' && \Carbon\Carbon::parse($l->fecha_realizacion)->isPast();
            // Lógica de fecha solicitada: siembra si existe, si no, fecha de la labor (preparación)
            $fRefRaw = $l->cultivo->fecha_siembra ?: $l->fecha_realizacion;
            $fechaRef = \Carbon\Carbon::parse($fRefRaw)->format('d/m/Y');
            $identificador = strtoupper($l->cultivo->detalleCatalogo->nombre) . " " . strtoupper($l->cultivo->variedad ?: 'GENERICA') . " - " . $fechaRef;
        @endphp
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-xl border border-slate-100 dark:border-white/5 group relative transition-all duration-500 hover:-translate-y-1.5 h-64 cursor-pointer"
             x-data="{ menuOpen: false }"
             @click="if (!$event.target.closest('.no-click')) $wire.showDetails({{ $l->id }})">

            <!-- IMAGEN DE FONDO CON OVERLAY PREMIUM -->
            <div class="absolute inset-0 z-0">
                @if($l->foto_path)
                    <img src="{{ Storage::url($l->foto_path) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-200 bg-slate-50 dark:bg-slate-800/50">
                        <i class="fa-solid {{ $icons[$l->detalleCatalogo->nombre] ?? 'fa-tractor' }} text-8xl opacity-10"></i>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/60 to-black/20 dark:from-slate-950 dark:via-slate-950/80 dark:to-transparent"></div>
            </div>

            <!-- CONTENIDO ESTRUCTURADO (Z-10) -->
            <div class="relative z-10 p-6 h-full flex flex-col justify-between">

                <!-- 1. HEADER: NOMBRE LABOR (Donde iba el clima) + BADGE + CRUD -->
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-agri-green rounded-xl flex items-center justify-center border border-white/20 shadow-lg">
                            <i class="fa-solid {{ $icons[$l->detalleCatalogo->nombre] ?? 'fa-tractor' }} text-white text-lg"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-[17px] font-black italic uppercase tracking-tight text-white leading-none shadow-sm">{{ $l->detalleCatalogo->nombre }}</h3>
                                <span class="px-3 py-1 bg-amber-500 text-white text-[11px] font-black uppercase rounded-xl border border-white/10 italic leading-none shadow-2xl flex items-center gap-1.5">
                                    <i class="fa-solid fa-barcode text-[10px]"></i> {{ $l->cultivo->nombre_lote }}
                                </span>
                            </div>
                            <div class="flex gap-1.5 mt-1.5">
                                <span class="px-2 py-0.5 bg-white/10 backdrop-blur-md rounded text-[8px] font-black uppercase tracking-widest text-emerald-400 border border-white/10">{{ $l->estado }}</span>
                                @if($isAtrasada)
                                    <span class="px-2 py-0.5 bg-rose-500/20 backdrop-blur-md rounded text-[8px] font-black uppercase tracking-widest text-rose-400 border border-rose-500/30 animate-pulse">Atrasada</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="relative no-click">
                        <button @click.stop="menuOpen = !menuOpen" @click.away="menuOpen = false"
                                class="w-8 h-8 bg-white/10 backdrop-blur-md border border-white/20 rounded-lg text-white flex items-center justify-center shadow-lg hover:bg-white/20 transition-all">
                            <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                        </button>
                        <div x-show="menuOpen" x-transition class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 dark:border-white/10 z-50 overflow-hidden" x-cloak>
                            <button @click.stop="$wire.edit({{ $l->id }})" class="w-full px-4 py-3 text-left text-[10px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 flex items-center gap-2 italic">
                                <i class="fa-solid fa-pen-to-square text-agri-green"></i> EDITAR
                            </button>
                            <button @click.stop="$wire.delete({{ $l->id }})" wire:confirm="¿Eliminar registro?" class="w-full px-4 py-3 text-left text-[10px] font-black uppercase text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 flex items-center gap-2 italic">
                                <i class="fa-solid fa-trash-can"></i> BORRAR
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. DATOS TÉCNICOS CENTRALES (Lote + Fecha Ref) -->
                <div class="space-y-2 mb-1">
                    <div class="flex items-center gap-2.5 text-white/90">
                        <i class="fa-solid fa-seedling text-[11px] text-agri-green shadow-sm"></i>
                        <p class="text-[11px] font-bold uppercase italic tracking-wide truncate">
                            <span class="text-white font-black">{{ $identificador }}</span>
                        </p>
                    </div>

                    @if($l->observaciones)
                    <div class="flex items-start gap-2 pt-1 border-t border-white/10 mt-1 opacity-70">
                        <i class="fa-solid fa-note-sticky text-[9px] text-agri-green"></i>
                        <p class="text-[10px] font-bold italic text-white line-clamp-1 truncate uppercase tracking-tighter">{{ $l->observaciones }}</p>
                    </div>
                    @endif

                    <!-- Fila de Datos Estilo Cultivos -->
                    <div class="grid grid-cols-3 gap-2 pt-3 border-t border-white/20">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none">Inversión</span>
                            <span class="text-[12px] font-black italic text-amber-400 mt-0.5">S/ {{ number_format($l->costo_total, 2) }}</span>
                        </div>
                        <div class="flex flex-col border-l border-white/10 pl-3">
                            <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none">Registro</span>
                            <span class="text-[10px] font-bold italic text-white opacity-95 mt-0.5">{{ \Carbon\Carbon::parse($l->fecha_realizacion)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex flex-col border-l border-white/10 pl-3 text-right">
                            <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none">Parcela</span>
                            <span class="text-[10px] font-bold italic opacity-95 text-white truncate mt-0.5 block">{{ $l->cultivo->terreno->nombre }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-8">{{ $labores->links() }}</div>
    @endif

    <!-- MODAL LABOR MANAGER -->
    <x-modal name="modal-labor-manager" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10"
             x-data="{ showStatus: false, showLands: false, showCats: false, showCrops: false }">

            <div class="bg-[#003a38] px-6 py-4 flex justify-between items-center text-white border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center border border-white/10 shadow-inner">
                        <i class="fa-solid fa-list-check text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black tracking-tighter uppercase leading-none italic">
                            @if($step === 1) Seleccionar Labor @else Registro: {{ strtoupper($catalogoLabores->find($catalogo_labor_id)->nombre ?? '') }} @endif
                        </h3>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @if($step === 2)
                        <button type="button" wire:click="$set('step', 1)" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 italic">
                            <i class="fa-solid fa-rotate-left"></i> Cambiar
                        </button>
                    @endif
                    <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 transition-all border border-white/10"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
            </div>

            @if($step === 1)
                <div class="p-6 animate-in zoom-in-95 duration-500 bg-slate-50/50 dark:bg-transparent">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($catalogoLabores as $l)
                            @php
                                $laborName = strtoupper($l->nombre);
                                $isDisabled = !($laborStatusMap[$l->categoria] ?? ($laborStatusMap[$l->nombre] ?? true));
                                if ($laborName === 'OTROS') $isDisabled = false;
                            @endphp
                            <button wire:click="selectLaborType({{ $l->id }})" @if($isDisabled) disabled @endif
                                    class="group flex flex-col items-center justify-center p-6 rounded-2xl border-2 transition-all duration-500
                                    {{ $isDisabled ? 'bg-slate-100/50 dark:bg-white/5 border-transparent opacity-20 cursor-not-allowed scale-95' : 'bg-white dark:bg-slate-800 border-slate-100 dark:border-white/5 hover:border-agri-green hover:shadow-xl hover:-translate-y-1' }}">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3 transition-all {{ $isDisabled ? 'bg-slate-300 text-slate-500' : 'bg-agri-green/10 text-agri-green group-hover:bg-agri-green group-hover:text-white shadow-inner' }}">
                                    <i class="fa-solid {{ $icons[$l->nombre] ?? 'fa-gears' }} text-xl"></i>
                                </div>
                                <span class="text-[11px] font-black uppercase tracking-widest {{ $isDisabled ? 'text-slate-500' : 'text-slate-800 dark:text-slate-100' }}">{{ $l->nombre }}</span>
                                @if($isDisabled)
                                    <p class="text-[7px] font-black text-rose-500 uppercase mt-1 tracking-widest italic leading-none">FASE PREVIA REQUERIDA</p>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            @else
                <form wire:submit.prevent="save" class="max-h-[85vh] overflow-y-auto custom-scrollbar bg-white dark:bg-slate-900">
                    <div class="px-6 pt-6 pb-4 space-y-5">
                        <div class="flex items-center gap-3 border-l-4 border-agri-green pl-2.5">
                            <h4 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-widest italic leading-none">IDENTIFICACIÓN DEL CULTIVO</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                            <div class="md:col-span-3 space-y-1">
                                <label class="text-[9px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-flag text-agri-green"></i> ESTADO</label>
                                <select wire:model.live="selStatus" @if($filterCropId) disabled @endif class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[10px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green shadow-inner appearance-none @if($filterCropId) opacity-60 @endif">
                                    <option value="TODOS">TODOS LOS CULTIVOS</option>
                                    <option value="En proceso">EN CRECIMIENTO</option>
                                    <option value="Completada">COSECHADO</option>
                                    <option value="Perdido">PERDIDO</option>
                                </select>
                            </div>
                            <div class="md:col-span-5 space-y-1">
                                <label class="text-[9px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-location-dot text-agri-green"></i> TERRENO / PARCELA</label>
                                <select wire:model.live="selLandId" @if($selStatus === '' || $filterCropId) disabled @endif class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[10px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green shadow-inner appearance-none @if($filterCropId) opacity-60 @endif">
                                    <option value="">Seleccionar parcela...</option>
                                    @foreach($resultsLands as $r) <option value="{{ $r->id }}">{{ $r->nombre }} - {{ $r->hectareas }} Ha</option> @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-4 space-y-1">
                                <label class="text-[9px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-seedling text-agri-green"></i> TIPO DE CULTIVO</label>
                                <select wire:model.live="selCatId" @if(!$selLandId || $filterCropId) disabled @endif class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[10px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green shadow-inner appearance-none @if($filterCropId) opacity-60 @endif">
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach($resultsCats as $r) <option value="{{ $r->id }}">{{ $r->nombre }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                            <div class="md:col-span-4 space-y-1">
                                <label class="text-[9px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-tags text-agri-green text-[7px]"></i> VARIEDAD</label>
                                <select wire:model.live="selVarName" @if(!$selCatId || $filterCropId) disabled @endif class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[10px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green shadow-inner appearance-none @if($filterCropId) opacity-60 @endif">
                                    <option value="">Seleccionar variedad...</option>
                                    @foreach($resultsVars as $v) <option value="{{ $v }}">{{ strtoupper($v ?: 'GENERICA') }}</option> @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-8 space-y-1">
                                <label class="text-[9px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-leaf text-agri-green text-[7px]"></i> SELECCIONAR CAMPAÑA</label>
                                <select wire:model.live="cultivo_id" @if($selVarName === '' || $filterCropId) disabled @endif class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[10px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green shadow-inner border-2 border-agri-green/20 @if($filterCropId) opacity-60 @endif">
                                    <option value="">Elegir campaña específica...</option>
                                    @foreach($resultsCrops as $r) <option value="{{ $r->id }}">{{ $r->label_display }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-3">
                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-4 border border-slate-100 dark:border-white/5 shadow-inner">
                            <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-white/5 pb-2">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-list-check text-agri-green text-xs"></i>
                                    <h4 class="text-[9px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-[0.2em] italic leading-none">RESUMEN TÉCNICO</h4>
                                </div>
                                @if($cultivo_id)
                                    <div class="flex items-center gap-3">
                                        <span class="bg-agri-green/10 text-agri-green px-2 py-0.5 rounded text-[8px] font-black uppercase border border-agri-green/20">
                                            SEM: {{ $cropFechaPlanificada ? \Carbon\Carbon::parse($cropFechaPlanificada)->format('d/m/Y') : '---' }}
                                        </span>
                                        <span class="bg-blue-500/10 text-blue-500 px-2 py-0.5 rounded text-[8px] font-black uppercase border border-blue-500/20">
                                            ÁREA: {{ $cropHectareas ? number_format($cropHectareas, 2) : '0.00' }} Ha
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 bg-agri-green text-white rounded flex items-center justify-center font-black text-[10px] shadow-md">1</div>
                                    <div class="min-w-0"><p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-0.5">Terreno</p><p class="text-[9px] font-bold text-slate-900 dark:text-white uppercase truncate leading-none italic">{{ $landNombreSelected ?: '---' }}</p></div>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 bg-agri-green text-white rounded flex items-center justify-center font-black text-[10px] shadow-md">2</div>
                                    <div class="min-w-0"><p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-0.5">Cultivo</p><p class="text-[9px] font-bold text-slate-900 dark:text-white uppercase truncate leading-none italic">{{ $catNombreSelected ?: '---' }}</p></div>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 bg-agri-green text-white rounded flex items-center justify-center font-black text-[10px] shadow-md">3</div>
                                    <div class="min-w-0"><p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-0.5">Variedad</p><p class="text-[9px] font-bold text-slate-900 dark:text-white uppercase truncate leading-none italic">{{ $selVarName ?: '---' }}</p></div>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 bg-agri-green text-white rounded flex items-center justify-center font-black text-[10px] shadow-md">4</div>
                                    <div class="min-w-0"><p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-0.5">Labor</p><p class="text-[9px] font-bold text-slate-900 dark:text-white uppercase leading-none italic">{{ $catalogoLabores->find($catalogo_labor_id)->nombre ?? '---' }}</p></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($cultivo_id)
                        <div class="px-6 pb-6 space-y-6 animate-in fade-in duration-500">

                            @if($esCosecha)
                                <div class="space-y-4 animate-in slide-in-from-top-4 duration-500">
                                    <div class="flex justify-between items-center border-l-4 border-emerald-500 pl-2.5">
                                        <h4 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-widest italic leading-none">DETALLES DE COSECHA</h4>
                                        <button type="button" wire:click="addItemCosecha" class="px-4 py-1.5 bg-emerald-600 border-drak-700 text-dark rounded-lg text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg active:scale-95 italic">
                                            <i class="fa-solid fa-plus"></i> AGREGAR LOTE
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        @foreach($itemsCosecha as $idx => $item)
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-emerald-50 dark:bg-emerald-500/5 p-4 rounded-xl border border-emerald-500/20 shadow-inner items-end" wire:key="harvest-{{ $idx }}">
                                                <div class="md:col-span-3 space-y-1.5">
                                                    <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                                        <i class="fa-solid fa-weight-hanging text-emerald-500"></i> Cantidad
                                                    </label>
                                                    <input type="number" step="0.01" wire:model="itemsCosecha.{{ $idx }}.cantidad" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-[11px] font-black shadow-sm">
                                                </div>
                                                <div class="md:col-span-3 space-y-1.5">
                                                    <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                                        <i class="fa-solid fa-ruler-combined text-emerald-500"></i> Unidad
                                                    </label>
                                                    <select wire:model="itemsCosecha.{{ $idx }}.unidad" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-[11px] font-black shadow-sm appearance-none">
                                                        <option value="kg">KILOGRAMOS (KG)</option>
                                                        <option value="tn">TONELADAS (TN)</option>
                                                        <option value="sacos">SACOS (U)</option>
                                                        <option value="und">UNIDADES (UND)</option>
                                                        <option value="jabas">JABAS</option>
                                                    </select>
                                                </div>
                                                <div class="md:col-span-3 space-y-1.5">
                                                    <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                                        <i class="fa-solid fa-star text-emerald-500"></i> Calidad
                                                    </label>
                                                    <select wire:model="itemsCosecha.{{ $idx }}.calidad" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-[11px] font-black shadow-sm appearance-none">
                                                        <option value="primera">PRIMERA (A)</option>
                                                        <option value="segunda">SEGUNDA (B)</option>
                                                        <option value="descarte">DESCARTE / MERMA</option>
                                                    </select>
                                                </div>
                                                <div class="md:col-span-2 space-y-1.5">
                                                    <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                                        <i class="fa-solid fa-money-bill-transfer text-emerald-500"></i> Costo Op.
                                                    </label>
                                                    <input type="number" step="0.01" wire:model="itemsCosecha.{{ $idx }}.costo_operativo" class="w-full bg-white dark:bg-slate-800 border-none rounded-lg p-2.5 text-[11px] font-black shadow-sm" placeholder="0.00">
                                                </div>
                                                <div class="md:col-span-1 flex justify-center pb-1">
                                                    <button type="button" wire:click="removeItem('harvest', {{ $idx }})" class="w-8 h-8 text-rose-500 hover:bg-rose-50 rounded-lg transition-all">
                                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="space-y-5">
                                <div class="flex items-center justify-between border-l-4 border-blue-500 pl-2.5">
                                    <h4 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-widest italic leading-none">CRONOGRAMA Y COSTOS</h4>
                                    <div class="text-right">
                                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">TOTAL INVERSIÓN</p>
                                        <p class="text-xl font-black text-agri-green italic tracking-tighter leading-none">S/ {{ number_format($costo_total, 2) }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                                    <div class="space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-1">Fecha Realización *</label><input type="date" wire:model="fecha_realizacion" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner uppercase"></div>
                                    <div class="space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-1">Estado Labor *</label><select wire:model="estado" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner uppercase tracking-widest appearance-none cursor-pointer"><option value="Pendiente">PENDIENTE</option><option value="En progreso">EN PROGRESO</option><option value="Completada">COMPLETADA</option></select></div>
                                    <div class="space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-1">Observaciones</label><textarea wire:model="observaciones" rows="1" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner min-h-[44px] italic" placeholder="Notas técnicas..."></textarea></div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div class="bg-blue-50 dark:bg-blue-500/10 p-4 rounded-xl border border-blue-500/20 shadow-sm flex flex-col items-center">
                                        <p class="text-[8px] font-black text-blue-500 uppercase mb-1 leading-none italic tracking-[0.2em]">insumos</p>
                                        <p class="text-lg font-black text-slate-800 dark:text-white italic tracking-tighter">S/ {{ number_format($costo_insumos_total, 2) }}</p>
                                    </div>
                                    <div class="bg-amber-50 dark:bg-amber-500/10 p-4 rounded-xl border border-amber-500/20 shadow-sm flex flex-col items-center">
                                        <p class="text-[8px] font-black text-amber-500 uppercase mb-1 leading-none italic tracking-[0.2em]">personal</p>
                                        <p class="text-lg font-black text-slate-800 dark:text-white italic tracking-tighter">S/ {{ number_format($costo_mano_obra_total, 2) }}</p>
                                    </div>
                                    <div class="bg-violet-50 dark:bg-violet-500/10 p-4 rounded-xl border border-violet-500/20 shadow-sm flex flex-col items-center">
                                        <p class="text-[8px] font-black text-violet-500 uppercase mb-1 leading-none italic tracking-[0.2em]">maquinaria</p>
                                        <p class="text-lg font-black text-slate-800 dark:text-white italic tracking-tighter">S/ {{ number_format($costo_maquinaria_total, 2) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <!-- SECCIÓN: INSUMOS -->
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center border-l-4 border-blue-500 pl-2.5">
                                        <div class="flex items-center gap-2.5"><i class="fa-solid fa-boxes-stacked text-blue-500 text-xs"></i><h4 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-widest italic">INSUMOS UTILIZADOS</h4></div>
                                        <button type="button" wire:click="addItemInsumo" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg active:scale-95 italic">
                                            <i class="fa-solid fa-plus text-[8px]"></i> AGREGAR INSUMO
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        @foreach($itemsInsumos as $idx => $item)
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-blue-50 dark:bg-blue-500/5 p-4 rounded-xl border border-blue-500/20 shadow-inner" wire:key="insumo-{{ $idx }}">
                                                <div class="md:col-span-5 relative space-y-1">
                                                    <label class="text-[9px] font-black text-slate-500 uppercase italic flex items-center gap-2"><i class="fa-solid fa-flask text-blue-500"></i> Producto</label>
                                                    <input type="text" wire:model.live="itemsInsumos.{{ $idx }}.insumo_nombre" wire:input="searchInsumo({{ $idx }}, $event.target.value)" class="w-full bg-white dark:bg-slate-900 border-none rounded-lg p-2.5 text-[11px] font-black uppercase shadow-sm italic" placeholder="BUSCAR...">
                                                    <div x-show="$wire.showIns && $wire.activeIdx === {{ $idx }}" class="absolute w-full mt-1 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 z-[70] overflow-hidden max-h-40 overflow-y-auto">
                                                        @foreach($resultsIns as $ri)
                                                            <div wire:click="selectInsumoItem({{ $idx }}, {{ $ri->id }}, '{{ $ri->nombre }}')" class="p-3 hover:bg-blue-600 hover:text-white cursor-pointer border-b last:border-0 text-[10px] font-black uppercase italic tracking-widest transition-colors">{{ $ri->nombre }}</div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="md:col-span-1 flex flex-col items-center gap-1.5">
                                                    <label class="text-[9px] font-black text-slate-500 uppercase italic">PROV.</label>
                                                    <input type="checkbox" wire:click="openAddProvider({{ $idx }})" @if($item['proveedor_id']) checked @endif class="w-6 h-6 text-blue-600 border-slate-300 rounded-lg shadow-sm focus:ring-0">
                                                </div>
                                                <div class="md:col-span-2 space-y-1">
                                                    <label class="text-[9px] font-black text-slate-500 uppercase italic text-center block">Cant.</label>
                                                    <input type="number" wire:model.live.blur="itemsInsumos.{{ $idx }}.cantidad" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-lg p-2.5 text-[11px] font-black text-center shadow-sm">
                                                </div>
                                                <div class="md:col-span-3 space-y-1">
                                                    <label class="text-[9px] font-black text-slate-500 uppercase italic text-center block">Costo U. (S/)</label>
                                                    <input type="number" step="0.01" wire:model.live.blur="itemsInsumos.{{ $idx }}.costo_unitario" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-lg p-2.5 text-[11px] font-black text-center shadow-sm" placeholder="0.00">
                                                </div>
                                                <div class="md:col-span-1 flex justify-center pb-1">
                                                    <button type="button" wire:click="removeItem('insumo', {{ $idx }})" class="w-9 h-9 text-rose-500 hover:bg-rose-50 rounded-lg transition-all shadow-sm"><i class="fa-solid fa-trash-can text-sm"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- SECCIÓN: PERSONAL -->
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center border-l-4 border-amber-500 pl-2.5">
                                        <div class="flex items-center gap-2.5"><i class="fa-solid fa-people-group text-amber-500 text-xs"></i><h4 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-widest italic">MANO DE OBRA / JORNALES</h4></div>
                                        <button type="button" wire:click="addItemManoObra" class="px-4 py-1.5 bg-amber-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg active:scale-95 italic">
                                            <i class="fa-solid fa-plus text-[8px]"></i> AGREGAR PERSONAL
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        @foreach($itemsManoObra as $idx => $item)
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-amber-50 dark:bg-amber-500/5 p-4 rounded-xl border border-amber-500/20 shadow-inner" wire:key="mano-{{ $idx }}">
                                                <div class="md:col-span-4 space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase italic flex items-center gap-2"><i class="fa-solid fa-user-gear text-amber-500"></i> Perfil</label><select wire:model.live="itemsManoObra.{{ $idx }}.tipo_id" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-lg p-2.5 text-[11px] font-black uppercase italic shadow-sm appearance-none"><option value="">SELECCIONE...</option>@foreach($manoObraTipos as $mot) <option value="{{ $mot->id }}">{{ $mot->nombre }}</option> @endforeach</select></div>
                                                <div class="md:col-span-2 space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase italic text-center block">Pers.</label><input type="number" wire:model.live.blur="itemsManoObra.{{ $idx }}.cantidad" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-lg p-2.5 text-[11px] font-black text-center shadow-sm"></div>
                                                <div class="md:col-span-2 space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase italic text-center block">Días</label><input type="number" wire:model.live.blur="itemsManoObra.{{ $idx }}.dias" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-lg p-2.5 text-[11px] font-black text-center shadow-sm"></div>
                                                <div class="md:col-span-3 space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase italic text-center block">Costo D. (S/)</label><input type="number" step="0.1" wire:model.live.blur="itemsManoObra.{{ $idx }}.costo_dia" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-lg p-2.5 text-[11px] font-black text-center shadow-sm" placeholder="0.00"></div>
                                                <div class="md:col-span-1 flex justify-center pb-1"><button type="button" wire:click="removeItem('mano', {{ $idx }})" class="w-9 h-9 text-rose-500 hover:bg-rose-50 rounded-lg transition-all shadow-sm"><i class="fa-solid fa-trash-can text-sm"></i></button></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- SECCIÓN: MAQUINARIA -->
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center border-l-4 border-violet-500 pl-2.5">
                                        <div class="flex items-center gap-2.5"><i class="fa-solid fa-truck-tractor text-violet-500 text-xs"></i><h4 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-widest italic">MAQUINARIA Y EQUIPOS</h4></div>
                                        <button type="button" wire:click="addItemMaquinaria" class="px-4 py-1.5 bg-violet-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg active:scale-95 italic">
                                            <i class="fa-solid fa-plus text-[8px]"></i> AGREGAR EQUIPO
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        @foreach($itemsMaquinaria as $idx => $item)
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-violet-50 dark:bg-violet-500/5 p-4 rounded-xl border border-violet-500/20 shadow-inner" wire:key="maq-{{ $idx }}">
                                                <div class="md:col-span-6 space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase italic flex items-center gap-2"><i class="fa-solid fa-tractor text-violet-500"></i> Nombre Equipo</label><input type="text" wire:model.live="itemsMaquinaria.{{ $idx }}.nombre" class="w-full bg-white dark:bg-slate-900 border-none rounded-lg p-2.5 text-[11px] font-black uppercase italic shadow-sm" placeholder="EJ: TRACTOR CASE 150"></div>
                                                <div class="md:col-span-2 space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase italic text-center block">Hrs.</label><input type="number" wire:model.live.blur="itemsMaquinaria.{{ $idx }}.horas" class="w-full bg-white dark:bg-slate-900 border-none rounded-lg p-2.5 text-[11px] font-black text-center shadow-sm"></div>
                                                <div class="md:col-span-3 space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase italic text-center block">Inversión T. (S/)</label><input type="number" step="0.01" wire:model.live.blur="itemsMaquinaria.{{ $idx }}.costo_total" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-lg p-2.5 text-[11px] font-black text-center shadow-sm" placeholder="0.00"></div>
                                                <div class="md:col-span-1 flex justify-center pb-1"><button type="button" wire:click="removeItem('maq', {{ $idx }})" class="w-9 h-9 text-rose-500 hover:bg-rose-50 rounded-lg transition-all shadow-sm"><i class="fa-solid fa-trash-can text-sm"></i></button></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4 pt-2">
                                <div class="flex items-center gap-2.5 border-l-4 border-slate-600 pl-2.5"><h4 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-widest italic leading-none">REGISTRO FOTOGRÁFICO</h4></div>
                                <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-2xl border-2 border-dashed border-slate-200 dark:border-white/10 shadow-inner flex items-center gap-6">
                                    <div class="w-24 h-18 rounded-xl overflow-hidden bg-white dark:bg-slate-800 shrink-0 shadow-lg border-2 border-white dark:border-white/10 flex items-center justify-center">
                                        @if($laborPhoto && method_exists($laborPhoto, 'temporaryUrl'))
                                            <img src="{{ $laborPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                                        @elseif($currentPhotoPath)
                                            <img src="{{ Storage::url($currentPhotoPath) }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fa-solid fa-camera text-slate-200 text-2xl"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 flex flex-col sm:flex-row items-center justify-between gap-4">
                                        <div class="space-y-1">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase italic max-w-[200px] leading-tight">Sincroniza una foto del avance para el control técnico de auditoría.</p>
                                            @error('laborPhoto') <span class="text-[8px] text-rose-500 font-black uppercase italic">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="relative w-full sm:w-auto" wire:ignore.self>
                                            <input type="file" wire:model.live="laborPhoto" wire:key="labor-photo-input-{{ $laborId ?? 'new' }}" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"/>
                                            <div wire:loading.remove wire:target="laborPhoto" class="px-6 py-2.5 bg-agri-green text-white rounded-xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/20 flex items-center justify-center gap-2.5 italic transition-all hover:scale-105 active:scale-95">
                                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                                <span>SUBIR FOTO</span>
                                            </div>
                                            <div wire:loading wire:target="laborPhoto" class="px-6 py-2.5 bg-slate-400 text-white rounded-xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl flex items-center justify-center gap-2.5 italic">
                                                <i class="fa-solid fa-spinner fa-spin"></i>
                                                <span>CARGANDO...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6">
                                @if ($errors->any())
                                    <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-5 rounded-xl animate-in slide-in-from-left-5">
                                        <div class="flex items-center gap-2.5 mb-2">
                                            <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                            <p class="text-[10px] font-black text-rose-700 uppercase italic">Validación Requerida:</p>
                                        </div>
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li class="text-[9px] font-bold text-rose-600 uppercase italic">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                               <button type="submit"
                                   wire:loading.attr="disabled"
                                   wire:target="save"
                                   class="w-full py-4 bg-agri-green text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/20 hover:bg-emerald-600 hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center gap-3 italic">
                                   <i class="fa-solid fa-shield-check text-sm" wire:loading.remove wire:target="save"></i>
                                   <i class="fa-solid fa-spinner fa-spin text-sm" wire:loading wire:target="save"></i>
                                   <span>CONFIRMAR Y GUARDAR REGISTRO</span>
                               </button>
                                <p class="text-[8px] text-center text-slate-400 mt-3 font-black uppercase tracking-[0.3em] italic opacity-50">Auditoría AgroSys Cloud Enterprise v2.4 Activa</p>
                            </div>
                        </div>
                    @endif
                </form>
            @endif
        </div>
    </x-modal>

    <!-- MODAL REGISTRO RÁPIDO PROVEEDOR -->
    <x-modal name="modal-add-provider" :show="false">
        <div class="bg-white dark:bg-agri-d_bg rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <div class="bg-[#003a38] px-6 py-4 flex justify-between items-center text-white border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center border border-white/10 shadow-inner">
                        <i class="fa-solid fa-truck-fast text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black tracking-tighter uppercase leading-none italic">Nuevo Proveedor</h3>
                    </div>
                </div>
                <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 transition-all border border-white/10"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form wire:submit.prevent="saveQuickProvider" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest italic ml-1">Nombre Empresa *</label>
                        <input type="text" wire:model="newProvNombre" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner uppercase focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white" placeholder="EJ: AGROSOLUCIONES SAC">
                        @error('newProvNombre') <span class="text-[8px] text-rose-500 font-bold uppercase ml-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest italic ml-1">RUC / Registro</label>
                        <input type="text" wire:model="newProvRuc" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white" placeholder="20123456789">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest italic ml-1">Teléfono Contacto</label>
                        <input type="text" wire:model="newProvTelf" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white" placeholder="987 654 321">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest italic ml-1">Especialidad</label>
                        <select wire:model="newProvTipo" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-[11px] font-black shadow-inner focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white appearance-none cursor-pointer">
                            <option value="Insumos">INSUMOS</option>
                            <option value="Maquinaria">MAQUINARIA</option>
                            <option value="Logística">LOGÍSTICA</option>
                        </select>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-blue-600 text-white rounded-xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center justify-center gap-3 active:scale-95 italic">
                        <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                        <span>REGISTRAR SOCIO COMERCIAL</span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- MODAL VISTA DETALLADA DE LABOR -->
    <x-modal name="modal-view-labor" :show="false" focusable>
        <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/5"
             x-data="{
                 initLaborChart() {
                     const canvas = document.getElementById('laborReportChartCanvas');
                     if (!canvas) return;
                     const ctx = canvas.getContext('2d');

                     const cIns = parseFloat(document.getElementById('hidden-val-insumos')?.value || 0);
                     const cPer = parseFloat(document.getElementById('hidden-val-personal')?.value || 0);
                     const cMaq = parseFloat(document.getElementById('hidden-val-maquinaria')?.value || 0);

                     const labels = [];
                     const values = [];
                     const colors = [];

                     if (cIns > 0) { labels.push('Insumos'); values.push(cIns); colors.push('#3b82f6'); }
                     if (cPer > 0) { labels.push('Personal'); values.push(cPer); colors.push('#f59e0b'); }
                     if (cMaq > 0) { labels.push('Maquinaria'); values.push(cMaq); colors.push('#8b5cf6'); }

                     if (values.length === 0) return;

                     if (window.myLaborReportChart) window.myLaborReportChart.destroy();
                     window.myLaborReportChart = new Chart(ctx, {
                         type: 'pie',
                         data: {
                             labels: labels,
                             datasets: [{
                                 data: values,
                                 backgroundColor: colors,
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
                                         font: { size: 10, weight: 'bold' }
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
             x-init="setTimeout(() => initLaborChart(), 600)"
             x-on:open-modal.window="$event.detail == 'modal-view-labor' ? setTimeout(() => initLaborChart(), 700) : null">
            @if($viewingLabor)
                <!-- Header Bar -->
                <div class="bg-[#003a38] px-8 py-3 flex justify-between items-center border-b border-white/10">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2 text-white/70">
                            @php
                                $clima = $viewingLabor->cultivo->terreno->latestClima;
                                $icon = 'fa-sun';
                                if($clima) {
                                    $cond = strtolower($clima->condicion);
                                    if(str_contains($cond, 'lluvia') || str_contains($cond, 'llovizna')) $icon = 'fa-cloud-showers-heavy';
                                    elseif(str_contains($cond, 'nublado')) $icon = 'fa-cloud';
                                    elseif(str_contains($cond, 'tormenta')) $icon = 'fa-cloud-bolt';
                                }
                            @endphp
                            <i class="fa-solid {{ $icon }} text-amber-400 text-xs"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest italic">
                                @if($clima)
                                    {{ round($clima->temperatura) }}°C | {{ $clima->humedad }}% HR
                                @else
                                    S/D
                                @endif
                            </span>
                        </div>
                        <div class="h-4 w-px bg-white/10"></div>
                        <span class="text-[9px] font-black text-agri-green uppercase tracking-[0.3em] italic">Reporte Técnico de Labor</span>
                    </div>
                    <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center text-white/40 hover:text-white transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <!-- Header Hero -->
                <div class="relative h-64 w-full group/hero">
                    @if($viewingLabor->foto_path)
                        <img src="{{ Storage::url($viewingLabor->foto_path) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-[#003a38] to-emerald-900 flex items-center justify-center">
                            <i class="fa-solid {{ $icons[$viewingLabor->detalleCatalogo->nombre] ?? 'fa-tractor' }} text-6xl text-white/10"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/40 to-transparent"></div>

                    <!-- BADGE DE LOTE POSICIONADO (Top Right HUD) -->
                    <div class="absolute top-6 right-8 z-50">
                        <div class="bg-amber-500 text-white px-4 py-2 rounded-xl shadow-2xl border border-white/20 tracking-widest italic flex items-center gap-2 animate-in slide-in-from-right-4 duration-700">
                            <i class="fa-solid fa-barcode text-sm"></i>
                            <span class="text-[11px] font-black uppercase">LOTE: {{ $viewingLabor->cultivo->nombre_lote }}</span>
                        </div>
                    </div>

                    <div class="absolute bottom-6 left-8 right-8 z-30 space-y-4">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-3 py-1 bg-agri-green text-white text-[9px] font-black uppercase rounded shadow-xl border border-white/20 tracking-widest">LABOR #{{ $viewingLabor->id }}</span>
                                <span class="px-3 py-1 bg-white/10 backdrop-blur-md text-white text-[9px] font-black uppercase rounded border border-white/10 tracking-widest">{{ strtoupper($viewingLabor->estado) }}</span>
                            </div>
                            <h2 class="text-4xl font-black text-white italic tracking-tighter uppercase leading-none drop-shadow-2xl mb-2">{{ $viewingLabor->detalleCatalogo->nombre }}</h2>
                        </div>

                        <!-- Ubicación y Fechas Overlay -->
                        <div class="flex flex-wrap items-center gap-6 pt-4 border-t border-white/10">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 bg-agri-green rounded flex items-center justify-center text-white text-xs shadow-lg"><i class="fa-solid fa-location-dot"></i></div>
                                <div>
                                    <p class="text-[8px] font-black text-white/40 uppercase tracking-widest">Terreno</p>
                                    <p class="text-[10px] font-black text-white uppercase italic leading-none">{{ $viewingLabor->cultivo->terreno->nombre }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 bg-blue-500 rounded flex items-center justify-center text-white text-xs shadow-lg"><i class="fa-solid fa-leaf"></i></div>
                                <div>
                                    <p class="text-[8px] font-black text-white/40 uppercase tracking-widest">Lote</p>
                                    <p class="text-[10px] font-black text-white uppercase italic leading-none">{{ $viewingLabor->cultivo->nombre_lote }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 bg-amber-500 rounded flex items-center justify-center text-white text-xs shadow-lg"><i class="fa-solid fa-calendar-check"></i></div>
                                <div>
                                    <p class="text-[8px] font-black text-white/40 uppercase tracking-widest">Fecha Labor</p>
                                    <p class="text-[10px] font-black text-white uppercase italic leading-none">{{ \Carbon\Carbon::parse($viewingLabor->fecha_realizacion)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-8 max-h-[65vh] overflow-y-auto custom-scrollbar bg-slate-50/30 dark:bg-transparent">
                    <div class="space-y-8">
                        <div class="flex items-center gap-3 border-l-4 border-violet-500 pl-4">
                            <h4 class="text-[11px] font-black text-slate-800 dark:text-white uppercase tracking-[0.4em] italic leading-none">RECURSOS Y LOGÍSTICA</h4>
                        </div>

                        <!-- Visualización de Gastos (Gráfico y Totales Verticales Estilo Img 2) -->
                        @php
                            $cInsumos = (float)$viewingLabor->insumos->sum(fn($i) => ($i->cantidad * $i->costo_unitario) + $i->costo_flete);
                            $cPersonal = (float)$viewingLabor->manoDeObra->sum('subtotal');
                            $cMaq = (float)$viewingLabor->maquinaria->sum('costo_total');

                            $chartLabels = [];
                            $chartValues = [];
                            $chartColors = [];

                            if($cInsumos > 0) { $chartLabels[] = 'Insumos'; $chartValues[] = $cInsumos; $chartColors[] = '#3b82f6'; }
                            if($cPersonal > 0) { $chartLabels[] = 'Personal'; $chartValues[] = $cPersonal; $chartColors[] = '#f59e0b'; }
                            if($cMaq > 0) { $chartLabels[] = 'Maquinaria'; $chartValues[] = $cMaq; $chartColors[] = '#8b5cf6'; }

                            $chartData = [
                                'labels' => $chartLabels,
                                'values' => $chartValues,
                                'colors' => $chartColors,
                                'title' => 'Inversión por Categoría',
                                'unit' => 'S/'
                            ];
                        @endphp

                        @if(count($chartLabels) > 0)
                        <div class="bg-white dark:bg-slate-900/50 p-8 rounded-[2.5rem] border border-black/5 dark:border-white/5 shadow-inner">
                            <input type="hidden" id="hidden-val-insumos" value="{{ $cInsumos }}">
                            <input type="hidden" id="hidden-val-personal" value="{{ $cPersonal }}">
                            <input type="hidden" id="hidden-val-maquinaria" value="{{ $cMaq }}">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                                <!-- Gráfico -->
                                <div class="lg:col-span-6 h-64 relative bg-white/50 dark:bg-slate-900/50 rounded-3xl p-4" wire:ignore>
                                    <canvas id="laborReportChartCanvas" class="w-full h-full"></canvas>
                                </div>
                                <!-- Totales Verticales -->
                                <div class="lg:col-span-6 flex flex-col gap-4">
                                    @if($cInsumos > 0)
                                    <div class="bg-white dark:bg-slate-800 p-6 rounded-[1.8rem] shadow-xl border-l-[6px] border-blue-500 transform hover:scale-[1.02] transition-all">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Total Insumos / Productos</p>
                                        <h5 class="text-2xl font-black text-blue-600 italic tracking-tighter">S/ {{ number_format($cInsumos, 2) }}</h5>
                                    </div>
                                    @endif
                                    @if($cPersonal > 0)
                                    <div class="bg-white dark:bg-slate-800 p-6 rounded-[1.8rem] shadow-xl border-l-[6px] border-amber-500 transform hover:scale-[1.02] transition-all">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Total Mano de Obra</p>
                                        <h5 class="text-2xl font-black text-amber-600 italic tracking-tighter">S/ {{ number_format($cPersonal, 2) }}</h5>
                                    </div>
                                    @endif
                                    @if($cMaq > 0)
                                    <div class="bg-white dark:bg-slate-800 p-6 rounded-[1.8rem] shadow-xl border-l-[6px] border-violet-500 transform hover:scale-[1.02] transition-all">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Total Maquinaria / Equipos</p>
                                        <h5 class="text-2xl font-black text-violet-600 italic tracking-tighter">S/ {{ number_format($cMaq, 2) }}</h5>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Tablas Detalladas (Solo si existen datos) -->
                        <div class="space-y-8">
                            <!-- Insumos -->
                            @if($viewingLabor->insumos->count() > 0)
                            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 rounded-xl overflow-hidden shadow-2xl">
                                <div class="bg-blue-600 px-6 py-3 flex justify-between items-center text-white">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-boxes-stacked text-base"></i>
                                        <h5 class="text-[10px] font-black uppercase tracking-[0.2em] italic">Detalle de Insumos / Productos</h5>
                                    </div>
                                    <span class="bg-white/20 px-3 py-1 rounded-full text-[9px] font-black">S/ {{ number_format($cInsumos, 2) }}</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-slate-50 dark:bg-white/5 text-[8px] font-black text-slate-400 uppercase tracking-widest">
                                            <tr>
                                                <th class="px-6 py-3">Producto Aplicado</th>
                                                <th class="px-5 py-3">Proveedor</th>
                                                <th class="px-3 py-3 text-center">Cantidad</th>
                                                <th class="px-5 py-3 text-right">Costo Unit.</th>
                                                <th class="px-6 py-3 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-[10px] font-bold italic text-slate-600 dark:text-slate-300">
                                            @foreach($viewingLabor->insumos as $ins)
                                                <tr class="border-t border-slate-50 dark:border-white/5 hover:bg-slate-50/50 transition-colors">
                                                    <td class="px-6 py-3 uppercase">{{ $ins->detalleCatalogo->nombre ?? 'Insumo' }}</td>
                                                    <td class="px-5 py-3 uppercase text-slate-400">{{ $ins->proveedor?->nombre_empresa ?? 'SIN PROVEEDOR' }}</td>
                                                    <td class="px-3 py-3 text-center font-black">{{ $ins->cantidad }} ud.</td>
                                                    <td class="px-5 py-3 text-right">S/ {{ number_format($ins->costo_unitario, 2) }}</td>
                                                    <td class="px-6 py-3 text-right text-slate-900 dark:text-white font-black">S/ {{ number_format(($ins->cantidad * $ins->costo_unitario) + $ins->costo_flete, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <!-- Personal -->
                            @if($viewingLabor->manoDeObra->count() > 0)
                            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 rounded-xl overflow-hidden shadow-sm">
                                <div class="bg-amber-500 px-6 py-3 flex justify-between items-center text-white">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-people-group text-base"></i>
                                        <h5 class="text-[10px] font-black uppercase tracking-[0.2em] italic">Detalle de Personal / Jornales</h5>
                                    </div>
                                    <span class="bg-white/20 px-3 py-1 rounded-full text-[9px] font-black">S/ {{ number_format($cPersonal, 2) }}</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-slate-50 dark:bg-white/5 text-[8px] font-black text-slate-400 uppercase tracking-widest">
                                            <tr>
                                                <th class="px-6 py-3">Perfil Operativo</th>
                                                <th class="px-5 py-3 text-center">N° Pers.</th>
                                                <th class="px-5 py-3 text-center">Días Trab.</th>
                                                <th class="px-5 py-3 text-right">Costo/Día</th>
                                                <th class="px-6 py-3 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-[10px] font-bold italic text-slate-600 dark:text-slate-300">
                                            @foreach($viewingLabor->manoDeObra as $mo)
                                                <tr class="border-t border-slate-50 dark:border-white/5 hover:bg-slate-50/50 transition-colors">
                                                    <td class="px-6 py-3 uppercase">{{ $mo->tipoPersona->nombre }}</td>
                                                    <td class="px-5 py-3 text-center font-black">{{ $mo->cantidad_trabajadores }}</td>
                                                    <td class="px-5 py-3 text-center">{{ $mo->dias_trabajados }} días</td>
                                                    <td class="px-5 py-3 text-right">S/ {{ number_format($mo->costo_por_dia, 2) }}</td>
                                                    <td class="px-6 py-3 text-right text-slate-900 dark:text-white font-black">S/ {{ number_format($mo->subtotal, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <!-- Maquinaria -->
                            @if($viewingLabor->maquinaria->count() > 0)
                            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 rounded-xl overflow-hidden shadow-sm">
                                <div class="bg-violet-600 px-6 py-3 flex justify-between items-center text-white">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-truck-tractor text-base"></i>
                                        <h5 class="text-[10px] font-black uppercase tracking-[0.2em] italic">Detalle de Maquinaria / Equipos</h5>
                                    </div>
                                    <span class="bg-white/20 px-3 py-1 rounded-full text-[9px] font-black">S/ {{ number_format($cMaq, 2) }}</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-slate-50 dark:bg-white/5 text-[8px] font-black text-slate-400 uppercase tracking-widest">
                                            <tr>
                                                <th class="px-6 py-3">Equipo / Vehículo</th>
                                                <th class="px-5 py-3">Labor Realizada</th>
                                                <th class="px-5 py-3 text-center">Horas</th>
                                                <th class="px-6 py-3 text-right">Inversión Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-[10px] font-bold italic text-slate-600 dark:text-slate-300">
                                            @foreach($viewingLabor->maquinaria as $maq)
                                                <tr class="border-t border-slate-50 dark:border-white/5 hover:bg-slate-50/50 transition-colors">
                                                    <td class="px-6 py-3 uppercase font-black text-slate-800 dark:text-slate-100">{{ $maq->nombre_maquinaria }}</td>
                                                    <td class="px-5 py-3 uppercase text-slate-400">{{ $maq->labor_realizada }}</td>
                                                    <td class="px-5 py-3 text-center font-black">{{ $maq->horas_trabajadas }} h</td>
                                                    <td class="px-6 py-3 text-right text-slate-900 dark:text-white font-black">S/ {{ number_format($maq->costo_total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                        <div class="space-y-4">
                            <p class="text-[9px] font-black text-slate-400 uppercase italic tracking-widest border-b border-slate-100 dark:border-white/10 pb-1.5 leading-none">Bitácora de Observaciones</p>
                            <div class="bg-white dark:bg-white/5 p-6 rounded-xl min-h-[140px] italic text-[11px] font-bold text-slate-600 dark:text-slate-400 shadow-sm border border-slate-100 dark:border-white/5 leading-relaxed">{{ $viewingLabor->observaciones ?: 'Sin anotaciones técnicas registradas para esta labor.' }}</div>
                        </div>
                        <div class="space-y-4">
                            <p class="text-[9px] font-black text-slate-400 uppercase italic tracking-widest border-b border-slate-100 dark:border-white/10 pb-1.5 leading-none">Certificación Digital</p>
                            <div class="bg-slate-100 dark:bg-black/20 p-6 rounded-xl flex flex-col items-center justify-center text-center gap-3 border border-slate-200 dark:border-white/5 h-full">
                                <div class="w-12 h-12 bg-agri-green/10 rounded-full flex items-center justify-center text-agri-green shadow-inner"><i class="fa-solid fa-shield-halved text-xl"></i></div>
                                <p class="text-[9px] font-black text-slate-400 uppercase italic tracking-widest">Este registro cuenta con respaldo de auditoría AgroSys Cloud Enterprise.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-100 dark:bg-black/40 p-6 flex justify-center border-t border-slate-200 dark:border-white/5"><button @click="$dispatch('close')" class="px-12 py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-black text-[11px] uppercase tracking-widest transition-all shadow-2xl hover:scale-105 active:scale-95 italic">Cerrar Reporte</button></div>
            @endif
        </div>
    </x-modal>
</div>

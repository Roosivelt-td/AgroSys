<div class="space-y-6 p-2 md:p-1 transition-colors duration-500">

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

                <button @click="$wire.resetForm(); $dispatch('open-modal', 'modal-labor-manager')"
                        class="px-10 py-4 bg-agri-green hover:bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/20 transition-all flex items-center gap-3 active:scale-95 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                    <i class="fa-solid fa-plus text-sm group-hover:rotate-90 transition-transform"></i> NUEVO REGISTRO
                </button>
            </div>
        </div>

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
                            <h3 class="text-[17px] font-black italic uppercase tracking-tight text-white leading-none shadow-sm">{{ $l->detalleCatalogo->nombre }}</h3>
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

    <!-- MODAL LABOR MANAGER -->
    <x-modal name="modal-labor-manager" :show="false" focusable>
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10"
             x-data="{ showStatus: false, showLands: false, showCats: false, showCrops: false }">

            <div class="bg-[#003a38] px-8 py-5 flex justify-between items-center text-white border-b border-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/10 shadow-inner">
                        <i class="fa-solid fa-list-check text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-[16px] font-black tracking-tight uppercase leading-none italic">
                            @if($step === 1) SELECCIONAR LABOR @else NUEVA LABOR: {{ strtoupper($catalogoLabores->find($catalogo_labor_id)->nombre ?? '') }} @endif
                        </h3>
                        <p class="text-[9px] opacity-60 uppercase font-black tracking-[0.2em] mt-1 italic">GESTIÓN DE PROCESOS OPERATIVOS</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @if($step === 2)
                        <button type="button" wire:click="$set('step', 1)" class="px-4 py-1.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                            <i class="fa-solid fa-rotate-left"></i> Cambiar
                        </button>
                    @endif
                    <div class="bg-black/20 px-3 py-1.5 rounded-xl border border-white/5 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-day text-[10px] text-agri-green"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest opacity-80 italic">{{ date('d-m-Y') }}</span>
                    </div>
                    <button @click="$dispatch('close')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 transition-all border border-white/10"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
            </div>

            @if($step === 1)
                <div class="p-8 animate-in zoom-in-95 duration-500 bg-slate-50/50 dark:bg-transparent">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php
                            $icons = ['Preparar'=>'fa-trowel-bricks','Siembra'=>'fa-seedling','Riego'=>'fa-droplet','Fumigar'=>'fa-spray-can-sparkles','Aporque'=>'fa-mountain','Desierbe'=>'fa-scissors','Deshierbe'=>'fa-scissors','Abonar'=>'fa-flask-vial','Cosechar'=>'fa-basket-shopping','Otros'=>'fa-ellipsis'];
                        @endphp
                        @foreach($catalogoLabores as $l)
                            @php $isDisabled = !($laborStatusMap[$l->nombre] ?? true); @endphp
                            <button wire:click="selectLaborType({{ $l->id }})" @if($isDisabled) disabled @endif
                                    class="group flex flex-col items-center justify-center p-8 rounded-[2rem] border-2 transition-all duration-500
                                    {{ $isDisabled ? 'bg-slate-100/50 dark:bg-white/5 border-transparent opacity-20 cursor-not-allowed scale-95' : 'bg-white dark:bg-slate-800 border-slate-100 dark:border-white/5 hover:border-agri-green hover:shadow-xl hover:-translate-y-2' }}">
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4 transition-all {{ $isDisabled ? 'bg-slate-300 text-slate-500' : 'bg-agri-green/10 text-agri-green group-hover:bg-agri-green group-hover:text-white shadow-inner' }}">
                                    <i class="fa-solid {{ $icons[$l->nombre] ?? 'fa-gears' }} text-2xl"></i>
                                </div>
                                <span class="text-[12px] font-black uppercase tracking-[0.1em] {{ $isDisabled ? 'text-slate-500' : 'text-slate-800 dark:text-slate-100' }}">{{ $l->nombre }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @else
                <form wire:submit.prevent="save" class="max-h-[85vh] overflow-y-auto custom-scrollbar bg-white dark:bg-slate-900 flex flex-col">
                    <div class="px-8 pt-8 pb-4 space-y-6">
                        <div class="flex items-center gap-3 border-l-4 border-agri-green pl-3">
                            <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight italic">IDENTIFICACIÓN DEL CULTIVO</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-x-6 gap-y-4">
                            <div class="md:col-span-3 space-y-1">
                                <label class="text-[10px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-flag text-agri-green"></i> 1. ESTADO</label>
                                <select wire:model.live="selStatus" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-xl p-2.5 text-[11px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green shadow-sm">
                                    <option value="TODOS">TODOS LOS CULTIVOS</option>
                                    <option value="En proceso">EN CRECIMIENTO</option>
                                    <option value="Completada">COSECHADO</option>
                                    <option value="Perdido">PERDIDO</option>
                                </select>
                            </div>
                            <div class="md:col-span-5 space-y-1">
                                <label class="text-[10px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-location-dot text-agri-green"></i> 2. TERRENO / PARCELA</label>
                                <select wire:model.live="selLandId" @if($selStatus === '') disabled @endif class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-xl p-2.5 text-[11px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green shadow-sm">
                                    <option value="">Seleccionar parcela...</option>
                                    @foreach($resultsLands as $r) <option value="{{ $r->id }}">{{ $r->nombre }} - {{ $r->hectareas }} Ha</option> @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-4 space-y-1">
                                <label class="text-[10px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-seedling text-agri-green"></i> 3. TIPO DE CULTIVO</label>
                                <select wire:model.live="selCatId" @if(!$selLandId) disabled @endif class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-xl p-2.5 text-[11px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green shadow-sm">
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach($resultsCats as $r) <option value="{{ $r->id }}">{{ $r->nombre }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-x-6 gap-y-4 mt-2">
                            <div class="md:col-span-4 space-y-1">
                                <label class="text-[10px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-tags text-agri-green text-[7px]"></i> 4. VARIEDAD</label>
                                <select wire:model.live="selVarName" @if(!$selCatId) disabled @endif class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-xl p-2.5 text-[11px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green shadow-sm">
                                    <option value="">Seleccionar variedad...</option>
                                    @foreach($resultsVars as $v) <option value="{{ $v }}">{{ strtoupper($v ?: 'GENERICA') }}</option> @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-8 space-y-1">
                                <label class="text-[10px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-leaf text-agri-green text-[7px]"></i> 5. CULTIVO</label>
                                <select wire:model.live="cultivo_id" @if($selVarName === '') disabled @endif class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-xl p-2.5 text-[11px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green shadow-sm border-2 border-agri-green/30">
                                    <option value="">Elegir campaña específica...</option>
                                    @foreach($resultsCrops as $r) <option value="{{ $r->id }}">{{ $r->label_display }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-4">
                        <div class="bg-slate-50 dark:bg-white/5 rounded-2xl p-5 border border-slate-100 dark:border-white/5 shadow-inner">
                            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-white/5 pb-2">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-list-check text-agri-green"></i>
                                    <h4 class="text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest italic leading-none">DETALLE TÉCNICO DE LA SELECCIÓN</h4>
                                </div>
                                @if($cultivo_id)
                                    <div class="flex items-center gap-4">
                                        <span class="bg-agri-green/10 text-agri-green px-3 py-1 rounded-lg text-[9px] font-black uppercase border border-agri-green/20 animate-pulse">
                                            <i class="fa-solid fa-calendar-check mr-1"></i> SEMBRADO EL: {{ $cropFechaPlanificada ? \Carbon\Carbon::parse($cropFechaPlanificada)->format('d/m/Y') : '---' }}
                                        </span>
                                        <span class="bg-blue-500/10 text-blue-500 px-3 py-1 rounded-lg text-[9px] font-black uppercase border border-blue-500/20">
                                            <i class="fa-solid fa-expand mr-1"></i> ÁREA: {{ $cropHectareas ? number_format($cropHectareas, 2) : '0.00' }} Ha
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 bg-agri-green text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg">1</div>
                                    <div class="flex flex-col"><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Terreno</p><p class="text-[10px] font-bold text-slate-900 dark:text-white uppercase truncate max-w-[120px] leading-none">{{ $landNombreSelected ?: '---' }}</p></div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 bg-agri-green text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg">2</div>
                                    <div class="flex flex-col"><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Cultivo</p><p class="text-[10px] font-bold text-slate-900 dark:text-white uppercase truncate max-w-[120px] leading-none">{{ $catNombreSelected ?: '---' }}</p></div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 bg-agri-green text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg">3</div>
                                    <div class="flex flex-col"><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Variedad</p><p class="text-[10px] font-bold text-slate-900 dark:text-white uppercase truncate max-w-[120px] leading-none">{{ $selVarName ?: '---' }}</p></div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 bg-agri-green text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg">4</div>
                                    <div class="flex flex-col"><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Labor</p><p class="text-[10px] font-bold text-slate-900 dark:text-white uppercase leading-none">{{ $catalogoLabores->find($catalogo_labor_id)->nombre ?? '---' }}</p></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($cultivo_id)
                        <div class="px-8 pb-4 space-y-4 animate-in fade-in duration-500">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3 border-l-4 border-blue-500 pl-3"><h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">REGISTRO DE COSTOS OPERATIVOS</h4></div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-emerald-50 dark:bg-emerald-500/10 p-4 rounded-2xl border-2 border-emerald-500/20 shadow-inner"><p class="text-[9px] font-black text-emerald-600 uppercase mb-1 leading-none italic tracking-widest">Inversión Total</p><p class="text-2xl font-black text-slate-900 dark:text-white italic tracking-tighter">S/ {{ number_format($costo_total, 2) }}</p></div>
                                    <div class="p-4 border-2 border-slate-50 dark:border-white/5 rounded-2xl flex flex-col justify-center bg-slate-50/50 shadow-sm"><p class="text-[9px] font-black text-slate-400 uppercase mb-1 leading-none italic tracking-widest">Mano de Obra</p><p class="text-lg font-black text-slate-700 dark:text-slate-300 italic">S/ {{ number_format($costo_mano_obra_total, 2) }}</p></div>
                                    <div class="p-4 border-2 border-slate-50 dark:border-white/5 rounded-2xl flex flex-col justify-center bg-slate-50/50 shadow-sm"><p class="text-[9px] font-black text-slate-400 uppercase mb-1 leading-none italic tracking-widest">Maquinaria</p><p class="text-lg font-black text-slate-700 dark:text-slate-300 italic">S/ {{ number_format($costo_maquinaria_total, 2) }}</p></div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div class="space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Fecha Realización</label><input type="date" wire:model="fecha_realizacion" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-2.5 text-xs font-black shadow-inner uppercase"></div>
                                    <div class="space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Estado Labor</label><select wire:model="estado" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-2.5 text-xs font-black shadow-inner uppercase tracking-widest"><option value="Pendiente">PENDIENTE</option><option value="En progreso">EN PROGRESO</option><option value="Completada">COMPLETADA</option></select></div>
                                    <div class="space-y-1"><label class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Notas</label><textarea wire:model="observaciones" rows="1" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-2.5 text-xs font-black shadow-inner min-h-[42px]"></textarea></div>
                                </div>
                            </div>
                            <div class="space-y-6">
                                @php $sections = [
                                    ['title' => 'Insumos / Productos', 'add' => 'addItemInsumo', 'items' => $itemsInsumos, 'color' => 'blue', 'icon' => 'fa-boxes-stacked'],
                                    ['title' => 'Personal / Jornales', 'add' => 'addItemManoObra', 'items' => $itemsManoObra, 'color' => 'amber', 'icon' => 'fa-people-group'],
                                    ['title' => 'Maquinaria / Equipos', 'add' => 'addItemMaquinaria', 'items' => $itemsMaquinaria, 'color' => 'violet', 'icon' => 'fa-truck-tractor']
                                ]; @endphp
                                @foreach($sections as $sec)
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center border-b border-slate-100 dark:border-white/5 pb-1.5">
                                            <div class="flex items-center gap-3"><i class="fa-solid {{ $sec['icon'] }} text-{{ $sec['color'] }}-500 text-sm"></i><h4 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-[0.2em] italic">{{ $sec['title'] }}</h4></div>
                                            <button type="button" wire:click="{{ $sec['add'] }}" class="px-5 py-1.5 {{ $sec['color'] === 'blue' ? 'bg-blue-600' : ($sec['color'] === 'amber' ? 'bg-amber-500' : 'bg-violet-600') }} text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg active:scale-95">
                                                <i class="fa-solid fa-plus"></i> AGREGAR
                                            </button>
                                        </div>
                                        <div class="space-y-2">
                                            @if($sec['add'] === 'addItemInsumo')
                                                @foreach($sec['items'] as $idx => $item)
                                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center bg-slate-50/50 dark:bg-white/5 p-2 rounded-2xl border border-slate-100 shadow-sm" wire:key="insumo-{{ $idx }}">
                                                        <div class="md:col-span-5 relative"><label class="text-[9px] font-black text-slate-400 uppercase italic ml-1">Producto</label><input type="text" wire:model.live="itemsInsumos.{{ $idx }}.insumo_nombre" wire:input="searchInsumo({{ $idx }}, $event.target.value)" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[10px] font-black uppercase shadow-sm"><div x-show="$wire.showIns && $wire.activeIdx === {{ $idx }}" class="absolute w-full mt-1 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border z-[70] overflow-hidden max-h-40">@foreach($resultsIns as $ri) <div wire:click="selectInsumoItem({{ $idx }}, {{ $ri->id }}, '{{ $ri->nombre }}')" class="p-2.5 hover:bg-blue-500 hover:text-white cursor-pointer border-b text-[9px] font-black uppercase italic">{{ $ri->nombre }}</div> @endforeach</div></div>
                                                        <div class="md:col-span-1 flex flex-col items-center pt-2"><label class="text-[9px] font-black text-slate-400 uppercase italic">PROVE</label><input type="checkbox" wire:click="openAddProvider({{ $idx }})" @if($item['proveedor_id']) checked @endif class="w-5 h-5 text-agri-green border-slate-200 rounded"></div>
                                                        <div class="md:col-span-6 grid grid-cols-11 gap-3 items-center"><div class="col-span-4"><label class="text-[9px] font-black text-slate-400 uppercase text-center block">Cant.</label><input type="number" wire:model.live.blur="itemsInsumos.{{ $idx }}.cantidad" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-2 text-[11px] font-black text-center shadow-sm"></div><div class="col-span-5"><label class="text-[7px] font-black text-slate-400 uppercase text-center block">Costo U.</label><input type="number" step="0.01" wire:model.live.blur="itemsInsumos.{{ $idx }}.costo_unitario" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[11px] font-black text-center shadow-sm"></div><div class="col-span-2 pt-3"><button type="button" wire:click="removeItem('insumo', {{ $idx }})" class="w-8 h-8 text-rose-500 hover:bg-rose-50 rounded-lg"><i class="fa-solid fa-trash-can text-[11px]"></i></button></div></div>
                                                    </div>
                                                @endforeach
                                            @elseif($sec['add'] === 'addItemManoObra')
                                                @foreach($sec['items'] as $idx => $item)
                                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center bg-slate-50/50 dark:bg-white/5 p-2.5 rounded-2xl border" wire:key="mano-{{ $idx }}">
                                                        <div class="md:col-span-4"><label class="text-[7px] font-black text-slate-400 uppercase ml-1">Perfil</label><select wire:model.live="itemsManoObra.{{ $idx }}.tipo_id" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[10px] font-black uppercase"><option value="">Elegir...</option>@foreach($manoObraTipos as $mot) <option value="{{ $mot->id }}">{{ $mot->nombre }}</option> @endforeach</select></div>
                                                        <div class="md:col-span-2"><label class="text-[7px] font-black text-slate-400 uppercase text-center block">Pers.</label><input type="number" wire:model.live.blur="itemsManoObra.{{ $idx }}.cantidad" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-1 text-[10px] font-black text-center"></div>
                                                        <div class="md:col-span-2"><label class="text-[7px] font-black text-slate-400 uppercase text-center block">Días</label><input type="number" wire:model.live.blur="itemsManoObra.{{ $idx }}.dias" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-1 text-[10px] font-black text-center"></div>
                                                        <div class="md:col-span-3"><label class="text-[7px] font-black text-slate-400 uppercase text-center block">Costo D.</label><input type="number" step="0.1" wire:model.live.blur="itemsManoObra.{{ $idx }}.costo_dia" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[10px] font-black text-center"></div>
                                                        <div class="md:col-span-1 pt-3"><button type="button" wire:click="removeItem('mano', {{ $idx }})" class="w-7 h-7 text-rose-500"><i class="fa-solid fa-trash-can text-[10px]"></i></button></div>
                                                    </div>
                                                @endforeach
                                            @else
                                                @foreach($sec['items'] as $idx => $item)
                                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center bg-slate-50/50 dark:bg-white/5 p-2.5 rounded-2xl border" wire:key="maq-{{ $idx }}">
                                                        <div class="md:col-span-6"><label class="text-[7px] font-black text-slate-400 uppercase ml-1">Maquinaria</label><input type="text" wire:model.live="itemsMaquinaria.{{ $idx }}.nombre" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[10px] font-black uppercase"></div>
                                                        <div class="md:col-span-2"><label class="text-[7px] font-black text-slate-400 uppercase text-center block">Hrs.</label><input type="number" wire:model.live.blur="itemsMaquinaria.{{ $idx }}.horas" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-1 text-[10px] font-black text-center"></div>
                                                        <div class="md:col-span-3"><label class="text-[7px] font-black text-slate-400 uppercase text-center block">Inversión T.</label><input type="number" step="0.01" wire:model.live.blur="itemsMaquinaria.{{ $idx }}.costo_total" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[10px] font-black text-center"></div>
                                                        <div class="md:col-span-1 pt-3"><button type="button" wire:click="removeItem('maq', {{ $idx }})" class="w-7 h-7 text-rose-500"><i class="fa-solid fa-trash-can text-[10px]"></i></button></div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="space-y-3 pt-2">
                                <div class="flex items-center gap-3 border-l-4 border-slate-600 pl-3"><h4 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-widest italic">EVIDENCIA TÉCNICA</h4></div>
                                <div class="bg-white dark:bg-slate-800/50 p-3 rounded-[1.5rem] border shadow-sm flex items-center gap-5">
                                    <div class="w-20 h-16 rounded-xl overflow-hidden bg-slate-50 dark:bg-slate-900 border-2 flex items-center justify-center">@if($laborPhoto && method_exists($laborPhoto, 'isPreviewable') && $laborPhoto->isPreviewable()) <img src="{{ $laborPhoto->temporaryUrl() }}" class="w-full h-full object-cover"> @elseif($currentPhotoPath) <img src="{{ Storage::url($currentPhotoPath) }}" class="w-full h-full object-cover"> @else <i class="fa-solid fa-camera text-slate-200 text-lg"></i> @endif</div>
                                    <div class="flex-1 flex flex-col sm:flex-row items-center justify-between gap-3"><p class="text-[10px] font-bold text-slate-400 uppercase italic max-w-[180px]">Sincroniza una foto del avance para el control técnico.</p><div class="relative w-full sm:w-auto"><input type="file" wire:model="laborPhoto" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"/><div class="px-5 py-2 bg-agri-green text-white rounded-xl font-black text-[11px] uppercase tracking-widest shadow-lg flex items-center justify-center gap-2 italic"><i class="fa-solid fa-cloud-arrow-up"></i><span>Subir</span></div></div></div>
                                </div>
                            </div>
                            <div class="pt-4 pb-2">
                                <button type="submit" wire:loading.attr="disabled" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center gap-3 italic">
                                    <i class="fa-solid fa-shield-check text-sm" wire:loading.remove></i><i class="fa-solid fa-spinner fa-spin text-sm" wire:loading></i><span>GUARDAR REGISTRO</span>
                                </button>
                                <p class="text-[9px] text-center text-slate-400 mt-2 font-black uppercase tracking-widest italic opacity-60">Seguridad AgroSys Cloud Habilitada</p>
                            </div>
                        </div>
                    @endif
                </form>
            @endif
        </div>
    </x-modal>

    <!-- MODAL REGISTRO RÁPIDO PROVEEDOR -->
    <x-modal name="modal-add-provider" :show="false">
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10 p-8 space-y-8">
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-white/5 pb-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-500/10 text-blue-500 rounded-xl flex items-center justify-center border border-blue-500/20 shadow-inner">
                        <i class="fa-solid fa-truck-fast text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-[16px] font-black tracking-tight uppercase leading-none italic dark:text-white text-slate-800">NUEVO PROVEEDOR</h3>
                        <p class="text-[8px] opacity-60 uppercase font-black tracking-[0.2em] mt-1 italic dark:text-slate-400 text-slate-500">REGISTRO RÁPIDO DE SOCIO COMERCIAL</p>
                    </div>
                </div>
                <button @click="$dispatch('close')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-white/5 hover:bg-slate-100 transition-all border border-slate-100 dark:border-white/10"><i class="fa-solid fa-xmark text-xl dark:text-white"></i></button>
            </div>

            <form wire:submit.prevent="saveQuickProvider" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Nombre Empresa *</label>
                        <input type="text" wire:model="newProvNombre" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-xs font-black shadow-inner uppercase focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white" placeholder="Ej: Agrosoluciones SAC">
                        @error('newProvNombre') <span class="text-[9px] text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest">RUC</label>
                        <input type="text" wire:model="newProvRuc" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-xs font-black shadow-inner focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white" placeholder="20123456789">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Teléfono</label>
                        <input type="text" wire:model="newProvTelf" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-xs font-black shadow-inner focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white" placeholder="987 654 321">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Especialidad</label>
                        <select wire:model="newProvTipo" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-xs font-black shadow-inner focus:ring-1 focus:ring-blue-500 text-slate-800 dark:text-white">
                            <option value="Insumos">INSUMOS</option>
                            <option value="Maquinaria">MAQUINARIA</option>
                            <option value="Logística">LOGÍSTICA</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-[1.5rem] font-black text-[12px] uppercase tracking-[0.3em] shadow-xl shadow-blue-500/30 hover:bg-blue-700 transition-all flex items-center justify-center gap-3 active:scale-95 italic">
                        <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                        <span>REGISTRAR PROVEEDOR</span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- MODAL VISTA DETALLADA DE LABOR -->
    <x-modal name="modal-view-labor" :show="false" focusable>
        <div class="bg-white dark:bg-[#0a1a19] rounded-[2.5rem] overflow-hidden shadow-2xl border border-slate-100 dark:border-white/5">
            @if($viewingLabor)
                <div class="bg-[#003a38] px-10 py-7 flex justify-between items-center text-white relative overflow-hidden">
                    <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-1/4 -translate-y-1/4">
                        <i class="fa-solid {{ $icons[$viewingLabor->detalleCatalogo->nombre] ?? 'fa-tractor' }} text-[120px]"></i>
                    </div>
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20 shadow-inner backdrop-blur-md">
                            <i class="fa-solid {{ $icons[$viewingLabor->detalleCatalogo->nombre] ?? 'fa-tractor' }} text-2xl text-agri-green"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black italic tracking-tighter uppercase leading-none">{{ $viewingLabor->detalleCatalogo->nombre }}</h3>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] mt-1.5 opacity-60 italic">Reporte Detallado de Operación</p>
                        </div>
                    </div>
                    <button @click="$dispatch('close')" class="w-11 h-11 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 transition-all border border-white/10 relative z-10">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="p-10 space-y-8 max-h-[80vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 border-l-4 border-agri-green pl-4"><h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic">Origen y Destino</h4></div>
                            <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-[1.8rem] space-y-3.5 shadow-inner border border-black/5 dark:border-white/5">
                                <div class="flex items-center justify-between"><span class="text-[9px] font-black text-slate-400 uppercase italic">Terreno</span><span class="text-[11px] font-black text-slate-800 dark:text-slate-200 uppercase italic">{{ $viewingLabor->cultivo->terreno->nombre }}</span></div>
                                <div class="flex items-center justify-between"><span class="text-[9px] font-black text-slate-400 uppercase italic">Cultivo / Lote</span><span class="text-[11px] font-black text-slate-800 dark:text-slate-200 uppercase italic">{{ $viewingLabor->cultivo->nombre_lote }}</span></div>
                                <div class="flex items-center justify-between"><span class="text-[9px] font-black text-slate-400 uppercase italic">Área Aplicada</span><span class="text-[11px] font-black text-agri-green uppercase italic">{{ number_format($viewingLabor->cultivo->area_destinada, 2) }} Ha</span></div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 border-l-4 border-blue-500 pl-4"><h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic">Tiempos y Estado</h4></div>
                            <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-[1.8rem] space-y-3.5 shadow-inner border border-black/5 dark:border-white/5">
                                <div class="flex items-center justify-between"><span class="text-[9px] font-black text-slate-400 uppercase italic">Fecha Registro</span><span class="text-[11px] font-black text-slate-800 dark:text-slate-200 uppercase italic">{{ \Carbon\Carbon::parse($viewingLabor->fecha_realizacion)->format('d/m/Y') }}</span></div>
                                <div class="flex items-center justify-between"><span class="text-[9px] font-black text-slate-400 uppercase italic">Situación</span><span class="px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase italic {{ $viewingLabor->estado === 'Completada' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">{{ $viewingLabor->estado }}</span></div>
                                <div class="flex items-center justify-between"><span class="text-[9px] font-black text-slate-400 uppercase italic">Inversión Final</span><span class="text-[13px] font-black text-blue-600 italic">S/ {{ number_format($viewingLabor->costo_total, 2) }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-8">
                        <div class="flex items-center gap-3 border-l-4 border-violet-500 pl-4">
                            <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic">Recursos y Logística</h4>
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
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                                <!-- Gráfico -->
                                <div class="lg:col-span-6 h-56 relative bg-white/50 dark:bg-slate-900/50 rounded-3xl p-4">
                                    <div data-react-component="agro-bar-chart"
                                         data-props="{{ json_encode(['data' => $chartData]) }}"
                                         class="w-full h-full"
                                         wire:key="view-chart-final-{{ $viewingLabor->id }}-{{ $viewTimestamp }}"></div>
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
                            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 rounded-[2rem] overflow-hidden shadow-2xl">
                                <div class="bg-blue-600 px-8 py-4 flex justify-between items-center text-white">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-boxes-stacked text-lg"></i>
                                        <h5 class="text-[11px] font-black uppercase tracking-[0.2em] italic">Detalle de Insumos / Productos</h5>
                                    </div>
                                    <span class="bg-white/20 px-4 py-1 rounded-full text-[10px] font-black">S/ {{ number_format($cInsumos, 2) }}</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-slate-50 dark:bg-white/5 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                            <tr>
                                                <th class="px-8 py-4">Producto Aplicado</th>
                                                <th class="px-6 py-4">Proveedor</th>
                                                <th class="px-4 py-4 text-center">Cantidad</th>
                                                <th class="px-6 py-4 text-right">Costo Unit.</th>
                                                <th class="px-8 py-4 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-[11px] font-bold italic text-slate-600 dark:text-slate-300">
                                            @foreach($viewingLabor->insumos as $ins)
                                                <tr class="border-t border-slate-50 dark:border-white/5 hover:bg-slate-50/50 transition-colors">
                                                    <td class="px-8 py-4 uppercase">{{ $ins->detalleCatalogo->nombre ?? 'Insumo' }}</td>
                                                    <td class="px-6 py-4 uppercase text-slate-400">{{ $ins->proveedor?->nombre_empresa ?? 'SIN PROVEEDOR' }}</td>
                                                    <td class="px-4 py-4 text-center font-black">{{ $ins->cantidad }} ud.</td>
                                                    <td class="px-6 py-4 text-right">S/ {{ number_format($ins->costo_unitario, 2) }}</td>
                                                    <td class="px-8 py-4 text-right text-slate-900 dark:text-white font-black">S/ {{ number_format(($ins->cantidad * $ins->costo_unitario) + $ins->costo_flete, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <!-- Personal -->
                            @if($viewingLabor->manoDeObra->count() > 0)
                            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 rounded-[2rem] overflow-hidden shadow-sm">
                                <div class="bg-amber-500 px-8 py-4 flex justify-between items-center text-white">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-people-group text-lg"></i>
                                        <h5 class="text-[11px] font-black uppercase tracking-[0.2em] italic">Detalle de Personal / Jornales</h5>
                                    </div>
                                    <span class="bg-white/20 px-4 py-1 rounded-full text-[10px] font-black">S/ {{ number_format($cPersonal, 2) }}</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-slate-50 dark:bg-white/5 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                            <tr>
                                                <th class="px-8 py-4">Perfil Operativo</th>
                                                <th class="px-6 py-4 text-center">N° Pers.</th>
                                                <th class="px-6 py-4 text-center">Días Trab.</th>
                                                <th class="px-6 py-4 text-right">Costo/Día</th>
                                                <th class="px-8 py-4 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-[11px] font-bold italic text-slate-600 dark:text-slate-300">
                                            @foreach($viewingLabor->manoDeObra as $mo)
                                                <tr class="border-t border-slate-50 dark:border-white/5 hover:bg-slate-50/50 transition-colors">
                                                    <td class="px-8 py-4 uppercase">{{ $mo->tipoPersona->nombre }}</td>
                                                    <td class="px-6 py-4 text-center font-black">{{ $mo->cantidad_trabajadores }}</td>
                                                    <td class="px-6 py-4 text-center">{{ $mo->dias_trabajados }} días</td>
                                                    <td class="px-6 py-4 text-right">S/ {{ number_format($mo->costo_por_dia, 2) }}</td>
                                                    <td class="px-8 py-4 text-right text-slate-900 dark:text-white font-black">S/ {{ number_format($mo->subtotal, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <!-- Maquinaria -->
                            @if($viewingLabor->maquinaria->count() > 0)
                            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 rounded-[2rem] overflow-hidden shadow-sm">
                                <div class="bg-violet-600 px-8 py-4 flex justify-between items-center text-white">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-truck-tractor text-lg"></i>
                                        <h5 class="text-[11px] font-black uppercase tracking-[0.2em] italic">Detalle de Maquinaria / Equipos</h5>
                                    </div>
                                    <span class="bg-white/20 px-4 py-1 rounded-full text-[10px] font-black">S/ {{ number_format($cMaq, 2) }}</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-slate-50 dark:bg-white/5 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                            <tr>
                                                <th class="px-8 py-4">Equipo / Vehículo</th>
                                                <th class="px-6 py-4">Labor Realizada</th>
                                                <th class="px-6 py-4 text-center">Horas</th>
                                                <th class="px-8 py-4 text-right">Inversión Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-[11px] font-bold italic text-slate-600 dark:text-slate-300">
                                            @foreach($viewingLabor->maquinaria as $maq)
                                                <tr class="border-t border-slate-50 dark:border-white/5 hover:bg-slate-50/50 transition-colors">
                                                    <td class="px-8 py-4 uppercase font-black text-slate-800 dark:text-slate-100">{{ $maq->nombre_maquinaria }}</td>
                                                    <td class="px-6 py-4 uppercase text-slate-400">{{ $maq->labor_realizada }}</td>
                                                    <td class="px-6 py-4 text-center font-black">{{ $maq->horas_trabajadas }} h</td>
                                                    <td class="px-8 py-4 text-right text-slate-900 dark:text-white font-black">S/ {{ number_format($maq->costo_total, 2) }}</td>
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
                        <div class="space-y-4"><p class="text-[9px] font-black text-slate-400 uppercase italic tracking-widest border-b pb-1.5">Registro Fotográfico</p><div class="aspect-video w-full rounded-[2rem] overflow-hidden border-2 border-white dark:border-white/10 shadow-xl bg-slate-100 dark:bg-white/5">@if($viewingLabor->foto_path)<img src="{{ Storage::url($viewingLabor->foto_path) }}" class="w-full h-full object-cover">@else<div class="w-full h-full flex items-center justify-center text-slate-200"><i class="fa-solid fa-camera text-4xl"></i></div>@endif</div></div>
                        <div class="space-y-4"><p class="text-[9px] font-black text-slate-400 uppercase italic tracking-widest border-b pb-1.5">Bitácora de Observaciones</p><div class="bg-slate-50 dark:bg-white/5 p-6 rounded-[2rem] min-h-[140px] italic text-[11px] font-bold text-slate-600 dark:text-slate-300 shadow-inner">{{ $viewingLabor->observaciones ?: 'Sin anotaciones técnicas registradas para esta labor.' }}</div></div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-black/20 p-8 flex justify-center border-t dark:border-white/5"><button @click="$dispatch('close')" class="px-12 py-3 bg-slate-800 text-white rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-black/20 italic">Cerrar Reporte</button></div>
            @endif
        </div>
    </x-modal>
</div>

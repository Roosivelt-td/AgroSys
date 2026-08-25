<div class="space-y-6 p-2 md:p-1 transition-colors duration-500">

    <!-- CABECERA DE FILTROS -->
    <div class="flex flex-wrap items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-sm relative">
        <div class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative">
                <select wire:model.live="searchType" class="bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 rounded-xl text-[11px] font-black uppercase py-2 px-4 focus:ring-agri-green shadow-sm">
                    <option value="">TODOS LOS TIPOS</option>
                    @foreach($catalogoLabores as $l) <option value="{{ $l->nombre }}">{{ strtoupper($l->nombre) }}</option> @endforeach
                </select>
            </div>
            <div class="relative">
                <select wire:model.live="filterCropId" class="bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 rounded-xl text-[11px] font-black uppercase py-2 px-4 focus:ring-agri-green shadow-sm">
                    <option value="">TODOS LOS CULTIVOS</option>
                    @foreach($misCultivos as $crop) <option value="{{ $crop->id }}">{{ strtoupper($crop->nombre_lote) }}</option> @endforeach
                </select>
            </div>
            <div class="relative">
                <select wire:model.live="filterStatus" class="bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 rounded-xl text-[11px] font-black uppercase py-2 px-4 focus:ring-agri-green shadow-sm">
                    <option value="">TODOS LOS ESTADOS</option>
                    <option value="Completada">COMPLETADA</option>
                    <option value="Pendiente">PENDIENTE</option>
                    <option value="En progreso">EN PROGRESO</option>
                </select>
            </div>
            <div class="flex items-center bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-1.5 space-x-2 shadow-sm">
                <div class="flex items-center space-x-2">
                    <span class="text-[9px] font-black text-slate-400">INICIO:</span>
                    <input type="date" wire:model.live="filterDateStart" class="bg-transparent border-none p-0 text-[10px] font-bold text-slate-600 dark:text-white focus:ring-0 w-28 uppercase">
                </div>
                <div class="w-px h-4 bg-slate-300 dark:bg-white/10"></div>
                <div class="flex items-center space-x-2">
                    <span class="text-[9px] font-black text-slate-400">FIN:</span>
                    <input type="date" wire:model.live="filterDateEnd" class="bg-transparent border-none p-0 text-[10px] font-bold text-slate-600 dark:text-white focus:ring-0 w-28 uppercase">
                </div>
            </div>
            <button wire:click="resetFilters" class="p-2 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-xl hover:bg-slate-200 transition-colors shadow-sm"><i class="fa-solid fa-rotate-left"></i></button>
        </div>
        <button @click="$wire.resetForm(); $dispatch('open-modal', 'modal-labor-manager')"
                class="px-8 py-3 bg-agri-green hover:bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-2 active:scale-95">
            <i class="fa-solid fa-plus text-sm"></i> NUEVA LABOR
        </button>
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

    <!-- LISTADO DE TARJETAS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @foreach($labores as $l)
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-xl border border-slate-100 dark:border-white/5 group relative transition-all" x-data="{ menuOpen: false }">
            <div class="h-40 bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
                @if($l->foto_path) <img src="{{ Storage::url($l->foto_path) }}" class="w-full h-full object-cover">
                @else <div class="w-full h-full flex items-center justify-center text-slate-300 bg-slate-50 dark:bg-[#0a1a19]"><i class="fa-solid fa-tractor text-5xl"></i></div> @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                <div class="absolute top-4 right-4 z-10"><button @click="menuOpen = !menuOpen" @click.away="menuOpen = false" class="w-8 h-8 bg-white/20 backdrop-blur-md border border-white/30 rounded-lg text-white hover:bg-white/40 flex items-center justify-center shadow-lg"><i class="fa-solid fa-ellipsis-vertical text-xs"></i></button>
                    <div x-show="menuOpen" class="absolute right-0 mt-2 w-32 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 dark:border-white/10 overflow-hidden z-20">
                        <button wire:click="edit({{ $l->id }})" class="w-full px-4 py-2 text-left text-[11px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5">Editar</button>
                        <button wire:click="delete({{ $l->id }})" wire:confirm="¿Borrar?" class="w-full px-4 py-2 text-left text-[11px] font-black uppercase text-rose-500 hover:bg-rose-50">Borrar</button>
                    </div>
                </div>
                <div class="absolute bottom-4 left-6 right-6 flex justify-between items-end text-white">
                    <div class="min-w-0 flex-1 pr-2"><h3 class="text-[16px] font-black italic uppercase leading-none truncate tracking-tighter">{{ $l->detalleCatalogo->nombre }}</h3><p class="text-[10px] font-bold uppercase opacity-80 mt-1 truncate tracking-widest">{{ $l->cultivo->nombre_lote }}</p></div>
                    <div class="px-2 py-0.5 bg-agri-green rounded text-[8px] font-black italic shadow-lg uppercase border border-white/20">{{ $l->estado }}</div>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between text-[11px] font-bold italic text-slate-600 dark:text-slate-400"><span>{{ $l->fecha_realizacion->format('d/m/Y') }}</span><span>S/ {{ number_format($l->costo_total,2) }}</span></div>
                @if($l->observaciones) <p class="text-[9px] font-bold text-slate-400 uppercase italic line-clamp-1 border-t pt-2">{{ $l->observaciones }}</p> @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-8">{{ $labores->links() }}</div>

    <!-- MODAL LABOR MANAGER -->
    <x-modal name="modal-labor-manager" :show="false" focusable>
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10"
             x-data="{ showStatus: false, showLands: false, showCats: false, showCrops: false }">

            <!-- HEADER COMPACTO -->
            <div class="bg-[#003a38] px-8 py-5 flex justify-between items-center text-white border-b border-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/10 shadow-inner">
                        <i class="fa-solid fa-list-check text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-[16px] font-black tracking-tight uppercase leading-none italic">
                            @if($step === 1) SELECCIONAR LABOR @else NUEVA LABOR: {{ strtoupper($catalogoLabores->find($catalogo_labor_id)->nombre ?? '') }} @endif
                        </h3>
                        <p class="text-[8px] opacity-60 uppercase font-black tracking-[0.2em] mt-1 italic">GESTIÓN DE PROCESOS OPERATIVOS</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @if($step === 2)
                        <button type="button" wire:click="backToGrid" class="px-4 py-1.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                            <i class="fa-solid fa-rotate-left"></i> Cambiar
                        </button>
                    @endif
                    <div class="bg-black/20 px-3 py-1.5 rounded-xl border border-white/5 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-day text-[10px] text-agri-green"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest opacity-80 italic">{{ date('d-m-Y') }}</span>
                    </div>
                    <button @click="$dispatch('close')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 transition-all border border-white/10"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
            </div>

            @if($step === 1)
                <!-- PASO 1: GRID 3x3 -->
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
                <!-- PASO 2: FORMULARIO COMPACTO (IMG 2 STYLE) -->
                <form wire:submit.prevent="save" class="max-h-[85vh] overflow-y-auto custom-scrollbar bg-white dark:bg-slate-900 flex flex-col">

                    <div class="px-8 pt-8 pb-4 space-y-6">
                        <div class="flex items-center gap-3 border-l-4 border-agri-green pl-3">
                            <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight italic">IDENTIFICACIÓN DEL CULTIVO</h4>
                        </div>

                        <!-- Fila Superior: Estado y Terreno (Basado en imagen) -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-x-6 gap-y-4">
                            <div class="md:col-span-4 space-y-1">
                                <label class="text-[9px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-flag text-agri-green"></i> ESTADO</label>
                                <select wire:model.live="selStatus" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-xl p-2.5 text-[11px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green">
                                    <option value="">Seleccionar...</option>
                                    @foreach(['TODOS','En proceso','Completada','Perdido'] as $st) <option value="{{ $st }}">{{ $st }}</option> @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-8 space-y-1">
                                <label class="text-[9px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-location-dot text-agri-green"></i> TERRENO / PARCELA</label>
                                <select wire:model.live="selLandId"
                                        @if(!$selStatus) disabled @endif
                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-xl p-2.5 text-[11px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green {{ !$selStatus ? 'opacity-30 cursor-not-allowed' : '' }}">
                                    <option value="">Seleccionar parcela...</option>
                                    @foreach($resultsLands as $r) <option value="{{ $r->id }}">{{ $r->nombre }} - {{ $r->hectareas }} Ha</option> @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Base: Tipo de Cultivo, Variedad y Cultivo Final (Campaign) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6 mt-2">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-seedling text-agri-green text-[7px]"></i> TIPO DE CULTIVO</label>
                                <select wire:model.live="selCatId"
                                        @if(!$selLandId) disabled @endif
                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-xl p-2.5 text-[11px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green {{ !$selLandId ? 'opacity-30 cursor-not-allowed' : '' }}">
                                    <option value="">Seleccionar...</option>
                                    @foreach($resultsCats as $r) <option value="{{ $r->id }}">{{ $r->nombre }}</option> @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-tags text-agri-green text-[7px]"></i> VARIEDAD</label>
                                <div class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-xl py-2 px-3 text-[11px] font-bold text-slate-400 uppercase italic min-h-[38px] flex items-center shadow-inner">
                                    {{ $selVarName ?: '---' }}
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase text-slate-400 flex items-center gap-2"><i class="fa-solid fa-leaf text-agri-green text-[7px]"></i> CULTIVO (LOTE)</label>
                                <select wire:model.live="cultivo_id"
                                        @if(!$selCatId) disabled @endif
                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-white/5 rounded-xl p-2.5 text-[11px] font-bold text-slate-800 dark:text-white focus:ring-1 focus:ring-agri-green {{ !$selCatId ? 'opacity-30 cursor-not-allowed' : '' }}">
                                    <option value="">Elegir lote activo...</option>
                                    @foreach($resultsCrops as $r) <option value="{{ $r->id }}">{{ $r->nombre_lote }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Detalle de la Selección (Dinámico) -->
                    <div class="px-8 py-4">
                        <div class="bg-slate-50 dark:bg-white/5 rounded-2xl p-5 border border-slate-100 dark:border-white/5 shadow-inner">
                            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-white/5 pb-2">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-list-check text-agri-green"></i>
                                    <h4 class="text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest italic leading-none">DETALLE DE LA SELECCIÓN</h4>
                                </div>
                                <span class="bg-slate-200 dark:bg-white/10 px-2.5 py-0.5 rounded-lg text-[7px] font-black uppercase text-slate-500">resumen</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <!-- 1. ESTADO -->
                                <div class="flex items-center gap-4 animate-in zoom-in-95">
                                    <div class="w-8 h-8 bg-agri-green text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg">1</div>
                                    <div class="flex flex-col">
                                        <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest">Estado</p>
                                        <p class="text-[10px] font-bold text-slate-900 dark:text-white uppercase">{{ $selStatus ?: '---' }}</p>
                                    </div>
                                </div>
                                <!-- 2. TERRENO -->
                                <div class="flex items-center gap-4 animate-in zoom-in-95">
                                    <div class="w-8 h-8 bg-agri-green text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg">2</div>
                                    <div class="flex flex-col">
                                        <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest">Terreno</p>
                                        <p class="text-[10px] font-bold text-slate-900 dark:text-white uppercase truncate max-w-[120px]">{{ $landNombreSelected ?: '---' }}</p>
                                    </div>
                                </div>
                                <!-- 3. CULTIVO (LOTE) -->
                                <div class="flex items-center gap-4 animate-in zoom-in-95">
                                    <div class="w-8 h-8 bg-agri-green text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg">3</div>
                                    <div class="flex flex-col">
                                        <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest">Cultivo</p>
                                        <p class="text-[10px] font-bold text-slate-900 dark:text-white uppercase truncate max-w-[120px]">{{ $cropNombreSelected ?: '---' }}</p>
                                    </div>
                                </div>
                                <!-- 4. VARIEDAD -->
                                <div class="flex items-center gap-4 animate-in zoom-in-95">
                                    <div class="w-8 h-8 bg-agri-green text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg">4</div>
                                    <div class="flex flex-col">
                                        <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest">Variedad</p>
                                        <p class="text-[10px] font-bold text-slate-900 dark:text-white uppercase truncate max-w-[120px]">{{ $selVarName ?: '---' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($cultivo_id)
                        <div class="px-8 pb-10 space-y-10 animate-in fade-in duration-500">
                            <!-- 2. ANÁLISIS ECONÓMICO -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3 border-l-4 border-blue-500 pl-3"><h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">ANÁLISIS ECONÓMICO GENERAL</h4></div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="bg-emerald-50 dark:bg-emerald-500/10 p-5 rounded-2xl border-2 border-emerald-500/20 shadow-inner"><p class="text-[9px] font-black text-emerald-600 uppercase mb-1 leading-none italic tracking-widest">Costo Total</p><p class="text-3xl font-black text-slate-900 dark:text-white italic tracking-tighter">S/ {{ number_format($costo_total, 2) }}</p></div>
                                    <div class="p-5 border-2 border-slate-50 dark:border-white/5 rounded-2xl flex flex-col justify-center bg-slate-50/50 shadow-sm"><p class="text-[9px] font-black text-slate-400 uppercase mb-1 leading-none italic tracking-widest">Mano de Obra</p><p class="text-xl font-black text-slate-700 dark:text-slate-300 italic">S/ {{ number_format($costo_mano_obra_total, 2) }}</p></div>
                                    <div class="p-5 border-2 border-slate-50 dark:border-white/5 rounded-2xl flex flex-col justify-center bg-slate-50/50 shadow-sm"><p class="text-[9px] font-black text-slate-400 uppercase mb-1 leading-none italic tracking-widest">Maquinaria</p><p class="text-xl font-black text-slate-700 dark:text-slate-300 italic">S/ {{ number_format($costo_maquinaria_total, 2) }}</p></div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="space-y-2"><label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Fecha Realización</label><input type="date" wire:model="fecha_realizacion" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-xs font-black shadow-inner uppercase"></div>
                                    <div class="space-y-2"><label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Estado Labor</label><select wire:model="estado" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-xs font-black shadow-inner uppercase tracking-widest"><option value="Pendiente">PENDIENTE</option><option value="En progreso">EN PROGRESO</option><option value="Completada">COMPLETADA</option></select></div>
                                    <div class="space-y-2 lg:col-span-1"><label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Notas</label><textarea wire:model="observaciones" rows="1" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl p-3 text-xs font-black shadow-inner min-h-[50px]"></textarea></div>
                                </div>
                            </div>

                            <!-- DINÁMICOS -->
                            <div class="space-y-12">
                                @php $sections = [
                                    ['title' => 'Insumos / Productos', 'add' => 'addItemInsumo', 'items' => $itemsInsumos, 'color' => 'blue', 'icon' => 'fa-boxes-stacked'],
                                    ['title' => 'Personal / Jornales', 'add' => 'addItemManoObra', 'items' => $itemsManoObra, 'color' => 'amber', 'icon' => 'fa-people-group'],
                                    ['title' => 'Maquinaria / Equipos', 'add' => 'addItemMaquinaria', 'items' => $itemsMaquinaria, 'color' => 'purple', 'icon' => 'fa-truck-tractor']
                                ]; @endphp
                                @foreach($sections as $sec)
                                    <div class="space-y-6">
                                        <div class="flex justify-between items-center border-b-2 border-slate-50 dark:border-white/5 pb-2">
                                            <div class="flex items-center gap-4"><i class="fa-solid {{ $sec['icon'] }} text-{{ $sec['color'] }}-500"></i><h4 class="text-[12px] font-black text-slate-800 dark:text-white uppercase tracking-[0.2em] italic">{{ $sec['title'] }}</h4></div>
                                            <button type="button" wire:click="{{ $sec['add'] }}" class="px-6 py-2 bg-{{ $sec['color'] }}-500 hover:bg-{{ $sec['color'] }}-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-{{ $sec['color'] }}-500/20 active:scale-95 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i> AGREGAR</button>
                                        </div>
                                        @if(count($sec['items']) > 0)
                                            <div class="space-y-4">
                                                @if($sec['add'] === 'addItemInsumo')
                                                    @foreach($sec['items'] as $idx => $item)
                                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center bg-slate-50/50 dark:bg-white/5 p-3 rounded-[1.5rem] border border-slate-100 dark:border-white/5 shadow-sm animate-in slide-in-from-left-2" wire:key="insumo-{{ $idx }}">
                                                            <!-- 2. Producto + Proveedor Texto Suelto -->
                                                            <div class="md:col-span-5 space-y-0.5 relative">
                                                                <label class="text-[7px] font-black text-slate-400 uppercase italic ml-1 leading-none">Producto / Insumo</label>
                                                                <div x-data="{ open: false }">
                                                                    <input type="text"
                                                                           wire:model.live="itemsInsumos.{{ $idx }}.insumo_nombre"
                                                                           wire:input="searchInsumo({{ $idx }}, $event.target.value)"
                                                                           @focus="open = true" @click.away="open = false"
                                                                           class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[10px] font-black uppercase shadow-sm focus:ring-1 focus:ring-blue-500"
                                                                           placeholder="Escribir producto...">

                                                                    <div x-show="open && $wire.showIns && $wire.activeIdx === {{ $idx }}"
                                                                         class="absolute w-full mt-1 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 dark:border-white/10 z-[70] overflow-hidden max-h-40 overflow-y-auto">
                                                                        @foreach($resultsIns as $ri)
                                                                            <div wire:click="selectInsumoItem({{ $idx }}, {{ $ri->id }}, '{{ $ri->nombre }}')"
                                                                                 @click="open = false"
                                                                                 class="p-2.5 hover:bg-blue-500 hover:text-white cursor-pointer border-b last:border-0 border-slate-50 dark:border-white/5 text-[9px] font-black uppercase italic transition-colors">
                                                                                {{ $ri->nombre }}
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>

                                                                <!-- Nombre del Proveedor (Area 2) -->
                                                                @if($item['proveedor_id'])
                                                                    <div class="flex items-center gap-1.5 ml-1 mt-0.5 animate-in fade-in duration-300">
                                                                        <span class="text-[9px] font-bold text-rose-500 uppercase italic leading-none">{{ $item['proveedor_nombre'] }}</span>
                                                                        <button type="button" wire:click="$set('itemsInsumos.{{ $idx }}.proveedor_id', null)" class="text-slate-300 hover:text-rose-500"><i class="fa-solid fa-xmark text-[7px]"></i></button>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <!-- 1. Checkbox Proveedor -->
                                                            <div class="md:col-span-1 flex flex-col items-center justify-center pt-2">
                                                                <label class="text-[7px] font-black text-slate-400 uppercase italic leading-none mb-1">PROVE</label>
                                                                <input type="checkbox"
                                                                       wire:click="openAddProvider({{ $idx }})"
                                                                       @if($item['proveedor_id']) checked @endif
                                                                       class="w-5 h-5 text-agri-green border-slate-200 rounded focus:ring-agri-green cursor-pointer">
                                                            </div>

                                                            <!-- Area 1 Rediseñada: Cantidad, Costo y Eliminar (100% Ancho) -->
                                                            <div class="md:col-span-6 grid grid-cols-11 gap-3 items-center">
                                                                <div class="col-span-4 space-y-0.5">
                                                                    <label class="text-[7px] font-black text-slate-400 uppercase italic text-center block leading-none">Cant.</label>
                                                                    <input type="number" wire:model.live.blur="itemsInsumos.{{ $idx }}.cantidad" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-2 text-[11px] font-black text-center shadow-sm">
                                                                </div>
                                                                <div class="col-span-5 space-y-0.5">
                                                                    <label class="text-[7px] font-black text-slate-400 uppercase italic text-center block leading-none">Costo U.</label>
                                                                    <input type="number" step="0.01" wire:model.live.blur="itemsInsumos.{{ $idx }}.costo_unitario" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[11px] font-black text-center shadow-sm">
                                                                </div>
                                                                <div class="col-span-2 flex justify-center pt-3">
                                                                    <button type="button" wire:click="removeItem('insumo', {{ $idx }})" class="w-8 h-8 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-lg transition-all shadow-sm"><i class="fa-solid fa-trash-can text-[11px]"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @elseif($sec['add'] === 'addItemManoObra')
                                                    @foreach($sec['items'] as $idx => $item)
                                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-slate-50/50 dark:bg-white/5 p-3 rounded-[1.5rem] border border-slate-100 dark:border-white/5 shadow-sm animate-in slide-in-from-left-2" wire:key="mano-{{ $idx }}">
                                                            <div class="md:col-span-4 space-y-0.5">
                                                                <label class="text-[7px] font-black text-slate-400 uppercase italic leading-none ml-1">Perfil Operativo</label>
                                                                <select wire:model.live="itemsManoObra.{{ $idx }}.tipo_id" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[10px] font-black uppercase shadow-sm italic focus:ring-1 focus:ring-amber-500">
                                                                    <option value="">Elegir...</option>
                                                                    @foreach($manoObraTipos as $mot) <option value="{{ $mot->id }}">{{ $mot->nombre }}</option> @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="md:col-span-2 space-y-0.5">
                                                                <label class="text-[7px] font-black text-slate-400 uppercase italic text-center block leading-none">Pers.</label>
                                                                <input type="number" wire:model.live.blur="itemsManoObra.{{ $idx }}.cantidad" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-1 text-[10px] font-black text-center shadow-sm">
                                                            </div>
                                                            <div class="md:col-span-2 space-y-0.5">
                                                                <label class="text-[7px] font-black text-slate-400 uppercase italic text-center block leading-none">Días</label>
                                                                <input type="number" wire:model.live.blur="itemsManoObra.{{ $idx }}.dias" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-1 text-[10px] font-black text-center shadow-sm">
                                                            </div>
                                                            <div class="md:col-span-3 space-y-0.5">
                                                                <label class="text-[7px] font-black text-slate-400 uppercase italic text-center block leading-none">Costo D.</label>
                                                                <input type="number" step="0.1" wire:model.live.blur="itemsManoObra.{{ $idx }}.costo_dia" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[10px] font-black text-center shadow-sm">
                                                            </div>
                                                            <div class="md:col-span-1 flex justify-center pb-1">
                                                                <button type="button" wire:click="removeItem('mano', {{ $idx }})" class="w-8 h-8 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-lg transition-all shadow-sm"><i class="fa-solid fa-trash-can text-[10px]"></i></button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    @foreach($sec['items'] as $idx => $item)
                                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-slate-50/50 dark:bg-white/5 p-3 rounded-[1.5rem] border border-slate-100 dark:border-white/5 shadow-sm animate-in slide-in-from-left-2" wire:key="maq-{{ $idx }}">
                                                            <div class="md:col-span-6 space-y-0.5">
                                                                <label class="text-[7px] font-black text-slate-400 uppercase italic leading-none ml-1">Maquinaria / Equipo</label>
                                                                <input type="text" wire:model.live="itemsMaquinaria.{{ $idx }}.nombre" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[10px] font-black uppercase shadow-sm italic focus:ring-1 focus:ring-purple-500" placeholder="Marca/Modelo">
                                                            </div>
                                                            <div class="md:col-span-2 space-y-0.5">
                                                                <label class="text-[7px] font-black text-slate-400 uppercase italic text-center block leading-none">Hrs.</label>
                                                                <input type="number" wire:model.live.blur="itemsMaquinaria.{{ $idx }}.horas" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-1 text-[10px] font-black text-center shadow-sm">
                                                            </div>
                                                            <div class="md:col-span-3 space-y-0.5">
                                                                <label class="text-[7px] font-black text-slate-400 uppercase italic text-center block leading-none">Inversión T.</label>
                                                                <input type="number" step="0.01" wire:model.live.blur="itemsMaquinaria.{{ $idx }}.costo_total" wire:change="calculateTotals" class="w-full bg-white dark:bg-slate-900 border-none rounded-xl py-1.5 px-3 text-[10px] font-black text-center shadow-sm">
                                                            </div>
                                                            <div class="md:col-span-1 flex justify-center pb-1">
                                                                <button type="button" wire:click="removeItem('maq', {{ $idx }})" class="w-8 h-8 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-lg transition-all shadow-sm"><i class="fa-solid fa-trash-can text-[10px]"></i></button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- EVIDENCIA -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3 border-l-4 border-slate-400 pl-3"><h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">EVIDENCIA FOTOGRÁFICA</h4></div>
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-[2.5rem] border-2 border-dashed border-slate-200 flex flex-col md:flex-row items-center gap-10">
                                    <div class="w-56 h-40 rounded-3xl overflow-hidden bg-white dark:bg-slate-800 shadow-2xl border-4 border-white shrink-0 relative">@if($laborPhoto && method_exists($laborPhoto, 'isPreviewable') && $laborPhoto->isPreviewable()) <img src="{{ $laborPhoto->temporaryUrl() }}" class="w-full h-full object-cover"> @elseif($currentPhotoPath) <img src="{{ Storage::url($currentPhotoPath) }}" class="w-full h-full object-cover"> @else <div class="w-full h-full flex flex-col items-center justify-center text-slate-200"><i class="fa-solid fa-camera-retro text-4xl mb-2 opacity-30"></i><p class="text-[8px] font-black uppercase opacity-40">Área de captura</p></div> @endif</div>
                                    <div class="flex-1 space-y-5 text-center md:text-left"><p class="text-[12px] font-bold text-slate-500 uppercase italic tracking-tighter leading-relaxed">Carga una fotografía nítida del avance para garantizar el control técnico de calidad.</p><div class="relative"><input type="file" wire:model="laborPhoto" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"/><div class="px-10 py-5 bg-agri-green text-white rounded-[1.5rem] font-black text-[11px] uppercase tracking-[0.2em] text-center shadow-xl shadow-emerald-500/30 hover:bg-emerald-600 transition-all flex items-center justify-center gap-3"><i class="fa-solid fa-cloud-arrow-up text-lg"></i><span>Subir Imagen</span></div></div></div>
                                </div>
                            </div>

                            <!-- FINALIZAR (ESTILO MEJORADO) -->
                            <div class="bg-slate-950 p-12 rounded-[4rem] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] border-t border-white/5 relative overflow-hidden group">
                                <div class="absolute inset-0 bg-gradient-to-r from-agri-green/10 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
                                <button type="submit" wire:loading.attr="disabled" class="w-full py-8 bg-agri-green text-white rounded-[3.5rem] font-black text-2xl uppercase tracking-[0.5em] shadow-[0_20px_60px_-10px_rgba(16,185,129,0.5)] hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center gap-8 italic relative z-10">
                                    <i class="fa-solid fa-shield-check text-3xl" wire:loading.remove></i>
                                    <i class="fa-solid fa-spinner fa-spin text-3xl" wire:loading></i>
                                    <span>GUARDAR REGISTRO</span>
                                </button>
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
</div>

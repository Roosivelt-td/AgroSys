<div class="space-y-8 p-4 md:p-1 transition-colors duration-500">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-100 dark:border-white/5 pb-6">
        <div class="flex items-center space-x-5">
            <div class="w-16 h-16 bg-[#003a38] rounded-2xl flex items-center justify-center shadow-2xl border border-white/10">
                <i class="fa-solid fa-seedling text-agri-green text-3xl"></i>
            </div>
            <div>
                <h2 class="text-4xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">Gestión de Cultivos</h2>
                <p class="text-[10px] text-agri-green font-black uppercase tracking-[0.3em] mt-2 italic">Control de Campañas y Lotes</p>
            </div>
        </div>
        <button @click="$wire.resetForm(); $dispatch('open-modal', 'modal-crop-manager')" class="px-10 py-3 bg-agri-green text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all italic">
            <i class="fa-solid fa-plus mr-2"></i> Nuevo Registro
        </button>
    </div>

    <!-- Filtros Searchables -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 bg-white dark:bg-agri-d_bg p-5 rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-2xl">
        <div class="relative w-full">
            <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-slate-400"><i class="fa-solid fa-mountain-sun text-xs"></i></span>
            <input list="terrenos-list" wire:model.live.debounce.300ms="searchTerreno" placeholder="ESCRIBE TERRENO..." class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-[11px] font-black uppercase focus:ring-4 focus:ring-agri-green/10 outline-none transition-all italic">
            <datalist id="terrenos-list">@foreach($misTerrenos as $t) <option value="{{ $t->nombre }}"> @endforeach</datalist>
        </div>
        <div class="relative w-full">
            <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-slate-400"><i class="fa-solid fa-leaf text-xs"></i></span>
            <input list="crops-catalog-list" wire:model.live.debounce.300ms="searchCultivo" placeholder="BUSCAR CULTIVO..." class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-[11px] font-black uppercase focus:ring-4 focus:ring-agri-green/10 outline-none transition-all italic">
            <datalist id="crops-catalog-list">@foreach($catalogo as $cat) <option value="{{ $cat->nombre }}"> @endforeach</datalist>
        </div>
        <div class="relative w-full">
            <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-slate-400"><i class="fa-solid fa-tags text-xs"></i></span>
            <input wire:model.live.debounce.300ms="searchVariedad" placeholder="BUSCAR VARIEDAD..." class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-[11px] font-black uppercase focus:ring-4 focus:ring-agri-green/10 outline-none transition-all italic">
        </div>
        <button wire:click="resetFilters" class="px-8 py-4 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all italic shadow-sm"><i class="fa-solid fa-undo-alt mr-2"></i> Reset</button>
    </div>

    <!-- Grid de Cultivos Premium -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
        @foreach($cultivos as $cultivo)
        @php
            $temp = rand(18, 26); $hmd = rand(55, 75); // Ficticios
            $siembra = \Carbon\Carbon::parse($cultivo->fecha_siembra);
            $cosecha = $cultivo->fecha_cosecha_estimada ? \Carbon\Carbon::parse($cultivo->fecha_cosecha_estimada) : $siembra->copy()->addMonths(4);
            $totalDias = $siembra->diffInDays($cosecha);
            $diasTranscurridos = $siembra->diffInDays(now(), false);
            $progreso = $totalDias > 0 ? max(0, min(100, ($diasTranscurridos / $totalDias) * 100)) : 0;
            $colorProgreso = $progreso < 30 ? 'bg-emerald-300' : ($progreso < 70 ? 'bg-amber-400' : 'bg-agri-green');
        @endphp

        <div class="bg-white dark:bg-agri-d_bg rounded-[3rem] overflow-hidden shadow-2xl border border-slate-100 dark:border-white/5 group relative transition-all duration-700 hover:-translate-y-2" x-data="{ menuOpen: false }">

            <!-- IMAGEN CON DATOS INTEGRADOS -->
            <div class="h-72 bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
                @if($cultivo->foto_path)
                    <img src="{{ Storage::url($cultivo->foto_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-200 bg-slate-50 dark:bg-white/5"><i class="fa-solid fa-leaf text-8xl"></i></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/30 to-transparent"></div>

                <!-- 1. Clima (Fondo Blanco) -->
                <div class="absolute top-5 left-5 flex items-center space-x-2 bg-white px-3.5 py-1.5 rounded-xl shadow-2xl">
                    <i class="fa-solid fa-cloud-sun text-amber-500 text-xs"></i>
                    <span class="text-[10px] font-black text-slate-800 italic uppercase leading-none">{{ $temp }}°C | {{ $hmd }}% HR</span>
                </div>

                <!-- 2. Gastos + Ver Labores (Top Right) -->
                <div class="absolute top-5 right-14 flex items-center">
                    <a href="#" class="px-3 py-1.5 bg-emerald-500/20 backdrop-blur-md border border-white/30 rounded-xl text-white text-[9px] font-black uppercase tracking-widest hover:bg-agri-green transition-all shadow-lg">
                        Ver Labores <i class="fa-solid fa-arrow-right-long ml-1 animate-pulse"></i>
                    </a>
                </div>

                <!-- Menú "..." -->
                <div class="absolute top-5 right-5">
                    <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false" class="w-8 h-8 bg-white/10 backdrop-blur-md border border-white/20 rounded-lg text-white flex items-center justify-center shadow-lg hover:bg-white/30 transition-all">
                        <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                    </button>
                    <div x-show="menuOpen" x-transition class="absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden" x-cloak>
                        <button wire:click="edit({{ $cultivo->id }})" class="w-full px-6 py-4 text-left text-[11px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition-all italic">EDITAR</button>
                        <button wire:click="delete({{ $cultivo->id }})" wire:confirm="¿Borrar?" class="w-full px-6 py-4 text-left text-[11px] font-black uppercase text-rose-500 hover:bg-rose-50 transition-all italic">BORRAR</button>
                    </div>
                </div>

                <!-- DATOS TÉCNICOS (Sobre la imagen) -->
                <div class="absolute bottom-5 left-7 right-7 text-white space-y-4">
                    <div class="flex justify-between items-end">
                        <div class="min-w-0 flex-1 pr-2">
                            <h3 class="text-2xl font-black italic tracking-tighter uppercase leading-none">{{ $cultivo->detalleCatalogo->nombre }}</h3>
                            <p class="text-[11px] font-black text-emerald-400 uppercase tracking-widest italic mt-1">{{ $cultivo->variedad ?: 'VAR. GENERICA' }}</p>
                        </div>
                        <div class="px-3 py-1.5 bg-agri-green rounded-lg text-[11px] font-black italic shadow-lg shrink-0 border border-white/20">{{ number_format($cultivo->area_destinada, 2) }} ha</div>
                    </div>

                    <!-- Fila de Inversión, Riego y Lugar -->
                    <div class="grid grid-cols-3 gap-2 pt-3 border-t border-white/20">
                        <div class="flex flex-col">
                            <span class="text-[7px] font-black text-white/50 uppercase tracking-widest">Inversión Actual</span>
                            <span class="text-[12px] font-black italic text-amber-400">S/ {{ number_format($cultivo->total_inversion, 2) }}</span>
                        </div>
                        <div class="flex flex-col border-l border-white/10 pl-2">
                            <span class="text-[7px] font-black text-white/50 uppercase tracking-widest">Riego</span>
                            <span class="text-[10px] font-bold italic opacity-90 truncate">{{ $cultivo->terreno->fuente_agua }}</span>
                        </div>
                        <div class="flex flex-col border-l border-white/10 pl-2 text-right">
                            <span class="text-[7px] font-black text-white/50 uppercase tracking-widest">Lugar</span>
                            <a href="{{ route('admin.terrenos') }}" class="text-[10px] font-bold italic opacity-90 hover:text-agri-green transition-colors truncate block">
                                {{ $cultivo->terreno->nombre }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LÍNEA DE TIEMPO FENOLÓGICA (Único elemento inferior) -->
            <div class="px-8 py-7 space-y-3">
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

    <!-- MODAL DE REGISTRO (CONSERVANDO DISEÑO COMPLETO) -->
    <x-modal name="modal-crop-manager" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg rounded-[2.5rem] overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <div class="bg-[#003a38] px-10 py-6 flex justify-between items-center text-white">
                <h3 class="text-2xl font-black italic tracking-tighter uppercase">{{ $cropId ? 'Actualizar Ficha Técnica' : 'Nueva Campaña AgroSys' }}</h3>
                <button @click="$dispatch('close')" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <form wire:submit.prevent="save" class="p-10 space-y-8 max-h-[85vh] overflow-y-auto custom-scrollbar">
                <!-- Secciones de búsqueda visual mantenidas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <x-input-label :value="__('1. Seleccionar Terreno *')" class="text-[10px] font-black uppercase text-agri-green tracking-widest" />
                        @if($terreno_id && $selectedTerrenoModel)
                            <div class="flex items-center justify-between bg-emerald-50 dark:bg-white/5 border-2 border-emerald-500/30 rounded-2xl p-4 animate-in zoom-in-95">
                                <div class="flex items-center space-x-4 overflow-hidden">
                                    <img src="{{ $selectedTerrenoModel->foto_path ? Storage::url($selectedTerrenoModel->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($selectedTerrenoModel->nombre).'&background=003a38&color=fff' }}" class="w-12 h-12 rounded-xl object-cover shadow-md">
                                    <div class="min-w-0"><p class="text-[11px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $selectedTerrenoModel->nombre }}</p></div>
                                </div>
                                <button type="button" wire:click="$set('terreno_id', null)" class="text-slate-400 hover:text-rose-500"><i class="fa-solid fa-rotate-right"></i></button>
                            </div>
                        @else
                            <input type="text" wire:model.live="queryTerreno" @focus="showTerrenos = true" @click.away="showTerrenos = false" class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-xs font-bold p-5 shadow-inner" placeholder="Escribe el terreno...">
                            <div x-show="showTerrenos" class="absolute w-full mt-2 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 z-50 overflow-hidden" x-cloak>
                                @foreach($resultsTerrenos as $t)
                                <div wire:click="selectTerreno({{ $t->id }}, '{{ $t->nombre }}', {{ $t->disponible }})" @click="showTerrenos = false" class="flex items-center space-x-4 p-4 hover:bg-emerald-50 cursor-pointer border-b last:border-0">
                                    <img src="{{ $t->foto_path ? Storage::url($t->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($t->nombre) }}" class="w-12 h-12 rounded-xl object-cover shadow-sm">
                                    <div class="min-w-0 flex-1"><p class="text-[12px] font-black uppercase truncate">{{ $t->nombre }}</p><p class="text-[9px] font-bold text-emerald-600">Dip: {{ number_format($t->disponible, 2) }} ha</p></div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <x-input-label :value="__('2. Tipo de Cultivo *')" class="text-[10px] font-black uppercase text-agri-green tracking-widest" />
                        @if($catalogo_cultivo_id && $selectedCultivoModel)
                            <div class="flex items-center justify-between bg-blue-50 dark:bg-white/5 border-2 border-blue-500/30 rounded-2xl p-4 animate-in zoom-in-95">
                                <div class="flex items-center space-x-4 overflow-hidden">
                                    <img src="{{ $selectedCultivoModel->foto_path ? Storage::url($selectedCultivoModel->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($selectedCultivoModel->nombre).'&background=00ba2e&color=fff' }}" class="w-12 h-12 rounded-xl object-cover shadow-md">
                                    <p class="text-[11px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $selectedCultivoModel->nombre }}</p>
                                </div>
                                <button type="button" wire:click="$set('catalogo_cultivo_id', null)" class="text-slate-400 hover:text-rose-500"><i class="fa-solid fa-rotate-right"></i></button>
                            </div>
                        @else
                            <input type="text" wire:model.live="queryCultivo" @focus="showCultivos = true" @click.away="showCultivos = false" class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-xs font-bold p-5 shadow-inner" placeholder="Busca en el catálogo...">
                            <div x-show="showCultivos" class="absolute w-full mt-2 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 z-50 overflow-hidden" x-cloak>
                                @foreach($resultsCatalogo as $cat)
                                <div wire:click="selectCultivo({{ $cat->id }}, '{{ $cat->nombre }}')" @click="showCultivos = false" class="flex items-center space-x-4 p-4 hover:bg-blue-50 cursor-pointer border-b last:border-0">
                                    <img src="{{ $cat->foto_path ? Storage::url($cat->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($cat->nombre).'&background=00ba2e&color=fff' }}" class="w-10 h-10 rounded-xl object-cover shadow-sm">
                                    <p class="text-[12px] font-black uppercase">{{ $cat->nombre }}</p>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Los demás campos técnicos se mantienen abajo según la tabla img -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-slate-50">
                    <div class="space-y-1.5"><x-input-label :value="__('Variedad del Producto')" class="text-[10px] font-black uppercase text-slate-400" /><x-text-input wire:model.live="variedad" type="text" class="block w-full uppercase" placeholder="Ej: Morada, Canchan..." /></div>
                    <div class="space-y-1.5"><x-input-label :value="__('Nombre de Lote (Auto)')" class="text-[10px] font-black text-slate-400" /><x-text-input wire:model="nombre_lote" type="text" readonly class="block w-full bg-slate-50 font-black text-agri-green italic" /></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Área a Sembrar (ha) *')" class="text-[10px] font-black text-slate-400" />
                        <x-text-input wire:model.live="area_destinada" type="number" step="0.01" class="block w-full {{ !$terreno_id ? 'bg-slate-100 opacity-50' : '' }}" :disabled="!$terreno_id" />
                    </div>
                    <div class="space-y-1.5"><x-input-label :value="__('Fecha Siembra *')" class="text-[10px] font-black text-slate-400" /><x-text-input wire:model.live="fecha_siembra" type="date" class="block w-full" /></div>
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Estado Fenológico')" class="text-[10px] font-black text-slate-400" />
                        <select wire:model="estado" class="block w-full bg-slate-50 border-none rounded-2xl text-xs font-bold p-4">
                            <option value="Planificado">Planificado</option><option value="En crecimiento">En crecimiento</option><option value="Cosechado">Cosechado</option><option value="Perdido">Perdido</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-6 pt-10 border-t border-slate-100 dark:border-white/5">
                    <button type="button" @click="$dispatch('close')" class="px-10 py-4 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-slate-200">Cancelar</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-16 py-4 bg-agri-green text-white rounded-2xl font-black text-[12px] uppercase tracking-widest shadow-2xl shadow-agri-green/30 hover:scale-105 active:scale-95 transition-all flex items-center gap-3">
                        <i class="fa-solid fa-cloud-arrow-up" wire:loading.remove></i>
                        <i class="fa-solid fa-spinner fa-spin" wire:loading></i>
                        {{ $cropId ? 'Guardar Cambios' : 'Confirmar Siembra' }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>

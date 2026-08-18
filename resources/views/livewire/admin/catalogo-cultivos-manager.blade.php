<div class="space-y-8 p-4 md:p-1 transition-colors duration-500">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-100 dark:border-white/5 pb-6">
        <div class="flex items-center space-x-5">
            <div class="w-16 h-16 bg-[#003a38] rounded-2xl flex items-center justify-center shadow-2xl border border-white/10">
                <i class="fa-solid fa-book text-agri-green text-3xl"></i>
            </div>
            <div>
                <h2 class="text-4xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">Catálogo de Cultivos</h2>
                <p class="text-[10px] text-agri-green font-black uppercase tracking-[0.3em] mt-2 italic">Gestión Maestra de Especies y Parámetros</p>
            </div>
        </div>
        <button @click="$wire.resetForm(); $dispatch('open-modal', 'modal-cat-cultivo')" class="px-10 py-3 bg-agri-green text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all italic">
            <i class="fa-solid fa-plus mr-2"></i> Nuevo Tipo de Cultivo
        </button>
    </div>

    <!-- Buscador -->
    <div class="max-w-md bg-white dark:bg-agri-d_bg p-4 rounded-3xl border border-slate-100 dark:border-white/5 shadow-xl">
        <div class="relative group">
            <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="BUSCAR POR NOMBRE..." class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-[11px] font-black uppercase outline-none focus:ring-4 focus:ring-agri-green/10 italic">
        </div>
    </div>

    <!-- Grid de Catálogo -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @foreach($cultivos as $cultivo)
        <div class="bg-white dark:bg-agri-d_bg rounded-[2.8rem] overflow-hidden shadow-sm border border-slate-100 dark:border-white/5 group relative" x-data="{ menuOpen: false }">
            <div class="h-48 bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
                @if($cultivo->foto_path)
                    <img src="{{ Storage::url($cultivo->foto_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-200 bg-slate-50 dark:bg-white/5"><i class="fa-solid fa-leaf text-7xl"></i></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>

                <div class="absolute top-4 right-4">
                    <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false" class="w-10 h-10 bg-white/20 backdrop-blur-md border border-white/30 rounded-xl text-white flex items-center justify-center shadow-lg"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                    <div x-show="menuOpen" x-transition class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 z-20 overflow-hidden" x-cloak>
                        <button wire:click="edit({{ $cultivo->id }})" class="w-full px-5 py-4 text-left text-[11px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition-all italic">EDITAR</button>
                        <button wire:click="delete({{ $cultivo->id }})" wire:confirm="¿Borrar del catálogo?" class="w-full px-5 py-4 text-left text-[11px] font-black uppercase text-rose-500 hover:bg-rose-50 transition-all italic">BORRAR</button>
                    </div>
                </div>

                <div class="absolute bottom-5 left-8 right-8 text-white">
                    <h3 class="text-2xl font-black italic truncate tracking-tight uppercase leading-none">{{ $cultivo->nombre }}</h3>
                    <p class="text-[10px] font-bold uppercase italic opacity-60 truncate mt-1">{{ $cultivo->nombre_cientifico ?: 'N/A' }}</p>
                </div>
            </div>

            <div class="p-8 space-y-6">
                <div class="grid grid-cols-2 gap-4 border-b border-slate-50 dark:border-white/5 pb-4">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Ciclo</p>
                        <p class="text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase italic">{{ str_replace('_', ' ', $cultivo->tipo_ciclo) }}</p>
                    </div>
                    <div class="space-y-1 text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Días Cosecha</p>
                        <p class="text-[11px] font-black text-emerald-600 italic">{{ $cultivo->dias_a_cosecha_promedio ?: '---' }} días</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Vista Técnica</p>
                    <div class="flex items-center space-x-2 text-[10px] font-bold text-slate-600 dark:text-slate-400 italic">
                        <i class="fa-solid fa-droplet text-blue-500"></i>
                        <span class="truncate">{{ Str::limit($cultivo->instrucciones_base_riego, 30) ?: 'Sin instrucciones' }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-[10px] font-bold text-slate-600 dark:text-slate-400 italic">
                        <i class="fa-solid fa-bug text-rose-500"></i>
                        <span class="truncate">{{ Str::limit($cultivo->instrucciones_base_plagas, 30) ?: 'Sin instrucciones' }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-12">{{ $cultivos->links() }}</div>

    <!-- MODAL DE REGISTRO / EDICIÓN -->
    <x-modal name="modal-cat-cultivo" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg rounded-3xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <div class="bg-[#003a38] px-10 py-5 flex justify-between items-center text-white border-b border-white/5">
                <h3 class="text-2xl font-black italic tracking-tighter uppercase">{{ $catId ? 'Editar Especie Maestra' : 'Nueva Especie en Catálogo' }}</h3>
                <button @click="$dispatch('close')" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <form wire:submit.prevent="save" class="p-10 space-y-8 max-h-[85vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Nombre Común *')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <x-text-input wire:model="nombre" type="text" class="block w-full" placeholder="Ej: MAÍZ, PAPA, PALTA..." required />
                        <x-input-error :messages="$errors->get('nombre')" />
                    </div>
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Nombre Científico')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <x-text-input wire:model="nombre_cientifico" type="text" class="block w-full italic" placeholder="Ej: Solanum tuberosum" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-50 dark:border-white/5">
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Tipo de Ciclo *')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <select wire:model="tipo_ciclo" class="block w-full bg-slate-50 dark:bg-white/5 border-none rounded-xl text-xs font-bold focus:ring-4 focus:ring-agri-green/10" required>
                            <option value="ciclo_corto">Ciclo Corto</option>
                            <option value="perenne">Perenne</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Días a Cosecha Prom.')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <x-text-input wire:model="dias_a_cosecha_promedio" type="number" class="block w-full" />
                    </div>
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Vida Útil (Meses)')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <x-text-input wire:model="vida_util_estimada_meses" type="number" class="block w-full" />
                    </div>
                </div>

                <div class="space-y-6 pt-4 border-t border-slate-50 dark:border-white/5">
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Instrucciones Base de Riego')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <textarea wire:model="instrucciones_base_riego" rows="3" class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-xs font-bold p-4 focus:ring-4 focus:ring-agri-green/10" placeholder="Parámetros de humedad recomendados..."></textarea>
                    </div>
                    <div class="space-y-1.5">
                        <x-input-label :value="__('Prevención de Plagas/Enfermedades')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <textarea wire:model="instrucciones_base_plagas" rows="3" class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-xs font-bold p-4 focus:ring-4 focus:ring-agri-green/10" placeholder="Plagas comunes y tratamientos preventivos..."></textarea>
                    </div>
                </div>

                <div class="flex items-center space-x-6 bg-slate-50 dark:bg-white/5 p-8 rounded-[2.5rem] border border-dashed border-slate-200 dark:border-white/10">
                    <div class="w-40 h-32 rounded-3xl overflow-hidden bg-white dark:bg-slate-800 shrink-0 shadow-xl border-4 border-white">
                        @if($photo) <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($currentPhotoPath) <img src="{{ Storage::url($currentPhotoPath) }}" class="w-full h-full object-cover">
                        @else <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fa-solid fa-camera text-4xl"></i></div> @endif
                    </div>
                    <div class="flex-1">
                        <x-input-label :value="__('Imagen Representativa')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3" />
                        <input type="file" wire:model="photo" class="text-[10px] file:mr-4 file:py-2 file:px-6 file:rounded-xl file:border-0 file:bg-agri-green file:text-white hover:file:bg-emerald-600 transition-all cursor-pointer"/>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-6 pt-10 border-t border-slate-100 dark:border-white/5">
                    <button type="button" @click="$dispatch('close')" class="px-10 py-4 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-slate-200 transition-all italic">Cancelar</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-16 py-4 bg-agri-green text-white rounded-2xl font-black text-[12px] uppercase tracking-widest shadow-2xl shadow-agri-green/30 hover:scale-105 active:scale-95 italic transition-all flex items-center gap-3">
                        <i class="fa-solid fa-cloud-arrow-up" wire:loading.remove></i>
                        <i class="fa-solid fa-spinner fa-spin" wire:loading></i>
                        {{ $catId ? 'Actualizar Catálogo' : 'Confirmar Registro' }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>

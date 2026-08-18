<div class="space-y-8 p-4 md:p-1 transition-colors duration-500">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-100 dark:border-white/5 pb-6">
        <div class="flex items-center space-x-5">
            <div class="w-16 h-16 bg-[#003a38] rounded-2xl flex items-center justify-center shadow-2xl border border-white/10">
                <i class="fa-solid fa-book text-agri-green text-3xl"></i>
            </div>
            <div>
                <h2 class="text-4xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">Catálogo Maestro</h2>
                <p class="text-[10px] text-agri-green font-black uppercase tracking-[0.3em] mt-2 italic">Control Global de Especies AgroSys</p>
            </div>
        </div>
        <button @click="$wire.resetForm(); $dispatch('open-modal', 'modal-cat-cultivo')" class="px-10 py-3 bg-agri-green text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all italic">
            <i class="fa-solid fa-plus mr-2"></i> Añadir Especie
        </button>
    </div>

    <!-- Buscador -->
    <div class="max-w-md bg-white dark:bg-agri-d_bg p-4 rounded-3xl border border-slate-100 dark:border-white/5 shadow-xl">
        <div class="relative group">
            <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="BUSCAR CULTIVO..." class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-[11px] font-black uppercase outline-none focus:ring-4 focus:ring-agri-green/10 italic">
        </div>
    </div>

    <!-- TABLA DE CATÁLOGO (DISEÑO PREMIUM) -->
    <div class="bg-white dark:bg-agri-d_bg rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100 dark:border-white/5">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-white/5 border-b border-slate-100 dark:border-white/10">
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Imagen</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Nombre Común</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Nombre Científico</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Tipo de Ciclo</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                @foreach($cultivos as $cultivo)
                <tr wire:click="edit({{ $cultivo->id }})" class="hover:bg-agri-green/5 cursor-pointer transition-all group">
                    <td class="px-8 py-4">
                        <div class="w-16 h-12 rounded-xl overflow-hidden border-2 border-white dark:border-slate-800 shadow-md">
                            @if($cultivo->foto_path)
                                <img src="{{ Storage::url($cultivo->foto_path) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-300">
                                    <i class="fa-solid fa-leaf text-xl"></i>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-8 py-4">
                        <p class="text-sm font-black text-slate-800 dark:text-white uppercase italic tracking-tighter">{{ $cultivo->nombre }}</p>
                    </td>
                    <td class="px-8 py-4">
                        <p class="text-xs font-bold text-slate-500 italic">{{ $cultivo->nombre_cientifico ?: 'No registrado' }}</p>
                    </td>
                    <td class="px-8 py-4">
                        <span class="px-3 py-1 bg-slate-100 dark:bg-white/5 rounded-lg text-[9px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-400 italic">
                            {{ str_replace('_', ' ', $cultivo->tipo_ciclo) }}
                        </span>
                    </td>
                    <td class="px-8 py-4 text-right">
                        <button class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-white/5 text-slate-400 group-hover:text-agri-green transition-all">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-8">{{ $cultivos->links() }}</div>

    <!-- MODAL CRUD (GESTIÓN MAESTRA) -->
    <x-modal name="modal-cat-cultivo" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg rounded-3xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
            <div class="bg-[#003a38] px-10 py-6 flex justify-between items-center text-white border-b border-white/5">
                <div>
                    <h3 class="text-2xl font-black italic tracking-tighter uppercase">{{ $catId ? 'Editar Especie Maestra' : 'Nueva Especie en Catálogo' }}</h3>
                    <p class="text-[9px] opacity-60 uppercase font-black tracking-widest mt-1">Configuración técnica de especie</p>
                </div>
                <button @click="$dispatch('close')" class="w-12 h-12 flex items-center justify-center rounded-2xl hover:bg-white/10 transition-colors shadow-inner"><i class="fa-solid fa-xmark text-2xl"></i></button>
            </div>

            <form wire:submit.prevent="save" class="p-10 space-y-8 max-h-[85vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <x-input-label :value="__('Nombre Común *')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <x-text-input wire:model="nombre" type="text" class="block w-full" placeholder="Ej: MAÍZ, PAPA, PALTA..." required />
                        <x-input-error :messages="$errors->get('nombre')" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label :value="__('Nombre Científico / Inglés')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <x-text-input wire:model="nombre_cientifico" type="text" class="block w-full italic" placeholder="Ej: Solanum tuberosum" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-50 dark:border-white/5">
                    <div class="space-y-2">
                        <x-input-label :value="__('Tipo de Ciclo *')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <select wire:model="tipo_ciclo" class="block w-full bg-slate-50 dark:bg-white/5 border-none rounded-xl text-xs font-bold focus:ring-4 focus:ring-agri-green/10" required>
                            <option value="ciclo_corto">Ciclo Corto</option>
                            <option value="perenne">Perenne</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <x-input-label :value="__('Días a Cosecha Prom.')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <x-text-input wire:model="dias_a_cosecha_promedio" type="number" class="block w-full" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label :value="__('Vida Útil (Meses)')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <x-text-input wire:model="vida_util_estimada_meses" type="number" class="block w-full" />
                    </div>
                </div>

                <div class="space-y-6 pt-4 border-t border-slate-50 dark:border-white/5">
                    <div class="space-y-2">
                        <x-input-label :value="__('Instrucciones Base de Riego')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <textarea wire:model="instrucciones_base_riego" rows="3" class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-xs font-bold p-4 focus:ring-4 focus:ring-agri-green/10" placeholder="Parámetros de humedad recomendados..."></textarea>
                    </div>
                    <div class="space-y-2">
                        <x-input-label :value="__('Prevención de Plagas/Enfermedades')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest" />
                        <textarea wire:model="instrucciones_base_plagas" rows="3" class="w-full bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-xs font-bold p-4 focus:ring-4 focus:ring-agri-green/10" placeholder="Plagas comunes y tratamientos preventivos..."></textarea>
                    </div>
                </div>

                <div class="flex items-center space-x-6 bg-slate-50 dark:bg-white/5 p-8 rounded-[2.5rem] border border-dashed border-slate-200 dark:border-white/10">
                    <div class="w-40 h-32 rounded-3xl overflow-hidden bg-white dark:bg-slate-800 shrink-0 shadow-xl border-4 border-white">
                        @if($photo && method_exists($photo, 'isPreviewable') && $photo->isPreviewable())
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($photo)
                            <div class="w-full h-full flex flex-col items-center justify-center bg-slate-50 text-slate-400">
                                <i class="fa-solid fa-file-image text-3xl mb-1"></i>
                                <span class="text-[8px] font-black uppercase">Vista no disponible</span>
                            </div>
                        @elseif($currentPhotoPath)
                            <img src="{{ Storage::url($currentPhotoPath) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="fa-solid fa-camera text-4xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <x-input-label :value="__('Imagen Maestra')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3" />
                        <input type="file" wire:model="photo" class="text-[10px] file:mr-4 file:py-2 file:px-6 file:rounded-xl file:border-0 file:bg-agri-green file:text-white hover:file:bg-emerald-600 transition-all cursor-pointer"/>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-6 pt-10 border-t border-slate-100 dark:border-white/5">
                    @if($catId)
                        <button type="button" wire:click="delete({{ $catId }})" wire:confirm="¿Borrar especie permanentemente?" class="px-10 py-4 bg-rose-50 text-rose-500 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all italic">Eliminar del Sistema</button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-4">
                        <button type="button" @click="$dispatch('close')" class="px-10 py-4 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-slate-200 transition-all italic">Cancelar</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-16 py-4 bg-agri-green text-white rounded-2xl font-black text-[12px] uppercase tracking-widest shadow-2xl shadow-agri-green/30 hover:scale-105 active:scale-95 italic transition-all flex items-center gap-3">
                            <i class="fa-solid fa-cloud-arrow-up" wire:loading.remove></i>
                            <i class="fa-solid fa-spinner fa-spin" wire:loading></i>
                            {{ $catId ? 'Guardar Cambios' : 'Registrar Especie' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>
</div>

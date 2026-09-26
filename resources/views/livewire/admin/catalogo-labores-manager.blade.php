<div class="space-y-6">
    <!-- VISTA (FRONTEND) - GESTIÓN DEL CATÁLOGO DE LABORES (Solo Super Admin) -->

    <div class="flex flex-col lg:flex-row justify-between items-end gap-4 mb-2">
        <div>
            <h2 class="text-4xl font-black text-slate-900 dark:text-white italic tracking-tighter uppercase leading-none">Catálogo <span class="text-indigo-600">Labores</span></h2>
            <p class="text-slate-400 font-bold text-[10px] uppercase mt-1 tracking-[0.4em]">Configuración de Actividades Base del Sistema</p>
        </div>
        <button x-on:click="$dispatch('open-modal', 'modal-labor-form')" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg font-black shadow-lg transition-all hover:scale-105 uppercase text-[10px] tracking-widest flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Añadir Tipo de Labor
        </button>
    </div>

    <!-- Filtro y Búsqueda -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-slate-50 dark:border-white/5">
        <div class="relative w-full max-w-md">
            <input type="text" wire:model.live="search" placeholder="Buscar labor por nombre..." class="w-full rounded-lg border-2 border-slate-100 dark:border-white/10 bg-white dark:bg-slate-900 text-slate-700 dark:text-white text-[10px] font-black py-2 pl-10 shadow-sm focus:ring-2 focus:ring-indigo-500 uppercase italic">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
        </div>
    </div>

    <!-- Tabla de Labores -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-slate-50 dark:border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-white/5 border-b border-slate-100 dark:border-white/10">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Nombre de la Labor</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Categoría Operativa</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-right">Acciones de Control</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                    @foreach($labores as $labor)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600">
                                        <i class="fa-solid fa-screwdriver-wrench text-xs"></i>
                                    </div>
                                    <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase italic tracking-tighter">{{ $labor->nombre }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $catColor = match($labor->categoria) {
                                        'preparacion' => 'bg-amber-100 text-amber-700',
                                        'siembra' => 'bg-emerald-100 text-emerald-700',
                                        'mantenimiento' => 'bg-blue-100 text-blue-700',
                                        'cosecha' => 'bg-rose-100 text-rose-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase italic {{ $catColor }}">
                                    {{ $labor->categoria }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="edit({{ $labor->id }})" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/10 text-slate-500 hover:bg-indigo-600 hover:text-white transition-all">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                    </button>
                                    <button onclick="confirm('¿Estás seguro de eliminar esta labor del catálogo?') || event.stopImmediatePropagation()" wire:click="delete({{ $labor->id }})" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/10 text-slate-500 hover:bg-rose-600 hover:text-white transition-all">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-slate-50/50 dark:bg-white/5 border-t border-slate-100 dark:border-white/10">
            {{ $labores->links() }}
        </div>
    </div>

    <!-- MODAL DE FORMULARIO -->
    <x-modal name="modal-labor-form" focusable>
        <div class="p-8">
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase italic mb-6 border-b pb-4">
                {{ $laborId ? 'Editar Labor del Catálogo' : 'Añadir Nueva Labor Maestras' }}
            </h3>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nombre de la Labor</label>
                        <input type="text" wire:model="nombre" class="w-full rounded-xl border-2 border-slate-100 dark:border-white/10 bg-white dark:bg-slate-900 text-slate-700 dark:text-white text-xs font-bold p-3 focus:ring-2 focus:ring-indigo-500 uppercase italic">
                        @error('nombre') <span class="text-[10px] text-rose-500 font-bold ml-1 uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Categoría Operativa</label>
                        <select wire:model="categoria" class="w-full rounded-xl border-2 border-slate-100 dark:border-white/10 bg-white dark:bg-slate-900 text-slate-700 dark:text-white text-xs font-bold p-3 focus:ring-2 focus:ring-indigo-500 uppercase italic">
                            <option value="preparacion">PREPARACIÓN</option>
                            <option value="siembra">SIEMBRA</option>
                            <option value="mantenimiento">MANTENIMIENTO</option>
                            <option value="cosecha">COSECHA</option>
                        </select>
                        @error('categoria') <span class="text-[10px] text-rose-500 font-bold ml-1 uppercase">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Descripción / Instrucciones Base</label>
                    <textarea wire:model="descripcion" rows="3" class="w-full rounded-xl border-2 border-slate-100 dark:border-white/10 bg-white dark:bg-slate-900 text-slate-700 dark:text-white text-xs font-bold p-3 focus:ring-2 focus:ring-indigo-500 italic"></textarea>
                    @error('descripcion') <span class="text-[10px] text-rose-500 font-bold ml-1 uppercase">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-white/10">
                    <button type="button" x-on:click="$dispatch('close')" class="px-6 py-2.5 bg-slate-100 text-slate-500 rounded-lg font-black uppercase text-[10px] tracking-widest hover:bg-slate-200 transition-all">Cancelar</button>
                    <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white rounded-lg font-black shadow-lg hover:bg-indigo-700 transition-all uppercase text-[10px] tracking-widest flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i> {{ $laborId ? 'Guardar Cambios' : 'Registrar Labor' }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>

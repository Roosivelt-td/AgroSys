<div class="space-y-6">
    <!-- VISTA (FRONTEND) - GESTIÓN GLOBAL DE TERRENOS (Solo Super Admin) -->

    <div class="flex flex-col lg:flex-row justify-between items-end gap-4 mb-2">
        <div>
            <h2 class="text-4xl font-black text-slate-900 dark:text-white italic tracking-tighter uppercase leading-none">Gestión <span class="text-indigo-600">Terrenos</span></h2>
            <p class="text-slate-400 font-bold text-[10px] uppercase mt-1 tracking-[0.4em]">Control de todos los activos de tierra del ecosistema</p>
        </div>
    </div>

    <!-- Filtro y Búsqueda -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-slate-50 dark:border-white/5">
        <div class="relative w-full max-w-md">
            <input type="text" wire:model.live="search" placeholder="Buscar terreno por nombre..." class="w-full rounded-lg border-2 border-slate-100 dark:border-white/10 bg-white dark:bg-slate-900 text-slate-700 dark:text-white text-[10px] font-black py-2 pl-10 shadow-sm focus:ring-2 focus:ring-indigo-500 uppercase italic">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
        </div>
    </div>

    <!-- Tabla de Terrenos -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-slate-50 dark:border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-white/5 border-b border-slate-100 dark:border-white/10">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Nombre Terreno</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Propietario / Responsable</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Organización</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">Hectáreas</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">Tenencia</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                    @foreach($terrenos as $terreno)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600">
                                        <i class="fa-solid fa-map-location-dot text-xs"></i>
                                    </div>
                                    <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase italic tracking-tighter">{{ $terreno->nombre }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-[10px] font-bold text-slate-600 dark:text-slate-300 uppercase">{{ $terreno->responsable->nombres ?? 'Sin asignar' }}</p>
                                <p class="text-[8px] text-slate-400">{{ $terreno->responsable->email ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-[10px] font-bold text-slate-600 dark:text-slate-300 uppercase">{{ $terreno->organizacion->nombre ?? 'Personal' }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[11px] font-black text-slate-700 dark:text-white">{{ number_format($terreno->hectareas, 2) }} ha</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase italic {{ $terreno->tipo_tenencia === 'propio' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $terreno->tipo_tenencia }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick="confirm('¿Estás seguro de eliminar este terreno del sistema?') || event.stopImmediatePropagation()" wire:click="delete({{ $terreno->id }})" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/10 text-slate-500 hover:bg-rose-600 hover:text-white transition-all">
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
            {{ $terrenos->links() }}
        </div>
    </div>
</div>

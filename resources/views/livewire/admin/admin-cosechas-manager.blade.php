<div class="space-y-6">
    <!-- VISTA (FRONTEND) - PRODUCCIÓN GLOBAL (Solo Super Admin) -->

    <div class="flex flex-col lg:flex-row justify-between items-end gap-4 mb-2">
        <div>
            <h2 class="text-4xl font-black text-slate-900 dark:text-white italic tracking-tighter uppercase leading-none">Producción <span class="text-emerald-600">Global</span></h2>
            <p class="text-slate-400 font-bold text-[10px] uppercase mt-1 tracking-[0.4em]">Resumen de todas las cosechas del sistema</p>
        </div>
    </div>

    <!-- Filtro y Búsqueda -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-slate-50 dark:border-white/5">
        <div class="relative w-full max-w-md">
            <input type="text" wire:model.live="search" placeholder="Buscar por producto..." class="w-full rounded-lg border-2 border-slate-100 dark:border-white/10 bg-white dark:bg-slate-900 text-slate-700 dark:text-white text-[10px] font-black py-2 pl-10 shadow-sm focus:ring-2 focus:ring-emerald-500 uppercase italic">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
        </div>
    </div>

    <!-- Tabla de Cosechas -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-slate-50 dark:border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-white/5 border-b border-slate-100 dark:border-white/10">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Fecha</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Producto / Variedad</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Empresa / Fundo</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">Cantidad</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">Calidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                    @foreach($cosechas as $cosecha)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 text-[11px] font-bold text-slate-600 dark:text-slate-300 italic">
                                {{ $cosecha->fecha_cosecha->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase italic tracking-tighter">
                                    {{ $cosecha->labor->cultivo->detalleCatalogo->nombre }}
                                </p>
                                <p class="text-[8px] font-bold text-slate-400 uppercase">
                                    {{ $cosecha->labor->cultivo->variedad ?: 'Genérica' }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-[10px] font-bold text-slate-600 dark:text-slate-300 uppercase">
                                    {{ $cosecha->labor->cultivo->terreno->organizacion->nombre ?? 'Personal' }}
                                </p>
                                <p class="text-[8px] text-slate-400 uppercase">{{ $cosecha->labor->cultivo->terreno->nombre }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[11px] font-black text-emerald-600 italic">
                                    {{ number_format($cosecha->cantidad_kg, 2) }} {{ strtoupper($cosecha->unidad_medida) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $qualityColor = match($cosecha->calidad) {
                                        'primera' => 'bg-emerald-100 text-emerald-700',
                                        'segunda' => 'bg-amber-100 text-amber-700',
                                        'descarte' => 'bg-rose-100 text-rose-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase italic {{ $qualityColor }}">
                                    {{ $cosecha->calidad }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-slate-50/50 dark:bg-white/5 border-t border-slate-100 dark:border-white/10">
            {{ $cosechas->links() }}
        </div>
    </div>
</div>

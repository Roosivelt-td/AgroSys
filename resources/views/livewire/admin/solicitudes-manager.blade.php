<div class="space-y-0 p-4 md:p-1">
    <!-- Contenedor Principal Estilo ADMIRO -->
    <div class="bg-white dark:bg-agri-d_bg rounded-2xl shadow-2xl overflow-hidden border border-slate-200 dark:border-white/10 transition-colors duration-500">

        <!-- Header: Light (Menta) / Dark (Teal Oscuro) -->
        <div class="bg-agri-l_card dark:bg-black px-8 py-6 border-b border-agri-green/10 dark:border-white/10 transition-colors duration-500">
            <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight italic">Historial de Transiciones: Solicitudes</h2>
            <p class="text-[10px] text-agri-green font-black uppercase tracking-widest mt-1">Gestión Global de Peticiones</p>
        </div>

        <!-- Cuerpo de la Tabla: Light (Gris Suave) / Dark (Teal Profundo) -->
        <div class="bg-agri-l_bg dark:bg-agri-d_bg/30 p-6 md:p-12 transition-colors duration-500">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">
                            <th class="px-6 py-2">Solicitante</th>
                            <th class="px-6 py-2">RUC / ID</th>
                            <th class="px-6 py-2 text-center">Acción</th>
                            <th class="px-6 py-2">Fecha / Hora</th>
                            <th class="px-6 py-2 text-right">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $sol)
                        <tr class="group transition-all duration-300 {{ $highlightId == $sol->id ? 'animate-focus' : '' }}">
                            <!-- Solicitante -->
                            <td class="px-6 py-6 first:rounded-l-xl border-y border-l border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                <div class="flex items-center space-x-5">
                                    <div class="w-12 h-12 rounded-full bg-agri-l_bg dark:bg-white/5 flex items-center justify-center border border-slate-100 dark:border-white/10 shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-building-circle-check text-agri-green text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-base font-black text-slate-800 dark:text-agri-green leading-tight italic">{{ $sol->solicitante->nombres }}</p>
                                        <p class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">Registro de Empresa</p>
                                    </div>
                                </div>
                            </td>

                            <!-- RUC -->
                            <td class="px-6 py-6 border-y border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                <span class="font-black text-slate-700 dark:text-slate-300 text-sm tabular-nums">#{{ $sol->datos_extra['ruc'] ?? $sol->id }}</span>
                            </td>

                            <!-- Acción visual -->
                            <td class="px-6 py-6 border-y border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg text-center shadow-sm">
                                <span class="text-xs font-black text-emerald-500 uppercase tracking-widest">+ SOLICITUD</span>
                            </td>

                            <!-- Date/Time -->
                            <td class="px-6 py-6 border-y border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 tabular-nums">
                                    <p class="uppercase">{{ $sol->created_at->translatedFormat('M d') }}</p>
                                    <p class="opacity-70">{{ $sol->created_at->format('H:i:s') }}</p>
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-6 last:rounded-r-xl border-y border-r border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg text-right shadow-sm">
                                <button wire:click="clearHighlight({{ $sol->id }})"
                                    class="inline-block px-8 py-2.5 rounded-xl font-black text-[11px] uppercase tracking-[0.2em] transition-all hover:scale-105
                                    {{ $sol->estado == 0 ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 border border-amber-100 dark:border-amber-900/40' : '' }}
                                    {{ $sol->estado == 1 ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 border border-emerald-100 dark:border-emerald-900/40' : '' }}
                                    {{ $sol->estado == 2 ? 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 border border-rose-100 dark:border-rose-900/40' : '' }}">
                                    {{ $sol->estado == 0 ? 'Pendiente' : ($sol->estado == 1 ? 'Completado' : 'Rechazado') }}
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-10">
                {{ $solicitudes->links() }}
            </div>
        </div>
    </div>

    <!-- Modales Detallados -->
    @foreach($solicitudes as $sol)
    <x-modal name="process-solicitud-{{ $sol->id }}" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg overflow-hidden shadow-2xl rounded-2xl border border-slate-100 dark:border-white/5">
            <div class="bg-agri-l_card dark:bg-black px-10 py-8 flex justify-between items-center text-slate-800 dark:text-white border-b border-agri-green/10 dark:border-white/10">
                <h3 class="text-2xl font-black italic tracking-tighter">Detalles de la Petición</h3>
                <button @click="$dispatch('close')" wire:click="close" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <div class="p-10 space-y-8 custom-scrollbar max-h-[75vh] overflow-y-auto">
                <div class="bg-agri-l_bg dark:bg-black/20 p-8 rounded-2xl border border-slate-100 dark:border-white/5 text-center italic">
                    <p class="text-[10px] font-black text-agri-green uppercase tracking-[0.3em] mb-3">Descripción</p>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300 leading-relaxed">
                        {{ $sol->datos_extra['descripcion'] ?? 'Sin descripción detallada.' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div class="p-6 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-50 dark:border-white/5">
                        <p class="text-[9px] uppercase font-black text-slate-400 mb-2">Solicitante</p>
                        <p class="text-sm font-black text-slate-800 dark:text-white">{{ $sol->solicitante->nombres }}</p>
                        <p class="text-[10px] text-slate-500">{{ $sol->solicitante->dni }}</p>
                    </div>
                    <div class="p-6 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-50 dark:border-white/5 text-right">
                        <p class="text-[9px] uppercase font-black text-slate-400 mb-2">Empresa</p>
                        <p class="text-sm font-black text-agri-green italic">"{{ $sol->datos_extra['nombre'] ?? 'N/A' }}"</p>
                        <p class="text-[10px] text-slate-500 font-mono">RUC: {{ $sol->datos_extra['ruc'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="p-10 bg-slate-50 dark:bg-black/40 border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
                <span class="text-xs font-black text-slate-400 italic">Estado: {{ $sol->estado == 0 ? 'En revisión' : 'Resuelto' }}</span>

                @if($sol->estado == 0)
                <div class="flex space-x-4">
                    <button wire:click="rechazar({{ $sol->id }})" class="px-8 py-3 bg-white dark:bg-white/5 border border-rose-300 text-rose-500 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-rose-50 transition-all">
                        Rechazar
                    </button>
                    <button wire:click="aprobar({{ $sol->id }})" class="px-10 py-3 bg-agri-green text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all">
                        Aprobar
                    </button>
                </div>
                @else
                <button @click="$dispatch('close')" wire:click="close" class="px-10 py-3 bg-slate-800 text-white rounded-xl font-black text-xs uppercase">Cerrar</button>
                @endif
            </div>
        </div>
    </x-modal>
    @endforeach
</div>

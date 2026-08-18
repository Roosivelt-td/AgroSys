<div class="space-y-0 p-4 md:p-8">
    <!-- Contenedor Principal Estilo ADMIRO -->
    <div class="bg-white dark:bg-agri-d_bg rounded-2xl shadow-2xl overflow-hidden border border-slate-200 dark:border-white/10 transition-colors duration-500">

        <!-- Header: Estilo ADMIRO -->
        <div class="bg-agri-l_card dark:bg-black px-8 py-6 border-b border-agri-green/10 dark:border-white/10 flex flex-col lg:flex-row lg:items-center justify-between gap-6 transition-colors duration-500">
            <div>
                <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight italic">Historial de Transiciones: Auditoría Global</h2>
                <p class="text-[10px] text-agri-green font-black uppercase tracking-widest mt-1">Monitoreo de Acciones del Sistema</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <select wire:model.live="filterCategory" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest px-4 py-2 text-slate-600 dark:text-white focus:ring-agri-green outline-none shadow-sm">
                    <option value="">Todas las Categorías</option>
                    <option value="super_admin">Super Administradores</option>
                    <option value="admin_org">Administradores de Org</option>
                    <option value="supervisor">Supervisores</option>
                    <option value="agricultor">Agricultores</option>
                </select>

                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input wire:model.live="search" type="text" placeholder="Buscar en auditoría..."
                           class="w-full pl-9 pr-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl text-xs focus:ring-agri-green outline-none shadow-sm">
                </div>
            </div>
        </div>

        <!-- Cuerpo de la Tabla -->
        <div class="bg-agri-l_card/30 dark:bg-agri-d_bg/30 p-6 md:p-12 transition-colors duration-500">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">
                            <th class="px-6 py-2">Autor del Evento</th>
                            <th class="px-6 py-2">Organización</th>
                            <th class="px-6 py-2 text-center">Acción</th>
                            <th class="px-6 py-2">Fecha / Hora</th>
                            <th class="px-6 py-2 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr class="group transition-all duration-300">
                            <!-- Autor -->
                            <td class="px-6 py-5 first:rounded-l-xl border-y border-l border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                <div class="flex items-center space-x-5">
                                    <div class="w-12 h-12 rounded-full bg-agri-l_bg dark:bg-white/5 flex items-center justify-center border border-slate-100 dark:border-white/10 shrink-0 group-hover:scale-110 transition-transform shadow-inner">
                                        <i class="fa-solid fa-fingerprint text-agri-green text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800 dark:text-agri-green leading-tight italic">{{ $log->usuario->nombres }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase italic tracking-tighter">{{ $log->usuario->rol->nombre }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Org -->
                            <td class="px-6 py-5 border-y border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                @if($log->organizacion)
                                    <span class="font-black text-slate-700 dark:text-slate-300 text-xs italic tracking-tighter">{{ $log->organizacion->nombre }}</span>
                                @else
                                    <span class="text-rose-400 font-black text-[10px] uppercase tracking-widest italic">Sistema Global</span>
                                @endif
                            </td>

                            <!-- Acción -->
                            <td class="px-6 py-5 border-y border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg text-center shadow-sm">
                                <span class="text-[10px] font-black tabular-nums tracking-widest
                                    {{ in_array($log->accion, ['SOLICITUD', 'REGISTRO', 'APROBACIÓN', 'NUEVO MIEMBRO']) ? 'text-emerald-500' : 'text-rose-500' }}">
                                    @php
                                        $label = match($log->accion) {
                                            'REGISTRO' => '+ UNIÓN AL SISTEMA',
                                            'SOLICITUD' => '+ NUEVA PETICIÓN',
                                            'APROBACIÓN' => '✓ TRÁMITE APROBADO',
                                            'RECHAZO' => '✕ PETICIÓN DENEGADA',
                                            'ACTUALIZACIÓN' => '• CAMBIO REALIZADO',
                                            default => $log->accion
                                        };
                                    @endphp
                                    {{ $label }}
                                </span>
                            </td>

                            <!-- Fecha / Hora -->
                            <td class="px-6 py-5 border-y border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 tabular-nums">
                                    <p class="uppercase">{{ $log->created_at->translatedFormat('M d') }}</p>
                                    <p class="opacity-70">{{ $log->created_at->format('H:i:s') }}</p>
                                </div>
                            </td>

                            <!-- Status Button -->
                            <td class="px-6 py-5 last:rounded-r-xl border-y border-r border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg text-right shadow-sm">
                                <button wire:click="showDetails({{ $log->id }})"
                                    class="inline-block px-8 py-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 border border-emerald-100 dark:border-emerald-900/30 font-black text-[10px] uppercase tracking-widest transition-all hover:scale-105">
                                    Completado
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-10 px-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Audit -->
    @if($selectedItem)
    <x-modal name="audit-details" :show="true" focusable>
        <div class="bg-white dark:bg-agri-d_bg overflow-hidden shadow-2xl rounded-2xl border border-slate-100 dark:border-white/5">
            <div class="bg-agri-l_card dark:bg-black px-10 py-8 flex justify-between items-center text-slate-800 dark:text-white border-b border-agri-green/10 dark:border-white/10">
                <div>
                    <h3 class="text-2xl font-black italic tracking-tighter">Informe de Auditoría</h3>
                    <p class="text-[10px] opacity-60 uppercase font-black tracking-[0.3em]">Registro Forense #{{ $selectedItem->id }}</p>
                </div>
                <button wire:click="closeDetails" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <div class="p-10 space-y-8 max-h-[75vh] overflow-y-auto custom-scrollbar">
                <div class="bg-agri-l_bg dark:bg-black/20 p-8 rounded-xl border border-slate-100 dark:border-white/5 text-center shadow-inner">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 italic">Resumen de la Acción</p>
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200 italic leading-relaxed">
                        "{{ $selectedItem->descripcion }}"
                    </p>
                </div>

                @if($selectedItem->detalles_previos)
                <div class="space-y-3">
                    <p class="text-[10px] uppercase font-black text-rose-500 text-center tracking-widest italic">Captura Técnica de Datos (Audit JSON)</p>
                    <pre class="bg-slate-900 text-emerald-400 p-8 rounded-2xl text-[10px] overflow-x-auto shadow-inner font-mono leading-relaxed border border-white/10">{{ json_encode($selectedItem->detalles_previos, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
            </div>

            <div class="p-10 bg-slate-50 dark:bg-black/40 border-t border-slate-100 dark:border-white/5 flex justify-end">
                <button wire:click="closeDetails" class="px-12 py-3 bg-agri-green text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all shadow-xl shadow-agri-green/30 hover:scale-105">
                    Cerrar Informe
                </button>
            </div>
        </div>
    </x-modal>
    @endif
</div>

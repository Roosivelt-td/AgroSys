<div class="space-y-0 p-4 md:p-1">
    <!-- PANEL PRINCIPAL -->
    <div class="bg-agri-l_card dark:bg-agri-d_sidebar rounded-2xl shadow-2xl overflow-hidden border border-slate-200 dark:border-white/10 transition-colors duration-500">

        <!-- CABECERA DEL PANEL (Color: l_card / d_sidebar) -->
        <div class="bg-agri-l_card dark:bg-agri-d_sidebar px-10 py-6">
            <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight italic">Panel de Control: Solicitudes de Personal</h2>
        </div>

        <!-- CUERPO DEL PANEL (Color: l_accent / d_accent) -->
        <div class="bg-agri-l_accent dark:bg-agri-d_accent p-6 md:p-12 transition-colors duration-500 min-h-[600px]">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-[11px] font-black text-slate-500 dark:text-slate-600 uppercase tracking-widest">
                            <th class="px-6 py-4">Aspirante / Miembro</th>
                            <th class="px-6 py-4">Tipo de Trámite</th>
                            <th class="px-6 py-4">Fecha / Hora</th>
                            <th class="px-6 py-4 text-right">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $sol)
                        <tr class="group transition-all duration-300 {{ $highlightId == $sol->id ? 'animate-focus' : '' }}">
                            <!-- CELDA: Blanco (L) | Fondo Gris (D) -->
                            <td class="px-6 py-5 first:rounded-l-xl border-y border-l border-slate-100 dark:border-white/10 bg-white dark:bg-agri-d_bg shadow-sm group-hover:shadow-md">
                                <div class="flex items-center space-x-5">
                                    <div class="w-12 h-12 rounded-full bg-agri-l_bg dark:bg-white/5 flex items-center justify-center border border-slate-100 dark:border-white/10 shrink-0 shadow-inner group-hover:scale-105 transition-transform">
                                        <i class="fa-solid fa-user-gear text-agri-green"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-[#2d6a4f] dark:text-agri-green leading-tight italic">{{ $sol->solicitante->nombres }}</p>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-tighter italic">ID: {{ $sol->solicitante->dni }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-5 border-y border-slate-100 dark:border-white/10 bg-white dark:bg-agri-d_bg shadow-sm">
                                <span class="text-[10px] font-black uppercase italic tracking-widest {{ $sol->tipo === 'ascenso_rol' ? 'text-amber-500' : ($sol->tipo === 'renuncia_rol' ? 'text-rose-500' : 'text-blue-500') }}">
                                    {{ match($sol->tipo) { 'unirse_organizacion' => 'Ingreso de Miembro', 'ascenso_rol' => 'Solicitud de Supervisor', 'renuncia_rol' => 'Baja de Cargo', default => 'Trámite' } }}
                                </span>
                            </td>

                            <td class="px-6 py-5 border-y border-slate-100 dark:border-white/10 bg-white dark:bg-agri-d_bg shadow-sm">
                                <div class="text-[11px] font-bold text-slate-600 dark:text-slate-500 tabular-nums">
                                    <p class="uppercase">{{ $sol->created_at->translatedFormat('M d') }}</p>
                                    <p class="opacity-70">{{ $sol->created_at->format('H:i:s') }}</p>
                                </div>
                            </td>

                            <td class="px-6 py-5 last:rounded-r-xl border-y border-r border-slate-100 dark:border-white/10 bg-white dark:bg-agri-d_bg text-right shadow-sm">
                                <button wire:click="clearHighlight({{ $sol->id }})" class="inline-block px-8 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all hover:scale-105 {{ $sol->estado == 0 ? 'bg-amber-100 text-amber-600' : ($sol->estado == 1 ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600') }}">
                                    {{ $sol->estado == 0 ? 'Pendiente' : ($sol->estado == 1 ? 'Completado' : 'Rechazado') }}
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-10 px-4">{{ $solicitudes->links() }}</div>
        </div>
    </div>

    <!-- Modales Detallados -->
    @foreach($solicitudes as $sol)
    <x-modal name="process-solicitud-{{ $sol->id }}" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg overflow-hidden shadow-2xl rounded-2xl border border-slate-100 dark:border-white/5">
            <!-- Header Moderno y Compacto -->
            <div class="bg-agri-l_card dark:bg-agri-d_sidebar px-8 py-5 flex justify-between items-center border-b border-agri-green/10 dark:border-white/5">
                <div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">Validación de Personal</h3>
                    <p class="text-[9px] text-agri-green dark:text-white/40 uppercase font-black tracking-widest mt-1 italic">Revisión de Solicitud #{{ $sol->id }}</p>
                </div>
                <button @click="$dispatch('close')" wire:click="close" class="w-8 h-8 flex items-center justify-center rounded-lg bg-black/5 dark:bg-white/10 hover:bg-rose-500 hover:text-white transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 md:p-8 space-y-6">
                <!-- Perfil del Solicitante -->
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        <div class="w-24 h-24 rounded-full border-4 border-agri-green/20 p-0.5 shadow-xl transition-transform group-hover:scale-105 duration-500">
                            <img src="{{ $sol->solicitante->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($sol->solicitante->nombres).'&background=00ba2e&color=fff' }}"
                                 class="w-full h-full rounded-full object-cover">
                        </div>
                        <div class="absolute bottom-1 right-1 w-6 h-6 bg-emerald-500 border-2 border-white dark:border-agri-d_bg rounded-full flex items-center justify-center text-white shadow-lg">
                            <i class="fa-solid fa-check text-[8px]"></i>
                        </div>
                    </div>
                    <h4 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tight mt-4">{{ $sol->solicitante->nombres }} {{ $sol->solicitante->apellidos }}</h4>
                    <p class="text-[9px] text-agri-green font-black uppercase tracking-[0.4em] mt-0.5">Identidad de Agricultor</p>
                </div>

                <!-- Bloques de Información Compactos -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5 flex flex-col items-center justify-center text-center">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Documento Nacional</p>
                        <div class="flex items-center space-x-2 text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-address-card text-agri-green text-xs"></i>
                            <span class="font-black text-xs tracking-widest tabular-nums">{{ $sol->solicitante->dni }}</span>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5 flex flex-col items-center justify-center text-center">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Ubicación</p>
                        <div class="flex items-center space-x-2 text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-location-dot text-agri-green text-xs"></i>
                            <span class="text-[10px] font-bold italic truncate">{{ $sol->solicitante->ubicacion ?? 'Ayacucho, Perú' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Detalles del Trámite -->
                <div class="bg-agri-l_card/20 dark:bg-black/20 p-5 rounded-xl border border-agri-green/10 text-center">
                    <p class="text-[9px] font-black text-agri-green uppercase tracking-[0.2em] mb-2 italic">Detalles de la Petición</p>
                    <p class="text-xs font-bold text-slate-600 dark:text-slate-300 leading-relaxed italic">
                        El miembro solicita: <br>
                        <span class="text-agri-green uppercase tracking-wider font-black text-sm">
                        {{ match($sol->tipo) {
                            'unirse_organizacion' => 'Ingreso como Miembro Agricultor',
                            'ascenso_rol' => 'Promoción a Supervisor',
                            'renuncia_rol' => 'Baja del cargo de Supervisor',
                            default => 'Gestión Interna'
                        } }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Footer de Acciones -->
            <div class="px-8 py-6 bg-slate-50 dark:bg-black/20 border-t border-slate-100 dark:border-white/5 flex justify-center gap-4">
                @if($sol->estado == 0)
                    <button wire:click="rechazar({{ $sol->id }})" class="flex-1 px-6 py-3 bg-white dark:bg-white/5 border border-rose-200 text-rose-500 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-rose-50 transition-all italic">
                        Declinar
                    </button>
                    <button wire:click="aprobar({{ $sol->id }})" class="flex-1 px-6 py-3 bg-agri-green text-white rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-agri-green/30 hover:scale-[1.02] active:scale-95 transition-all italic">
                        Aceptar Trámite
                    </button>
                @else
                    <button @click="$dispatch('close')" wire:click="close" class="w-full py-3 bg-slate-800 text-white rounded-xl font-black text-[10px] uppercase tracking-widest italic">
                        Cerrar Registro
                    </button>
                @endif
            </div>
        </div>
    </x-modal>
    @endforeach
</div>

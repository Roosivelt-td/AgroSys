<div class="space-y-6 p-4 md:p-8 transition-colors duration-500">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden border border-slate-200 dark:border-white/10 transition-colors duration-500">
        <!-- Header -->
        <div class="bg-agri-l_card dark:bg-black px-10 py-6 border-b border-agri-green/10 dark:border-white/10">
            <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight italic">Historial de Transiciones: Mis Trámites</h2>
            <p class="text-[10px] text-agri-green font-black uppercase tracking-widest mt-1">Invitaciones Recibidas y Peticiones Enviadas</p>
        </div>

        <!-- Cuerpo de la Tabla -->
        <div class="bg-agri-l_bg dark:bg-agri-d_bg/30 p-6 md:p-10 transition-colors duration-500">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                            <th class="px-6 py-2">Tipo de Trámite</th>
                            <th class="px-6 py-2">Organización</th>
                            <th class="px-6 py-2">Fecha / Hora</th>
                            <th class="px-6 py-2 text-right">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $sol)
                        @php
                            $esInvitacion = $sol->destinatario_usuario_id === auth()->id();
                        @endphp
                        <tr class="group transition-all duration-300 {{ $highlightId == $sol->id ? 'animate-focus' : '' }}">
                            <!-- Tipo de Trámite -->
                            <td class="px-6 py-5 first:rounded-l-xl border-y border-l border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                <div class="flex items-center space-x-5">
                                    <div class="w-12 h-12 rounded-full bg-agri-l_card dark:bg-white/5 flex items-center justify-center border border-slate-100 dark:border-white/10 shrink-0 shadow-inner group-hover:scale-105 transition-transform">
                                        <i class="fa-solid {{ $esInvitacion ? 'fa-envelope-open-text text-amber-500' : 'fa-paper-plane text-agri-green' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800 dark:text-white leading-tight italic">
                                            {{ $esInvitacion ? 'Invitación Recibida' : 'Petición Enviada' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                                            {{ match($sol->tipo) {
                                                'creacion_organizacion' => 'Registro Empresa',
                                                'unirse_organizacion' => 'Unirse a Equipo',
                                                'invitacion_organizacion' => 'Invitación a Miembro',
                                                'ascenso_rol' => 'Solicitud de Supervisor',
                                                'renuncia_rol' => 'Baja de Cargo',
                                                default => 'Trámite General'
                                            } }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Organización -->
                            <td class="px-6 py-5 border-y border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                <span class="font-black text-slate-700 dark:text-slate-300 text-sm italic">{{ $sol->organizacion->nombre ?? ($sol->datos_extra['nombre'] ?? 'Sistema / Nueva Org') }}</span>
                            </td>

                            <!-- Fecha / Hora -->
                            <td class="px-6 py-5 border-y border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 tabular-nums">
                                    <p class="uppercase">{{ $sol->created_at->translatedFormat('M d') }}</p>
                                    <p class="opacity-70">{{ $sol->created_at->format('H:i:s') }}</p>
                                </div>
                            </td>

                            <!-- Estado / Acción -->
                            <td class="px-6 py-5 last:rounded-r-xl border-y border-r border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg text-right shadow-sm">
                                <button wire:click="clearHighlight({{ $sol->id }})"
                                    class="inline-block px-8 py-2.5 rounded-xl font-black text-[11px] uppercase tracking-widest transition-all hover:scale-105 shadow-sm
                                    {{ $sol->estado == 0 ? 'bg-amber-100 text-amber-600 border border-amber-200' : ($sol->estado == 1 ? 'bg-emerald-100 text-emerald-600 border border-emerald-200' : 'bg-rose-100 text-rose-600 border border-rose-200') }}">
                                    {{ $sol->estado == 0 ? ($esInvitacion ? 'Revisar' : 'Pendiente') : ($sol->estado == 1 ? 'Aceptado' : 'Rechazado') }}
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-8">
                {{ $solicitudes->links() }}
            </div>
        </div>
    </div>

    <!-- Modales para Revisar Invitaciones -->
    @foreach($solicitudes as $sol)
    <x-modal name="process-invitacion-{{ $sol->id }}" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg overflow-hidden shadow-2xl rounded-2xl border border-slate-100 dark:border-white/5">
            <!-- Header Compacto Estilo ADMIRO -->
            <div class="bg-agri-l_card dark:bg-agri-d_sidebar px-8 py-4 flex justify-between items-center border-b border-agri-green/10 dark:border-white/5">
                <div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">Detalles del Trámite</h3>
                    <p class="text-[9px] text-agri-green dark:text-white/40 uppercase font-black tracking-widest mt-1 italic">Referencia #{{ $sol->id }}</p>
                </div>
                <button @click="$dispatch('close')" wire:click="close" class="w-8 h-8 flex items-center justify-center rounded-lg bg-black/5 dark:bg-white/10 hover:bg-rose-500 hover:text-white transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 md:p-8 space-y-5">
                <!-- Entidad Card (Top) -->
                <div class="bg-agri-l_bg/50 dark:bg-black/20 p-6 rounded-2xl border border-agri-green/10 text-center">
                    <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-sm border border-slate-100 dark:border-white/5">
                        <i class="fa-solid fa-building-wheat text-agri-green text-xl"></i>
                    </div>
                    <p class="text-[8px] font-black text-agri-green uppercase tracking-[0.3em] mb-1 italic">Entidad de Destino</p>
                    <h4 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tight italic">"{{ $sol->organizacion->nombre ?? ($sol->datos_extra['nombre'] ?? 'SISTEMA') }}"</h4>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-x-8 gap-y-3 px-2">
                    <div class="space-y-0">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic leading-none">Identificación Fiscal</p>
                        <p class="text-xs font-black text-slate-700 dark:text-slate-300">RUC {{ $sol->organizacion->ruc ?? ($sol->datos_extra['ruc'] ?? 'N/A') }}</p>
                    </div>
                    <div class="space-y-0">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic leading-none">Ubicación de Sede</p>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 italic">{{ $sol->organizacion->direccion ?? ($sol->datos_extra['direccion'] ?? 'Ayacucho, Perú') }}</p>
                    </div>
                    <div class="space-y-0">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic leading-none">Correo Institucional</p>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 italic truncate">{{ $sol->organizacion->email ?? ($sol->datos_extra['email'] ?? 'No especificado') }}</p>
                    </div>
                    <div class="space-y-0">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic leading-none">Teléfono de Contacto</p>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 tabular-nums">{{ $sol->organizacion->telefono ?? ($sol->datos_extra['telefono'] ?? 'Sin número') }}</p>
                    </div>
                </div>

                <!-- Status Card (Bottom) -->
                <div class="bg-agri-l_card/20 dark:bg-black/20 p-5 rounded-xl border border-agri-green/10 text-center">
                    <p class="text-[9px] font-black text-agri-green uppercase tracking-[0.2em] mb-2 italic leading-none">Situación del Trámite</p>
                    <p class="text-sm font-black text-slate-700 dark:text-white uppercase tracking-wider">
                        @if($sol->tipo === 'creacion_organizacion')
                            REGISTRO DE NUEVA EMPRESA
                        @elseif($sol->tipo === 'unirse_organizacion')
                            SOLICITUD DE INGRESO
                        @else
                            INVITACIÓN AL CARGO DE {{ $sol->datos_extra['rol_nombre'] ?? 'AGRICULTOR' }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="px-8 py-6 bg-slate-50 dark:bg-black/20 border-t border-slate-100 dark:border-white/5 flex justify-end gap-4">
                @if($sol->estado == 0 && $sol->destinatario_usuario_id === auth()->id())
                    <button wire:click="rechazar({{ $sol->id }})" class="px-8 py-3 bg-white dark:bg-white/5 border border-rose-200 text-rose-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-50 transition-all italic">
                        Declinar Invitación
                    </button>
                    <button wire:click="aceptar({{ $sol->id }})" class="px-10 py-3 bg-agri-green text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-agri-green/30 hover:scale-[1.02] active:scale-95 transition-all italic">
                        Aceptar y Unirme
                    </button>
                @else
                    <button @click="$dispatch('close')" wire:click="close" class="px-10 py-3 bg-slate-800 text-white rounded-xl font-black text-[10px] uppercase tracking-widest italic shadow-xl shadow-black/20">
                        Cerrar Registro
                    </button>
                @endif
            </div>
        </div>
    </x-modal>
    @endforeach
</div>

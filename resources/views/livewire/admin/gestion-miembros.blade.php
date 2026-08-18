<div class="space-y-8 p-4 md:p-1 transition-colors duration-500">
    <!-- Header de Organización Estilo ADMIRO -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 px-4">
        <div class="flex items-center space-x-5">
            <div class="w-16 h-16 bg-[#003a38] rounded-2xl flex items-center justify-center shadow-2xl border border-white/10">
                <i class="fa-solid fa-people-group text-agri-green text-3xl"></i>
            </div>
            <div>
                <h2 class="text-4xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">{{ $organizacion->nombre }}</h2>
                <div class="mt-2">
                    @foreach($miRolEnOrg as $rol)
                        <span class="text-[10px] font-black uppercase px-4 py-1.5 bg-agri-green text-white rounded-full shadow-sm tracking-[0.2em]">{{ $rol }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4">
            @if(in_array('Administrador', $miRolEnOrg))
                <a href="{{ route('admin.organizacion.asignar-supervisores', ['id' => $orgId]) }}"
                   class="px-8 py-3 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-agri-l_card transition-all shadow-sm italic">
                    <i class="fa-solid fa-user-check mr-2 text-agri-green"></i> Asignar Supervisores
                </a>
            @endif

            @if(!in_array('Supervisor', $miRolEnOrg) && !in_array('Administrador', $miRolEnOrg))
                <button wire:click="solicitarCargo('supervisor')"
                        class="px-8 py-3 bg-[#fffce6] dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30 text-amber-600 dark:text-amber-400 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:scale-105 transition-all shadow-sm italic">
                    Solicitar ser Supervisor
                </button>
            @endif

            <button wire:click="abandonarOrganizacion" onclick="confirm('¿Estás seguro?') || event.stopImmediatePropagation()"
                    class="px-8 py-3 bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30 text-rose-500 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-rose-600 hover:text-white transition-all shadow-sm italic">
                Abandonar
            </button>
        </div>
    </div>

    <!-- Buscador e Invitación (Solo Admin) -->
    @if(in_array('Administrador', $miRolEnOrg))
    <div class="mx-4 bg-white dark:bg-agri-d_bg p-6 rounded-3xl border border-slate-100 dark:border-white/5 shadow-2xl flex flex-col md:flex-row items-center gap-4 transition-all relative overflow-visible">
        <div class="relative flex-1 w-full group">
            <span class="absolute inset-y-0 left-0 pl-6 flex items-center text-slate-400 group-focus-within:text-agri-green">
                <i class="fa-solid fa-user-plus"></i>
            </span>
            <input wire:model.live.debounce.300ms="searchUserInvite" type="text" placeholder="BUSCAR POR NOMBRE O DNI PARA INVITAR..."
                   class="w-full pl-14 pr-4 py-4 bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-agri-green/10 outline-none transition-all placeholder:italic">

            @if(!empty($usuariosEncontrados))
            <div class="absolute left-0 right-0 mt-3 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-white/10 z-50 overflow-hidden animate-in fade-in slide-in-from-top-2 duration-300">
                <div class="p-3 space-y-1">
                    @foreach($usuariosEncontrados as $user)
                    <div wire:click="seleccionarParaInvitar('{{ $user['dni'] }}', '{{ $user['nombres'] }} {{ $user['apellidos'] }}')"
                         class="flex items-center justify-between p-5 hover:bg-agri-green/5 rounded-2xl cursor-pointer group transition-all">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-full bg-agri-green/10 flex items-center justify-center text-agri-green font-black text-sm italic shadow-inner">
                                {{ substr($user['nombres'], 0, 1) }}{{ substr($user['apellidos'], 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-800 dark:text-white leading-tight italic uppercase tracking-tighter">{{ $user['nombres'] }} {{ $user['apellidos'] }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">DNI: {{ $user['dni'] }}</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-plus text-agri-green opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <div class="flex items-center gap-4 w-full md:w-auto">
            <select wire:model="rolInvitacion" class="bg-slate-50 dark:bg-white/5 border-none rounded-2xl text-[10px] font-black uppercase px-8 py-4 outline-none focus:ring-4 focus:ring-agri-green/10 transition-all cursor-pointer shadow-inner">
                @foreach($rolesCatalogo as $rol)
                    <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                @endforeach
            </select>
            <button wire:click="enviarInvitacion" class="flex-1 md:flex-none px-12 py-4 bg-agri-green text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-agri-green/20 hover:scale-105 active:scale-95 transition-all italic">
                Enviar
            </button>
        </div>
    </div>
    @endif

    <!-- PANEL PRINCIPAL DE MIEMBROS -->
    <div class="mx-4 bg-[#dee2e6] dark:bg-slate-900 rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-300 dark:border-white/10 transition-colors duration-500">

        <!-- Header con color #d2e3d5 -->
        <div class="bg-[#d2e3d5] dark:bg-agri-d_sidebar px-4 py-4 flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-agri-green/10">
            <div>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white tracking-tighter italic uppercase leading-none">Miembros de la Organización</h3>
                <p class="text-[10px] text-agri-green dark:text-white/40 font-black uppercase tracking-[0.4em] mt-3 italic leading-none">Gestión técnica de personal</p>
            </div>

            <div class="relative w-full md:w-96 group">
                <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-[14px]"></i>
                </span>
                <input wire:model.live="search" type="text" placeholder="FILTRAR POR NOMBRE O DNI..."
                       class="w-full pl-12 pr-4 py-3.5 bg-white/70 dark:bg-white/5 border border-slate-300 dark:border-white/10 rounded-2xl text-[13px] font-bold text-slate-800 dark:text-white placeholder-slate-400 focus:ring-4 focus:ring-agri-green/20 outline-none transition-all backdrop-blur-sm italic shadow-inner">
            </div>
        </div>

        <!-- Área de Contenido de Miembros -->
        <div class="p-8 md:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-10">
                @foreach($miembros as $miembro)
                @php
                    $esSupervisor = $miembro->roles->contains(fn($r) => $r->rolDetalle->nombre === 'Supervisor');
                    $soyYo = Auth::id() === $miembro->usuario_id;
                @endphp

                <!-- CARD DE MIEMBRO PREMIUM -->
                <div class="bg-white dark:bg-agri-d_bg rounded-[2rem] p-4 shadow-sm hover:shadow-2xl hover:-translate-y-3 transition-all duration-700 border-2 {{ $soyYo ? 'border-agri-green' : 'border-transparent' }} flex flex-col items-center text-center group relative cursor-pointer"
                     @if($esSupervisor && (in_array('Administrador', $miRolEnOrg) || $soyYo))
                        onclick="window.location.href='{{ route('admin.organizacion.supervisor', ['orgId' => $orgId, 'miembroId' => $miembro->id]) }}'"
                     @else
                        wire:click="showProfile({{ $miembro->id }})"
                     @endif>

                    <!-- Etiqueta 'TÚ' Estilo Imagen -->
                    @if($soyYo)
                        <div class="absolute top-4 left-4 px-3 py-1.5 bg-agri-green text-white text-[8px] font-black uppercase rounded-lg shadow-lg z-10 animate-pulse tracking-widest leading-none">Tú</div>
                    @endif

                    <!-- Avatar Circular (Estilo Imagen) -->
                    <div class="relative mb-6">
                        <div class="w-24 h-24 rounded-full border-4 border-agri-green/20 p-1 shadow-2xl transition-transform duration-700 group-hover:scale-110 flex items-center justify-center bg-slate-50 dark:bg-white/5 overflow-hidden">
                            @if($miembro->usuario->foto_perfil_url)
                                <img src="{{ $miembro->usuario->foto_perfil_url }}" class="w-full h-full rounded-full object-cover">
                            @else
                                <div class="w-full h-full rounded-full bg-agri-green flex items-center justify-center text-white text-3xl font-black italic shadow-inner">
                                    {{ substr($miembro->usuario->nombres, 0, 1) }}{{ substr($miembro->usuario->apellidos, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        @if($miembro->estado == 0)
                            <div class="absolute -top-1 -right-1 w-8 h-8 bg-rose-500 rounded-full flex items-center justify-center border-4 border-white dark:border-agri-d_bg text-white shadow-xl">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1.5 mb-6 w-full">
                        <p class="text-lg font-black text-slate-800 dark:text-white leading-tight truncate px-2 italic uppercase tracking-tighter">{{ $miembro->usuario->nombres }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em]">DNI: {{ $miembro->usuario->dni }}</p>
                    </div>

                    @php
                        $asignacion = $miembro->usuario->misSupervisores->where('organizacion_id', $orgId)->first();
                        $miSupervisor = $asignacion?->supervisor;
                    @endphp

                    @if($miSupervisor)
                        <div class=" flex items-center space-x-2 px-3 py-1.5 bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30 rounded-xl shadow-sm">
                            <i class="fa-solid fa-user-tie text-[10px] text-amber-500"></i>
                            <div class="min-w-0 text-left">
                                <p class="text-[9px] font-black text-slate-400 uppercase leading-none tracking-widest mb-0.5">Supervisado por:</p>
                                <p class="text-[10px] font-black text-amber-700 dark:text-amber-400 truncate leading-none italic uppercase">{{ $miSupervisor->usuario->nombres }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-auto flex flex-wrap justify-center gap-2">
                        @foreach($miembro->roles as $mRol)
                            <span class="px-4 py-1.5 bg-slate-50 dark:bg-agri-green/10 border border-slate-100 dark:border-agri-green/20 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] italic
                                {{ $mRol->rolDetalle->nombre === 'Administrador' ? 'text-blue-500' : ($mRol->rolDetalle->nombre === 'Supervisor' ? 'text-amber-500' : 'text-agri-green') }}">
                                {{ $mRol->rolDetalle->nombre }}
                            </span>
                        @endforeach
                    </div>

                    <!-- Acciones Rápidas (Solo Admin) -->
                    @if(in_array('Administrador', $miRolEnOrg) && Auth::id() !== $miembro->usuario_id)
                    <div class="absolute top-4 right-4 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition-all duration-500 scale-90 group-hover:scale-100">
                        <button wire:click.stop="toggleBloqueo({{ $miembro->id }})" class="w-9 h-9 bg-white dark:bg-slate-800 text-amber-500 border border-amber-100 dark:border-white/5 rounded-xl flex items-center justify-center shadow-2xl hover:bg-amber-500 hover:text-white transition-all">
                            <i class="fa-solid {{ $miembro->estado ? 'fa-user-lock' : 'fa-user-check' }} text-xs"></i>
                        </button>
                        <button wire:click.stop="eliminarMiembro({{ $miembro->id }})" class="w-9 h-9 bg-white dark:bg-slate-800 text-rose-500 border border-rose-100 dark:border-white/5 rounded-xl flex items-center justify-center shadow-2xl hover:bg-rose-500 hover:text-white transition-all">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="mt-16">
                {{ $miembros->links() }}
            </div>
        </div>
    </div>

    <!-- Modales Detallados -->
    @if($selectedMember)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
        <div class="bg-white dark:bg-agri-d_bg w-full max-w-xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/10 animate-in zoom-in duration-300">
            <div class="bg-[#003a38] px-12 py-10 flex justify-between items-center text-white border-b border-white/5">
                <div>
                    <h3 class="text-3xl font-black italic tracking-tighter uppercase">Perfil de Seguridad</h3>
                    <p class="text-[10px] opacity-60 uppercase font-black tracking-[0.4em] mt-2 italic">Validación de Identidad AgroSys</p>
                </div>
                <button wire:click="closeProfile" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white/10 hover:bg-rose-500 transition-all shadow-inner">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <div class="p-12 space-y-10">
                <div class="flex flex-col items-center">
                    <div class="w-36 h-32 rounded-full border-8 border-agri-green/10 p-1 shadow-2xl relative">
                        @if($selectedMember->usuario->foto_perfil_url)
                            <img src="{{ $selectedMember->usuario->foto_perfil_url }}" class="w-full h-full rounded-full object-cover">
                        @else
                            <div class="w-full h-full rounded-full bg-agri-green flex items-center justify-center text-white text-4xl font-black italic shadow-inner">
                                {{ substr($selectedMember->usuario->nombres, 0, 1) }}{{ substr($selectedMember->usuario->apellidos, 0, 1) }}
                            </div>
                        @endif
                        <div class="absolute bottom-1 right-1 w-12 h-12 bg-agri-green rounded-full border-4 border-white dark:border-agri-d_bg flex items-center justify-center text-white shadow-lg">
                            <i class="fa-solid fa-shield-check text-base"></i>
                        </div>
                    </div>
                    <h4 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tight mt-8 uppercase leading-none">{{ $selectedMember->usuario->nombres }} {{ $selectedMember->usuario->apellidos }}</h4>
                    <p class="text-[11px] text-agri-green font-black uppercase tracking-[0.6em] mt-3 leading-none">{{ $selectedMember->usuario->display_role }}</p>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="p-8 bg-slate-50 dark:bg-white/5 rounded-[2rem] border border-slate-100 dark:border-white/5 text-center shadow-inner group transition-all hover:border-agri-green/30">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">DNI Verificado</p>
                        <p class="text-base font-black text-slate-700 dark:text-slate-300 tracking-[0.3em] tabular-nums">{{ $selectedMember->usuario->dni }}</p>
                    </div>
                    <div class="p-8 bg-slate-50 dark:bg-white/5 rounded-[2rem] border border-slate-100 dark:border-white/5 text-center overflow-hidden shadow-inner group transition-all hover:border-agri-green/30">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Ubicación</p>
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300 italic truncate uppercase tracking-tighter">{{ $selectedMember->usuario->ubicacion ?? 'NO REGISTRADA' }}</p>
                    </div>
                </div>
            </div>

            <div class="p-10 bg-slate-50 dark:bg-black/20 border-t border-slate-100 dark:border-white/5 flex justify-center">
                <button wire:click="closeProfile" class="w-full py-4 bg-agri-green text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.3em] shadow-xl shadow-agri-green/30 hover:scale-105 active:scale-95 transition-all italic">
                    Cerrar Expediente
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

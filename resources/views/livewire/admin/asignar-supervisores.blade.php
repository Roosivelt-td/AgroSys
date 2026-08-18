<div class="space-y-6 p-4 md:p-1 transition-colors duration-500">
    <!-- Header Profesional Compacto -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-100 dark:border-white/5 pb-6">
        <div class="flex items-center space-x-5">
            <div class="w-14 h-14 bg-[#003a38] rounded-xl flex items-center justify-center shadow-xl shadow-agri-green/10">
                <i class="fa-solid fa-user-shield text-agri-green text-2xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter">{{ $organizacion->nombre }}</h2>
                <p class="text-[9px] text-agri-green font-black uppercase tracking-[0.3em] mt-1 italic">Asignación de Supervisión</p>
            </div>
        </div>

        <div class="relative w-full md:w-72 group">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-white/40 group-focus-within:text-agri-green transition-colors">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input wire:model.live="search" type="text" placeholder="Buscar miembro..."
                   class="w-full pl-10 pr-4 py-2 bg-white dark:bg-agri-d_bg border border-slate-200 dark:border-white/10 rounded-xl text-xs focus:ring-4 focus:ring-agri-green/10 outline-none shadow-sm italic">
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-r-xl italic shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- TABLA DE CUADROS - Estilo Compacto 6 por fila -->
    <div class="bg-[#ced4da] dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden border border-slate-300 dark:border-white/10 transition-colors duration-500">
        <div class="bg-[#003a38] px-10 py-5">
            <h3 class="text-xl font-bold text-white tracking-tight italic">Miembros Disponibles</h3>
        </div>

        <div class="bg-[#dee2e6] dark:bg-agri-d_bg/30 p-6 md:p-10">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($miembros as $miembro)
                @php
                    $esSupervisor = $miembro->roles->contains(fn($r) => $r->rolDetalle->nombre === 'Supervisor');
                    $invitacionId = $invitacionesPendientes[$miembro->usuario_id] ?? null;
                @endphp

                <div class="bg-white dark:bg-agri-d_bg rounded-xl p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all border border-slate-200 dark:border-white/5 flex flex-col items-center text-center group relative">

                    <!-- Avatar Circular Pequeño -->
                    <div class="relative mb-3">
                        <div class="w-16 h-16 rounded-full border-2 {{ $esSupervisor ? 'border-amber-500' : 'border-agri-green/20' }} p-0.5 shadow-inner transition-transform group-hover:scale-110">
                            <img src="{{ $miembro->usuario->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($miembro->usuario->nombres).'&background='.($esSupervisor ? 'ff8f00' : '00ba2e').'&color=fff' }}"
                                 class="w-full h-full rounded-full object-cover">
                        </div>
                        @if($esSupervisor)
                            <div class="absolute -top-1 -right-1 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center border-2 border-white text-white shadow-lg">
                                <i class="fa-solid fa-star text-[7px]"></i>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs font-black text-slate-800 dark:text-white leading-tight truncate w-full px-1">{{ $miembro->usuario->nombres }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase truncate w-full">{{ $miembro->usuario->dni }}</p>
                    </div>

                    <div class="mt-3 flex flex-wrap justify-center gap-1">
                        @foreach($miembro->roles as $mRol)
                            <span class="px-2 py-0.5 bg-agri-l_card/40 dark:bg-agri-green/10 border border-agri-green/20 rounded-md font-black text-[7px] uppercase tracking-tighter italic
                                {{ $mRol->rolDetalle->nombre === 'Administrador' ? 'text-blue-500' : ($mRol->rolDetalle->nombre === 'Supervisor' ? 'text-amber-500' : 'text-agri-green') }}">
                                {{ $mRol->rolDetalle->nombre }}
                            </span>
                        @endforeach
                    </div>

                    <!-- Botón Dinámico Compacto -->
                    <div class="mt-5 w-full space-y-2">
                        @if($esSupervisor)
                            <a href="{{ route('admin.organizacion.supervisor', ['orgId' => $orgId, 'miembroId' => $miembro->id]) }}"
                               wire:navigate
                               class="block w-full py-2 bg-agri-green text-white rounded-lg font-black text-[8px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg text-center">
                                <i class="fa-solid fa-people-arrows mr-1"></i> Gestionar Equipo
                            </a>
                            <button wire:click="bajarGrado({{ $miembro->id }})"
                                    wire:confirm="¿Estás seguro de quitar el cargo de supervisor? Se perderán todas sus asignaciones de agricultores."
                                    class="w-full py-2 bg-rose-50 dark:bg-rose-900/10 text-rose-600 rounded-lg font-black text-[8px] uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all shadow-sm italic">
                                Bajar Grado
                            </button>
                        @elseif($invitacionId)
                            <button wire:click="cancelarInvitacion({{ $invitacionId }})"
                                    class="w-full py-2 bg-amber-50 dark:bg-amber-900/10 text-amber-600 border border-amber-200 rounded-lg font-black text-[8px] uppercase tracking-widest hover:bg-amber-100 transition-all shadow-sm">
                                Cancelar
                            </button>
                        @else
                            @if(!$miembro->es_propietario)
                                <button wire:click="invitar({{ $miembro->id }})"
                                        class="w-full py-2 bg-agri-green text-white rounded-lg font-black text-[8px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-agri-green/20">
                                    Invitar
                                </button>
                            @else
                                <div class="py-2 text-[10px] font-black text-slate-300 uppercase tracking-widest italic">Dueño</div>
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $miembros->links() }}
            </div>
        </div>
    </div>
</div>

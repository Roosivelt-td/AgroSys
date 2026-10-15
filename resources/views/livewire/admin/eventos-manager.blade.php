<div class="space-y-8 pb-20">
    <!-- Header principal -->
    <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-white/5 p-4 md:p-6">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-agri-green/5 rounded-full blur-3xl"></div>

        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-agri-green/10 text-agri-green text-[9px] font-black uppercase tracking-widest rounded border border-agri-green/20 italic">
                        <i class="fa-solid fa-users mr-1"></i> Comunicación & Agenda
                    </span>
                    @if($selectedOrgId)
                        @php
                            $currentOrgName = $organizaciones->firstWhere('id', $selectedOrgId)?->nombre;
                        @endphp
                        @if($currentOrgName)
                            <span class="px-3 py-1 bg-blue-500/10 text-blue-500 text-[9px] font-black uppercase tracking-widest rounded border border-blue-500/20 italic">
                                <i class="fa-solid fa-building mr-1"></i> {{ $currentOrgName }}
                            </span>
                        @endif
                    @endif
                </div>
                <h1 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-tight">
                    Eventos y <span class="text-agri-green">Reuniones</span>
                </h1>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-medium italic max-w-md leading-relaxed">
                    Agenda centralizada de capacitaciones, asambleas, reuniones técnicas y avisos de la organización.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if(count($organizaciones) > 1 || auth()->user()->rol_id === 1)
                    <div class="relative">
                        <select wire:model.live="selectedOrgId" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white text-xs font-bold rounded-xl px-4 py-3 pr-8 uppercase focus:ring-agri-green">
                            @foreach($organizaciones as $org)
                                <option value="{{ $org->id }}">{{ $org->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($canManageEvents)
                    <button wire:click="openCreateModal" class="px-6 py-3 bg-agri-green hover:bg-emerald-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 active:scale-95 transition-all italic flex items-center gap-2">
                        <i class="fa-solid fa-calendar-plus text-sm"></i>
                        <span>Programar Evento</span>
                    </button>
                @else
                    <div class="px-4 py-2.5 bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 rounded-xl text-xs font-bold flex items-center gap-2 italic">
                        <i class="fa-solid fa-eye text-sm"></i>
                        <span>Modo Lectura</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Mensajes Flash de Estado o Error -->
    @if (session()->has('status'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 p-4 rounded-2xl text-xs font-bold flex items-center justify-between animate-fade-in shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-base"></i>
                <span>{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 p-4 rounded-2xl text-xs font-bold flex items-center justify-between animate-fade-in shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if(!$hasActiveOrg)
        <!-- Tarjeta de bienvenida para usuarios recién registrados sin organización activa -->
        <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-900 rounded-[3rem] border border-dashed border-slate-200 dark:border-white/10 shadow-inner px-6">
            <div class="w-24 h-24 bg-agri-green/10 rounded-full flex items-center justify-center mb-6 relative">
                <i class="fa-solid fa-handshake text-5xl text-agri-green"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic tracking-tighter mb-3 text-center">
                ¡Bienvenido a AgroSys, {{ auth()->user()->nombres }}!
            </h3>
            <p class="text-slate-500 dark:text-slate-400 text-xs font-medium max-w-lg text-center italic leading-relaxed mb-8">
                Su cuenta se encuentra activa. Para acceder a la agenda de eventos, capacitaciones y reuniones técnicas, debe formar parte de una organización agrícola.
            </p>
            <a href="{{ route('profile.organizaciones') }}" class="px-8 py-3.5 bg-agri-green hover:bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all italic flex items-center gap-2">
                <i class="fa-solid fa-building"></i>
                <span>Ir a Mis Organizaciones</span>
            </a>
        </div>
    @else
        <!-- Tarjetas de Estadísticas Rápidas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Total Registrados</p>
                    <p class="text-xl font-black text-slate-800 dark:text-white">{{ $stats['total'] }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-agri-green/10 text-agri-green flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Próximos Eventos</p>
                    <p class="text-xl font-black text-slate-800 dark:text-white">{{ $stats['proximos'] }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Eventos para Hoy</p>
                    <p class="text-xl font-black text-slate-800 dark:text-white">{{ $stats['hoy'] }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-video"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Reuniones Virtuales</p>
                    <p class="text-xl font-black text-slate-800 dark:text-white">{{ $stats['virtuales'] }}</p>
                </div>
            </div>
        </div>

        <!-- Barra de Búsqueda y Pestañas de Filtro -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-100 dark:border-white/5 shadow-sm space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
            <!-- Pestañas de filtro por fecha -->
            <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800/60 p-1 rounded-xl overflow-x-auto scrollbar-none">
                <button wire:click="$set('filterStatus', 'proximos')" class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all {{ $filterStatus === 'proximos' ? 'bg-agri-green text-white shadow-md' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    <i class="fa-solid fa-forward mr-1 text-[10px]"></i> Próximos
                </button>
                <button wire:click="$set('filterStatus', 'hoy')" class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all {{ $filterStatus === 'hoy' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    <i class="fa-solid fa-bullseye mr-1 text-[10px]"></i> Para Hoy
                </button>
                <button wire:click="$set('filterStatus', 'pasados')" class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all {{ $filterStatus === 'pasados' ? 'bg-slate-700 text-white shadow-md' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    <i class="fa-solid fa-clock-history mr-1 text-[10px]"></i> Pasados
                </button>
                <button wire:click="$set('filterStatus', 'todos')" class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all {{ $filterStatus === 'todos' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    <i class="fa-solid fa-list mr-1 text-[10px]"></i> Todos
                </button>
            </div>

            <!-- Buscador -->
            <div class="relative flex-1 max-w-md">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="BUSCAR EVENTO POR TÍTULO, LUGAR O DESCRIPCIÓN..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl p-3 pl-10 text-xs font-bold uppercase text-slate-800 dark:text-white focus:ring-agri-green">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500 text-xs"><i class="fa-solid fa-xmark"></i></button>
                @endif
            </div>
        </div>

        <!-- Grilla de Eventos -->
        @if($eventos->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-200 dark:border-white/10 shadow-inner">
                <div class="w-24 h-24 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-calendar-xmark text-4xl text-slate-300 dark:text-white/20"></i>
                </div>
                <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase italic tracking-tighter mb-2">No se encontraron eventos</h3>
                <p class="text-slate-400 text-xs font-medium max-w-sm text-center italic leading-relaxed">
                    No hay actividades ni reuniones registradas para el filtro seleccionado.
                </p>
                @if($canManageEvents)
                    <button wire:click="openCreateModal" class="mt-6 px-6 py-2.5 bg-agri-green text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition-all italic">
                        <i class="fa-solid fa-plus mr-1"></i> Programar Evento
                    </button>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($eventos as $ev)
                    @php
                        $fecha = \Carbon\Carbon::parse($ev->fecha_evento);
                        $esHoy = $fecha->isToday();
                        $esPasado = $fecha->isPast() && !$esHoy;
                    @endphp
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-white/10 overflow-hidden shadow-sm hover:shadow-xl transition-all flex flex-col justify-between group">
                        <!-- Cabecera de la tarjeta con fecha destacada -->
                        <div>
                            <div class="p-5 border-b border-slate-100 dark:border-white/5 flex items-start justify-between gap-4">
                                <!-- Recuadro Día / Mes -->
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl {{ $esHoy ? 'bg-amber-500 text-white' : ($esPasado ? 'bg-slate-100 dark:bg-white/5 text-slate-400' : 'bg-agri-green text-white') }} flex flex-col items-center justify-center shadow-md shrink-0">
                                        <span class="text-xs font-black uppercase leading-none opacity-80">{{ $fecha->translatedFormat('M') }}</span>
                                        <span class="text-xl font-black leading-tight">{{ $fecha->format('d') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded {{ $esHoy ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : ($esPasado ? 'bg-slate-100 dark:bg-white/5 text-slate-400' : 'bg-agri-green/10 text-agri-green border border-agri-green/20') }}">
                                            {{ $esHoy ? '¡HOY / EN VIVO!' : ($esPasado ? 'FINALIZADO' : 'PRÓXIMO') }}
                                        </span>
                                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-clock text-[10px]"></i>
                                            <span>{{ $fecha->format('h:i A') }} ({{ $ev->duracion_minutos }} min)</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Acciones flotantes en la tarjeta -->
                                <div class="flex items-center gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                    @if($this->canEditEvent($ev))
                                        <button wire:click="editEvento({{ $ev->id }})" title="Editar Evento" class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-white/5 text-slate-500 hover:text-agri-green hover:bg-agri-green/10 transition-colors flex items-center justify-center">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                    @endif

                                    <button wire:click="confirmDelete({{ $ev->id }})" title="{{ $this->canDeleteGlobal($ev) ? 'Opciones de Eliminación' : 'Quitar de mi vista' }}" class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-white/5 text-slate-500 hover:text-rose-500 hover:bg-rose-500/10 transition-colors flex items-center justify-center">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Cuerpo de la tarjeta -->
                            <div class="p-5 space-y-3">
                                <h3 wire:click="showDetail({{ $ev->id }})" class="text-base font-black text-slate-800 dark:text-white uppercase tracking-tight group-hover:text-agri-green transition-colors cursor-pointer line-clamp-2">
                                    {{ $ev->titulo }}
                                </h3>

                                @if($ev->descripcion)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium line-clamp-2 leading-relaxed">
                                        {{ $ev->descripcion }}
                                    </p>
                                @endif

                                <!-- Ubicación presencial u online -->
                                <div class="space-y-1.5 pt-2">
                                    @if($ev->lugar)
                                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                                            <i class="fa-solid fa-location-dot text-agri-green shrink-0"></i>
                                            <span class="truncate uppercase">{{ $ev->lugar }}</span>
                                        </div>
                                    @endif

                                    @if($ev->enlace_virtual)
                                        <a href="{{ $ev->enlace_virtual }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3 py-1.5 bg-purple-500/10 hover:bg-purple-500 text-purple-600 hover:text-white dark:text-purple-400 rounded-xl text-xs font-black uppercase tracking-wider transition-all border border-purple-500/20 shadow-xs">
                                            <i class="fa-solid fa-video text-xs"></i>
                                            <span>Unirse a Reunión Virtual</span>
                                            <i class="fa-solid fa-up-right-from-square text-[9px]"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Pie de la tarjeta -->
                        <div class="p-4 bg-slate-50/50 dark:bg-white/[0.02] border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full overflow-hidden bg-agri-green/20 text-agri-green font-black flex items-center justify-center text-[10px]">
                                    {{ strtoupper(substr($ev->creador?->nombres ?? 'A', 0, 1)) }}
                                </div>
                                <span class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 truncate max-w-[130px]">
                                    {{ $ev->creador?->nombres ?? 'Organizador' }}
                                </span>
                            </div>

                            <button wire:click="showDetail({{ $ev->id }})" class="text-[10px] font-black text-agri-green hover:underline uppercase tracking-wider flex items-center gap-1">
                                <span>Ver Detalles</span>
                                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="pt-4">
                {{ $eventos->links() }}
            </div>
        @endif
    @endif

    <!-- 1. MODAL CREAR / EDITAR EVENTO (SOLO ADMINISTRADORES / SUPERADMIN) -->
    @if($canManageEvents)
        <x-modal name="modal-evento" :show="false">
            <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
                <div class="bg-[#003a38] px-6 py-4 flex justify-between items-center text-white">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-calendar-plus text-agri-green text-lg"></i>
                        <h3 class="text-base font-black tracking-tighter uppercase italic">
                            {{ $eventId ? 'Editar Evento / Reunión' : 'Programar Nuevo Evento' }}
                        </h3>
                    </div>
                    <button type="button" @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 transition-colors"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form wire:submit.prevent="saveEvento" class="p-6 space-y-4">
                    @if($errors->any())
                        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 p-4 rounded-xl text-xs font-bold space-y-2">
                            <div class="flex items-center gap-2 font-black">
                                <i class="fa-solid fa-circle-exclamation text-base shrink-0 text-rose-500"></i>
                                <span>Atención: No se pudo registrar el evento por los siguientes motivos:</span>
                            </div>
                            <ul class="list-disc list-inside pl-2 space-y-1 text-[11px] font-bold">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Selector de Organización si aplica -->
                    @if(count($organizaciones) > 1 || auth()->user()->rol_id === 1)
                        <div class="space-y-1">
                            <label class="text-[9px] font-black uppercase text-slate-400">Organización *</label>
                            <select wire:model="selectedOrgId" class="w-full bg-slate-50 dark:bg-slate-800 border @error('selectedOrgId') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-xl p-3 text-xs font-bold uppercase text-slate-800 dark:text-white">
                                @foreach($organizaciones as $org)
                                    <option value="{{ $org->id }}">{{ $org->nombre }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('selectedOrgId')" class="mt-1 text-xs" />
                        </div>
                    @endif

                    <!-- Alcance / Tipo de Destinatarios -->
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400">Alcance / Destinatarios de la Notificación *</label>
                        <select wire:model="destinatario_tipo" class="w-full bg-slate-50 dark:bg-slate-800 border @error('destinatario_tipo') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-xl p-3 text-xs font-bold uppercase text-slate-800 dark:text-white">
                            @if(auth()->user()->rol_id === 1)
                                <option value="todos_usuarios">🌐 A TODOS LOS USUARIOS DE LA PLATAFORMA (GLOBAL)</option>
                                <option value="todos_admins_org">🏢 A TODOS LOS ADMINISTRADORES DE ORGANIZACIONES</option>
                                <option value="organizacion_completa">👥 A TODOS LOS MIEMBROS DE LA ORGANIZACIÓN SELECCIONADA</option>
                                <option value="organizacion_admins">👔 SOLO A ADMINISTRADORES DE LA ORGANIZACIÓN SELECCIONADA</option>
                            @else
                                <option value="organizacion_completa">👥 A TODOS LOS MIEMBROS DE MI ORGANIZACIÓN</option>
                                <option value="organizacion_supervisores_agricultores">🌾 A SUPERVISORES Y AGRICULTORES DE MI ORGANIZACIÓN</option>
                                <option value="organizacion_admins">👔 SOLO A ADMINISTRADORES Y DIRECTORES DE MI ORGANIZACIÓN</option>
                            @endif
                        </select>
                        <x-input-error :messages="$errors->get('destinatario_tipo')" class="mt-1 text-xs" />
                    </div>

                    <!-- Título -->
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400">Título del Evento / Asamblea / Capacitación *</label>
                        <input type="text" wire:model="titulo" placeholder="EJ. ASAMBLEA GENERAL DE SOCIOS / CAPACITACIÓN EN FERTILIZACIÓN" class="w-full bg-slate-50 dark:bg-slate-800 border @error('titulo') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-xl p-3 text-xs font-bold uppercase text-slate-800 dark:text-white">
                        <x-input-error :messages="$errors->get('titulo')" class="mt-1 text-xs" />
                    </div>

                    <!-- Fecha / Hora y Duración -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black uppercase text-slate-400">Fecha y Hora de Inicio *</label>
                            <input type="datetime-local" wire:model="fecha_evento" class="w-full bg-slate-50 dark:bg-slate-800 border @error('fecha_evento') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-xl p-3 text-xs font-bold text-slate-800 dark:text-white">
                            <x-input-error :messages="$errors->get('fecha_evento')" class="mt-1 text-xs" />
                        </div>

                        <div class="space-y-1">
                            <label class="text-[9px] font-black uppercase text-slate-400">Duración Estimada (Minutos) *</label>
                            <select wire:model="duracion_minutos" class="w-full bg-slate-50 dark:bg-slate-800 border @error('duracion_minutos') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-xl p-3 text-xs font-bold text-slate-800 dark:text-white">
                                <option value="30">30 minutos</option>
                                <option value="45">45 minutos</option>
                                <option value="60">1 hora (60 min)</option>
                                <option value="90">1.5 horas (90 min)</option>
                                <option value="120">2 horas (120 min)</option>
                                <option value="180">3 horas (180 min)</option>
                                <option value="240">4 horas (240 min)</option>
                            </select>
                            <x-input-error :messages="$errors->get('duracion_minutos')" class="mt-1 text-xs" />
                        </div>
                    </div>

                    <!-- Lugar presencial y Enlace Virtual -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black uppercase text-slate-400">Lugar Presencial (Opcional)</label>
                            <input type="text" wire:model="lugar" placeholder="EJ. OFICINA PRINCIPAL / PARCELA SAN ISIDRO" class="w-full bg-slate-50 dark:bg-slate-800 border @error('lugar') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-xl p-3 text-xs font-bold uppercase text-slate-800 dark:text-white">
                            <x-input-error :messages="$errors->get('lugar')" class="mt-1 text-xs" />
                        </div>

                        <div class="space-y-1">
                            <label class="text-[9px] font-black uppercase text-slate-400">Enlace Reunión Virtual (Opcional)</label>
                            <input type="url" wire:model="enlace_virtual" placeholder="EJ. HTTPS://MEET.GOOGLE.COM/ABC-DEFG-HIJ" class="w-full bg-slate-50 dark:bg-slate-800 border @error('enlace_virtual') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-xl p-3 text-xs font-bold text-slate-800 dark:text-white">
                            <x-input-error :messages="$errors->get('enlace_virtual')" class="mt-1 text-xs" />
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400">Descripción / Agenda del Evento</label>
                        <textarea wire:model="descripcion" rows="3" placeholder="DETALLES DE LA REUNIÓN, PUNTOS A TRATAR, REQUISITOS O INDICACIONES..." class="w-full bg-slate-50 dark:bg-slate-800 border @error('descripcion') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-xl p-3 text-xs font-bold text-slate-800 dark:text-white"></textarea>
                        <x-input-error :messages="$errors->get('descripcion')" class="mt-1 text-xs" />
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-white/5">
                        <button type="button" @click="$dispatch('close')" class="px-5 py-2.5 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-200 transition-all">
                            Cancelar
                        </button>
                        <button type="submit" class="px-8 py-2.5 bg-agri-green hover:bg-emerald-600 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-md transition-all flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>{{ $eventId ? 'Guardar Cambios' : 'Programar Evento y Notificar' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>
    @endif

    <!-- 2. MODAL DETALLE DE EVENTO -->
    <x-modal name="modal-detalle-evento" :show="false">
        @if($detailEvent)
            <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10">
                <div class="bg-[#003a38] px-6 py-4 flex justify-between items-center text-white">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-agri-green text-lg"></i>
                        <h3 class="text-base font-black tracking-tighter uppercase italic">Detalle de la Reunión / Evento</h3>
                    </div>
                    <button type="button" @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 transition-colors"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded bg-agri-green/10 text-agri-green border border-agri-green/20">
                            {{ $detailEvent->organizacion?->nombre }}
                        </span>
                        <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase mt-2 leading-snug">
                            {{ $detailEvent->titulo }}
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-100 dark:border-white/5">
                        <div>
                            <p class="text-[9px] font-black uppercase text-slate-400">Fecha y Hora</p>
                            <p class="text-xs font-black text-slate-800 dark:text-white mt-0.5">
                                <i class="fa-solid fa-calendar mr-1 text-agri-green"></i>
                                {{ \Carbon\Carbon::parse($detailEvent->fecha_evento)->translatedFormat('l, d \d\e F \d\e Y — h:i A') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase text-slate-400">Duración Estimada</p>
                            <p class="text-xs font-black text-slate-800 dark:text-white mt-0.5">
                                <i class="fa-solid fa-hourglass-half mr-1 text-agri-green"></i>
                                {{ $detailEvent->duracion_minutos }} Minutos
                            </p>
                        </div>
                    </div>

                    @if($detailEvent->lugar || $detailEvent->enlace_virtual)
                        <div class="space-y-3">
                            <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider">Ubicación y Acceso</h4>
                            @if($detailEvent->lugar)
                                <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/30 p-3 rounded-xl border border-slate-100 dark:border-white/5">
                                    <i class="fa-solid fa-location-dot text-agri-green text-base"></i>
                                    <div>
                                        <p class="text-[9px] font-black uppercase text-slate-400">Lugar Presencial</p>
                                        <p class="text-xs font-bold text-slate-800 dark:text-white uppercase">{{ $detailEvent->lugar }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($detailEvent->enlace_virtual)
                                <div class="flex items-center justify-between bg-purple-500/10 p-3 rounded-xl border border-purple-500/20">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-video text-purple-600 dark:text-purple-400 text-base"></i>
                                        <div>
                                            <p class="text-[9px] font-black uppercase text-purple-600 dark:text-purple-400">Videollamada / Virtual</p>
                                            <p class="text-xs font-bold text-slate-800 dark:text-white truncate max-w-xs">{{ $detailEvent->enlace_virtual }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ $detailEvent->enlace_virtual }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-1 shadow-md">
                                        <span>Entrar</span>
                                        <i class="fa-solid fa-up-right-from-square text-[9px]"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($detailEvent->descripcion)
                        <div class="space-y-2">
                            <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider">Agenda / Descripción</h4>
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/30 rounded-2xl border border-slate-100 dark:border-white/5 text-xs font-medium text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                {{ $detailEvent->descripcion }}
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <i class="fa-solid fa-user-gear text-slate-400"></i>
                            <span>Organizado por: <strong class="text-slate-700 dark:text-slate-200">{{ $detailEvent->creador?->nombres ?? 'AgroSys Admin' }}</strong></span>
                        </div>

                        <button type="button" @click="$dispatch('close')" class="px-6 py-2.5 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-200 transition-all">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </x-modal>

    <!-- 3. MODAL CONFIRMAR ELIMINACIÓN / OCULTAMIENTO PERSONAL -->
    <x-modal name="modal-delete-evento" :show="false">
        <div class="p-6 bg-white dark:bg-slate-900 rounded-2xl space-y-4">
            <div class="w-12 h-12 rounded-full bg-rose-500/10 text-rose-500 flex items-center justify-center text-xl mx-auto">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <div class="text-center space-y-2">
                <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase italic">
                    {{ $isDeletingCreator ? '¿Eliminar Evento / Reunión?' : '¿Quitar de mi Vista Personal?' }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                    @if($isDeletingCreator)
                        Usted es el creador de <strong class="text-slate-800 dark:text-white">'{{ $deletingEventTitle }}'</strong>. Puede quitarlo únicamente de su vista personal o eliminarlo definitivamente para todos los usuarios.
                    @else
                        Esta opción quitará la tarjeta de la reunión <strong class="text-slate-800 dark:text-white">'{{ $deletingEventTitle }}'</strong> únicamente de su vista personal. Esto no afectará a los demás usuarios.
                    @endif
                </p>
            </div>

            <div class="flex flex-col gap-2 pt-2">
                @if($isDeletingCreator)
                    <button type="button" wire:click="deleteEvento('global')" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-lg shadow-rose-600/20 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-users-slash text-xs"></i>
                        <span>Eliminar Definitivamente para Todos</span>
                    </button>
                    <button type="button" wire:click="deleteEvento('personal')" class="w-full py-3 bg-slate-200 dark:bg-white/10 hover:bg-slate-300 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-eye-slash text-xs"></i>
                        <span>Quitar solo de mi vista</span>
                    </button>
                @else
                    <button type="button" wire:click="deleteEvento('personal')" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-lg shadow-rose-600/20 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-eye-slash text-xs"></i>
                        <span>Quitar de mi Vista Personal</span>
                    </button>
                @endif

                <button type="button" @click="$dispatch('close')" class="w-full py-2 bg-transparent text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs font-bold uppercase tracking-wider">
                    Cancelar
                </button>
            </div>
        </div>
    </x-modal>
</div>

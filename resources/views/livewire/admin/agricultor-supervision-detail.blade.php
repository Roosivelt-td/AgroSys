<div class="space-y-6 p-4 md:p-1 transition-colors duration-500">
    <!-- Header: Perfil del Agricultor Supervisado -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-100 dark:border-white/5 pb-8">
        <div class="flex items-center space-x-6">
            <a href="{{ route('supervisor.agricultores') }}" class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center shadow-lg hover:bg-agri-green hover:text-white transition-all">
                <i class="fa-solid fa-chevron-left text-xl"></i>
            </a>
            <div class="flex items-center space-x-5">
                <div class="relative">
                    <div class="w-20 h-20 rounded-full border-4 border-agri-green p-0.5 shadow-2xl">
                        <img src="{{ $agricultor->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($agricultor->nombres).'&background=00ba2e&color=fff' }}"
                             class="w-full h-full rounded-full object-cover">
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-agri-green rounded-full border-4 border-white dark:border-agri-d_bg flex items-center justify-center text-white text-[10px]">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter">{{ $agricultor->nombres }} {{ $agricultor->apellidos }}</h2>
                    <p class="text-[10px] text-agri-green font-black uppercase tracking-[0.3em] mt-1 italic">Vigilancia Activa en {{ $organizacion->nombre }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ficha Técnica del Agricultor -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl transition-all hover:border-agri-green/30">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Identidad</p>
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-id-card text-agri-green"></i>
                <span class="text-sm font-black text-slate-700 dark:text-slate-300 font-mono tracking-tighter">DNI: {{ $agricultor->dni }}</span>
            </div>
        </div>
        <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Contacto</p>
            <div class="flex items-center space-x-3 truncate">
                <i class="fa-solid fa-envelope text-agri-green"></i>
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $agricultor->email }}</span>
            </div>
        </div>
        <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Ubicación</p>
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-location-dot text-agri-green"></i>
                <span class="text-xs font-black text-slate-700 dark:text-slate-300 italic">{{ $agricultor->ubicacion ?? 'No registrada' }}</span>
            </div>
        </div>
        <div class="bg-white dark:bg-agri-d_bg p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-xl">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Experiencia</p>
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-briefcase text-agri-green"></i>
                <span class="text-xs font-black text-slate-700 dark:text-slate-300">{{ $agricultor->experiencia_anios ?? '0' }} Años en campo</span>
            </div>
        </div>
    </div>

    <!-- Espacio para futura información técnica -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-10">
        <div class="lg:col-span-2 bg-white dark:bg-agri-d_bg p-10 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-white/5 flex flex-col items-center justify-center text-center opacity-60">
            <div class="w-20 h-20 bg-agri-l_card dark:bg-white/5 rounded-full flex items-center justify-center text-agri-green mb-6 shadow-inner">
                <i class="fa-solid fa-map-location-dot text-3xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 dark:text-white italic">Módulo de Producción Próximamente</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto mt-2">Aquí podrás supervisar los terrenos, cultivos y labores registradas por este agricultor.</p>
        </div>

        <div class="bg-white dark:bg-agri-d_bg p-8 rounded-[3rem] border border-slate-100 dark:border-white/5 shadow-xl overflow-hidden relative group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-agri-green/5 -mr-10 -mt-10 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic">Últimas Acciones Registradas</p>
            <div class="space-y-4">
                <div class="flex items-center space-x-4 p-4 bg-slate-50 dark:bg-black/20 rounded-2xl border border-slate-100 dark:border-white/5">
                    <div class="w-2 h-2 bg-agri-green rounded-full shadow-lg shadow-agri-green/50"></div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 italic leading-tight">Asignación completada con éxito</p>
                        <p class="text-[9px] text-slate-400 font-black uppercase mt-1">Hoy, 08:30 AM</p>
                    </div>
                </div>
                <!-- ... más registros estáticos por ahora ... -->
            </div>
        </div>
    </div>
</div>

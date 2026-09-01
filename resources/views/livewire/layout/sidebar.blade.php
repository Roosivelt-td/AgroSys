<div class="flex flex-col h-full overflow-hidden">
    <!-- VISTA (FRONTEND) - Sidebar ADMIRO -->

    <!-- Brand Area -->
    <div class="h-20 flex items-center shrink-0 bg-agri-green shadow-lg relative z-10 transition-all duration-300"
         :class="sidebarCollapsed && !mobileOpen ? 'px-0 justify-center' : 'px-6'">

        <!-- Contenedor de Logos con Alternancia Dinámica -->
        <div class="flex items-center justify-center overflow-hidden w-full h-full">
            <!-- Logo Expandido (Img 1) -->
            <div x-show="!sidebarCollapsed || mobileOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="h-16 flex items-center justify-center px-4">
                <img src="{{ asset('AgroSys_completo.png') }}" alt="Logo Completo" class="h-16 object-contain">
            </div>

            <!-- Logo Colapsado / Icono (Img 2) -->
            <div x-show="sidebarCollapsed && !mobileOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="h-12 w-12 flex items-center justify-center">
                <img src="{{ asset('AgroSys_logo.png') }}" alt="Icono" class="h-12 w-12 object-contain shadow-sm">
                <img src="{{ asset('AgroSys_text.png') }}" alt="Icono" class="h-12 w-12 object-contain shadow-sm">
            </div>
        </div>
    </div>

    <!-- Navigation Area -->
    <div class="flex-1 overflow-y-auto custom-scrollbar px-2 py-4 space-y-6">

        <!-- Section: ADMINISTRACIÓN GLOBAL (Solo Super Admin) -->
        @can('superadmin-only')
        <div>
            <div class="px-3 mb-2" x-show="!sidebarCollapsed || mobileOpen" x-transition>
                <span class="text-[10px] font-black text-rose-500 dark:text-rose-400 uppercase tracking-[0.2em]">Administración Global</span>
            </div>
            <div class="space-y-0.5">
                <x-sidebar-link :href="route('admin.solicitudes')" :active="request()->routeIs('admin.solicitudes*')" icon="fa-solid fa-clipboard-list">
                    {{ __('Solicitudes Globales') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.usuarios')" :active="request()->routeIs('admin.usuarios*')" icon="fa-solid fa-users-viewfinder">
                    {{ __('Directorio Usuarios') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.organizaciones')" :active="request()->routeIs('admin.organizaciones*')" icon="fa-solid fa-building-shield">
                    {{ __('Organizaciones') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.catalogo-cultivos')" :active="request()->routeIs('admin.catalogo-cultivos*')" icon="fa-solid fa-book">
                    {{ __('Catálogo Maestro') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.historial')" :active="request()->routeIs('admin.historial*')" icon="fa-solid fa-clock-rotate-left">
                    {{ __('Historial General') }}
                </x-sidebar-link>
            </div>
        </div>
        @endcan

        <!-- Section: MI ORGANIZACIÓN (Visible para todos los miembros) -->
        @if(auth()->user()->membresias()->where('estado', 1)->exists())
        <div>
            <div class="px-3 mb-2" x-show="!sidebarCollapsed || mobileOpen" x-transition>
                <span class="text-[10px] font-black text-blue-500 dark:text-blue-400 uppercase tracking-[0.2em]">Mi Organización</span>
            </div>
            <div class="space-y-0.5">
                <x-sidebar-link :href="route('profile.organizaciones')" :active="request()->routeIs('profile.organizaciones') || request()->routeIs('admin.organizacion.*')" icon="fa-solid fa-building-wheat">
                    {{ __('Mis Organizaciones') }}
                </x-sidebar-link>

                @can('admin-org')
                    <x-sidebar-link :href="route('admin.solicitudes.internas')" :active="request()->routeIs('admin.solicitudes.internas*')" icon="fa-solid fa-envelope-open-text">
                        {{ __('Solicitudes Internas') }}
                    </x-sidebar-link>
                @endcan
            </div>
        </div>
        @endif

        <!-- Section: SUPERVISIÓN (Solo Supervisores) -->
        @can('supervisor-org')
        <div>
            <div class="px-3 mb-2" x-show="!sidebarCollapsed || mobileOpen" x-transition>
                <span class="text-[10px] font-black text-amber-500 dark:text-amber-400 uppercase tracking-[0.2em]">Supervisión</span>
            </div>
            <div class="space-y-0.5">
                <x-sidebar-link :href="route('supervisor.agricultores')" :active="request()->routeIs('supervisor.agricultores') || request()->routeIs('supervisor.agricultor.*')" icon="fa-solid fa-people-roof">
                    {{ __('Mis Agricultores') }}
                </x-sidebar-link>
                <x-sidebar-link href="#" icon="fa-solid fa-list-check">
                    {{ __('Tareas y Sugerencias') }}
                </x-sidebar-link>
            </div>
        </div>
        @endcan

        <!-- Section: PRODUCCIÓN (Base operativa) -->
        <div>
            <div class="px-3 mb-2" x-show="!sidebarCollapsed || mobileOpen" x-transition>
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Mi Producción</span>
            </div>
            <div class="space-y-0.5">
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="fa-solid fa-table-cells-large">
                    {{ __('Dashboard') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.terrenos')" :active="request()->routeIs('admin.terrenos*')" icon="fa-solid fa-map-location-dot">
                    {{ __('Mis Terrenos') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.cultivos')" :active="request()->routeIs('admin.cultivos*')" icon="fa-solid fa-seedling">
                    {{ __('Mis Cultivos') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.labores')" :active="request()->routeIs('admin.labores*')" icon="fa-solid fa-screwdriver-wrench">
                    {{ __('Mis Labores') }}
                </x-sidebar-link>
                <x-sidebar-link href="#" icon="fa-solid fa-wheat-awn">
                    {{ __('Mis Cosechas') }}
                </x-sidebar-link>
            </div>
        </div>

        <!-- Section: ANÁLISIS E INTELIGENCIA -->
        <div>
            <div class="px-3 mb-2" x-show="!sidebarCollapsed || mobileOpen" x-transition>
                <span class="text-[10px] font-black text-agri-green dark:text-emerald-400 uppercase tracking-[0.2em]">Inteligencia AgroSys</span>
            </div>
            <div class="space-y-0.5">
                <x-sidebar-link :href="route('admin.clima-ia')" :active="request()->routeIs('admin.clima-ia')" icon="fa-solid fa-cloud-showers-water">
                    {{ __('Clima IA') }}
                </x-sidebar-link>
                <x-sidebar-link href="#" icon="fa-solid fa-robot">
                    {{ __('Alertas IA') }}
                </x-sidebar-link>
                <x-sidebar-link href="#" icon="fa-solid fa-chart-pie">
                    {{ __('Reportes') }}
                </x-sidebar-link>
            </div>
        </div>

        <!-- Section: COMUNICACIÓN -->
        <div>
            <div class="px-3 mb-2" x-show="!sidebarCollapsed || mobileOpen" x-transition>
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Comunicación</span>
            </div>
            <div class="space-y-0.5">
                <x-sidebar-link :href="route('chat.index')" :active="request()->routeIs('chat.index')" icon="fa-solid fa-comments">
                    {{ __('Mensajería Técnica') }}
                </x-sidebar-link>
                <x-sidebar-link href="#" icon="fa-solid fa-calendar-days">
                    {{ __('Eventos y Reuniones') }}
                </x-sidebar-link>
            </div>
        </div>
    </div>

    <!-- Sidebar Bottom -->
    <div class="mt-auto border-t border-slate-100 dark:border-white/5 transition-all duration-300"
         :class="sidebarCollapsed && !mobileOpen ? 'p-2' : 'p-4'">
        <div class="flex items-center rounded-2xl transition-all duration-300"
             :class="[
                darkMode ? 'bg-white/5 border border-white/10' : 'bg-white shadow-sm border border-slate-50',
                sidebarCollapsed && !mobileOpen ? 'p-2 justify-center' : 'p-3'
             ]">
            <div class="w-9 h-9 rounded-full overflow-hidden border border-agri-green/30 shrink-0">
                <img src="{{ auth()->user()->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->nombres).'&color=FFFFFF&background=00ba2e' }}" class="w-full h-full object-cover">
            </div>
            <div class="ms-3 overflow-hidden" x-show="!sidebarCollapsed || mobileOpen" x-transition>
                <p class="text-[11px] font-bold truncate" :class="darkMode ? 'text-white' : 'text-slate-800'">
                    {{ auth()->user()->nombres }}
                </p>
                <p class="text-[9px] font-bold uppercase tracking-wider opacity-60 text-agri-green">
                    {{ auth()->user()->display_role }}
                </p>
            </div>
            <button wire:click="logout" class="ms-auto p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" x-show="!sidebarCollapsed || mobileOpen">
                <i class="fa-solid fa-power-off text-xs"></i>
            </button>
        </div>
    </div>
</div>

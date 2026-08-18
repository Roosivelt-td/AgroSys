<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
          mobileOpen: false,
          darkMode: localStorage.getItem('darkMode') === 'true',
          notificationsOpen: false,
          messagesOpen: false,
          profileOpen: false
      }"
      x-init="
          $watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', value));
          $watch('darkMode', value => {
              localStorage.setItem('darkMode', value);
              if (value) document.documentElement.classList.add('dark');
              else document.documentElement.classList.remove('dark');
          });
      "
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AgroSys') }}</title>

        <script>
            if (localStorage.getItem('darkMode') === 'true') document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; }
            .sidebar-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
            .custom-scrollbar::-webkit-scrollbar { width: 5px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #00ba2e44; border-radius: 10px; }

            @keyframes focus-pulse {
                0%, 100% { border-color: transparent; background-color: transparent; }
                50% { border-color: #00ba2e; background-color: rgba(0, 186, 46, 0.05); }
            }
            .animate-focus { animation: focus-pulse 2s infinite; border: 2px solid #00ba2e !important; border-radius: 1rem !important; }
        </style>
    </head>
    <body class="antialiased bg-agri-l_bg dark:bg-agri-d_bg text-slate-800 transition-colors duration-300">

        <div class="flex h-screen overflow-hidden">
            <!-- SIDEBAR UNIFICADO -->
            <aside
                class="fixed inset-y-0 left-0 z-50 flex flex-col sidebar-transition bg-agri-l_sidebar dark:bg-agri-d_sidebar shadow-2xl md:relative md:translate-x-0 border-r border-slate-100 dark:border-white/5"
                :class="{
                    'w-64': !sidebarCollapsed || mobileOpen,
                    'w-20': sidebarCollapsed && !mobileOpen,
                    '-translate-x-full': !mobileOpen && window.innerWidth < 768,
                    'translate-x-0': mobileOpen
                }"
            >
                <livewire:layout.sidebar />
            </aside>

            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
                <!-- HEADER UNIFICADO -->
                <header class="h-20 bg-agri-l_sidebar dark:bg-agri-d_sidebar border-b border-slate-100 dark:border-white/5 flex items-center justify-between px-6 md:px-10 sticky top-0 z-30 shadow-sm transition-colors duration-500">
                    <div class="flex items-center flex-1 space-x-6">
                        <button @click="if(window.innerWidth >= 768) { sidebarCollapsed = !sidebarCollapsed } else { mobileOpen = !mobileOpen }" class="text-slate-400 dark:text-white/60 hover:text-agri-green transition-all">
                            <i class="fa-solid fa-bars-staggered text-xl"></i>
                        </button>
                        <div class="hidden md:flex items-center w-full max-w-[400px] bg-white/50 dark:bg-white/10 px-5 py-2.5 rounded-full border border-slate-200 dark:border-white/10">
                            <input type="text" placeholder="Buscar..." class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder-slate-400 dark:text-white py-0">
                            <i class="fa-solid fa-magnifying-glass text-slate-300 dark:text-white/20 text-sm"></i>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button @click="darkMode = !darkMode" class="w-10 h-10 flex items-center justify-center text-slate-400 dark:text-white/80 hover:text-agri-green transition-all">
                            <i class="fa-solid" :class="darkMode ? 'fa-sun text-amber-400' : 'fa-moon'"></i>
                        </button>
                        <livewire:layout.message-center />
                        <livewire:layout.notification-center />
                        <div class="relative">
                            <button @click="profileOpen = !profileOpen" class="flex items-center space-x-3 ps-4 border-l border-slate-100 dark:border-white/10">
                                <div class="w-10 h-10 rounded-xl overflow-hidden border-2 border-agri-green shadow-sm">
                                    <img src="{{ Auth::user()->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->nombres).'&color=FFFFFF&background=00ba2e' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="hidden lg:block text-left">
                                    <p class="text-xs font-black text-slate-800 dark:text-white leading-none italic">{{ Auth::user()->nombres }}</p>
                                    <p class="text-[9px] font-black uppercase text-agri-green mt-1 tracking-tighter">{{ Auth::user()->display_role }}</p>
                                </div>
                            </button>
                            <div x-show="profileOpen" @click.away="profileOpen = false" x-cloak x-transition class="absolute right-0 mt-4 w-64 bg-white dark:bg-slate-900 shadow-2xl rounded-xl border border-slate-100 dark:border-white/10 z-50 p-2">
                                <div class="p-4 border-b border-slate-50 dark:border-white/5 text-center">
                                    <p class="text-sm font-black italic text-slate-800 dark:text-white">{{ Auth::user()->nombres }}</p>
                                    <p class="text-[10px] text-slate-400">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="p-2 space-y-1">
                                    <a href="{{ route('profile') }}" class="flex items-center px-5 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-agri-l_card dark:hover:bg-white/5 hover:text-agri-green rounded-lg transition-all" wire:navigate>
                                        <i class="fa-regular fa-user-circle mr-3 text-lg"></i> Ver mi Perfil
                                    </a>

                                    <a href="{{ route('profile.actividad') }}" class="flex items-center px-5 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-agri-l_card dark:hover:bg-white/5 hover:text-agri-green rounded-lg transition-all" wire:navigate>
                                        <i class="fa-solid fa-clock-rotate-left mr-3 text-lg"></i> Mi Actividad
                                    </a>

                                    @if(Auth::user()->rol_id !== 1)
                                        <button @click="$dispatch('open-modal', 'create-organization'); profileOpen = false" class="w-full flex items-center px-5 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-agri-l_card dark:hover:bg-white/5 hover:text-agri-green rounded-lg transition-all text-left">
                                            <i class="fa-solid fa-building-circle-plus mr-3 text-lg"></i> Solicitar Organización
                                        </button>
                                        <button @click="$dispatch('open-modal', 'join-organization'); profileOpen = false" class="w-full flex items-center px-5 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-agri-l_card dark:hover:bg-white/5 hover:text-agri-green rounded-lg transition-all text-left">
                                            <i class="fa-solid fa-paper-plane mr-3 text-lg"></i> Solicitar Unirse
                                        </button>
                                    @else
                                        <button @click="$dispatch('open-modal', 'create-superadmin'); profileOpen = false" class="w-full flex items-center px-5 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-agri-l_card dark:hover:bg-white/5 hover:text-rose-500 rounded-lg transition-all text-left">
                                            <i class="fa-solid fa-user-shield mr-3 text-lg"></i> Crear Nuevo Super Admin
                                        </button>
                                    @endif

                                    <div class="my-1 border-t border-slate-100 dark:border-white/5"></div>
                                    <livewire:layout.navigation />
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- WORKSPACE DINÁMICO -->
                <main class="flex-1 overflow-y-auto custom-scrollbar p-6 md:p-10 transition-colors duration-500 bg-agri-l_bg dark:bg-agri-d_bg">
                    <div class="max-w-[1600px] mx-auto pb-10">
                        @yield('content')
                        {{ $slot ?? '' }}
                    </div>
                </main>
            </div>
        </div>

        <x-modal name="create-organization" :show="false" focusable><livewire:organizaciones.crear-form /></x-modal>
        <x-modal name="join-organization" :show="false" focusable><livewire:organizaciones.solicitar-unirse /></x-modal>
        @can('superadmin-only')
            <x-modal name="create-superadmin" :show="false" focusable><livewire:admin.create-super-admin /></x-modal>
        @endcan
    </body>
</html>

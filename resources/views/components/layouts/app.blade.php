<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true',
        sidebarOpen: false
      }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AgroSys') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-emerald-950 text-gray-900 dark:text-gray-100 transition-colors duration-300" x-data="{ sidebarCollapsed: false, mobileOpen: false }">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-emerald-900 border-r border-gray-200 dark:border-emerald-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
            :class="mobileOpen ? 'translate-x-0' : '-translate-x-full'">

            <div class="flex items-center justify-between h-20 px-6 border-b border-gray-100 dark:border-emerald-800">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="h-10 w-auto">
                    <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400">AgroSys</span>
                </a>
                <button @click="mobileOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="fa-solid fa-gauge">Dashboard</x-sidebar-link>
                <x-sidebar-link href="#" icon="fa-solid fa-map-location-dot">Terrenos</x-sidebar-link>
                <x-sidebar-link href="#" icon="fa-solid fa-seedling">Cultivos</x-sidebar-link>
                <x-sidebar-link href="#" icon="fa-solid fa-hand-holding-droplet">Labores</x-sidebar-link>
                <x-sidebar-link href="#" icon="fa-solid fa-cloud-sun-rain">Clima IA</x-sidebar-link>

                <div class="pt-4 pb-2 border-t border-gray-100 dark:border-emerald-800">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administración</p>
                </div>
                <x-sidebar-link href="#" icon="fa-solid fa-users">Usuarios</x-sidebar-link>
                <x-sidebar-link href="#" icon="fa-solid fa-building">Organizaciones</x-sidebar-link>
                <x-sidebar-link href="#" icon="fa-solid fa-book">Catálogos</x-sidebar-link>
            </nav>

            <div class="p-4 border-t border-gray-100 dark:border-emerald-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30 rounded-xl transition-all italic font-bold">
                        <i class="fa-solid fa-right-from-bracket mr-3"></i>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Header -->
            <header class="h-20 bg-white dark:bg-emerald-900 border-b border-gray-200 dark:border-emerald-800 flex items-center justify-between px-4 lg:px-8 shrink-0">
                <div class="flex items-center gap-4">
                    <button @click="mobileOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-emerald-800">
                        <i class="fa-solid fa-bars text-gray-600 dark:text-gray-300"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                        {{ $header ?? 'Panel de Control' }}
                    </h1>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="p-2 rounded-xl bg-gray-100 dark:bg-emerald-800 text-gray-600 dark:text-emerald-400 hover:ring-2 ring-emerald-500 transition-all">
                        <i x-show="!darkMode" class="fa-solid fa-moon"></i>
                        <i x-show="darkMode" class="fa-solid fa-sun" x-cloak></i>
                    </button>

                    <!-- Notifications -->
                    <button class="relative p-2 rounded-xl bg-gray-100 dark:bg-emerald-800 text-gray-600 dark:text-emerald-400 hover:ring-2 ring-emerald-500 transition-all text-xl">
                        <i class="fa-solid fa-bell"></i>
                        <span class="absolute top-1 right-1 h-3 w-3 bg-red-500 border-2 border-white dark:border-emerald-900 rounded-full"></span>
                    </button>

                    <!-- User Profile -->
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200 dark:border-emerald-800">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ Auth::user()->nombres }}</p>
                            <p class="text-xs text-gray-500 dark:text-emerald-400 uppercase tracking-tighter font-semibold">Super Admin</p>
                        </div>
                        <img class="h-10 w-10 rounded-xl object-cover ring-2 ring-emerald-500" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nombres) }}&background=00ba2e&color=fff" alt="Avatar">
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-8 bg-gray-50 dark:bg-emerald-950/50 relative">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Scripts Globales -->
    <script>
        window.addEventListener('swal', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: event.detail.icon,
                confirmButtonColor: '#00ba2e',
            });
        });
    </script>

    @livewireScripts
</body>
</html>

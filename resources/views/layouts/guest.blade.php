<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ mobileMenu: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AgroSys') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .nav-blur { background: rgba(0, 58, 56, 0.4); backdrop-filter: blur(15px); }
            .nav-item { position: relative; }
            .nav-item::after {
                content: '';
                position: absolute;
                bottom: -4px;
                left: 0;
                width: 0;
                height: 2px;
                background: #00ba2e;
                transition: width 0.4s ease;
            }
            .nav-item:hover::after { width: 100%; }
        </style>
    </head>
    <body class="antialiased text-gray-900 overflow-x-hidden relative bg-agri-l_bg dark:bg-black transition-colors duration-300">

        <!-- Header de Información (Solo para no autenticados) -->
        <nav class="fixed top-0 left-0 right-0 z-[100] px-6 md:px-16 py-4 flex items-center justify-between transition-all duration-500"
             :class="scrolled ? 'nav-blur py-3 border-b border-white/5 shadow-2xl' : 'py-6'">

            <!-- Logo AgroSys -->
            <div class="flex items-center">
                <!--
                <a href="/" class="text-2xl md:text-3xl font-black italic tracking-tighter select-none flex items-center group">
                    <span class="bg-gradient-to-b from-[#55cd44] to-[#1b5e0f] bg-clip-text text-transparent px-1">Agro</span><span class="bg-gradient-to-b from-[#ff8a00] to-[#b71c1c] bg-clip-text text-transparent">Sys</span>
                </a> -->
                <div class=" h-32  flex items-center justify-center shadow-lg shrink-0">
                    <img src="{{ asset('AgroSys_completo.png') }}" alt="Logo" class=" h-32 object-contain">
                </div>
            </div>

            <!-- Menú de Navegación (Inspirado en Versat) -->
            <div class="hidden lg:flex items-center space-x-12">
                <a href="#" class="nav-item text-[10px] font-black uppercase text-white/80 hover:text-white tracking-[0.4em] transition-colors italic">Tecnología</a>
                <a href="#" class="nav-item text-[10px] font-black uppercase text-white/80 hover:text-white tracking-[0.4em] transition-colors italic">Gestión Agro</a>
                <a href="#" class="nav-item text-[10px] font-black uppercase text-white/80 hover:text-white tracking-[0.4em] transition-colors italic">Servicios IA</a>
                <a href="#" class="nav-item text-[10px] font-black uppercase text-white/80 hover:text-white tracking-[0.4em] transition-colors italic">Comunidad</a>
                <a href="#" class="nav-item text-[10px] font-black uppercase text-white/80 hover:text-white tracking-[0.4em] transition-colors italic">Nosotros</a>
                <a href="#" class="nav-item text-[10px] font-black uppercase text-white/80 hover:text-white tracking-[0.4em] transition-colors italic">Blog</a>

                <div class="h-6 w-px bg-white/20 mx-2"></div>

                <a href="{{ route('login') }}"
                   class="px-10 py-3 bg-agri-green text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.4em] hover:scale-105 active:scale-95 transition-all shadow-[0_15px_30px_-10px_rgba(0,186,46,0.4)] italic">
                    Entrar
                </a>
            </div>

            <!-- Botón Móvil -->
            <button @click="mobileMenu = !mobileMenu" class="lg:hidden w-12 h-12 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-white">
                <i class="fa-solid" :class="mobileMenu ? 'fa-xmark' : 'fa-bars-staggered'"></i>
            </button>
        </nav>

        <!-- Menú Móvil Desplegable -->
        <div x-show="mobileMenu"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-full"
             class="fixed inset-0 z-[90] bg-[#003a38] pt-28 px-10">
            <div class="flex flex-col space-y-8">
                <a @click="mobileMenu = false" href="#" class="text-xl font-black uppercase text-white tracking-[0.3em] italic border-b border-white/10 pb-4">Tecnología</a>
                <a @click="mobileMenu = false" href="#" class="text-xl font-black uppercase text-white tracking-[0.3em] italic border-b border-white/10 pb-4">Gestión Agro</a>
                <a @click="mobileMenu = false" href="#" class="text-xl font-black uppercase text-white tracking-[0.3em] italic border-b border-white/10 pb-4">Servicios IA</a>
                <a @click="mobileMenu = false" href="#" class="text-xl font-black uppercase text-white tracking-[0.3em] italic border-b border-white/10 pb-4">Comunidad</a>
                <a @click="mobileMenu = false" href="{{ route('login') }}" class="w-full py-6 bg-agri-green text-white text-center rounded-[2.5rem] font-black uppercase tracking-[0.5em] italic shadow-2xl">Entrar al Sistema</a>
            </div>
        </div>

        <main class="relative z-10">
            {{ $slot }}
        </main>

        <!-- Footer Informativo -->
        @if(!request()->routeIs('login') && !request()->routeIs('register'))
        <footer class="relative z-20 py-8 px-6 md:px-20 {{ request()->path() === '/' ? 'bg-black/60 backdrop-blur-3xl' : 'bg-white dark:bg-slate-900 border-t border-slate-50 dark:border-white/5' }}">
            <div class="max-w-[1400px] mx-auto grid grid-cols-1 md:grid-cols-4 gap-16">
                <div class="col-span-1 md:col-span-2 space-y-8">
                    <a href="/" class="text-4xl font-black italic tracking-tighter select-none">
                        <span class="bg-gradient-to-b from-[#55cd44] to-[#1b5e0f] bg-clip-text text-transparent px-1">Agro</span><span class="bg-gradient-to-b from-[#ff8a00] to-[#b71c1c] bg-clip-text text-transparent">Sys</span>
                    </a>
                    <p class="text-base {{ request()->path() === '/' ? 'text-white/50' : 'text-slate-500 dark:text-slate-400' }} max-w-sm leading-relaxed italic font-medium">
                        Liderando la transformación digital del campo con inteligencia artificial, trazabilidad forense y gestión centralizada.
                    </p>
                    <div class="flex space-x-6 pt-4">
                        <a href="#" class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-agri-green hover:bg-white/10 transition-all"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-agri-green hover:bg-white/10 transition-all"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-agri-green hover:bg-white/10 transition-all"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="space-y-6">
                    <h4 class="text-xs font-black uppercase text-agri-green tracking-[0.4em]">Plataforma</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-sm font-bold {{ request()->path() === '/' ? 'text-white/40 hover:text-white' : 'text-slate-600 hover:text-agri-green' }} transition-colors">Tecnología IA</a></li>
                        <li><a href="#" class="text-sm font-bold {{ request()->path() === '/' ? 'text-white/40 hover:text-white' : 'text-slate-600 hover:text-agri-green' }} transition-colors">Gestión de Equipos</a></li>
                        <li><a href="#" class="text-sm font-bold {{ request()->path() === '/' ? 'text-white/40 hover:text-white' : 'text-slate-600 hover:text-agri-green' }} transition-colors">Trazabilidad Total</a></li>
                        <li><a href="#" class="text-sm font-bold {{ request()->path() === '/' ? 'text-white/40 hover:text-white' : 'text-slate-600 hover:text-agri-green' }} transition-colors">Soporte 24/7</a></li>
                    </ul>
                </div>

                <div class="space-y-6">
                    <h4 class="text-xs font-black uppercase text-agri-green tracking-[0.4em]">Legal</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-sm font-bold {{ request()->path() === '/' ? 'text-white/40 hover:text-white' : 'text-slate-600 hover:text-agri-green' }} transition-colors">Términos de Uso</a></li>
                        <li><a href="#" class="text-sm font-bold {{ request()->path() === '/' ? 'text-white/40 hover:text-white' : 'text-slate-600 hover:text-agri-green' }} transition-colors">Privacidad</a></li>
                        <li><a href="#" class="text-sm font-bold {{ request()->path() === '/' ? 'text-white/40 hover:text-white' : 'text-slate-600 hover:text-agri-green' }} transition-colors">Cookies</a></li>
                    </ul>
                </div>
            </div>

            <div class="max-w-[1400px] mx-auto mt-8 pt-10 border-t border-white/5 text-center">
                <p class="text-[12px] font-black uppercase text-white/20 tracking-[0.5em] italic">&copy; {{ date('Y') }} AgroSys SaaS. Diseñado para el campo moderno.</p>
            </div>
        </footer>
        @endif
    </body>
</html>

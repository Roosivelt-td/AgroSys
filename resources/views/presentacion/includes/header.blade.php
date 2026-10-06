<!-- =====================================================
     HEADER / NAVIGATION MENU
====================================================== -->
<header id="header" x-data="{ open: false, solutionsOpen: false, quienesOpen: false }"
    @click.outside="open = false"
    class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="h-[82px] flex items-center justify-between">
            <!-- LOGO -->
            <a href="/" class="flex items-center gap-2 group">
                <img src="{{ asset('AgroSys_completo.png') }}" alt="AgroSys Logo" class="h-14 md:h-16 w-auto object-contain group-hover:scale-105 transition-transform">
            </a>

            <!-- DESKTOP NAV -->
            <nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-darkgreen">
                <!-- SOLUCIONES DROPDOWN -->
                <div class="relative" @click.outside="solutionsOpen = false">
                    <button @click="solutionsOpen = !solutionsOpen; quienesOpen = false"
                        class="flex items-center gap-1.5 hover:text-agricolus transition py-2 font-semibold">
                        <span>Soluciones</span>
                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform text-[#173B27]" :class="solutionsOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="solutionsOpen" x-transition x-cloak
                        class="absolute top-full left-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 py-3 px-2 z-50 space-y-1">
                        <a href="{{ route('soluciones.todas') }}"
                            class="block px-4 py-2.5 rounded-xl hover:bg-softgreen hover:text-agricolus transition text-sm">Todas las soluciones</a>
                        <a href="{{ route('soluciones.agronomica') }}"
                            class="block px-4 py-2.5 rounded-xl hover:bg-softgreen hover:text-agricolus transition text-sm">Gestión agronómica</a>
                        <a href="{{ route('soluciones.satelital') }}"
                            class="block px-4 py-2.5 rounded-xl hover:bg-softgreen hover:text-agricolus transition text-sm">Monitoreo satelital</a>
                        <a href="{{ route('soluciones.agrometeo') }}"
                            class="block px-4 py-2.5 rounded-xl hover:bg-softgreen hover:text-agricolus transition text-sm">Estaciones agrometeo</a>
                        <a href="{{ route('soluciones.defensa') }}"
                            class="block px-4 py-2.5 rounded-xl hover:bg-softgreen hover:text-agricolus transition text-sm">Defensa de cultivos</a>
                        <a href="{{ route('soluciones.exploracion') }}"
                            class="block px-4 py-2.5 rounded-xl hover:bg-softgreen hover:text-agricolus transition text-sm">Exploración de cultivos</a>
                        <a href="{{ route('soluciones.controlCadena') }}"
                            class="block px-4 py-2.5 rounded-xl hover:bg-softgreen hover:text-agricolus transition text-sm">Control cadena agroalimentaria</a>
                    </div>
                </div>

                <!-- QUIENES SOMOS DROPDOWN -->
                <div class="relative" @click.outside="quienesOpen = false">
                    <button @click="quienesOpen = !quienesOpen; solutionsOpen = false"
                        class="flex items-center gap-1.5 hover:text-agricolus transition py-2 font-semibold">
                        <span>Quiénes somos</span>
                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform text-[#173B27]" :class="quienesOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="quienesOpen" x-transition x-cloak
                        class="absolute top-full left-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-gray-100 py-3 px-2 z-50 space-y-1">
                        <a href="{{ route('soluciones.empresa') }}"
                            class="block px-4 py-2.5 rounded-xl hover:bg-softgreen hover:text-agricolus transition text-sm">Empresa</a>
                        <a href="{{ route('soluciones.investigacion') }}"
                            class="block px-4 py-2.5 rounded-xl hover:bg-softgreen hover:text-agricolus transition text-sm">Investigación industrial</a>
                        <a href="{{ route('soluciones.nuestraRed') }}"
                            class="block px-4 py-2.5 rounded-xl hover:bg-softgreen hover:text-agricolus transition text-sm">Nuestra red</a>
                        <a href="{{ route('soluciones.tecnologia') }}"
                            class="block px-4 py-2.5 rounded-xl hover:bg-softgreen hover:text-agricolus transition text-sm">Tecnología</a>
                    </div>
                </div>

                <a href="{{ route('soluciones.academy') }}" class="hover:text-agricolus transition font-semibold">Academy</a>
                <a href="{{ route('soluciones.sostenibilidad') }}" class="hover:text-agricolus transition font-semibold">Sostenibilidad</a>
                <a href="{{ route('soluciones.contactos') }}" class="hover:text-agricolus transition font-semibold">Contacto</a>
            </nav>

            <!-- DESKTOP CTA -->
            <div class="hidden lg:flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="bg-[#173B27] hover:bg-[#245337] text-white px-6 py-2.5 rounded-full font-bold text-sm tracking-wide transition shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-gauge text-[#78B82A]"></i>
                        <span>Ir al Dashboard</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700 transition px-3 py-2">
                            Cerrar sesión
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-darkgreen hover:text-agricolus transition px-4 py-2">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}"
                        class="bg-agricolus hover:bg-agricolusDark text-white px-6 py-3 rounded-full font-semibold transition shadow-sm">
                        Registrarse
                    </a>
                @endauth
            </div>

            <!-- RESPONSIVE MOBILE BUTTON (3 Líneas / Cerrar X) -->
            <button @click.stop="open = !open"
                    class="lg:hidden text-[#173B27] p-2.5 rounded-xl hover:bg-slate-100 transition-colors focus:outline-none"
                    aria-label="Menú Móvil">
                <i x-show="!open" class="fa-solid fa-bars text-2xl"></i>
                <i x-show="open" x-cloak class="fa-solid fa-xmark text-2xl"></i>
            </button>
        </div>

        <!-- RESPONSIVE MOBILE MENU (Oculto por defecto, se cierra al hacer clic en el espacio blanco fuera) -->
        <div x-show="open"
             x-cloak
             x-transition
             @click.outside="open = false"
             class="lg:hidden py-5 border-t border-gray-100 max-h-[85vh] overflow-y-auto bg-white px-4 rounded-b-2xl shadow-2xl relative">

            <!-- Mobile Menu Header with explicit 'X' Close Button -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
                <span class="text-xs font-black uppercase text-gray-400 tracking-wider">Menú de Navegación</span>
                <button @click="open = false" class="text-gray-500 hover:text-rose-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors flex items-center gap-1.5 text-xs font-bold">
                    <span>Cerrar</span>
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <nav class="flex flex-col gap-3 text-darkgreen font-medium">
                <div x-data="{ subSol: false }">
                    <button @click="subSol = !subSol" class="flex items-center justify-between w-full py-2 hover:text-agricolus font-semibold">
                        <span>Soluciones</span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="subSol ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="subSol" x-cloak class="pl-4 flex flex-col gap-2 py-2 text-sm text-gray-600">
                        <a href="{{ route('soluciones.todas') }}" class="py-1 hover:text-agricolus">Todas las soluciones</a>
                        <a href="{{ route('soluciones.agronomica') }}" class="py-1 hover:text-agricolus">Gestión agronómica</a>
                        <a href="{{ route('soluciones.satelital') }}" class="py-1 hover:text-agricolus">Monitoreo satelital</a>
                        <a href="{{ route('soluciones.agrometeo') }}" class="py-1 hover:text-agricolus">Estaciones agrometeo</a>
                        <a href="{{ route('soluciones.defensa') }}" class="py-1 hover:text-agricolus">Defensa de cultivos</a>
                        <a href="{{ route('soluciones.exploracion') }}" class="py-1 hover:text-agricolus">Exploración de cultivos</a>
                        <a href="{{ route('soluciones.controlCadena') }}" class="py-1 hover:text-agricolus">Control cadena agroalimentaria</a>
                    </div>
                </div>

                <div x-data="{ subQs: false }">
                    <button @click="subQs = !subQs" class="flex items-center justify-between w-full py-2 hover:text-agricolus font-semibold">
                        <span>Quiénes somos</span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="subQs ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="subQs" x-cloak class="pl-4 flex flex-col gap-2 py-2 text-sm text-gray-600">
                        <a href="{{ route('soluciones.empresa') }}" class="py-1 hover:text-agricolus">Empresa</a>
                        <a href="{{ route('soluciones.investigacion') }}" class="py-1 hover:text-agricolus">Investigación industrial</a>
                        <a href="{{ route('soluciones.nuestraRed') }}" class="py-1 hover:text-agricolus">Nuestra red</a>
                        <a href="{{ route('soluciones.tecnologia') }}" class="py-1 hover:text-agricolus">Tecnología</a>
                    </div>
                </div>

                <a href="{{ route('soluciones.academy') }}" class="py-2 hover:text-agricolus font-semibold">Academy</a>
                <a href="{{ route('soluciones.sostenibilidad') }}" class="py-2 hover:text-agricolus font-semibold">Sostenibilidad</a>
                <a href="{{ route('soluciones.contactos') }}" class="py-2 hover:text-agricolus font-semibold">Contacto</a>

                <div class="pt-4 border-t border-gray-100 flex flex-col gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full bg-[#173B27] text-white text-center py-2.5 rounded-full font-bold shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-gauge text-[#78B82A]"></i>
                            <span>Ir al Dashboard</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full text-center py-2.5 rounded-full border border-red-500 font-semibold text-red-600">
                                Cerrar sesión
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="w-full text-center py-2.5 rounded-full border border-darkgreen font-semibold text-darkgreen">
                            Iniciar sesión
                        </a>
                        <a href="{{ route('register') }}"
                            class="w-full bg-agricolus hover:bg-agricolusDark text-white px-6 py-2.5 rounded-full font-semibold text-center transition">
                            Registrarse
                        </a>
                    @endauth
                </div>
            </nav>
        </div>
    </div>
</header>

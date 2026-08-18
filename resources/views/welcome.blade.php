<x-guest-layout>
    <div class="relative min-h-screen overflow-hidden">
        <!-- Imagen de Fondo Global Estática -->
        <div class="fixed inset-0 z-0 bg-fixed bg-cover bg-center"
             style="background-image: url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2832&auto=format&fit=crop');">
            <div class="absolute inset-0 bg-gradient-to-b from-black/90 via-black/40 to-black/95"></div>
        </div>

        <!-- Contenido Dinámico -->
        <div class="relative z-10">
            <!-- HERO SECTION -->
            <div class="min-h-screen flex items-center pt-20 px-6 md:px-20">
                <div class="max-w-[1400px] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center w-full">
                    <div class="space-y-8 animate-in fade-in slide-in-from-left-10 duration-1000">
                        <div class="inline-flex items-center space-x-2 px-4 py-2 bg-agri-green/10 border border-agri-green/20 rounded-full backdrop-blur-md">
                            <span class="w-2 h-2 bg-agri-green rounded-full animate-pulse"></span>
                            <span class="text-[10px] font-black text-agri-green uppercase tracking-widest">Plataforma SaaS 2024</span>
                        </div>

                        <h1 class="text-5xl md:text-8xl font-black italic tracking-tighter text-white leading-none drop-shadow-2xl">
                            Tecnología que <br>
                            <span class="bg-gradient-to-r from-[#55cd44] to-[#1b5e0f] bg-clip-text text-transparent">hace crecer</span> <br>
                            el campo.
                        </h1>

                        <p class="text-lg text-white/70 leading-relaxed max-w-lg font-medium italic">
                            Transforme su producción agrícola con inteligencia artificial, gestión de equipos centralizada y trazabilidad forense de cada labor en campo.
                        </p>

                        <div class="flex flex-wrap gap-6 pt-4">
                            <a href="{{ route('register') }}" class="px-12 py-5 bg-agri-green text-white rounded-[2rem] font-black uppercase tracking-[0.2em] shadow-2xl shadow-agri-green/40 hover:scale-105 active:scale-95 transition-all italic text-xs">
                                Comenzar Ahora
                            </a>
                            <a href="#info" class="px-12 py-5 bg-white/5 backdrop-blur-xl border border-white/10 text-white rounded-[2rem] font-black uppercase tracking-[0.2em] hover:bg-white/10 transition-all text-xs">
                                Explorar Soluciones
                            </a>
                        </div>
                    </div>

                    <!-- Floating Stats Card -->
                    <div class="hidden lg:block relative animate-in fade-in zoom-in duration-1000">
                        <div class="relative z-10 bg-black/40 backdrop-blur-3xl border border-white/10 p-10 rounded-[4rem] shadow-[0_50px_100px_-20px_rgba(0,0,0,1)]">
                            <div class="space-y-8">
                                <div class="flex items-center space-x-6">
                                    <div class="w-16 h-16 bg-agri-green rounded-3xl flex items-center justify-center text-white text-3xl shadow-xl shadow-agri-green/20">
                                        <i class="fa-solid fa-chart-line"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-white uppercase tracking-widest">Eficiencia</p>
                                        <p class="text-3xl font-black text-white italic tracking-tighter mt-1">+45.8% Anual</p>
                                    </div>
                                </div>
                                <div class="h-px w-full bg-white/10"></div>
                                <div class="grid grid-cols-2 gap-8">
                                    <div>
                                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Terrenos</p>
                                        <p class="text-xl font-black text-white tabular-nums">2,450 Ha</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Cultivos</p>
                                        <p class="text-xl font-black text-white tabular-nums">15 Variedades</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-agri-green/20 rounded-full blur-[80px]"></div>
                    </div>
                </div>
            </div>

            <!-- FEATURES SECTION (Transparente para ver el fondo fijo) -->
            <div id="info" class="py-40 px-6 md:px-20 bg-transparent">
                <div class="max-w-[1400px] mx-auto">
                    <div class="text-center mb-24 space-y-4">
                        <h2 class="text-xs font-black text-agri-green uppercase tracking-[0.5em]">Nuestras Capacidades</h2>
                        <h3 class="text-4xl md:text-6xl font-black italic tracking-tighter text-white">
                            Gestión Integral de su <span class="text-agri-green">Patrimonio Agrícola</span>
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <!-- Card 1 -->
                        <div class="group p-10 bg-white/5 backdrop-blur-xl rounded-[3.5rem] border border-white/10 hover:border-agri-green transition-all duration-500">
                            <div class="w-20 h-20 bg-white/5 rounded-3xl flex items-center justify-center text-agri-green text-3xl mb-10 shadow-xl group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-microchip"></i>
                            </div>
                            <h4 class="text-2xl font-black text-white italic tracking-tight mb-4">Inteligencia Agrícola</h4>
                            <p class="text-sm text-white/50 leading-relaxed font-medium">Predicciones climáticas y análisis de suelo en tiempo real para optimizar cada gota de agua y gramo de insumo.</p>
                        </div>

                        <!-- Card 2 -->
                        <div class="group p-10 bg-white/5 backdrop-blur-xl rounded-[3.5rem] border border-white/10 hover:border-agri-green transition-all duration-500">
                            <div class="w-20 h-20 bg-white/5 rounded-3xl flex items-center justify-center text-agri-green text-3xl mb-10 shadow-xl group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-people-group"></i>
                            </div>
                            <h4 class="text-2xl font-black text-white italic tracking-tight mb-4">Estructura SaaS</h4>
                            <p class="text-sm text-white/50 leading-relaxed font-medium">Administre múltiples organizaciones desde un solo lugar. Jerarquías claras entre dueños, supervisores y agricultores.</p>
                        </div>

                        <!-- Card 3 -->
                        <div class="group p-10 bg-white/5 backdrop-blur-xl rounded-[3.5rem] border border-white/10 hover:border-agri-green transition-all duration-500">
                            <div class="w-20 h-20 bg-white/5 rounded-3xl flex items-center justify-center text-agri-green text-3xl mb-10 shadow-xl group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <h4 class="text-2xl font-black text-white italic tracking-tight mb-4">Trazabilidad Total</h4>
                            <p class="text-sm text-white/50 leading-relaxed font-medium">Cada acción queda grabada. Auditoría forense para supervisar las labores diarias y asegurar la calidad de la cosecha.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

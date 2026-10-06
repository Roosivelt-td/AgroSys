<x-guest-layout>
    <div class="relative min-h-screen overflow-hidden text-white font-sans bg-black">

        <!-- Imagen de Fondo Agrícola Fija (Identica a /register) -->
        <div class="fixed inset-0 z-0 bg-fixed bg-cover bg-center pointer-events-none"
             style="background-image: url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2832&auto=format&fit=crop');">
            <div class="absolute inset-0 bg-gradient-to-br from-black/95 via-black/40 to-black/90"></div>
        </div>

        <div class="relative z-10">

            <!-- =====================================================
                 HERO SECTION
            ====================================================== -->
            <section class="min-h-screen flex items-center pt-28 pb-16 md:pt-36 px-6 md:px-20">
                <div class="max-w-[1400px] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center w-full">
                    <div class="space-y-8 animate-in fade-in slide-in-from-left-10 duration-1000">
                        <div class="inline-flex items-center space-x-2 px-4 py-2 bg-agri-green/10 border border-agri-green/20 rounded-full backdrop-blur-md">
                            <span class="w-2 h-2 bg-agri-green rounded-full animate-pulse"></span>
                            <span class="text-[10px] font-black text-agri-green uppercase tracking-widest">Plataforma SaaS AgroSys 2026</span>
                        </div>

                        <h1 class="text-5xl md:text-8xl font-black italic tracking-tighter text-white leading-none drop-shadow-2xl">
                            Tecnología que <br>
                            <span class="bg-gradient-to-r from-[#55cd44] to-[#1b5e0f] bg-clip-text text-transparent">hace crecer</span> <br>
                            el campo.
                        </h1>

                        <p class="text-lg text-white/70 leading-relaxed max-w-lg font-medium italic">
                            Transforme su producción agrícola con inteligencia artificial, monitoreo satelital, gestión de equipos centralizada y trazabilidad forense de la cadena agroalimentaria.
                        </p>

                        <div class="flex flex-wrap gap-6 pt-4">
                            <a href="{{ route('register') }}" class="px-12 py-5 bg-agri-green text-white rounded-[2rem] font-black uppercase tracking-[0.2em] shadow-2xl shadow-agri-green/40 hover:scale-105 active:scale-95 transition-all italic text-xs">
                                Comenzar Ahora
                            </a>
                            <a href="#soluciones-satelital" class="px-12 py-5 bg-white/5 backdrop-blur-xl border border-white/10 text-white rounded-[2rem] font-black uppercase tracking-[0.2em] hover:bg-white/10 transition-all text-xs italic">
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
                                        <p class="text-xs font-black text-white uppercase tracking-widest">Eficiencia Agro</p>
                                        <p class="text-3xl font-black text-white italic tracking-tighter mt-1">+45.8% Anual</p>
                                    </div>
                                </div>
                                <div class="h-px w-full bg-white/10"></div>
                                <div class="grid grid-cols-2 gap-8">
                                    <div>
                                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Terrenos Monitoreados</p>
                                        <p class="text-xl font-black text-white tabular-nums">2,450 Ha</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Cultivos & Lotes</p>
                                        <p class="text-xl font-black text-white tabular-nums">15 Variedades</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-agri-green/20 rounded-full blur-[80px]"></div>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 1: SOLUCIONES - MONITOREO SATELITAL Y GESTIÓN AGRONÓMICA -->
            <section id="soluciones-satelital" class="py-28 px-6 md:px-20 bg-transparent">
                <div class="max-w-[1400px] mx-auto space-y-20">
                    <div class="text-center space-y-4">
                        <h2 class="text-xs font-black text-agri-green uppercase tracking-[0.5em]">Plataforma de Agricultura Digital</h2>
                        <h3 class="text-4xl md:text-6xl font-black italic tracking-tighter text-white">
                            Monitoreo Satelital & <span class="text-agri-green">Gestión Agronómica</span>
                        </h3>
                        <p class="text-white/60 text-sm max-w-2xl mx-auto italic font-medium">
                            Análisis continuo mediante índices espectrales (NDVI, NDWI, EVI) para diagnosticar vigor vegetativo, humedad de hojas y requerimientos hídricos.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <!-- Solución 1 -->
                        <div class="p-10 bg-white/5 backdrop-blur-xl rounded-[3rem] border border-white/10 hover:border-agri-green transition-all duration-500 space-y-6">
                            <div class="w-16 h-16 bg-agri-green/10 rounded-2xl flex items-center justify-center text-agri-green text-2xl shadow-inner">
                                <i class="fa-solid fa-satellite"></i>
                            </div>
                            <h4 class="text-2xl font-black text-white italic tracking-tight">Monitoreo Satelital GPS</h4>
                            <p class="text-xs text-white/60 leading-relaxed italic">
                                Detección satelital de anomalías de clorofila y variabilidad en el vigor del cultivo antes de que sean visibles a simple vista.
                            </p>
                            <a href="{{ route('soluciones.satelital') }}" class="inline-flex font-black text-xs text-agri-green hover:underline italic uppercase tracking-widest">Ver Detalles &rarr;</a>
                        </div>

                        <!-- Solución 2 -->
                        <div id="soluciones-agronomica" class="p-10 bg-white/5 backdrop-blur-xl rounded-[3rem] border border-white/10 hover:border-agri-green transition-all duration-500 space-y-6">
                            <div class="w-16 h-16 bg-agri-green/10 rounded-2xl flex items-center justify-center text-agri-green text-2xl shadow-inner">
                                <i class="fa-solid fa-clipboard-check"></i>
                            </div>
                            <h4 class="text-2xl font-black text-white italic tracking-tight">Gestión Agronómica Integrada</h4>
                            <p class="text-xs text-white/60 leading-relaxed italic">
                                Planificación y registro detallado de siembras, etapas fenológicas, fertirriego y aplicación de insumos con control de costos en tiempo real.
                            </p>
                            <a href="{{ route('soluciones.agronomica') }}" class="inline-flex font-black text-xs text-agri-green hover:underline italic uppercase tracking-widest">Ver Detalles &rarr;</a>
                        </div>

                        <!-- Solución 3 -->
                        <div id="soluciones-defensa" class="p-10 bg-white/5 backdrop-blur-xl rounded-[3rem] border border-white/10 hover:border-agri-green transition-all duration-500 space-y-6">
                            <div class="w-16 h-16 bg-agri-green/10 rounded-2xl flex items-center justify-center text-agri-green text-2xl shadow-inner">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <h4 class="text-2xl font-black text-white italic tracking-tight">Defensa de Cultivos & Alertas</h4>
                            <p class="text-xs text-white/60 leading-relaxed italic">
                                Algoritmos de inteligencia artificial para la predicción de plagas, enfermedades fúngicas y alertas de estrés térmico o heladas.
                            </p>
                            <a href="{{ route('soluciones.defensa') }}" class="inline-flex font-black text-xs text-agri-green hover:underline italic uppercase tracking-widest">Ver Detalles &rarr;</a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 2: SOSTENIBILIDAD Y CONTROL DE LA CADENA AGROALIMENTARIA -->
            <section id="sostenibilidad" class="py-28 px-6 md:px-20 bg-black/40 backdrop-blur-3xl border-y border-white/5">
                <div class="max-w-[1400px] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="space-y-8">
                        <div class="inline-flex items-center space-x-2 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full">
                            <i class="fa-solid fa-leaf text-emerald-400 text-xs"></i>
                            <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Compromiso Ambiental</span>
                        </div>

                        <h3 class="text-4xl md:text-6xl font-black italic tracking-tighter text-white leading-none">
                            Sostenibilidad de la <br>
                            <span class="text-agri-green">Cadena Agroalimentaria</span>
                        </h3>

                        <p class="text-sm text-white/70 leading-relaxed font-medium italic">
                            AgroSys permite certificar la trazabilidad transparente del producto desde el campo hasta la mesa, optimizando los insumos hídricos y fertilizantes para reducir la huella de carbono y agregar valor comercial al cultivo.
                        </p>

                        <div class="space-y-4">
                            <div class="flex items-center space-x-4 p-4 bg-white/5 rounded-2xl border border-white/10">
                                <i class="fa-solid fa-droplet text-agri-green text-xl"></i>
                                <div>
                                    <h5 class="text-sm font-black text-white uppercase italic">Eficiencia en el Uso del Agua</h5>
                                    <p class="text-xs text-white/50 italic">Optimización del balance hídrico reduciendo hasta un 30% el desperdicio de riego.</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 p-4 bg-white/5 rounded-2xl border border-white/10">
                                <i class="fa-solid fa-barcode text-agri-green text-xl"></i>
                                <div>
                                    <h5 class="text-sm font-black text-white uppercase italic">Trazabilidad Total para Exportación</h5>
                                    <p class="text-xs text-white/50 italic">Generación de cuadernos de campo digitales e historial de aplicación de fitosanitarios.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/5 p-10 rounded-[3.5rem] border border-white/10 space-y-6">
                        <h4 class="text-2xl font-black text-white italic">Indicadores de Sostenibilidad</h4>
                        <div class="space-y-6">
                            <div>
                                <div class="flex justify-between text-xs font-black text-white uppercase mb-2">
                                    <span>Reducción de Insumos Químicos</span>
                                    <span class="text-agri-green">-22%</span>
                                </div>
                                <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-agri-green rounded-full w-[78%]"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs font-black text-white uppercase mb-2">
                                    <span>Ahorro en Consumo de Agua</span>
                                    <span class="text-agri-green">-31%</span>
                                </div>
                                <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-agri-green rounded-full w-[69%]"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs font-black text-white uppercase mb-2">
                                    <span>Cumplimiento de Estándares GlobalGAP</span>
                                    <span class="text-agri-green">100%</span>
                                </div>
                                <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-agri-green rounded-full w-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 3: AGROSYS PROFESSIONAL ACADEMY -->
            <section id="academy" class="py-28 px-6 md:px-20 bg-transparent">
                <div class="max-w-[1400px] mx-auto space-y-16">
                    <div class="text-center space-y-4">
                        <h2 class="text-xs font-black text-amber-400 uppercase tracking-[0.5em]">Capacitación Agronómica Continua</h2>
                        <h3 class="text-4xl md:text-6xl font-black italic tracking-tighter text-white">
                            AgroSys <span class="text-amber-400">Professional Academy</span>
                        </h3>
                        <p class="text-white/60 text-sm max-w-2xl mx-auto italic font-medium">
                            Programa de certificación en agricultura de precisión, manejo de sensores IoT y toma de decisiones basadas en datos para ingenieros, supervisores y técnicos.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="p-8 bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/10 space-y-4 hover:border-amber-400/50 transition-all">
                            <span class="px-3 py-1 bg-amber-400/10 text-amber-400 text-[9px] font-black uppercase rounded-full">Nivel Básico</span>
                            <h4 class="text-xl font-black text-white italic">Introducción al Monitoreo Satelital</h4>
                            <p class="text-xs text-white/50 italic leading-relaxed">Interpretación de mapas NDVI, índices de clorofila e identificación temprana de sectores con deficiencia hídrica.</p>
                        </div>

                        <div class="p-8 bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/10 space-y-4 hover:border-amber-400/50 transition-all">
                            <span class="px-3 py-1 bg-amber-400/10 text-amber-400 text-[9px] font-black uppercase rounded-full">Nivel Intermedio</span>
                            <h4 class="text-xl font-black text-white italic">Gestión de Equipos y Supervisión</h4>
                            <p class="text-xs text-white/50 italic leading-relaxed">Asignación de cuadrillas de campo, emisión de órdenes de trabajo con IA y auditoría de costos operacionales.</p>
                        </div>

                        <div class="p-8 bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/10 space-y-4 hover:border-amber-400/50 transition-all">
                            <span class="px-3 py-1 bg-amber-400/10 text-amber-400 text-[9px] font-black uppercase rounded-full">Nivel Avanzado</span>
                            <h4 class="text-xl font-black text-white italic">Modelos Predictivos y Telemetría</h4>
                            <p class="text-xs text-white/50 italic leading-relaxed">Configuración de estaciones agrometeorológicas e integración de datos climáticos para prevención de plagas.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 4: QUIÉNES SOMOS & INVESTIGACIÓN INDUSTRIAL -->
            <section id="quienes-somos" class="py-28 px-6 md:px-20 bg-black/40 backdrop-blur-3xl border-t border-white/5">
                <div class="max-w-[1400px] mx-auto space-y-16">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div class="space-y-6">
                            <h2 class="text-xs font-black text-agri-green uppercase tracking-[0.5em]">Nuestra Empresa</h2>
                            <h3 class="text-4xl md:text-5xl font-black italic tracking-tighter text-white leading-tight">
                                Innovación Tecnológica <br>Nacida para el <span class="text-agri-green">Campo Moderno</span>
                            </h3>
                            <p class="text-sm text-white/70 italic leading-relaxed font-medium">
                                AgroSys es una empresa tecnológica especializada en soluciones agronómicas digitales. Unimos la investigación agronómica de campo con la potencia de la inteligencia artificial para guiar la toma de decisiones diarias.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div id="nuestra-red" class="p-6 bg-white/5 rounded-2xl border border-white/10 text-center space-y-2">
                                <i class="fa-solid fa-network-wired text-agri-green text-3xl mb-2"></i>
                                <h5 class="text-base font-black text-white uppercase italic">Nuestra Red</h5>
                                <p class="text-[10px] text-white/50 italic">Conexión directa entre fundos, cooperativas y agroexportadoras.</p>
                            </div>
                            <div id="investigacion" class="p-6 bg-white/5 rounded-2xl border border-white/10 text-center space-y-2">
                                <i class="fa-solid fa-flask text-agri-green text-3xl mb-2"></i>
                                <h5 class="text-base font-black text-white uppercase italic">Investigación</h5>
                                <p class="text-[10px] text-white/50 italic">Desarrollo continuo de algoritmos agronómicos adaptados a la región.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 5: FORMULARIO DE CONTACTO -->
            <section id="contactos" class="py-28 px-6 md:px-20 bg-transparent">
                <div class="max-w-[900px] mx-auto bg-black/60 backdrop-blur-3xl p-12 md:p-16 rounded-[3.5rem] border border-white/10 shadow-2xl space-y-10">
                    <div class="text-center space-y-3">
                        <h2 class="text-xs font-black text-agri-green uppercase tracking-[0.5em]">Atención Comercial & Soporte</h2>
                        <h3 class="text-3xl md:text-5xl font-black italic tracking-tighter text-white">Contáctenos</h3>
                        <p class="text-xs text-white/60 italic">Solicite una demostración personalizada o asesoría para su fundo.</p>
                    </div>

                    <form class="space-y-6" onsubmit="event.preventDefault(); alert('Gracias por su consulta. Un asesor de AgroSys lo contactará a la brevedad.');">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <input type="text" placeholder="Nombres y Apellidos" required class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-xs font-bold text-white placeholder:text-white/30 outline-none focus:border-agri-green italic">
                            <input type="email" placeholder="Correo Electrónico" required class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-xs font-bold text-white placeholder:text-white/30 outline-none focus:border-agri-green italic">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <input type="text" placeholder="Teléfono de Contacto" required class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-xs font-bold text-white placeholder:text-white/30 outline-none focus:border-agri-green italic">
                            <input type="text" placeholder="Nombre del Fundo / Empresa" class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-xs font-bold text-white placeholder:text-white/30 outline-none focus:border-agri-green italic">
                        </div>
                        <textarea rows="4" placeholder="Describa su consulta o requerimiento técnico..." required class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-xs font-bold text-white placeholder:text-white/30 outline-none focus:border-agri-green italic"></textarea>

                        <button type="submit" class="w-full py-5 bg-agri-green text-white rounded-2xl font-black text-xs uppercase tracking-[0.4em] shadow-xl shadow-agri-green/30 hover:scale-105 active:scale-95 transition-all italic">
                            Enviar Consulta Comercial
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</x-guest-layout>

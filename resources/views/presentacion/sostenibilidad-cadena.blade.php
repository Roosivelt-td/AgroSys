<x-presentacion-guest>
    <div class="text-[#263d20] bg-white font-sans" x-data="{ activeChallenge: null, newsletterSent: false }">

        <style>
            .container-agri {
                width: min(1180px, calc(100% - 40px));
                margin: auto;
            }
            .hero-bg {
                background: radial-gradient(circle at 80% 20%, rgba(120,169,74,.18), transparent 30%), linear-gradient(180deg, #f4f8ef 0%, #ffffff 100%);
            }
            .hero-image {
                background-image: linear-gradient(90deg, rgba(38,61,32,.05), rgba(38,61,32,0)), url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1500&q=85');
                background-size: cover;
                background-position: center;
            }
            .green-image {
                background-image: linear-gradient(rgba(38,61,32,.68), rgba(38,61,32,.68)), url('https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1800&q=85');
                background-size: cover;
                background-position: center;
            }
            .shadow-agri {
                box-shadow: 0 25px 70px rgba(38,61,32,.12);
            }
            .card-hover {
                transition: transform .3s ease, box-shadow .3s ease;
            }
            .card-hover:hover {
                transform: translateY(-7px);
                box-shadow: 0 25px 50px rgba(38,61,32,.12);
            }
            .number {
                font-variant-numeric: tabular-nums;
            }
        </style>

        <!-- ==========================================================
             HERO
        ========================================================== -->
        <section class="hero-bg pt-12 overflow-hidden">
            <div class="container-agri">
                <div class="grid lg:grid-cols-2 min-h-[680px] items-center gap-12 py-20">
                    <!-- TEXT -->
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 bg-[#e8f1dc] text-[#4d762d] px-4 py-2 rounded-full text-sm font-bold">
                            <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-6 h-6 object-contain inline-block mr-1 align-middle">
                            Agricultura sostenible
                        </div>

                        <h1 class="text-5xl md:text-6xl lg:text-[67px] leading-[.98] font-black tracking-tight text-[#263d20]">
                            Sostenibilidad de la cadena <span class="text-[#619037]">agroalimentaria</span>
                        </h1>

                        <p class="text-lg leading-8 text-gray-600">
                            La transformación digital puede ayudar a todos los actores de la cadena agroalimentaria a afrontar los grandes retos de la agricultura actual.
                        </p>
                        <p class="text-lg leading-8 text-gray-600">
                            Datos, trazabilidad y herramientas digitales para tomar decisiones más eficientes y sostenibles.
                        </p>

                        <div class="pt-4 flex flex-wrap gap-4">
                            <a href="#retos" class="bg-[#619037] hover:bg-[#4d762d] text-white px-7 py-4 rounded-full font-bold transition shadow-md">
                                Descubre más
                            </a>
                            <a href="#resultados" class="border border-[#619037] text-[#4d762d] px-7 py-4 rounded-full font-bold hover:bg-[#f4f8ef] transition">
                                Ver resultados
                            </a>
                        </div>
                    </div>

                    <!-- IMAGE -->
                    <div class="relative">
                        <div class="absolute -top-10 -right-5 w-36 h-36 bg-[#d4e5bd] rounded-full opacity-70"></div>
                        <div class="absolute -bottom-8 -left-8 w-44 h-44 bg-[#e8f1dc] rounded-full"></div>

                        <div class="relative overflow-hidden rounded-[45px] rounded-bl-[120px] shadow-agri">
                            <img src="https://images.unsplash.com/photo-1523742810-5a0a9b3b5a9e?auto=format&fit=crop&w=1400&q=85" alt="Sostenibilidad" class="w-full h-[560px] object-cover">

                            <!-- Floating Card -->
                            <div class="absolute bottom-7 left-7 right-7 bg-white/95 backdrop-blur rounded-2xl p-5 shadow-xl">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-[#e8f1dc] flex items-center justify-center text-2xl">🌱</div>
                                    <div>
                                        <div class="font-bold text-[#263d20]">Making AgriTech Sustainable</div>
                                        <div class="text-sm text-gray-500">Tecnología para una agricultura más eficiente</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==========================================================
             INTRO
        ========================================================== -->
        <section class="py-24 bg-white">
            <div class="container-agri">
                <div class="max-w-3xl space-y-5">
                    <span class="text-[#619037] uppercase tracking-[.25em] text-sm font-bold">El desafío</span>
                    <h2 class="text-4xl md:text-5xl font-black text-[#263d20] leading-tight">
                        Una cadena agroalimentaria preparada para el futuro
                    </h2>
                    <p class="text-lg text-gray-600 leading-8">
                        El sector agroalimentario mundial se enfrenta a una serie de preocupaciones impulsadas por grandes tendencias económicas, ambientales y sociales.
                    </p>
                </div>
            </div>
        </section>

        <!-- ==========================================================
             CHALLENGES (RETOS)
        ========================================================== -->
        <section id="retos" class="py-24 bg-[#f4f8ef]">
            <div class="container-agri space-y-16">
                <div class="text-center space-y-4">
                    <span class="text-[#619037] uppercase tracking-[.25em] text-sm font-bold">Los retos</span>
                    <h2 class="text-4xl md:text-5xl font-black text-[#263d20]">Los grandes desafíos</h2>
                    <p class="max-w-2xl mx-auto text-gray-600 text-lg">Cinco factores están transformando la manera en que producimos, distribuimos y consumimos alimentos.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-5">
                    <!-- 1 -->
                    <button @click="activeChallenge = activeChallenge === 1 ? null : 1" class="card-hover text-left bg-white rounded-[28px] p-7 border border-gray-100 shadow-sm space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center text-2xl">☀️</div>
                        <h3 class="font-extrabold text-[#263d20] text-lg">Cambio climático</h3>
                        <p class="text-sm leading-6 text-gray-500">Fenómenos extremos y variabilidad climática.</p>
                        <div x-show="activeChallenge === 1" x-cloak class="text-xs text-[#619037] font-semibold pt-2">Monitorización y datos para anticiparse a las variaciones del campo.</div>
                    </button>

                    <!-- 2 -->
                    <button @click="activeChallenge = activeChallenge === 2 ? null : 2" class="card-hover text-left bg-white rounded-[28px] p-7 border border-gray-100 shadow-sm space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-2xl">📈</div>
                        <h3 class="font-extrabold text-[#263d20] text-lg">Rentabilidad</h3>
                        <p class="text-sm leading-6 text-gray-500">Competitividad y rendimiento de las explotaciones.</p>
                        <div x-show="activeChallenge === 2" x-cloak class="text-xs text-[#619037] font-semibold pt-2">Optimización de recursos y seguimiento de costes.</div>
                    </button>

                    <!-- 3 -->
                    <button @click="activeChallenge = activeChallenge === 3 ? null : 3" class="card-hover text-left bg-white rounded-[28px] p-7 border border-gray-100 shadow-sm space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center text-2xl">👨‍🌾</div>
                        <h3 class="font-extrabold text-[#263d20] text-lg">Mano de obra</h3>
                        <p class="text-sm leading-6 text-gray-500">Disponibilidad y gestión del trabajo agrícola.</p>
                        <div x-show="activeChallenge === 3" x-cloak class="text-xs text-[#619037] font-semibold pt-2">Digitalización para facilitar la planificación y coordinación.</div>
                    </button>

                    <!-- 4 -->
                    <button @click="activeChallenge = activeChallenge === 4 ? null : 4" class="card-hover text-left bg-white rounded-[28px] p-7 border border-gray-100 shadow-sm space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-yellow-50 flex items-center justify-center text-2xl">💶</div>
                        <h3 class="font-extrabold text-[#263d20] text-lg">Volatilidad</h3>
                        <p class="text-sm leading-6 text-gray-500">Precios y costes de las materias primas.</p>
                        <div x-show="activeChallenge === 4" x-cloak class="text-xs text-[#619037] font-semibold pt-2">Datos integrados para mejorar la toma de decisiones.</div>
                    </button>

                    <!-- 5 -->
                    <button @click="activeChallenge = activeChallenge === 5 ? null : 5" class="card-hover text-left bg-[#619037] rounded-[28px] p-7 text-white border border-[#619037] shadow-lg space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/15 flex items-center justify-center text-2xl">🌍</div>
                        <h3 class="font-extrabold text-lg">Sostenibilidad</h3>
                        <p class="text-sm leading-6 text-green-100">Reducir el impacto ambiental de la producción.</p>
                        <div x-show="activeChallenge === 5" x-cloak class="text-xs text-green-100 font-semibold pt-2">Control de agua, insumos, energía y biodiversidad.</div>
                    </button>
                </div>
            </div>
        </section>

        <!-- ==========================================================
             RESULTS
========================================================== -->
        <section id="resultados" class="py-24 bg-white">
            <div class="container-agri">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <!-- Image -->
                    <div class="relative">
                        <div class="absolute -top-7 -left-7 w-32 h-32 rounded-full bg-[#e8f1dc]"></div>
                        <div class="relative rounded-[45px] overflow-hidden shadow-agri">
                            <img src="https://images.unsplash.com/photo-1495107334309-fcf20504a5ab?auto=format&fit=crop&w=1400&q=85" alt="Resultados" class="w-full h-[560px] object-cover">
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="space-y-6">
                        <span class="text-[#619037] uppercase tracking-[.25em] text-sm font-bold">Datos que importan</span>
                        <h2 class="text-4xl md:text-5xl font-black text-[#263d20] leading-tight">Digitalización para medir y mejorar</h2>
                        <p class="text-lg leading-8 text-gray-600">
                            Las herramientas digitales permiten recopilar información de las explotaciones y transformarla en indicadores útiles para la toma de decisiones.
                        </p>

                        <!-- Metrics Grid -->
                        <div class="grid grid-cols-2 gap-4 pt-4">
                            <div class="rounded-3xl bg-[#f4f8ef] p-6 space-y-2">
                                <div class="text-4xl font-black text-[#619037] number">-25%</div>
                                <div class="text-sm text-gray-600">Uso de inputs</div>
                            </div>
                            <div class="rounded-3xl bg-blue-50 p-6 space-y-2">
                                <div class="text-4xl font-black text-blue-600 number">-30%</div>
                                <div class="text-sm text-gray-600">Consumo de agua</div>
                            </div>
                            <div class="rounded-3xl bg-emerald-50 p-6 space-y-2">
                                <div class="text-4xl font-black text-emerald-600">↓</div>
                                <div class="text-sm text-gray-600">Emisiones</div>
                            </div>
                            <div class="rounded-3xl bg-yellow-50 p-6 space-y-2">
                                <div class="text-4xl font-black text-yellow-600">↑</div>
                                <div class="text-sm text-gray-600">Rendimiento</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==========================================================
             VENTAJAS DE LA DIGITALIZACIÓN CON AGROSYS (NUEVO)
========================================================== -->
        <section class="py-24 bg-white border-t border-gray-100">
            <div class="container-agri space-y-20">
                <div class="text-center max-w-3xl mx-auto space-y-4">
                    <h2 class="text-4xl md:text-5xl font-black text-[#619037]">Las ventajas de la digitalización con <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span></h2>
                    <p class="text-gray-600 text-base leading-7">
                        <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> es reconocida por el Pacto Mundial de las Naciones Unidas como una de las empresas que promueven y fomentan el desarrollo sostenible de la cadena agroalimentaria a nivel mundial.
                    </p>
                    <p class="text-gray-600 text-base leading-7">
                        Las necesidades de los agricultores en relación con la innovación digital destacan una conciencia creciente sobre el valor de los datos.
                    </p>
                </div>

                <div class="space-y-20">
                    <!-- 1. Capacidad Predictiva -->
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-4">
                            <h3 class="text-3xl font-black text-[#b79635] tracking-tight">Mejorar la capacidad predictiva</h3>
                            <h4 class="text-2xl font-black text-[#263d20]">¿Cómo?</h4>
                            <div class="rounded-[35px] overflow-hidden shadow-lg h-[350px]">
                                <img src="https://images.unsplash.com/photo-1574943320219-553eb213f72d?auto=format&fit=crop&w=900&q=85" alt="Capacidad predictiva" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4 p-5 bg-[#f4f8ef] rounded-2xl border border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-[#263d20] text-white flex items-center justify-center shrink-0">🌾</div>
                                <p class="text-gray-700 font-medium text-sm">Modelos de predicción para el riego y la fertilización, con el fin de proporcionar a los cultivos exactamente lo que necesitan</p>
                            </div>
                            <div class="flex items-start gap-4 p-5 bg-[#f4f8ef] rounded-2xl border border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-[#263d20] text-white flex items-center justify-center shrink-0">🦠</div>
                                <p class="text-gray-700 font-medium text-sm">Modelos de predicción de enfermedades e insectos dañinos, para intervenir rápidamente</p>
                            </div>
                            <div class="flex items-start gap-4 p-5 bg-[#f4f8ef] rounded-2xl border border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-[#263d20] text-white flex items-center justify-center shrink-0">⛅</div>
                                <p class="text-gray-700 font-medium text-sm">Datos meteorológicos de sensores para monitorear en tiempo real las condiciones agroclimáticas en el campo</p>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Costes de gestión -->
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-4">
                            <h3 class="text-3xl font-black text-[#b79635] tracking-tight">Contener los costes de gestión, insumos y consumo</h3>
                            <h4 class="text-2xl font-black text-[#263d20]">¿Cómo?</h4>
                            <div class="rounded-[35px] overflow-hidden shadow-lg h-[350px]">
                                <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=900&q=85" alt="Costes de gestión" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4 p-5 bg-[#f4f8ef] rounded-2xl border border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-[#263d20] text-white flex items-center justify-center shrink-0">🛰️</div>
                                <p class="text-gray-700 font-medium text-sm">Imágenes satelitales e índices de vegetación para identificar la variabilidad espacio-temporal y optimizar las intervenciones en campo</p>
                            </div>
                            <div class="flex items-start gap-4 p-5 bg-[#f4f8ef] rounded-2xl border border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-[#263d20] text-white flex items-center justify-center shrink-0">💡</div>
                                <p class="text-gray-700 font-medium text-sm">Sistemas de soporte a la decisión (DSS) para reducir el uso de insumos</p>
                            </div>
                            <div class="flex items-start gap-4 p-5 bg-[#f4f8ef] rounded-2xl border border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-[#263d20] text-white flex items-center justify-center shrink-0">📋</div>
                                <p class="text-gray-700 font-medium text-sm">Gestión de tareas para optimizar el flujo de trabajo</p>
                            </div>
                            <div class="flex items-start gap-4 p-5 bg-[#f4f8ef] rounded-2xl border border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-[#263d20] text-white flex items-center justify-center shrink-0">📈</div>
                                <p class="text-gray-700 font-medium text-sm">Mapas de rendimiento y prescripción para aumentar la producción</p>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Calidad del producto -->
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-4">
                            <h3 class="text-3xl font-black text-[#b79635] tracking-tight">Mejorar la calidad del producto</h3>
                            <h4 class="text-2xl font-black text-[#263d20]">¿Cómo?</h4>
                            <div class="rounded-[35px] overflow-hidden shadow-lg h-[350px]">
                                <img src="https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&w=900&q=85" alt="Calidad del producto" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4 p-5 bg-[#f4f8ef] rounded-2xl border border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-[#263d20] text-white flex items-center justify-center shrink-0">🔗</div>
                                <p class="text-gray-700 font-medium text-sm">Herramientas digitales y conexión de maquinaria para la trazabilidad de la cadena de suministro</p>
                            </div>
                            <div class="flex items-start gap-4 p-5 bg-[#f4f8ef] rounded-2xl border border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-[#263d20] text-white flex items-center justify-center shrink-0">📱</div>
                                <p class="text-gray-700 font-medium text-sm">Informes de campo con App para geolocalizar daños en cultivos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==========================================================
             INDICADORES
========================================================== -->
        <section class="py-24 bg-[#f4f8ef]">
            <div class="container-agri space-y-16">
                <div class="text-center max-w-3xl mx-auto space-y-4">
                    <span class="text-[#619037] uppercase tracking-[.25em] text-sm font-bold">Indicadores</span>
                    <h2 class="text-4xl md:text-5xl font-black text-[#263d20]">Una visión completa de la sostenibilidad</h2>
                    <p class="text-gray-600 text-lg leading-8">Evalúa los principales factores económicos y ambientales de las explotaciones y realiza un seguimiento de tus objetivos.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <!-- Economic -->
                    <div class="card-hover bg-white rounded-[32px] p-8 space-y-6 shadow-sm border border-gray-100">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-3xl">📊</div>
                        <h3 class="text-2xl font-black text-[#263d20]">Económica</h3>
                        <p class="text-gray-500 leading-7">Indicadores relacionados con el rendimiento y producción de los cultivos.</p>
                        <div class="space-y-3 pt-2">
                            <div class="flex justify-between text-sm"><span>Rendimiento</span><strong>82%</strong></div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-blue-500 rounded-full" style="width:82%"></div></div>
                        </div>
                    </div>

                    <!-- Environmental -->
                    <div class="card-hover bg-white rounded-[32px] p-8 space-y-6 shadow-sm border border-gray-100">
                        <div class="w-16 h-16 rounded-2xl bg-green-50 flex items-center justify-center text-3xl">🌱</div>
                        <h3 class="text-2xl font-black text-[#263d20]">Ambiental</h3>
                        <p class="text-gray-500 leading-7">Seguimiento del agua, fertilizantes, productos fitosanitarios y biodiversidad.</p>
                        <div class="space-y-3 pt-2">
                            <div class="flex justify-between text-sm"><span>Objetivo ambiental</span><strong>76%</strong></div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-[#78a94a] rounded-full" style="width:76%"></div></div>
                        </div>
                    </div>

                    <!-- Objectives -->
                    <div class="card-hover bg-[#619037] rounded-[32px] p-8 space-y-6 text-white shadow-xl">
                        <div class="w-16 h-16 rounded-2xl bg-white/15 flex items-center justify-center text-3xl">🎯</div>
                        <h3 class="text-2xl font-black">Objetivos</h3>
                        <p class="text-green-100 leading-7">Define objetivos y monitoriza su evolución durante la campaña agrícola.</p>
                        <div class="space-y-3 pt-2">
                            <div class="flex justify-between text-sm text-green-100"><span>Progreso</span><strong>68%</strong></div>
                            <div class="h-2 bg-white/20 rounded-full overflow-hidden"><div class="h-full bg-white rounded-full" style="width:68%"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==========================================================
             TRACEABILITY
========================================================== -->
        <section class="py-24 bg-white">
            <div class="container-agri">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="space-y-6">
                        <span class="text-[#619037] uppercase tracking-[.25em] text-sm font-bold">Trazabilidad</span>
                        <h2 class="text-4xl md:text-5xl font-black text-[#263d20]">Una cadena más transparente</h2>
                        <p class="text-lg leading-8 text-gray-600">
                            La trazabilidad permite seguir el producto desde la producción hasta el consumidor y disponer de información en cada etapa del proceso.
                        </p>

                        <div class="space-y-5 pt-4">
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 flex-shrink-0 rounded-full bg-[#e8f1dc] text-[#619037] flex items-center justify-center font-bold">1</div>
                                <div><h3 class="font-bold text-[#263d20]">Producción</h3><p class="text-gray-500 text-sm mt-1">Datos y operaciones realizadas en el campo.</p></div>
                            </div>
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 flex-shrink-0 rounded-full bg-[#e8f1dc] text-[#619037] flex items-center justify-center font-bold">2</div>
                                <div><h3 class="font-bold text-[#263d20]">Transformación</h3><p class="text-gray-500 text-sm mt-1">Información de los procesos de transformación.</p></div>
                            </div>
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 flex-shrink-0 rounded-full bg-[#e8f1dc] text-[#619037] flex items-center justify-center font-bold">3</div>
                                <div><h3 class="font-bold text-[#263d20]">Distribución</h3><p class="text-gray-500 text-sm mt-1">Seguimiento hasta la llegada del producto.</p></div>
                            </div>
                        </div>
                    </div>

                    <!-- Traceability Visual -->
                    <div class="relative bg-[#f4f8ef] rounded-[40px] p-8 md:p-12 shadow-sm">
                        <div class="relative space-y-8">
                            <div class="absolute top-10 left-10 right-10 h-1 bg-[#b8d497] hidden md:block"></div>
                            <div class="grid md:grid-cols-3 gap-5 relative">
                                <div class="text-center space-y-2">
                                    <div class="mx-auto w-20 h-20 rounded-full bg-[#619037] text-white flex items-center justify-center text-3xl shadow-lg">🌾</div>
                                    <div class="font-bold text-[#263d20]">Campo</div>
                                </div>
                                <div class="text-center space-y-2">
                                    <div class="mx-auto w-20 h-20 rounded-full bg-[#619037] text-white flex items-center justify-center text-3xl shadow-lg">🏭</div>
                                    <div class="font-bold text-[#263d20]">Industria</div>
                                </div>
                                <div class="text-center space-y-2">
                                    <div class="mx-auto w-20 h-20 rounded-full bg-[#619037] text-white flex items-center justify-center text-3xl shadow-lg">🛒</div>
                                    <div class="font-bold text-[#263d20]">Consumidor</div>
                                </div>
                            </div>

                            <div class="mt-8 bg-white rounded-3xl p-6 shadow-lg space-y-5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-xs uppercase tracking-wider text-gray-400">Lote de producción</div>
                                        <div class="mt-1 font-bold text-[#263d20]">LOT-2026-00184</div>
                                    </div>
                                    <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-green-600 font-bold">✓</div>
                                </div>
                                <div class="grid grid-cols-3 gap-3 text-center">
                                    <div class="bg-gray-50 rounded-xl py-3"><div class="text-xs text-gray-400">Cultivo</div><div class="font-bold text-sm mt-1">Trigo</div></div>
                                    <div class="bg-gray-50 rounded-xl py-3"><div class="text-xs text-gray-400">Estado</div><div class="font-bold text-sm mt-1 text-green-600">Trazado</div></div>
                                    <div class="bg-gray-50 rounded-xl py-3"><div class="text-xs text-gray-400">Datos</div><div class="font-bold text-sm mt-1">100%</div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==========================================================
             PARTNERS
========================================================== -->
        <section class="py-24 bg-[#f4f8ef]">
            <div class="container-agri space-y-14">
                <div class="text-center space-y-4">
                    <span class="text-[#619037] uppercase tracking-[.25em] text-sm font-bold">Colaboraciones</span>
                    <h2 class="text-4xl md:text-5xl font-black text-[#263d20]">Juntos por una cadena más sostenible</h2>
                    <p class="max-w-2xl mx-auto text-gray-600 text-lg">La innovación necesita colaboración entre empresas, profesionales y organizaciones del sector.</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                    <div class="h-28 bg-white rounded-2xl flex items-center justify-center font-black text-2xl text-gray-400 shadow-sm">SATA</div>
                    <div class="h-28 bg-white rounded-2xl flex items-center justify-center font-black text-xl text-gray-400 shadow-sm">PARTNER</div>
                    <div class="h-28 bg-white rounded-2xl flex items-center justify-center font-black text-xl text-gray-400 shadow-sm">AGRIFOOD</div>
                    <div class="h-28 bg-white rounded-2xl flex items-center justify-center font-black text-xl text-gray-400 shadow-sm">INNOVATION</div>
                </div>
            </div>
        </section>

        <!-- ==========================================================
             BIG CTA
========================================================== -->
        <section class="green-image py-28">
            <div class="container-agri text-center text-white space-y-6">
                <div class="uppercase tracking-[.4em] text-sm font-bold text-green-200"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span></div>
                <h2 class="text-4xl md:text-6xl font-black">Making AgriTech Sustainable</h2>
                <p class="max-w-2xl mx-auto text-lg leading-8 text-green-100">
                    Descubre cómo las herramientas digitales pueden ayudarte a gestionar de forma más eficiente y sostenible tu cadena agroalimentaria.
                </p>
                <div class="pt-4">
                    <a href="#contacto" class="inline-flex bg-white text-[#263d20] px-8 py-4 rounded-full font-black hover:bg-[#f4f8ef] transition shadow-lg">
                        SOLICITA INFORMACIÓN
                    </a>
                </div>
            </div>
        </section>

        <!-- ==========================================================
             NEWSLETTER
========================================================== -->
        <section id="contacto" class="py-24 bg-[#263d20] text-white" x-data="{ newsletterSent: false }">
            <div class="container-agri">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="space-y-6">
                        <span class="text-[#b8d497] uppercase tracking-[.25em] text-sm font-bold">Mantente informado</span>
                        <h2 class="text-4xl md:text-5xl font-black leading-tight">Agricultura, tecnología y sostenibilidad.</h2>
                        <p class="text-green-100 text-lg leading-8">Recibe novedades sobre agricultura de precisión, innovación y herramientas digitales.</p>
                    </div>

                    <form @submit.prevent="newsletterSent = true" class="bg-white rounded-[30px] p-8 text-gray-800 shadow-2xl">
                        <div x-show="!newsletterSent" class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold mb-2">Nombre</label>
                                <input type="text" required placeholder="Tu nombre" class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a]">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Email</label>
                                <input type="email" required placeholder="tu@email.com" class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a]">
                            </div>
                            <label class="flex gap-3 text-sm text-gray-500">
                                <input type="checkbox" required class="mt-1 accent-green-600">
                                <span class="text-black">Acepto la política de privacidad.</span>
                            </label>
                            <button type="submit" class="w-full mt-6 bg-[#619037] hover:bg-[#4d762d] text-white py-4 rounded-full font-black transition shadow-md">
                                SUSCRIBIRME
                            </button>
                        </div>
                        <div x-show="newsletterSent" x-cloak class="text-center py-10 space-y-4">
                            <div class="w-16 h-16 mx-auto rounded-full bg-green-100 text-green-600 flex items-center justify-center text-3xl font-black">✓</div>
                            <h3 class="text-2xl font-black text-[#263d20]">¡Gracias!</h3>
                            <p class="text-gray-500">Tu solicitud ha sido registrada.</p>
                        </div>
                    </form>
                </div>
            </div>
        </section>

    </div>
</x-presentacion-guest>

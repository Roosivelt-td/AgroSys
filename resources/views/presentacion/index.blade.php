<x-presentacion-guest>
    <div class="text-[#173B27] bg-[#F7F8F2] font-sans">

        <!-- =====================================================
             HERO
        ====================================================== -->
        <section class="pt-20 pb-24 bg-[#F7F8F2] grid-bg overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div class="min-h-[700px] grid lg:grid-cols-2 gap-16 items-center">

                    <!-- HERO TEXT -->
                    <div class="py-12 reveal space-y-7">
                        <div class="inline-flex items-center gap-2 bg-white rounded-full px-4 py-2 shadow-sm border border-gray-100">
                            <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-4 h-4 object-contain inline-block mr-1 align-middle">
                            <span class="text-xs font-bold uppercase tracking-[2px] text-[#173B27]"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span></span>
                        </div>

                        <h1 class="text-5xl md:text-6xl lg:text-[72px] font-bold leading-[.96] tracking-[-4px] text-[#173B27]">
                            La tecnología al servicio de <span class="text-[#78B82A]">la agricultura</span>
                        </h1>

                        <p class="text-lg md:text-xl text-[#657067] leading-8 max-w-xl">
                            Soluciones digitales para gestionar el campo de forma sencilla, precisa y sostenible.
                        </p>

                        <div class="flex flex-wrap gap-4 pt-4">
                            <a href="#soluciones" class="bg-[#173B27] hover:bg-[#245337] text-white px-7 py-4 rounded-full font-semibold transition shadow-md">
                                Descubre <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span>
                            </a>
                            <a href="#contacto" class="border border-[#173B27] hover:bg-[#173B27] hover:text-white px-7 py-4 rounded-full font-semibold transition">
                                Solicita una demo
                            </a>
                        </div>

                        <!-- MINI STATS -->
                        <div class="flex flex-wrap gap-8 pt-8 border-t border-gray-200">
                            <div>
                                <strong class="block text-3xl font-bold text-[#173B27]">4.0</strong>
                                <span class="text-sm text-[#657067]">Agricultura digital</span>
                            </div>
                            <div>
                                <strong class="block text-3xl font-bold text-[#173B27]">360°</strong>
                                <span class="text-sm text-[#657067]">Gestión agrícola</span>
                            </div>
                            <div>
                                <strong class="block text-3xl font-bold text-[#173B27]">+Data</strong>
                                <span class="text-sm text-[#657067]">Decisiones inteligentes</span>
                            </div>
                        </div>
                    </div>

                    <!-- HERO IMAGE -->
                    <div class="relative min-h-[620px] flex items-center justify-center reveal">
                        <div class="absolute right-[-80px] bottom-[-40px] w-[520px] h-[520px] rounded-full bg-[#DDEBC9] pointer-events-none"></div>
                        <div class="absolute left-0 top-20 w-28 h-28 rounded-full border-[15px] border-[#78B82A]/20 pointer-events-none"></div>

                        <img src="https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1300&q=90"
                             alt="Agricultura digital"
                             class="hero-image relative z-10 w-full max-w-[600px] h-[560px] object-cover shadow-2xl">

                        <!-- FLOATING DATA CARD -->
                        <div class="float absolute z-20 left-0 bottom-12 bg-white rounded-[24px] p-5 shadow-2xl border border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-[#EEF6E5] flex items-center justify-center text-[#78B82A]">
                                    <svg width="25" height="25" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path d="M4 18h16"/><path d="M6 15l4-5 3 3 5-7"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-[#657067]">Datos agrícolas</p>
                                    <p class="font-bold text-[#173B27]">En tiempo real</p>
                                </div>
                            </div>
                        </div>

                        <!-- SECOND CARD -->
                        <div class="float absolute z-20 right-[-15px] top-20 bg-[#173B27] text-white rounded-[22px] px-5 py-4 shadow-xl" style="animation-delay:1.2s">
                            <div class="text-xs text-white/60">Precisión</div>
                            <div class="text-xl font-bold">Agricultura 4.0</div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- =====================================================
             INTRO
====================================================== -->
        <section class="py-24 bg-white">
            <div class="max-w-5xl mx-auto px-6 text-center reveal space-y-6">
                <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold">Agricultura digital</span>
                <h2 class="text-4xl md:text-5xl font-bold leading-tight text-[#173B27]">
                    Todo lo que necesitas para gestionar mejor tu campo
                </h2>
                <p class="mt-6 text-lg md:text-xl text-[#657067] leading-8 max-w-3xl mx-auto">
                    <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> integra datos, tecnologías y herramientas agronómicas en una única plataforma para ayudarte a conocer mejor tus cultivos y tomar decisiones basadas en información.
                </p>
            </div>
        </section>

        <!-- =====================================================
             SOLUCIONES
====================================================== -->
        <section id="soluciones" class="py-24 bg-[#F7F8F2]">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 space-y-14">
                <div class="max-w-3xl reveal space-y-4">
                    <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold">Nuestras soluciones</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-[#173B27]">Una plataforma. Muchas posibilidades.</h2>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- SATELLITE -->
                    <article class="solution-card bg-white rounded-[32px] overflow-hidden reveal shadow-sm border border-gray-100">
                        <div class="h-[270px] overflow-hidden"><img src="https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1000&q=85" class="w-full h-full object-cover" alt="Monitoreo satelital"></div>
                        <div class="p-7 space-y-3">
                            <span class="text-xs uppercase tracking-[2px] text-[#78B82A] font-bold">Teledetección</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">Satélite</h3>
                            <p class="text-[#657067] leading-7 text-sm">Observa el estado de tus cultivos mediante imágenes satelitales.</p>
                            <a href="{{ route('soluciones.satelital') }}" class="inline-flex font-semibold text-[#173B27] hover:text-[#78B82A] pt-2">Descubrir →</a>
                        </div>
                    </article>

                    <!-- IRRIGATION -->
                    <article class="solution-card bg-white rounded-[32px] overflow-hidden reveal shadow-sm border border-gray-100">
                        <div class="h-[270px] overflow-hidden"><img src="https://images.unsplash.com/photo-1564419320461-6870880221ad?auto=format&fit=crop&w=1000&q=85" class="w-full h-full object-cover" alt="Riego agrícola"></div>
                        <div class="p-7 space-y-3">
                            <span class="text-xs uppercase tracking-[2px] text-[#78B82A] font-bold">Agua y nutrición</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">Riego y nutrición</h3>
                            <p class="text-[#657067] leading-7 text-sm">Planifica y optimiza el uso del agua y los nutrientes.</p>
                            <a href="{{ route('soluciones.agronomica') }}" class="inline-flex font-semibold text-[#173B27] hover:text-[#78B82A] pt-2">Descubrir →</a>
                        </div>
                    </article>

                    <!-- PROTECTION -->
                    <article class="solution-card bg-white rounded-[32px] overflow-hidden reveal shadow-sm border border-gray-100">
                        <div class="h-[270px] overflow-hidden"><img src="https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&w=1000&q=85" class="w-full h-full object-cover" alt="Protección de cultivos"></div>
                        <div class="p-7 space-y-3">
                            <span class="text-xs uppercase tracking-[2px] text-[#78B82A] font-bold">Agronomía</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">Protección de cultivos</h3>
                            <p class="text-[#657067] leading-7 text-sm">Anticipa riesgos y gestiona la defensa de tus cultivos.</p>
                            <a href="{{ route('soluciones.defensa') }}" class="inline-flex font-semibold text-[#173B27] hover:text-[#78B82A] pt-2">Descubrir →</a>
                        </div>
                    </article>

                    <!-- APP -->
                    <article class="solution-card bg-[#173B27] text-white rounded-[32px] overflow-hidden reveal shadow-xl">
                        <div class="h-[270px] overflow-hidden"><img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1000&q=85" class="w-full h-full object-cover opacity-90" alt="AgroSys App"></div>
                        <div class="p-7 space-y-3">
                            <span class="text-xs uppercase tracking-[2px] text-[#9BD44B] font-bold">Digital</span>
                            <h3 class="text-2xl font-bold">La App de AgroSys</h3>
                            <p class="text-white/70 leading-7 text-sm">Lleva la gestión de tus parcelas directamente al campo.</p>
                            <a href="{{ route('soluciones.exploracion') }}" class="inline-flex font-semibold text-[#9BD44B] pt-2">Descubrir →</a>
                        </div>
                    </article>

                    <!-- AGRITRACK -->
                    <article class="solution-card bg-white rounded-[32px] overflow-hidden reveal shadow-sm border border-gray-100">
                        <div class="h-[270px] overflow-hidden"><img src="https://images.unsplash.com/photo-1586771107445-d3ca888129ff?auto=format&fit=crop&w=1000&q=85" class="w-full h-full object-cover" alt="AgriTrack"></div>
                        <div class="p-7 space-y-3">
                            <span class="text-xs uppercase tracking-[2px] text-[#78B82A] font-bold">Gestión</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">AgriTrack</h3>
                            <p class="text-[#657067] leading-7 text-sm">Gestiona operaciones, actividades y recursos agrícolas desde una plataforma digital.</p>
                            <a href="{{ route('soluciones.controlCadena') }}" class="inline-flex font-semibold text-[#173B27] hover:text-[#78B82A] pt-2">Descubrir →</a>
                        </div>
                    </article>

                    <!-- WEATHER -->
                    <article class="solution-card bg-white rounded-[32px] overflow-hidden reveal shadow-sm border border-gray-100">
                        <div class="h-[270px] overflow-hidden"><img src="https://images.unsplash.com/photo-1534088568595-a066f410bcda?auto=format&fit=crop&w=1000&q=85" class="w-full h-full object-cover" alt="Estación meteorológica"></div>
                        <div class="p-7 space-y-3">
                            <span class="text-xs uppercase tracking-[2px] text-[#78B82A] font-bold">Meteorología</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">Estaciones agrometeo</h3>
                            <p class="text-[#657067] leading-7 text-sm">Monitoriza las condiciones meteorológicas de tus parcelas.</p>
                            <a href="{{ route('soluciones.agrometeo') }}" class="inline-flex font-semibold text-[#173B27] hover:text-[#78B82A] pt-2">Descubrir →</a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- =====================================================
             DATA SECTION
====================================================== -->
        <section id="tecnologia" class="py-28 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="relative reveal">
                        <div class="absolute inset-0 bg-[#78B82A] rounded-[45px] rotate-3 opacity-20"></div>
                        <img src="https://images.unsplash.com/photo-1589923188900-85dae523342b?auto=format&fit=crop&w=1200&q=85" alt="Tecnología agrícola" class="relative w-full h-[530px] object-cover rounded-[45px] shadow-xl">
                        <div class="absolute right-5 bottom-5 bg-white rounded-[24px] px-6 py-5 shadow-xl border border-gray-100">
                            <p class="text-xs text-[#657067]">Información</p>
                            <p class="mt-1 text-xl font-bold text-[#173B27]">En un solo lugar</p>
                        </div>
                    </div>

                    <div class="reveal space-y-6">
                        <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold">Tecnología</span>
                        <h2 class="text-4xl md:text-5xl font-bold leading-tight text-[#173B27]">Datos que se convierten en decisiones</h2>
                        <p class="text-lg text-[#657067] leading-8">La plataforma combina datos meteorológicos, información agronómica, imágenes satelitales y tecnologías de agricultura de precisión.</p>

                        <div class="space-y-5 pt-4">
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 flex-shrink-0 rounded-full bg-[#EEF6E5] text-[#78B82A] flex items-center justify-center font-bold">✓</div>
                                <div>
                                    <h3 class="font-bold text-lg text-[#173B27]">Datos conectados</h3>
                                    <p class="mt-1 text-sm text-[#657067]">Centraliza la información de tus explotaciones.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 flex-shrink-0 rounded-full bg-[#EEF6E5] text-[#78B82A] flex items-center justify-center font-bold">✓</div>
                                <div>
                                    <h3 class="font-bold text-lg text-[#173B27]">Análisis inteligente</h3>
                                    <p class="mt-1 text-sm text-[#657067]">Convierte datos complejos en información útil.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 flex-shrink-0 rounded-full bg-[#EEF6E5] text-[#78B82A] flex items-center justify-center font-bold">✓</div>
                                <div>
                                    <h3 class="font-bold text-lg text-[#173B27]">Gestión desde cualquier lugar</h3>
                                    <p class="mt-1 text-sm text-[#657067]">Accede a tus datos desde ordenador, tablet o móvil.</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('soluciones.tecnologia') }}" class="mt-8 inline-flex bg-[#173B27] hover:bg-[#245337] text-white px-7 py-4 rounded-full font-semibold transition shadow-md">
                            Conoce nuestra tecnología
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
             HOW IT WORKS
====================================================== -->
        <section class="py-24 bg-[#EEF6E5]">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 space-y-16">
                <div class="text-center max-w-3xl mx-auto reveal space-y-4">
                    <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold">Cómo funciona</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-[#173B27]">Del dato a la acción</h2>
                    <p class="text-lg text-[#657067]">Una forma más sencilla de gestionar la información agronómica.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-[32px] p-8 reveal shadow-sm space-y-6">
                        <div class="flex justify-between items-center">
                            <span class="text-5xl font-bold text-[#78B82A]/30">01</span>
                            <div class="w-14 h-14 rounded-2xl bg-[#EEF6E5] flex items-center justify-center text-[#78B82A]"><svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/></svg></div>
                        </div>
                        <h3 class="text-2xl font-bold text-[#173B27]">Recopila</h3>
                        <p class="text-[#657067] leading-7">Recoge datos del campo, estaciones, satélites y actividades agrícolas.</p>
                    </div>

                    <div class="bg-[#173B27] text-white rounded-[32px] p-8 reveal shadow-xl space-y-6">
                        <div class="flex justify-between items-center">
                            <span class="text-5xl font-bold text-white/20">02</span>
                            <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-[#9BD44B]"><svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path d="M4 18h16"/><path d="M6 15l4-5 3 3 5-7"/></svg></div>
                        </div>
                        <h3 class="text-2xl font-bold">Analiza</h3>
                        <p class="text-white/60 leading-7">Procesa y relaciona la información para entender qué ocurre en tus parcelas.</p>
                    </div>

                    <div class="bg-white rounded-[32px] p-8 reveal shadow-sm space-y-6">
                        <div class="flex justify-between items-center">
                            <span class="text-5xl font-bold text-[#78B82A]/30">03</span>
                            <div class="w-14 h-14 rounded-2xl bg-[#EEF6E5] flex items-center justify-center text-[#78B82A]"><svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path d="M5 12l4 4L19 6"/></svg></div>
                        </div>
                        <h3 class="text-2xl font-bold text-[#173B27]">Decide</h3>
                        <p class="text-[#657067] leading-7">Utiliza la información para actuar de forma más precisa.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
             ACADEMY
====================================================== -->
        <section id="academy" class="py-28 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 grid lg:grid-cols-2 gap-16 items-center">
                <div class="reveal space-y-6">
                    <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> Academy</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-[#173B27]">Aprende a sacar todo el partido a la tecnología agrícola</h2>
                    <p class="text-lg text-[#657067] leading-8">Formación, contenidos y recursos para profesionales que quieren avanzar hacia una agricultura más digital y precisa.</p>
                    <a href="{{ route('soluciones.academy') }}" class="inline-flex bg-[#78B82A] hover:bg-[#609B20] text-white px-7 py-4 rounded-full font-semibold transition shadow-md">
                        Visita Academy
                    </a>
                </div>

                <div class="relative reveal">
                    <img src="https://images.unsplash.com/photo-1530267981375-f0de937f5f13?auto=format&fit=crop&w=1200&q=85" alt="Academy" class="w-full h-[500px] object-cover rounded-[45px] shadow-xl">
                    <div class="absolute right-5 bottom-5 bg-white rounded-[24px] px-6 py-5 shadow-xl border border-gray-100">
                        <p class="text-xs uppercase tracking-wider text-[#78B82A] font-bold">Formación</p>
                        <p class="mt-1 font-bold text-lg text-[#173B27]">Aprende. Aplica. Mejora.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
             SUSTAINABILITY
====================================================== -->
        <section id="sostenibilidad" class="py-24 bg-[#F7F8F2]">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 space-y-14">
                <div class="max-w-3xl reveal space-y-4">
                    <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold">Sostenibilidad</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-[#173B27]">Tecnología para una agricultura más sostenible</h2>
                    <p class="text-lg text-[#657067] leading-8">La digitalización puede ayudar a optimizar recursos, reducir desperdicios y mejorar la trazabilidad de las actividades agrícolas.</p>
                </div>

                <div class="grid lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-[30px] p-8 reveal shadow-sm space-y-4 border border-gray-100">
                        <div class="text-4xl text-[#78B82A]">💧</div>
                        <h3 class="text-xl font-bold text-[#173B27]">Optimización del agua</h3>
                        <p class="text-[#657067] leading-7">Mejora la planificación del riego utilizando datos del cultivo y del clima.</p>
                    </div>
                    <div class="bg-[#173B27] text-white rounded-[30px] p-8 reveal shadow-xl space-y-4">
                        <div class="text-4xl">🌱</div>
                        <h3 class="text-xl font-bold">Uso eficiente de recursos</h3>
                        <p class="text-white/60 leading-7">Apoya decisiones más precisas sobre tratamientos y operaciones.</p>
                    </div>
                    <div class="bg-white rounded-[30px] p-8 reveal shadow-sm space-y-4 border border-gray-100">
                        <div class="text-4xl text-[#78B82A]">📊</div>
                        <h3 class="text-xl font-bold text-[#173B27]">Trazabilidad</h3>
                        <p class="text-[#657067] leading-7">Registra y consulta la información de las actividades agrícolas.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
             COMPANY
====================================================== -->
        <section id="empresa" class="py-28 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 grid lg:grid-cols-2 gap-16 items-center">
                <div class="reveal space-y-6">
                    <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span></span>
                    <h2 class="text-4xl md:text-5xl font-bold text-[#173B27]">Tecnología nacida para el campo</h2>
                    <p class="text-lg text-[#657067] leading-8">Combinamos agronomía, tecnología y experiencia para desarrollar herramientas digitales pensadas para las necesidades reales del sector agroalimentario.</p>

                    <div class="grid grid-cols-2 gap-5 pt-2">
                        <div>
                            <p class="text-4xl font-bold text-[#78B82A]">4.0</p>
                            <p class="mt-1 text-sm text-[#657067]">Agricultura digital</p>
                        </div>
                        <div>
                            <p class="text-4xl font-bold text-[#78B82A]">Global</p>
                            <p class="mt-1 text-sm text-[#657067]">Red internacional</p>
                        </div>
                    </div>

                    <a href="{{ route('soluciones.empresa') }}" class="mt-6 inline-flex border border-[#173B27] hover:bg-[#173B27] hover:text-white px-7 py-4 rounded-full font-semibold transition">
                        Conoce <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span>
                    </a>
                </div>

                <div class="reveal">
                    <img src="https://images.unsplash.com/photo-1523742815505-3c34f0c9c5f5?auto=format&fit=crop&w=1200&q=85" alt="Agricultura" class="w-full h-[560px] object-cover rounded-[45px] shadow-xl">
                </div>
            </div>
        </section>

        <!-- =====================================================
             MARQUEE
====================================================== -->
        <section class="bg-[#78B82A] py-7 marquee overflow-hidden shadow-md">
            <div class="marquee-track flex">
                <div class="flex items-center gap-10 px-5 text-white text-2xl md:text-3xl font-bold">
                    <span>MAKING AGRITECH SUSTAINABLE</span>
                    <span class="text-white/40">✦</span>
                    <span>AGRICULTURE 4.0</span>
                    <span class="text-white/40">✦</span>
                    <span>DIGITAL AGRICULTURE</span>
                    <span class="text-white/40">✦</span>
                </div>
                <div class="flex items-center gap-10 px-5 text-white text-2xl md:text-3xl font-bold">
                    <span>MAKING AGRITECH SUSTAINABLE</span>
                    <span class="text-white/40">✦</span>
                    <span>AGRICULTURE 4.0</span>
                    <span class="text-white/40">✦</span>
                    <span>DIGITAL AGRICULTURE</span>
                </div>
            </div>
        </section>

        <!-- =====================================================
             CTA
====================================================== -->
        <section id="contacto" class="py-28 bg-[#173B27] text-white text-center">
            <div class="max-w-5xl mx-auto px-6 reveal space-y-6">
                <span class="text-[#9BD44B] uppercase tracking-[3px] text-sm font-bold">Empieza ahora</span>
                <h2 class="text-4xl md:text-6xl font-bold leading-tight">Lleva tu agricultura al siguiente nivel</h2>
                <p class="mt-6 max-w-2xl mx-auto text-lg text-white/60 leading-8">
                    Descubre cómo AgroSys puede ayudarte a digitalizar y mejorar la gestión de tus explotaciones agrícolas.
                </p>

                <div class="mt-9 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('register') }}" class="bg-[#78B82A] hover:bg-[#609B20] text-white px-8 py-4 rounded-full font-semibold transition shadow-md">
                        Solicita una demo
                    </a>
                    <a href="{{ route('soluciones.contactos') }}" class="border border-white/30 hover:bg-white hover:text-[#173B27] px-8 py-4 rounded-full font-semibold transition">
                        Contacta con nosotros
                    </a>
                </div>
            </div>
        </section>

    </div>

    <script>
        const elements = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: .12 });
        elements.forEach(element => observer.observe(element));

        window.addEventListener('scroll', () => {
            const image = document.querySelector('.hero-image');
            if (!image) return;
            const scroll = window.scrollY;
            if (scroll < 650) {
                image.style.transform = `translateY(${scroll * .035}px)`;
            }
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (!target) return;
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</x-presentacion-guest>

<x-presentacion-guest>
    <div class="text-[#173B27] bg-white font-sans">

        <!-- =========================================================
             HERO (Basado en la Captura)
        ========================================================= -->
        <section class="pt-24 pb-20 bg-[#F7F8F2] grid-bg overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div class="min-h-[600px] grid lg:grid-cols-2 gap-16 items-center py-12">
                    <div class="reveal space-y-7">
                        <div class="inline-flex items-center gap-2 bg-white rounded-full px-4 py-2 shadow-sm border border-gray-100">
                            <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-4 h-4 object-contain inline-block mr-1 align-middle">
                            <span class="text-xs font-bold uppercase tracking-[2px] text-[#173B27]">Investigación industrial</span>
                        </div>

                        <h1 class="text-5xl md:text-6xl lg:text-[72px] font-bold leading-[.96] tracking-[-4px] text-[#173B27]">
                            Investigación para <br>una agricultura <br><span class="text-[#78B82A]">más inteligente</span>
                        </h1>

                        <p class="text-lg md:text-xl text-[#606960] leading-8 max-w-xl">
                            Desarrollamos nuevas funciones y soluciones para responder a las necesidades de la Agricultura 4.0 y de todos los actores de la cadena agroalimentaria.
                        </p>

                        <div class="pt-4 flex flex-wrap gap-4">
                            <a href="#areas" class="bg-[#173B27] hover:bg-[#245337] text-white px-7 py-4 rounded-full font-semibold transition shadow-md">
                                Nuestras áreas
                            </a>
                            <a href="#proyectos" class="border border-[#173B27] hover:bg-[#173B27] hover:text-white px-7 py-4 rounded-full font-semibold transition">
                                Ver proyectos
                            </a>
                        </div>
                    </div>

                    <div class="relative min-h-[500px] flex items-center justify-center reveal">
                        <div class="absolute right-[-60px] bottom-[-30px] w-[450px] h-[450px] rounded-full bg-[#F1F7E9] pointer-events-none"></div>

                        <div class="relative overflow-hidden rounded-[40px] shadow-2xl max-w-[600px] w-full">
                            <img src="https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&w=1200&q=85" alt="Agricultura inteligente" class="w-full h-[520px] object-cover">

                            <!-- Floating Card -->
                            <div class="absolute bottom-6 left-6 bg-white rounded-2xl p-4 shadow-xl flex items-center gap-3 border border-gray-100">
                                <div class="w-10 h-10 rounded-xl bg-[#F1F7E9] flex items-center justify-center text-[#78B82A] text-lg font-bold">🎯</div>
                                <div>
                                    <div class="text-xs text-gray-400 font-semibold">Precisión</div>
                                    <div class="font-bold text-[#173B27] text-sm">Agricultura 4.0 • Innovación aplicada</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =========================================================
             CONVERTIMOS LA INVESTIGACIÓN (SUB-HERO)
        ========================================================= -->
        <section class="py-20 bg-white text-center">
            <div class="max-w-4xl mx-auto px-6 space-y-6">
                <span class="text-[#78B82A] uppercase tracking-[3px] text-xs font-bold">Investigación & Innovación</span>
                <h2 class="text-4xl md:text-5xl font-bold text-[#173B27] leading-tight">
                    Convertimos la investigación en soluciones para el campo
                </h2>
                <p class="text-lg text-[#606960] leading-8 max-w-2xl mx-auto">
                    <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> participa como socio tecnológico en proyectos nacionales e internacionales orientados al desarrollo de nuevas tecnologías para la agricultura digital.
                </p>

                <!-- Badges / Partners -->
                <div class="flex flex-wrap justify-center gap-3 pt-4">
                    <span class="px-5 py-2.5 bg-[#F7F9F5] border border-gray-100 rounded-full text-sm font-semibold text-[#173B27]">Horizon Europe</span>
                    <span class="px-5 py-2.5 bg-[#F7F9F5] border border-gray-100 rounded-full text-sm font-semibold text-[#173B27]">EIT Food</span>
                    <span class="px-5 py-2.5 bg-[#F7F9F5] border border-gray-100 rounded-full text-sm font-semibold text-[#173B27]">Fiware</span>
                    <span class="px-5 py-2.5 bg-[#F7F9F5] border border-gray-100 rounded-full text-sm font-semibold text-[#173B27]">Organizaciones europeas</span>
                </div>
            </div>
        </section>

        <!-- =========================================================
             ÁREAS DE INVESTIGACIÓN
        ========================================================= -->
        <section id="areas" class="py-24 bg-[#F7F8F2]">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 space-y-16">
                <div class="max-w-3xl reveal space-y-4">
                    <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold">Áreas de investigación</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-[#173B27]">Tres áreas para impulsar la innovación agrícola</h2>
                    <p class="text-lg text-[#606960] leading-8">Centramos nuestra actividad científica y tecnológica en tres pilares fundamentales para la agricultura del futuro.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- 1 -->
                    <article class="project-card bg-white rounded-[32px] p-8 reveal border border-gray-100 shadow-sm space-y-6">
                        <div class="w-14 h-14 rounded-2xl bg-[#F1F7E9] flex items-center justify-center text-[#78B82A] text-2xl font-bold">🛰️</div>
                        <span class="text-xs font-bold tracking-[2px] text-[#78B82A]">01</span>
                        <h3 class="text-2xl font-bold text-[#173B27]">Observación de la Tierra</h3>
                        <p class="text-[#606960] leading-7">Teledetección satelital, análisis multiespectral y monitorización espacial de cultivos.</p>
                    </article>

                    <!-- 2 -->
                    <article class="project-card bg-white rounded-[32px] p-8 reveal border border-gray-100 shadow-sm space-y-6">
                        <div class="w-14 h-14 rounded-2xl bg-[#F1F7E9] flex items-center justify-center text-[#78B82A] text-2xl font-bold">🤖</div>
                        <span class="text-xs font-bold tracking-[2px] text-[#78B82A]">02</span>
                        <h3 class="text-2xl font-bold text-[#173B27]">Modelos de predicción</h3>
                        <p class="text-[#606960] leading-7">Inteligencia artificial y algoritmos predictivos para riego, plagas y fenología.</p>
                    </article>

                    <!-- 3 -->
                    <article class="project-card bg-white rounded-[32px] p-8 reveal border border-gray-100 shadow-sm space-y-6">
                        <div class="w-14 h-14 rounded-2xl bg-[#F1F7E9] flex items-center justify-center text-[#78B82A] text-2xl font-bold">🌱</div>
                        <span class="text-xs font-bold tracking-[2px] text-[#78B82A]">03</span>
                        <h3 class="text-2xl font-bold text-[#173B27]">Sostenibilidad</h3>
                        <p class="text-[#606960] leading-7">Investigación orientada a una gestión eficiente y una cadena transparente.</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- =========================================================
             PROJECTS INTRO
        ========================================================= -->
        <section id="proyectos" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div class="max-w-3xl reveal space-y-4">
                    <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold">Proyectos internacionales</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-[#173B27]">Investigación aplicada a problemas reales</h2>
                    <p class="text-lg text-[#606960] leading-8">Participamos en proyectos que combinan investigación, datos, IA y teledetección para crear herramientas para el sector agrícola.</p>
                </div>
            </div>
        </section>

        <!-- =========================================================
             OBSERVACIÓN DE LA TIERRA
        ========================================================= -->
        <section class="pb-24 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 space-y-10">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-4 h-4 object-contain inline-block mr-1 align-middle">
                    <h2 class="text-3xl font-bold text-[#173B27]">Observación de la Tierra</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <article class="project-card bg-[#F7F9F5] rounded-[32px] overflow-hidden border border-gray-100 reveal shadow-sm">
                        <div class="h-[280px] overflow-hidden"><img src="https://images.unsplash.com/photo-1586771107445-d3ca888129ff?auto=format&fit=crop&w=1200&q=85" alt="AgriTrack" class="w-full h-full object-cover"></div>
                        <div class="p-8 space-y-4">
                            <span class="text-xs uppercase tracking-[2px] text-[#78B82A] font-bold">Observación de la Tierra</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">AgriTrack FullDSS Stack</h3>
                            <p class="text-[#606960] leading-7">Desarrollo de un sistema avanzado de soporte a la decisión capaz de combinar modelos e información histórica.</p>
                            <div class="inline-flex px-4 py-2 bg-white rounded-full text-xs font-bold text-[#173B27] shadow-sm">ESA Incubed</div>
                        </div>
                    </article>

                    <article class="project-card bg-[#F7F9F5] rounded-[32px] overflow-hidden border border-gray-100 reveal shadow-sm">
                        <div class="h-[280px] overflow-hidden"><img src="https://images.unsplash.com/photo-1504608524841-42fe6f032b4b?auto=format&fit=crop&w=1200&q=85" alt="Meteo Map" class="w-full h-full object-cover"></div>
                        <div class="p-8 space-y-4">
                            <span class="text-xs uppercase tracking-[2px] text-[#78B82A] font-bold">Observación de la Tierra</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">Meteo_Map</h3>
                            <p class="text-[#606960] leading-7">Herramienta para visualizar datos meteorológicos en tiempo real, espacializando la información a diferentes escalas.</p>
                            <div class="inline-flex px-4 py-2 bg-white rounded-full text-xs font-bold text-[#173B27] shadow-sm">NextGenerationEU</div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- =========================================================
             MODELOS DE PREDICCIÓN
        ========================================================= -->
        <section class="py-24 bg-[#F7F8F2]">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 space-y-10">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-4 h-4 object-contain inline-block mr-1 align-middle">
                    <h2 class="text-3xl font-bold text-[#173B27]">Modelos de predicción</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <article class="project-card bg-white rounded-[32px] overflow-hidden border border-gray-100 reveal shadow-sm">
                        <div class="h-[280px] overflow-hidden"><img src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1200&q=85" alt="CLEVER" class="w-full h-full object-cover"></div>
                        <div class="p-8 space-y-4">
                            <span class="text-xs uppercase tracking-[2px] text-[#78B82A] font-bold">Modelos de predicción</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">CLEVER</h3>
                            <p class="text-[#606960] leading-7">Sistema de soporte a la decisión para la recolección inteligente de naranjas mediante IA y computación periférica.</p>
                            <div class="inline-flex px-4 py-2 bg-[#EEF6E5] rounded-full text-xs font-bold text-[#173B27]">Horizon-KDT-JU</div>
                        </div>
                    </article>

                    <article class="project-card bg-white rounded-[32px] overflow-hidden border border-gray-100 reveal shadow-sm">
                        <div class="h-[280px] overflow-hidden"><img src="https://images.unsplash.com/photo-1495107334309-fcf20504a5ab?auto=format&fit=crop&w=1200&q=85" alt="VALPRO" class="w-full h-full object-cover"></div>
                        <div class="p-8 space-y-4">
                            <span class="text-xs uppercase tracking-[2px] text-[#78B82A] font-bold">Modelos de predicción</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">VALPRO</h3>
                            <p class="text-[#606960] leading-7">Proyecto orientado a mejorar la producción de proteínas vegetales para alimentos y piensos mediante innovaciones y laboratorios vivientes.</p>
                            <div class="inline-flex px-4 py-2 bg-[#EEF6E5] rounded-full text-xs font-bold text-[#173B27]">Horizon Europe</div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- =========================================================
             SOSTENIBILIDAD
        ========================================================= -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 space-y-10">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-4 h-4 object-contain inline-block mr-1 align-middle">
                    <h2 class="text-3xl font-bold text-[#173B27]">Sostenibilidad</h2>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <article class="project-card bg-[#F7F9F5] rounded-[30px] overflow-hidden border border-gray-100 reveal shadow-sm">
                        <div class="h-[230px] overflow-hidden"><img src="https://images.unsplash.com/photo-1492496913980-501348b61469?auto=format&fit=crop&w=900&q=85" alt="TITAN" class="w-full h-full object-cover"></div>
                        <div class="p-7 space-y-3">
                            <span class="text-xs text-[#78B82A] font-bold tracking-[2px] uppercase">Sostenibilidad</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">TITAN</h3>
                            <p class="text-sm text-[#606960] leading-6">Soluciones para aumentar la transparencia de la cadena alimentaria y mejorar la trazabilidad, sostenibilidad y seguridad.</p>
                        </div>
                    </article>

                    <article class="project-card bg-[#F7F9F5] rounded-[30px] overflow-hidden border border-gray-100 reveal shadow-sm">
                        <div class="h-[230px] overflow-hidden"><img src="https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&w=900&q=85" alt="CEBUS" class="w-full h-full object-cover"></div>
                        <div class="p-7 space-y-3">
                            <span class="text-xs text-[#78B82A] font-bold tracking-[2px] uppercase">Sostenibilidad</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">CEBUS</h3>
                            <p class="text-sm text-[#606960] leading-6">Modelo de balance húmico para estudiar la variación de materia orgánica del suelo y favorecer una gestión agrícola sostenible.</p>
                        </div>
                    </article>

                    <article class="project-card bg-[#F7F9F5] rounded-[30px] overflow-hidden border border-gray-100 reveal shadow-sm">
                        <div class="h-[230px] overflow-hidden"><img src="https://images.unsplash.com/photo-1533130061792-64b345e4a833?auto=format&fit=crop&w=900&q=85" alt="Action for Children in Conflict" class="w-full h-full object-cover"></div>
                        <div class="p-7 space-y-3">
                            <span class="text-xs text-[#78B82A] font-bold tracking-[2px] uppercase">Sostenibilidad</span>
                            <h3 class="text-2xl font-bold text-[#173B27]">Action for Children in Conflict</h3>
                            <p class="text-sm text-[#606960] leading-6">Proyecto desarrollado en Kenia para aplicar herramientas AgriTech en la agricultura rural.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- =========================================================
             PROCESS
========================================================= -->
        <section class="py-24 bg-[#EEF6E5]">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="reveal space-y-6">
                        <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold">Del laboratorio al campo</span>
                        <h2 class="text-4xl md:text-5xl font-bold leading-tight text-[#173B27]">Investigación que se convierte en innovación</h2>
                        <p class="text-lg text-[#606960] leading-8">La investigación industrial conecta conocimientos científicos, tecnologías digitales y necesidades reales del sector agroalimentario.</p>
                        <a href="{{ route('soluciones.contactos') }}" class="mt-4 inline-flex bg-[#173B27] hover:bg-[#245337] text-white px-7 py-4 rounded-full font-semibold transition">
                            Colabora con nosotros
                        </a>
                    </div>

                    <div class="space-y-5 reveal">
                        <div class="bg-white rounded-[25px] p-6 flex gap-5 items-start shadow-sm">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-[#EEF6E5] text-[#78B82A] flex items-center justify-center font-bold">01</div>
                            <div>
                                <h3 class="text-xl font-bold text-[#173B27]">Investigar</h3>
                                <p class="mt-2 text-sm text-[#606960] leading-6">Identificamos nuevos retos y oportunidades tecnológicas.</p>
                            </div>
                        </div>

                        <div class="bg-white rounded-[25px] p-6 flex gap-5 items-start shadow-sm">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-[#EEF6E5] text-[#78B82A] flex items-center justify-center font-bold">02</div>
                            <div>
                                <h3 class="text-xl font-bold text-[#173B27]">Desarrollar</h3>
                                <p class="mt-2 text-sm text-[#606960] leading-6">Convertimos los resultados de investigación en herramientas digitales.</p>
                            </div>
                        </div>

                        <div class="bg-white rounded-[25px] p-6 flex gap-5 items-start shadow-sm">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-[#EEF6E5] text-[#78B82A] flex items-center justify-center font-bold">03</div>
                            <div>
                                <h3 class="text-xl font-bold text-[#173B27]">Aplicar</h3>
                                <p class="mt-2 text-sm text-[#606960] leading-6">Llevamos la innovación al terreno para generar soluciones utilizables.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =========================================================
             MARQUEE
========================================================= -->
        <section class="bg-[#78B82A] py-7 marquee overflow-hidden shadow-md">
            <div class="marquee-track flex">
                <div class="flex items-center gap-10 px-5 text-white text-2xl md:text-3xl font-bold">
                    <span>MAKING AGRITECH SUSTAINABLE</span>
                    <span class="text-white/40">✦</span>
                    <span>RESEARCH & INNOVATION</span>
                    <span class="text-white/40">✦</span>
                    <span>AGRICULTURE 4.0</span>
                    <span class="text-white/40">✦</span>
                </div>
                <div class="flex items-center gap-10 px-5 text-white text-2xl md:text-3xl font-bold">
                    <span>MAKING AGRITECH SUSTAINABLE</span>
                    <span class="text-white/40">✦</span>
                    <span>RESEARCH & INNOVATION</span>
                    <span class="text-white/40">✦</span>
                    <span>AGRICULTURE 4.0</span>
                </div>
            </div>
        </section>

        <!-- =========================================================
             CTA
========================================================= -->
        <section id="contacto" class="py-28 bg-[#173B27] text-white text-center">
            <div class="max-w-5xl mx-auto px-6 reveal space-y-6">
                <span class="text-[#9BD44B] uppercase tracking-[3px] text-sm font-bold">Investigación y desarrollo</span>
                <h2 class="text-4xl md:text-6xl font-bold leading-tight">¿Quieres desarrollar el futuro de la agricultura?</h2>
                <p class="mt-6 text-lg md:text-xl text-white/60 leading-8 max-w-2xl mx-auto">
                    Conectamos empresas, centros de investigación y actores del sector agroalimentario para crear nuevas soluciones digitales.
                </p>

                <div class="mt-9 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('soluciones.contactos') }}" class="bg-[#78B82A] hover:bg-[#8ACA38] text-white px-8 py-4 rounded-full font-semibold transition shadow-md">
                        Contacta con nosotros
                    </a>
                    <a href="{{ route('register') }}" class="border border-white/30 hover:bg-white hover:text-[#173B27] px-8 py-4 rounded-full font-semibold transition">
                        Reserva una demo
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

<x-presentacion-guest>
    <div class="text-[#173B27] bg-white font-sans">

        <style>
            .hero-grid {
                background-image:
                    linear-gradient(rgba(23,59,39,.045) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(23,59,39,.045) 1px, transparent 1px);
                background-size: 48px 48px;
            }
            .image-hover {
                overflow: hidden;
            }
            .image-hover img {
                transition: transform .7s ease;
            }
            .image-hover:hover img {
                transform: scale(1.05);
            }
            .technology-card {
                transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
            }
            .technology-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 25px 55px rgba(23,59,39,.11);
                border-color: rgba(120,184,42,.45);
            }
            .technology-card:hover .tech-icon {
                background: #78B82A;
                color: white;
            }
            .tech-icon {
                transition: background .3s ease, color .3s ease;
            }
            .reveal {
                opacity: 0;
                transform: translateY(28px);
                transition: opacity .7s ease, transform .7s ease;
            }
            .reveal.show {
                opacity: 1;
                transform: translateY(0);
            }
            .floating {
                animation: floating 5s ease-in-out infinite;
            }
            @keyframes floating {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-12px); }
            }
            .marquee {
                overflow: hidden;
                white-space: nowrap;
            }
            .marquee-track {
                display: inline-flex;
                animation: marquee 28s linear infinite;
            }
            @keyframes marquee {
                from { transform: translateX(0); }
                to { transform: translateX(-50%); }
            }
        </style>

        <!-- =========================================================
             HERO
        ========================================================= -->
        <section class="pt-24 bg-[#F1F7E9] hero-grid overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div class="grid lg:grid-cols-2 gap-14 min-h-[620px] items-center py-16">
                    <!-- TEXT -->
                    <div class="py-12 reveal space-y-7">
                        <div class="inline-flex items-center gap-2 bg-white rounded-full px-4 py-2 shadow-sm border border-gray-100">
                            <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-4 h-4 object-contain inline-block mr-1 align-middle">
                            <span class="text-xs uppercase tracking-[2px] font-bold text-[#173B27]">Tecnologías</span>
                        </div>

                        <h1 class="text-5xl md:text-6xl lg:text-[70px] leading-[.98] tracking-[-3px] font-bold text-[#173B27]">
                            Tecnología para una <span class="text-[#78B82A]">agricultura</span> más inteligente
                        </h1>

                        <p class="max-w-xl text-lg md:text-xl text-[#5E665F] leading-8">
                            <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> reúne tecnologías innovadoras para ayudar a agricultores y técnicos a gestionar sus explotaciones de forma eficiente y sostenible.
                        </p>

                        <div class="pt-4 flex flex-wrap gap-4">
                            <a href="#tecnologias" class="bg-[#173B27] hover:bg-[#245337] text-white px-7 py-4 rounded-full font-semibold transition shadow-md">
                                Descubre las tecnologías
                            </a>
                            <a href="#contacto" class="border border-[#173B27] hover:bg-[#173B27] hover:text-white px-7 py-4 rounded-full font-semibold transition">
                                Reserva una demo
                            </a>
                        </div>
                    </div>

                    <!-- IMAGE -->
                    <div class="relative min-h-[540px] flex items-center justify-center reveal">
                        <div class="absolute w-[470px] h-[470px] rounded-full bg-[#DCEBC9] right-[-80px] bottom-0 pointer-events-none"></div>
                        <div class="absolute w-36 h-36 rounded-full border-[18px] border-[#78B82A]/20 left-0 top-20 pointer-events-none"></div>

                        <div class="relative z-10 w-full max-w-[570px] image-hover">
                            <img src="https://images.unsplash.com/photo-1530507629858-e4977d30e9e0?auto=format&fit=crop&w=1200&q=85" alt="Tecnología agrícola" class="w-full h-[510px] object-cover rounded-t-[190px] rounded-b-[35px] shadow-2xl">

                            <!-- FLOAT CARD -->
                            <div class="floating absolute left-6 bottom-6 bg-white rounded-[22px] px-6 py-5 shadow-xl border border-gray-100">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-[#F1F7E9] rounded-full flex items-center justify-center">
                                        <svg width="25" height="25" fill="none" stroke="#78B82A" stroke-width="1.7" viewBox="0 0 24 24">
                                            <path d="M12 3v18M3 12h18"/>
                                            <circle cx="12" cy="12" r="7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#173B27]">6 tecnologías</p>
                                        <p class="text-sm text-[#5E665F]">Una única plataforma</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =========================================================
             INTRO
        ========================================================= -->
        <section class="py-24 bg-white">
            <div class="max-w-4xl mx-auto px-6 text-center reveal space-y-6">
                <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold">Innovación agrícola</span>
                <h2 class="text-4xl md:text-5xl font-bold leading-tight text-[#173B27]">
                    Las tecnologías que hacen posible la agricultura de precisión
                </h2>
                <p class="text-lg md:text-xl text-[#5E665F] leading-8">
                    Desde el mapeo de parcelas hasta los sensores, las imágenes satelitales y los sistemas de apoyo a la decisión, <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> conecta diferentes fuentes de información para transformar los datos en conocimiento útil para el campo.
                </p>
            </div>
        </section>

        <!-- =========================================================
             TECHNOLOGIES
        ========================================================= -->
        <section id="tecnologias" class="py-24 bg-[#F7F9F5]">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 space-y-14">
                <div class="max-w-3xl reveal space-y-4">
                    <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold">Tecnologías innovadoras</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-[#173B27]">Todo a tu disposición en una única plataforma</h2>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- 01 -->
                    <article class="technology-card bg-white rounded-[30px] p-8 border border-gray-100 reveal space-y-6 shadow-sm">
                        <div class="tech-icon w-16 h-16 rounded-2xl bg-[#F1F7E9] text-[#78B82A] flex items-center justify-center">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path d="M4 5h16v14H4z"/><path d="M8 9h8M8 13h5M8 17h3"/></svg>
                        </div>
                        <div class="text-xs font-bold tracking-[2px] text-[#78B82A]">01</div>
                        <h3 class="text-2xl font-bold text-[#173B27]">Mapeo de campo</h3>
                        <p class="text-[#5E665F] leading-7">Los sistemas GIS permiten mapear parcelas y georreferenciar toda la información relacionada con los cultivos.</p>
                        <a href="{{ route('soluciones.agronomica') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#173B27] hover:text-[#78B82A]">
                            Descubrir tecnología <span>→</span>
                        </a>
                    </article>

                    <!-- 02 -->
                    <article class="technology-card bg-white rounded-[30px] p-8 border border-gray-100 reveal space-y-6 shadow-sm">
                        <div class="tech-icon w-16 h-16 rounded-2xl bg-[#F1F7E9] text-[#78B82A] flex items-center justify-center">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="M4 12h16M12 4c2 2 3 5 3 8s-1 6-3 8c-2-2-3-5-3-8s1-6 3-8Z"/></svg>
                        </div>
                        <div class="text-xs font-bold tracking-[2px] text-[#78B82A]">02</div>
                        <h3 class="text-2xl font-bold text-[#173B27]">Imágenes de satélite</h3>
                        <p class="text-[#5E665F] leading-7">Las imágenes satelitales permiten realizar un monitoreo remoto eficaz de los cultivos y detectar variaciones dentro de las parcelas.</p>
                        <a href="{{ route('soluciones.satelital') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#173B27] hover:text-[#78B82A]">
                            Descubrir tecnología <span>→</span>
                        </a>
                    </article>

                    <!-- 03 -->
                    <article class="technology-card bg-white rounded-[30px] p-8 border border-gray-100 reveal space-y-6 shadow-sm">
                        <div class="tech-icon w-16 h-16 rounded-2xl bg-[#F1F7E9] text-[#78B82A] flex items-center justify-center">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path d="M4 18h16"/><path d="M6 15l4-5 3 3 5-7"/><circle cx="6" cy="15" r="1"/><circle cx="10" cy="10" r="1"/><circle cx="13" cy="13" r="1"/><circle cx="18" cy="6" r="1"/></svg>
                        </div>
                        <div class="text-xs font-bold tracking-[2px] text-[#78B82A]">03</div>
                        <h3 class="text-2xl font-bold text-[#173B27]">Modelos de predicción</h3>
                        <p class="text-[#5E665F] leading-7">Modelos de predicción para fenología, riego, fertilización y defensa que ayudan a anticipar problemas y planificar actuaciones.</p>
                        <a href="{{ route('soluciones.defensa') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#173B27] hover:text-[#78B82A]">
                            Descubrir tecnología <span>→</span>
                        </a>
                    </article>

                    <!-- 04 -->
                    <article class="technology-card bg-white rounded-[30px] p-8 border border-gray-100 reveal space-y-6 shadow-sm">
                        <div class="tech-icon w-16 h-16 rounded-2xl bg-[#F1F7E9] text-[#78B82A] flex items-center justify-center">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/><path d="M4 4l3 3M20 4l-3 3"/></svg>
                        </div>
                        <div class="text-xs font-bold tracking-[2px] text-[#78B82A]">04</div>
                        <h3 class="text-2xl font-bold text-[#173B27]">DSS</h3>
                        <p class="text-[#5E665F] leading-7">Los Sistemas de Soporte a las Decisiones procesan datos y ofrecen información útil para ayudar a decidir las acciones en campo.</p>
                        <a href="{{ route('soluciones.agronomica') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#173B27] hover:text-[#78B82A]">
                            Descubrir tecnología <span>→</span>
                        </a>
                    </article>

                    <!-- 05 -->
                    <article class="technology-card bg-white rounded-[30px] p-8 border border-gray-100 reveal space-y-6 shadow-sm">
                        <div class="tech-icon w-16 h-16 rounded-2xl bg-[#F1F7E9] text-[#78B82A] flex items-center justify-center">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><rect x="5" y="5" width="14" height="14" rx="3"/><path d="M9 9h6v6H9z"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/></svg>
                        </div>
                        <div class="text-xs font-bold tracking-[2px] text-[#78B82A]">05</div>
                        <h3 class="text-2xl font-bold text-[#173B27]">Sensores</h3>
                        <p class="text-[#5E665F] leading-7">Los sensores agrícolas permiten recoger datos fundamentales sobre el estado de las plantas y las condiciones del campo.</p>
                        <a href="{{ route('soluciones.agrometeo') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#173B27] hover:text-[#78B82A]">
                            Descubrir tecnología <span>→</span>
                        </a>
                    </article>

                    <!-- 06 -->
                    <article class="technology-card bg-white rounded-[30px] p-8 border border-gray-100 reveal space-y-6 shadow-sm">
                        <div class="tech-icon w-16 h-16 rounded-2xl bg-[#F1F7E9] text-[#78B82A] flex items-center justify-center">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path d="M4 18V9l4-3 4 3v9"/><path d="M12 18v-7l4-3 4 3v7"/><path d="M2 18h20"/></svg>
                        </div>
                        <div class="text-xs font-bold tracking-[2px] text-[#78B82A]">06</div>
                        <h3 class="text-2xl font-bold text-[#173B27]">Agricultura de precisión</h3>
                        <p class="text-[#5E665F] leading-7">Herramientas innovadoras para realizar intervenciones agronómicas específicas, optimizando recursos y operaciones.</p>
                        <a href="{{ route('soluciones.todas') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#173B27] hover:text-[#78B82A]">
                            Descubrir tecnología <span>→</span>
                        </a>
                    </article>
                </div>
            </div>
        </section>

        <!-- =========================================================
             TECHNOLOGY FLOW
========================================================= -->
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <!-- IMAGE -->
                    <div class="relative reveal">
                        <div class="absolute w-full h-full bg-[#F1F7E9] rounded-[40px] rotate-[-3deg] pointer-events-none"></div>
                        <img src="https://images.unsplash.com/photo-1586771107445-d3ca888129ff?auto=format&fit=crop&w=1200&q=85" alt="Agricultura de precisión" class="relative w-full h-[560px] object-cover rounded-[40px] shadow-xl">

                        <!-- STAT -->
                        <div class="absolute right-[-15px] bottom-8 bg-[#173B27] text-white rounded-[25px] p-6 shadow-2xl">
                            <p class="text-4xl font-bold text-[#9BD44B]">6</p>
                            <p class="text-sm text-white/60 mt-1">tecnologías conectadas</p>
                        </div>
                    </div>

                    <!-- CONTENT -->
                    <div class="reveal space-y-6">
                        <span class="text-[#78B82A] uppercase tracking-[3px] text-sm font-bold">Datos conectados</span>
                        <h2 class="text-4xl md:text-5xl font-bold leading-tight text-[#173B27]">
                            Del dato bruto a una decisión agronómica
                        </h2>
                        <p class="text-lg text-[#5E665F] leading-8">
                            Las diferentes tecnologías trabajan de forma integrada para ofrecer una visión completa de lo que ocurre en cada parcela.
                        </p>

                        <!-- STEPS -->
                        <div class="mt-10 space-y-6">
                            <div class="flex gap-5 items-start">
                                <div class="flex-shrink-0 w-11 h-11 rounded-full bg-[#F1F7E9] text-[#78B82A] flex items-center justify-center font-bold">01</div>
                                <div>
                                    <h3 class="font-bold text-lg text-[#173B27]">Recopilar</h3>
                                    <p class="mt-1 text-sm text-[#5E665F] leading-6">Capturamos información mediante satélites, sensores, GIS y observaciones.</p>
                                </div>
                            </div>

                            <div class="flex gap-5 items-start">
                                <div class="flex-shrink-0 w-11 h-11 rounded-full bg-[#F1F7E9] text-[#78B82A] flex items-center justify-center font-bold">02</div>
                                <div>
                                    <h3 class="font-bold text-lg text-[#173B27]">Analizar</h3>
                                    <p class="mt-1 text-sm text-[#5E665F] leading-6">Los modelos y algoritmos convierten los datos en información comprensible.</p>
                                </div>
                            </div>

                            <div class="flex gap-5 items-start">
                                <div class="flex-shrink-0 w-11 h-11 rounded-full bg-[#F1F7E9] text-[#78B82A] flex items-center justify-center font-bold">03</div>
                                <div>
                                    <h3 class="font-bold text-lg text-[#173B27]">Actuar</h3>
                                    <p class="mt-1 text-sm text-[#5E665F] leading-6">El agricultor y el técnico pueden utilizar esta información para planificar sus operaciones.</p>
                                </div>
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
                    <span>MAKING AGRITECH SUSTAINABLE</span>
                    <span class="text-white/40">✦</span>
                    <span>MAKING AGRITECH SUSTAINABLE</span>
                    <span class="text-white/40">✦</span>
                </div>
                <div class="flex items-center gap-10 px-5 text-white text-2xl md:text-3xl font-bold">
                    <span>MAKING AGRITECH SUSTAINABLE</span>
                    <span class="text-white/40">✦</span>
                    <span>MAKING AGRITECH SUSTAINABLE</span>
                    <span class="text-white/40">✦</span>
                    <span>MAKING AGRITECH SUSTAINABLE</span>
                </div>
            </div>
        </section>

        <!-- =========================================================
             CTA
========================================================= -->
        <section id="contacto" class="py-28 bg-[#173B27] text-white text-center">
            <div class="max-w-5xl mx-auto px-6 reveal space-y-6">
                <span class="text-[#9BD44B] uppercase tracking-[3px] text-sm font-bold"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span></span>
                <h2 class="text-4xl md:text-6xl font-bold leading-tight">La tecnología al servicio del trabajo en campo</h2>
                <p class="mt-6 text-lg md:text-xl text-white/60 leading-8 max-w-2xl mx-auto">
                    Descubre cómo las tecnologías de <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> pueden integrarse en tu gestión agrícola y ayudarte a trabajar con información más precisa.
                </p>
                <div class="mt-9 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('soluciones.contactos') }}" class="bg-[#78B82A] hover:bg-[#8ACA38] text-white px-8 py-4 rounded-full font-semibold transition shadow-md">
                        Reserva una demo
                    </a>
                    <a href="{{ route('soluciones.todas') }}" class="border border-white/30 hover:bg-white hover:text-[#173B27] px-8 py-4 rounded-full font-semibold transition">
                        Descubre las soluciones
                    </a>
                </div>
            </div>
        </section>

        <!-- =========================================================
             NEWSLETTER
========================================================= -->
        <section x-data="{ submitted: false, email: '' }" class="py-24 bg-[#F1F7E9]">
            <div class="max-w-4xl mx-auto px-6 text-center space-y-6">
                <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-12 h-12 object-contain mx-auto">

                <h2 class="text-4xl md:text-5xl font-bold text-[#173B27]">
                    ¿Quieres profundizar en la agricultura de precisión?
                </h2>

                <p class="text-lg text-[#5E665F]">
                    Mantente actualizado sobre tecnología, innovación y agricultura digital.
                </p>

                <form @submit.prevent="submitted = true" class="mt-8 max-w-xl mx-auto">
                    <div x-show="!submitted" class="flex flex-col sm:flex-row gap-3">
                        <input type="email" required x-model="email" placeholder="Tu correo electrónico" class="flex-1 px-6 py-4 rounded-full border border-gray-200 bg-white outline-none focus:border-[#78B82A]">
                        <button type="submit" class="bg-[#173B27] hover:bg-[#245337] text-white px-7 py-4 rounded-full font-semibold transition shadow-md">
                            Suscribirme
                        </button>
                    </div>

                    <div x-show="submitted" x-cloak class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="text-[#78B82A] text-3xl font-bold">✓</div>
                        <p class="mt-2 font-semibold text-[#173B27]">¡Gracias por suscribirte!</p>
                    </div>
                </form>

                <label x-show="!submitted" class="mt-5 flex items-center justify-center gap-3 text-xs text-[#5E665F]">
                    <input type="checkbox" required class="accent-[#78B82A]">
                    <span>He leído y acepto la Política de Privacidad.</span>
                </label>
            </div>
        </section>

    </div>

    <script>
        const revealElements = document.querySelectorAll('.reveal');
        const revealObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: .12 });

        revealElements.forEach(element => revealObserver.observe(element));
    </script>
</x-presentacion-guest>

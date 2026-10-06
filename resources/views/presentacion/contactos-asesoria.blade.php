<x-presentacion-guest>
    <div class="text-[#263d20] bg-white font-sans" x-data="{ selected: null, submitted: false }">

        <style>
            .container-agri {
                width: min(1180px, calc(100% - 40px));
                margin: 0 auto;
            }
            .hero-bg {
                background: radial-gradient(circle at 85% 15%, rgba(120,169,74,.14), transparent 30%), linear-gradient(180deg, #f4f8ef 0%, #ffffff 100%);
            }
            .contact-card {
                transition: transform .3s ease, box-shadow .3s ease;
            }
            .contact-card:hover {
                transform: translateY(-7px);
                box-shadow: 0 25px 60px rgba(38,61,32,.12);
            }
            .green-banner {
                background: linear-gradient(rgba(38,61,32,.72), rgba(38,61,32,.72)), url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1800&q=85');
                background-size: cover;
                background-position: center;
            }
            .contact-image {
                background: linear-gradient(rgba(38,61,32,.05), rgba(38,61,32,.05)), url('https://images.unsplash.com/photo-1523742810-5a0a9b3b5a9e?auto=format&fit=crop&w=1500&q=85');
                background-size: cover;
                background-position: center;
            }
            .soft-shadow {
                box-shadow: 0 20px 60px rgba(38,61,32,.09);
            }
        </style>

        <!-- ======================================================
             HERO
        ====================================================== -->
        <section class="hero-bg pt-20 pb-12">
            <div class="container-agri">
                <div class="min-h-[400px] flex items-center justify-center text-center py-16">
                    <div class="max-w-4xl mx-auto space-y-6">
                        <div class="inline-flex items-center gap-2 bg-[#e8f1dc] text-[#4d762d] px-4 py-2 rounded-full text-sm font-bold">
                            <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-4 h-4 object-contain inline-block mr-1 align-middle">
                            Estamos aquí para ayudarte
                        </div>

                        <h1 class="text-5xl md:text-6xl lg:text-[70px] leading-[.95] font-black tracking-tight text-[#263d20]">
                            ¿Qué <span class="text-[#78a94a]">necesitas?</span>
                        </h1>

                        <p class="max-w-2xl mx-auto text-lg md:text-xl leading-8 text-gray-600">
                            Selecciona el área sobre la que quieres información y nuestro equipo se pondrá en contacto contigo.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======================================================
             CONTACT CARDS
        ====================================================== -->
        <section id="contacto" class="py-20 bg-white">
            <div class="container-agri">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- 1. PRODUCT -->
                    <article class="contact-card bg-white rounded-[30px] overflow-hidden border border-gray-100 soft-shadow">
                        <div class="h-52 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1586771107445-d3ca888129ce?auto=format&fit=crop&w=900&q=85" alt="Producto" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                        </div>
                        <div class="p-7 space-y-4">
                            <div class="w-12 h-12 rounded-xl bg-[#e8f1dc] flex items-center justify-center text-2xl">📱</div>
                            <h2 class="text-2xl font-black text-[#263d20]">Tengo una pregunta acerca del producto</h2>
                            <p class="text-gray-500 text-sm leading-6">¿Quieres conocer mejor las soluciones de <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span>?</p>
                            <button @click="selected = 'producto'" class="w-full bg-[#78a94a] hover:bg-[#619037] text-white py-3.5 rounded-full font-bold transition">
                                ESCRÍBENOS
                            </button>
                        </div>
                    </article>

                    <!-- 2. ACADEMY -->
                    <article class="contact-card bg-white rounded-[30px] overflow-hidden border border-gray-100 soft-shadow">
                        <div class="h-52 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=900&q=85" alt="Academy" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                        </div>
                        <div class="p-7 space-y-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl">🎓</div>
                            <h2 class="text-2xl font-black text-[#263d20]">Quiero información sobre <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> Academy</h2>
                            <p class="text-gray-500 text-sm leading-6">Descubre nuestros cursos y programas de formación.</p>
                            <button @click="selected = 'academy'" class="w-full bg-[#78a94a] hover:bg-[#619037] text-white py-3.5 rounded-full font-bold transition">
                                ESCRÍBENOS
                            </button>
                        </div>
                    </article>

                    <!-- 3. PARTNER -->
                    <article class="contact-card bg-white rounded-[30px] overflow-hidden border border-gray-100 soft-shadow">
                        <div class="h-52 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=900&q=85" alt="Partner" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                        </div>
                        <div class="p-7 space-y-4">
                            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-2xl">🤝</div>
                            <h2 class="text-2xl font-black text-[#263d20]">Me gustaría ser Socio de <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span></h2>
                            <p class="text-gray-500 text-sm leading-6">Conoce nuestro programa de Partners y colabora con nosotros.</p>
                            <button @click="selected = 'partner'" class="w-full bg-[#78a94a] hover:bg-[#619037] text-white py-3.5 rounded-full font-bold transition">
                                ESCRÍBENOS
                            </button>
                        </div>
                    </article>

                    <!-- 4. DISTRIBUTOR -->
                    <article class="contact-card bg-white rounded-[30px] overflow-hidden border border-gray-100 soft-shadow">
                        <div class="h-52 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1586528116493-da8b0f7c6d9e?auto=format&fit=crop&w=900&q=85" alt="Distribuidor" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                        </div>
                        <div class="p-7 space-y-4">
                            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-2xl">🌍</div>
                            <h2 class="text-2xl font-black text-[#263d20]">Me gustaría ser distribuidor</h2>
                            <p class="text-gray-500 text-sm leading-6">Descubre las oportunidades del programa de distribución.</p>
                            <button @click="selected = 'distribuidor'" class="w-full bg-[#78a94a] hover:bg-[#619037] text-white py-3.5 rounded-full font-bold transition">
                                ESCRÍBENOS
                            </button>
                        </div>
                    </article>

                    <!-- 5. SUPPORT -->
                    <article class="contact-card bg-white rounded-[30px] overflow-hidden border border-gray-100 soft-shadow">
                        <div class="h-52 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=900&q=85" alt="Soporte" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                        </div>
                        <div class="p-7 space-y-4">
                            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-2xl">🛠️</div>
                            <h2 class="text-2xl font-black text-[#263d20]">Estoy usando <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> y necesito soporte</h2>
                            <p class="text-gray-500 text-sm leading-6">Nuestro equipo está disponible para ayudarte con la plataforma.</p>
                            <button @click="selected = 'soporte'" class="w-full bg-[#78a94a] hover:bg-[#619037] text-white py-3.5 rounded-full font-bold transition">
                                ESCRÍBENOS
                            </button>
                        </div>
                    </article>

                    <!-- 6. PRESS -->
                    <article class="contact-card bg-white rounded-[30px] overflow-hidden border border-gray-100 soft-shadow">
                        <div class="h-52 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=900&q=85" alt="Prensa" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                        </div>
                        <div class="p-7 space-y-4">
                            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-2xl">📰</div>
                            <h2 class="text-2xl font-black text-[#263d20]">Oficina de prensa</h2>
                            <p class="text-gray-500 text-sm leading-6">Para entrevistas, notas de prensa y solicitudes de comunicación.</p>
                            <button @click="selected = 'prensa'" class="w-full bg-[#78a94a] hover:bg-[#619037] text-white py-3.5 rounded-full font-bold transition">
                                ESCRÍBENOS
                            </button>
                        </div>
                    </article>

                </div>
            </div>
        </section>

        <!-- ======================================================
             MODAL CONTACT FORM
        ====================================================== -->
        <div x-show="selected" x-cloak x-transition.opacity
             class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-5"
             @keydown.escape.window="selected = null">
            <div @click.outside="selected = null" x-transition class="bg-white rounded-[30px] max-w-xl w-full p-8 md:p-10 shadow-2xl">
                <div class="flex items-start justify-between gap-5">
                    <div>
                        <div class="text-sm uppercase tracking-[.2em] text-[#78a94a] font-bold">Contacto</div>
                        <h2 class="mt-2 text-3xl font-black text-[#263d20]">
                            <span x-show="selected === 'producto'">Información del producto</span>
                            <span x-show="selected === 'academy'"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> Academy</span>
                            <span x-show="selected === 'partner'">Programa Partner</span>
                            <span x-show="selected === 'distribuidor'">Programa Distribuidor</span>
                            <span x-show="selected === 'soporte'">Soporte</span>
                            <span x-show="selected === 'prensa'">Oficina de prensa</span>
                        </h2>
                    </div>
                    <button @click="selected = null" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500">✕</button>
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="submitted = true; setTimeout(() => { selected = null; submitted = false; }, 1800)">
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold mb-2">Nombre</label>
                            <input type="text" required placeholder="Tu nombre" class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a]">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Apellidos</label>
                            <input type="text" required placeholder="Tus apellidos" class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Email</label>
                        <input type="email" required placeholder="tu@email.com" class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a]">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Mensaje</label>
                        <textarea rows="4" required placeholder="¿En qué podemos ayudarte?" class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a] resize-none"></textarea>
                    </div>

                    <label class="flex gap-3 text-sm text-gray-500">
                        <input type="checkbox" required class="mt-1 accent-[#78a94a]">
                        <span>He leído y acepto la Política de Privacidad.</span>
                    </label>

                    <button type="submit" class="mt-6 w-full bg-[#78a94a] hover:bg-[#619037] text-white py-4 rounded-full font-black transition">
                        <span x-show="!submitted">ENVIAR MENSAJE</span>
                        <span x-show="submitted" x-cloak>✓ MENSAJE ENVIADO</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- ======================================================
             GREEN BANNER
====================================================== -->
        <section class="green-banner py-28">
            <div class="container-agri text-center text-white space-y-6">
                <div class="uppercase tracking-[.4em] text-sm font-bold text-green-200"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span></div>
                <h2 class="text-4xl md:text-6xl font-black">MAKING AGRITECH SUSTAINABLE</h2>
            </div>
        </section>

        <!-- ======================================================
             INFO / R&D
====================================================== -->
        <section class="py-24 bg-white">
            <div class="container-agri">
                <div class="grid lg:grid-cols-2 gap-14 items-center">
                    <div class="space-y-6">
                        <span class="text-[#78a94a] uppercase tracking-[.25em] text-sm font-bold"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span></span>
                        <h2 class="text-4xl md:text-5xl font-black text-[#263d20]">Investigación y desarrollo</h2>
                        <p class="text-lg text-gray-600 leading-8">
                            Trabajamos en tecnologías capaces de mejorar la productividad, sostenibilidad y eficiencia del sector agrícola.
                        </p>
                        <a href="{{ route('soluciones.investigacion') }}" class="inline-flex border border-[#78a94a] text-[#4d762d] px-7 py-4 rounded-full font-bold hover:bg-[#f4f8ef] transition">
                            DESCUBRE MÁS
                        </a>
                    </div>
                    <div class="contact-image h-[430px] rounded-[40px] overflow-hidden shadow-xl"></div>
                </div>
            </div>
        </section>

        <!-- ======================================================
             NEWSLETTER
====================================================== -->
        <section class="py-24 bg-[#263d20] text-white" x-data="{ submittedNl: false }">
            <div class="container-agri">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="space-y-4">
                        <span class="text-[#78a94a] uppercase tracking-[.3em] text-xs font-bold">NEWSLETTER</span>
                        <h2 class="text-3xl md:text-5xl font-black leading-tight">¿Quieres profundizar en el mundo de la agricultura de precisión?</h2>
                        <p class="text-white/70 text-base leading-7">Mantente actualizado sobre tecnología, innovación y agricultura de precisión.</p>
                    </div>

                    <div class="bg-white rounded-[30px] p-8 md:p-10 text-[#263d20] shadow-2xl">
                        <form @submit.prevent="submittedNl = true" class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold mb-2">Tu nombre</label>
                                <input type="text" required placeholder="Nombre" class="w-full px-5 py-3.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a]">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Tu correo electrónico</label>
                                <input type="email" required placeholder="correo@email.com" class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a]">
                            </div>
                            <label class="flex gap-3 text-sm text-gray-500">
                                <input type="checkbox" required class="mt-1 accent-[#78a94a]">
                                <span>He leído y acepto la Política de Privacidad.</span>
                            </label>
                            <button type="submit" class="w-full bg-[#619037] hover:bg-[#4d762d] text-white py-4 rounded-full font-black tracking-wide transition shadow-md">
                                <span x-show="!submittedNl">SUSCRIBIRME</span>
                                <span x-show="submittedNl" x-cloak>✓ SUSCRITO CORRECTAMENTE</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </div>
</x-presentacion-guest>

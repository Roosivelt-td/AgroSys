<x-presentacion-guest>
    <div class="text-[#263c1d] bg-white font-sans" x-data="{ newsletterSent: false }">

        <style>
            .container-custom {
                width: min(1180px, calc(100% - 40px));
                margin: 0 auto;
            }
            .hero-grid {
                background: radial-gradient(circle at 80% 20%, rgba(124,171,67,.14), transparent 30%), linear-gradient(180deg, #f5f8ef 0%, #ffffff 100%);
            }
            .green-image {
                background: linear-gradient(rgba(38,60,29,.68), rgba(38,60,29,.68)), url('https://images.unsplash.com/photo-1492496913980-501348b61469?auto=format&fit=crop&w=1800&q=85');
                background-size: cover;
                background-position: center;
            }
            .academy-card {
                box-shadow: 0 20px 60px rgba(40, 65, 25, .10);
            }
            .soft-shadow {
                box-shadow: 0 15px 45px rgba(42, 70, 30, .08);
            }
            .btn {
                transition: all .25s ease;
            }
            .btn:hover {
                transform: translateY(-2px);
            }
        </style>

        <!-- =====================================================
             HERO / ACADEMY
        ====================================================== -->
        <section class="hero-grid pt-12 overflow-hidden">
            <div class="container-custom">
                <div class="grid lg:grid-cols-2 gap-12 items-center min-h-[650px] py-20">
                    <!-- Text -->
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 bg-[#e8f2dc] text-[#507629] px-4 py-2 rounded-full text-sm font-semibold">
                            <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-6 h-6 object-contain inline-block mr-1 align-middle">
                            <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> Academy
                        </div>
                        <h1 class="text-5xl md:text-6xl lg:text-[68px] leading-[.98] font-extrabold tracking-tight text-[#263c1d]">Nuestra <span class="text-[#638f32]">Academy</span></h1>
                        <p class="text-lg leading-8 text-gray-600 max-w-xl">La difusión de tecnologías innovadoras para la agricultura requiere la formación de profesionales del sector.</p>
                        <p class="text-lg leading-8 text-gray-600 max-w-xl">Aprende a utilizar nuevas herramientas digitales e interpretar los datos para optimizar la producción agrícola y reducir los residuos.</p>
                        <div class="pt-4 flex flex-wrap gap-4">
                            <a href="#curso" class="btn bg-[#638f32] hover:bg-[#507629] text-white px-7 py-4 rounded-full font-bold shadow-md">Quiero saber más</a>
                            <a href="#dedicado" class="px-7 py-4 rounded-full border border-[#638f32] text-[#507629] font-bold hover:bg-[#f3f8ed] transition">Ver programa</a>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="relative">
                        <div class="absolute -top-8 -right-5 w-32 h-32 bg-[#e8f2dc] rounded-full"></div>
                        <div class="absolute -bottom-8 -left-5 w-40 h-40 bg-[#d4e5bd] rounded-full opacity-60"></div>
                        <div class="relative overflow-hidden rounded-[45px] rounded-bl-[110px] shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1530507629858-e4977d30e9e0?auto=format&fit=crop&w=1200&q=85" class="w-full h-[540px] object-cover" alt="Academy">
                            <!-- Floating Card -->
                            <div class="absolute bottom-7 left-7 right-7 bg-white/95 backdrop-blur rounded-2xl p-5 shadow-xl">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-[#e8f1dc] flex items-center justify-center text-2xl">🌱</div>
                                    <div>
                                        <div class="font-bold text-[#263c1d]">Agricultura 4.0</div>
                                        <div class="text-sm text-gray-500">Formación profesional</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
             INTRO CARD
        ====================================================== -->
        <section class="py-24 bg-white">
            <div class="container-custom">
                <div class="academy-card bg-[#263c1d] text-white rounded-[40px] overflow-hidden">
                    <div class="grid lg:grid-cols-2 items-center">
                        <div class="p-10 md:p-16 lg:p-20 space-y-6">
                            <span class="text-[#b8d497] uppercase tracking-[.25em] text-xs font-bold">Professional Academy</span>
                            <h2 class="text-4xl md:text-5xl font-extrabold leading-tight">Incrementa tus habilidades en nuevas tecnologías</h2>
                            <p class="text-green-100 text-lg leading-8">Fórmate en herramientas digitales para agricultura y conviértete en un profesional preparado para los retos de la Agricultura 4.0.</p>
                            <a href="#curso" class="inline-flex bg-white text-[#263c1d] px-7 py-4 rounded-full font-bold hover:bg-[#f3f8ed] transition shadow-lg">Descubre el curso</a>
                        </div>
                        <div class="min-h-[350px]">
                            <img src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1200&q=85" class="w-full h-full object-cover" alt="Campo">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
             A QUIÉN ESTÁ DEDICADO
        ====================================================== -->
        <section id="dedicado" class="py-24 bg-[#f5f8ef]">
            <div class="container-custom">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <!-- Image -->
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1592982537447-6f2a6a0a6e6b?auto=format&fit=crop&w=1100&q=85" class="rounded-[40px] w-full h-[570px] object-cover shadow-xl" alt="Agrónomo">
                        <div class="absolute -bottom-8 -right-8 bg-white rounded-3xl p-7 shadow-xl max-w-[240px]">
                            <div class="text-4xl font-extrabold text-[#638f32]">4</div>
                            <div class="text-sm text-gray-600 mt-1">lecciones online</div>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="space-y-6">
                        <span class="text-[#638f32] font-bold uppercase tracking-[.22em] text-sm">Formación profesional</span>
                        <h2 class="text-4xl md:text-5xl font-extrabold text-[#263c1d]">A quién está <span class="text-[#638f32]">dedicado</span></h2>
                        <p class="text-gray-600 text-lg leading-8">El curso está dirigido a cualquier persona que quiera profundizar en sus conocimientos sobre herramientas digitales para la agricultura y formarse en su uso práctico.</p>
                        <div class="pt-4 grid sm:grid-cols-2 gap-4">
                            <div class="bg-white p-5 rounded-2xl flex items-center gap-4 soft-shadow"><span class="text-2xl">🌾</span><span class="font-semibold">Agrónomos</span></div>
                            <div class="bg-white p-5 rounded-2xl flex items-center gap-4 soft-shadow"><span class="text-2xl">🧑‍🌾</span><span class="font-semibold">Expertos agrícolas</span></div>
                            <div class="bg-white p-5 rounded-2xl flex items-center gap-4 soft-shadow"><span class="text-2xl">📐</span><span class="font-semibold">Técnicos agrícolas</span></div>
                            <div class="bg-white p-5 rounded-2xl flex items-center gap-4 soft-shadow"><span class="text-2xl">🚜</span><span class="font-semibold">Agricultores</span></div>
                            <div class="bg-white p-5 rounded-2xl flex items-center gap-4 soft-shadow sm:col-span-2"><span class="text-2xl">🎓</span><span class="font-semibold">Nuevos graduados</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
             BENEFITS / EL CURSO
        ====================================================== -->
        <section id="curso" class="py-24 bg-white">
            <div class="container-custom space-y-16">
                <div class="text-center max-w-3xl mx-auto space-y-4">
                    <span class="text-[#638f32] uppercase tracking-[.25em] font-bold text-sm">El curso</span>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-[#263c1d]">Todo lo que necesitas para empezar</h2>
                    <p class="text-gray-600 text-lg">Una formación online sencilla, práctica y completamente accesible.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-5">
                    <div class="bg-[#f5f8ef] p-7 rounded-3xl hover:-translate-y-2 transition-all shadow-sm">
                        <div class="text-3xl mb-5">💻</div>
                        <h3 class="font-bold text-[#263c1d]">Curso online</h3>
                        <p class="text-gray-500 text-sm mt-2">Duración: 4 lecciones</p>
                    </div>
                    <div class="bg-[#f5f8ef] p-7 rounded-3xl hover:-translate-y-2 transition-all shadow-sm">
                        <div class="text-3xl mb-5">🎁</div>
                        <h3 class="font-bold text-[#263c1d]">Gratis</h3>
                        <p class="text-gray-500 text-sm mt-2">Disponible para todos</p>
                    </div>
                    <div class="bg-[#f5f8ef] p-7 rounded-3xl hover:-translate-y-2 transition-all shadow-sm">
                        <div class="text-3xl mb-5">🎬</div>
                        <h3 class="font-bold text-[#263c1d]">Material educativo</h3>
                        <p class="text-gray-500 text-sm mt-2">Videos y material digital</p>
                    </div>
                    <div class="bg-[#f5f8ef] p-7 rounded-3xl hover:-translate-y-2 transition-all shadow-sm">
                        <div class="text-3xl mb-5">🏆</div>
                        <h3 class="font-bold text-[#263c1d]">Certificado</h3>
                        <p class="text-gray-500 text-sm mt-2">Emitido al finalizar</p>
                    </div>
                    <div class="bg-[#638f32] text-white p-7 rounded-3xl hover:-translate-y-2 transition-all shadow-lg">
                        <div class="text-3xl mb-5">🌱</div>
                        <h3 class="font-bold"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> Observa</h3>
                        <p class="text-green-100 text-sm mt-2">Gratis durante un año</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
             EDUCATIONAL ACADEMY
        ====================================================== -->
        <section class="py-24 bg-[#f5f8ef]">
            <div class="container-custom">
                <div class="grid lg:grid-cols-2 bg-white rounded-[45px] overflow-hidden soft-shadow items-center">
                    <div class="p-10 md:p-16 lg:p-20 space-y-6">
                        <div class="inline-flex px-4 py-2 bg-[#e8f2dc] text-[#507629] rounded-full text-sm font-bold">Para estudiantes</div>
                        <h2 class="text-4xl md:text-5xl font-extrabold text-[#263c1d] leading-tight">
                            <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> <span class="text-[#638f32]">Educational</span> Academy
                        </h2>
                        <p class="text-gray-600 text-lg leading-8">Preparamos a los profesionales del mañana para hacer frente a un mundo en constante cambio y afrontar los retos de las nuevas tecnologías en el ámbito agronómico.</p>
                        <p class="text-gray-600 leading-7">Cursos dirigidos a estudiantes de Universidades Agrarias y Forestales, Institutos Agrarios e Institutos Técnicos Superiores.</p>
                        <a href="{{ route('soluciones.contactos') }}" class="btn inline-flex bg-[#638f32] hover:bg-[#507629] text-white px-7 py-4 rounded-full font-bold shadow-md">Quiero saber más</a>
                    </div>
                    <div class="min-h-[480px]">
                        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=85" class="w-full h-full object-cover" alt="Estudiantes">
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
             GREEN BANNER (MAKING AGRITECH SUSTAINABLE)
        ====================================================== -->
        <section class="green-image py-28">
            <div class="container-custom text-center text-white space-y-4">
                <div class="uppercase tracking-[.4em] text-sm font-bold text-green-200">M A K I N G &nbsp; A G R I T E C H &nbsp; S U S T A I N A B L E</div>
                <h2 class="text-4xl md:text-6xl font-black">Making Agritech Sustainable</h2>
            </div>
        </section>

        <!-- =====================================================
             NEWSLETTER
        ====================================================== -->
        <section id="contacto" class="py-24 bg-[#263c1d] text-white" x-data="{ newsletterSent: false }">
            <div class="container-custom">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="space-y-6">
                        <span class="text-[#91b958] uppercase tracking-[.25em] text-sm font-bold">NEWSLETTER</span>
                        <h2 class="text-4xl md:text-5xl font-black leading-tight">¿Quieres profundizar en el mundo de la agricultura de precisión?</h2>
                        <p class="text-green-100 text-lg leading-8">Mantente actualizado sobre innovación, tecnología agrícola y agricultura de precisión.</p>
                    </div>
                    <form @submit.prevent="newsletterSent = true" class="bg-white rounded-[30px] p-8 text-gray-800 shadow-2xl">
                        <div x-show="!newsletterSent" class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold mb-2">Tu nombre</label>
                                <input type="text" required placeholder="Nombre" class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#7cab43]">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Tu correo electrónico</label>
                                <input type="email" required placeholder="correo@email.com" class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#7cab43]">
                            </div>
                            <label class="flex gap-3 text-sm text-gray-500">
                                <input type="checkbox" required class="mt-1 accent-green-600">
                                <span class="text-black">He leído y acepto la Política de Privacidad.</span>
                            </label>
                            <button type="submit" class="w-full mt-6 bg-[#638f32] hover:bg-[#507629] text-white py-4 rounded-full font-black transition shadow-md">
                                SUSCRIBIRME
                            </button>
                        </div>
                        <div x-show="newsletterSent" x-cloak class="text-center py-10 space-y-4">
                            <div class="w-16 h-16 mx-auto rounded-full bg-green-100 text-green-600 flex items-center justify-center text-3xl font-black">✓</div>
                            <h3 class="text-2xl font-black text-[#263c1d]">¡Gracias!</h3>
                            <p class="text-gray-500">Tu solicitud ha sido registrada.</p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</x-presentacion-guest>

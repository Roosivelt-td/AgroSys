<?php
$page_title = "Nuestra red | Agricolus";
$base_url = "../";
$current_page = "empresa";
$body_class = "bg-white";

$page_styles = <<<'CSS'
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

        .partner-card {
            transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
        }

        .partner-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 25px 55px rgba(23,59,39,.10);
            border-color: rgba(120,184,42,.45);
        }

        .testimonial {
            transition: opacity .35s ease, transform .35s ease;
        }

        .marquee {
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-track {
            display: inline-flex;
            animation: marquee 25s linear infinite;
        }

        @keyframes marquee {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        .reveal {
            opacity: 0;
            transform: translateY(25px);
            transition: opacity .7s ease, transform .7s ease;
        }

        .reveal.show {
            opacity: 1;
            transform: translateY(0);
        }
CSS;

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/header.php';
?>

<!-- =========================================================
     HERO
========================================================= -->

<section
    class="pt-[82px] bg-softgreen hero-grid overflow-hidden"
>

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div
            class="grid lg:grid-cols-2 min-h-[610px] items-center gap-14"
        >


            <!-- TEXT -->

            <div class="py-20 reveal">

                <div
                    class="inline-flex items-center gap-2 bg-white rounded-full px-4 py-2 shadow-sm"
                >

                    <span
                        class="w-2 h-2 rounded-full bg-agricolus"
                    ></span>

                    <span
                        class="text-xs uppercase tracking-[2px] font-bold text-darkgreen"
                    >
                        Nuestra red
                    </span>

                </div>


                <h1
                    class="mt-7 text-5xl md:text-6xl lg:text-[70px] leading-[.98] tracking-[-3px] font-bold text-darkgreen"
                >

                    Un ecosistema
                    <span class="text-agricolus">
                        internacional
                    </span>
                    para la agricultura

                </h1>


                <p
                    class="mt-7 max-w-xl text-lg md:text-xl text-textgray leading-8"
                >
                    Colaboramos con empresas, agrónomos, consultores,
                    distribuidores y organizaciones que comparten nuestra
                    visión de una agricultura más innovadora y sostenible.
                </p>


                <div class="mt-9 flex flex-wrap gap-4">

                    <a
                        href="#partners"
                        class="bg-darkgreen hover:bg-[#245337] text-white px-7 py-4 rounded-full font-semibold transition"
                    >
                        Descubre nuestra red
                    </a>

                    <a
                        href="#testimonios"
                        class="border border-darkgreen hover:bg-darkgreen hover:text-white text-darkgreen px-7 py-4 rounded-full font-semibold transition"
                    >
                        Ver testimonios
                    </a>

                </div>

            </div>


            <!-- VISUAL -->

            <div
                class="relative min-h-[540px] flex items-center justify-center reveal"
            >

                <!-- CIRCLES -->

                <div
                    class="absolute w-[460px] h-[460px] rounded-full bg-[#DCEBC9] right-[-80px] bottom-0"
                ></div>

                <div
                    class="absolute w-[150px] h-[150px] rounded-full border-[20px] border-agricolus/20 top-14 left-0"
                ></div>


                <!-- IMAGE -->

                <div
                    class="relative z-10 w-full max-w-[570px] image-hover"
                >

                    <img
                        src="https://images.unsplash.com/photo-1524666041070-9d87656c25bb?auto=format&fit=crop&w=1200&q=85"
                        alt="Red agrícola internacional"
                        class="w-full h-[500px] object-cover rounded-t-[190px] rounded-b-[35px] shadow-2xl"
                    >


                    <!-- FLOAT CARD -->

                    <div
                        class="absolute left-6 bottom-6 bg-white rounded-[22px] px-6 py-5 shadow-xl"
                    >

                        <div class="flex items-center gap-4">

                            <div
                                class="w-12 h-12 bg-softgreen rounded-full flex items-center justify-center"
                            >

                                <svg
                                    width="25"
                                    height="25"
                                    fill="none"
                                    stroke="#78B82A"
                                    stroke-width="1.7"
                                    viewBox="0 0 24 24"
                                >

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="9"
                                    />

                                    <path
                                        d="M3 12h18M12 3c3 3 4 6 4 9s-1 6-4 9c-3-3-4-6-4-9s1-6 4-9Z"
                                    />

                                </svg>

                            </div>


                            <div>

                                <p class="font-bold text-darkgreen">
                                    Ecosistema Agricolus
                                </p>

                                <p class="text-sm text-textgray">
                                    Partners internacionales
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     INTRO / PARTNER
========================================================= -->

<section
    id="partners"
    class="py-24 bg-white"
>

    <div class="max-w-7xl mx-auto px-6 lg:px-10">


        <div
            class="grid lg:grid-cols-[.85fr_1.15fr] gap-16 items-center"
        >


            <!-- LEFT -->

            <div class="reveal">

                <span
                    class="text-agricolus uppercase tracking-[3px] text-sm font-bold"
                >
                    Partner
                </span>


                <h2
                    class="mt-5 text-4xl md:text-5xl font-bold text-darkgreen leading-tight"
                >
                    Descubre nuestro
                    ecosistema internacional
                </h2>


                <p
                    class="mt-6 text-lg text-textgray leading-8"
                >
                    La red Agricolus está formada por profesionales y
                    organizaciones que trabajan junto a nosotros para
                    llevar la agricultura de precisión a diferentes
                    mercados y realidades agrícolas.
                </p>


                <p
                    class="mt-5 text-lg text-textgray leading-8"
                >
                    Nuestro programa de partners permite integrar
                    conocimientos agronómicos, tecnología y servicios
                    especializados para ofrecer soluciones completas
                    a los agricultores.
                </p>


                <a
                    href="<?= $base_url ?>contacto.php"
                    class="inline-flex mt-8 bg-agricolus hover:bg-agricolusDark text-white px-7 py-4 rounded-full font-semibold transition"
                >
                    Conviértete en partner
                </a>

            </div>


            <!-- RIGHT VISUAL -->

            <div class="relative reveal">

                <div
                    class="absolute inset-0 bg-softgreen rounded-[40px] rotate-2"
                ></div>

                <div
                    class="relative bg-darkgreen rounded-[35px] p-8 md:p-12 overflow-hidden"
                >

                    <!-- decorative -->

                    <div
                        class="absolute w-72 h-72 rounded-full border border-white/10 -right-24 -top-24"
                    ></div>

                    <div
                        class="absolute w-48 h-48 rounded-full border border-agricolus/20 -left-24 -bottom-24"
                    ></div>


                    <div class="relative z-10">


                        <div class="flex items-center gap-4">

                            <div
                                class="w-14 h-14 bg-agricolus rounded-2xl flex items-center justify-center"
                            >

                                <svg
                                    width="28"
                                    height="28"
                                    fill="none"
                                    stroke="white"
                                    stroke-width="1.7"
                                    viewBox="0 0 24 24"
                                >

                                    <circle cx="12" cy="12" r="3"/>

                                    <circle
                                        cx="5"
                                        cy="8"
                                        r="2"
                                    />

                                    <circle
                                        cx="19"
                                        cy="8"
                                        r="2"
                                    />

                                    <circle
                                        cx="5"
                                        cy="17"
                                        r="2"
                                    />

                                    <circle
                                        cx="19"
                                        cy="17"
                                        r="2"
                                    />

                                    <path
                                        d="M7 9l2 2M17 9l-2 2M7 16l2-3M17 16l-2-3"
                                    />

                                </svg>

                            </div>


                            <div>

                                <p class="text-white font-bold text-xl">
                                    Agricolus Partner Network
                                </p>

                                <p class="text-white/50">
                                    Tecnología + conocimiento
                                </p>

                            </div>

                        </div>


                        <!-- NETWORK -->

                        <div class="mt-12 grid grid-cols-3 gap-4">


                            <div
                                class="bg-white/10 rounded-2xl p-5 text-center"
                            >

                                <div
                                    class="text-3xl font-bold text-white"
                                >
                                    01
                                </div>

                                <div
                                    class="text-xs text-white/50 mt-2"
                                >
                                    Agrónomos
                                </div>

                            </div>


                            <div
                                class="bg-white/10 rounded-2xl p-5 text-center"
                            >

                                <div
                                    class="text-3xl font-bold text-white"
                                >
                                    02
                                </div>

                                <div
                                    class="text-xs text-white/50 mt-2"
                                >
                                    Consultores
                                </div>

                            </div>


                            <div
                                class="bg-white/10 rounded-2xl p-5 text-center"
                            >

                                <div
                                    class="text-3xl font-bold text-white"
                                >
                                    03
                                </div>

                                <div
                                    class="text-xs text-white/50 mt-2"
                                >
                                    Empresas
                                </div>

                            </div>

                        </div>


                        <div
                            class="mt-6 h-px bg-white/10"
                        ></div>


                        <p
                            class="mt-6 text-white/60 leading-7"
                        >
                            Una red construida para compartir experiencia,
                            innovación y soluciones digitales para el campo.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     PARTNER TYPES
========================================================= -->

<section class="py-24 bg-palegreen">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">


        <div class="text-center max-w-3xl mx-auto mb-14 reveal">

            <span
                class="text-agricolus uppercase tracking-[3px] text-sm font-bold"
            >
                La red
            </span>


            <h2
                class="mt-4 text-4xl md:text-5xl font-bold text-darkgreen"
            >
                Diferentes perfiles,
                una misma visión
            </h2>


            <p
                class="mt-5 text-lg text-textgray leading-8"
            >
                La colaboración permite combinar diferentes
                competencias para acompañar la transformación
                digital del sector agrícola.
            </p>

        </div>


        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">


            <!-- CARD -->

            <article
                class="partner-card bg-white rounded-[28px] p-8 border border-gray-100 reveal"
            >

                <div
                    class="w-14 h-14 bg-softgreen rounded-2xl flex items-center justify-center"
                >

                    <svg
                        width="28"
                        height="28"
                        fill="none"
                        stroke="#78B82A"
                        stroke-width="1.7"
                        viewBox="0 0 24 24"
                    >

                        <path
                            d="M12 20V10"
                        />

                        <path
                            d="M8 14c-2-2-3-4-3-7 3 0 6 1 7 4"
                        />

                        <path
                            d="M16 13c2-2 3-4 3-7-3 0-6 1-7 4"
                        />

                    </svg>

                </div>


                <h3
                    class="mt-7 text-xl font-bold text-darkgreen"
                >
                    Agrónomos
                </h3>


                <p
                    class="mt-3 text-textgray leading-7 text-sm"
                >
                    Profesionales que aportan conocimiento agronómico
                    y acompañan a las empresas agrícolas.
                </p>

            </article>



            <article
                class="partner-card bg-white rounded-[28px] p-8 border border-gray-100 reveal"
            >

                <div
                    class="w-14 h-14 bg-softgreen rounded-2xl flex items-center justify-center"
                >

                    <svg
                        width="28"
                        height="28"
                        fill="none"
                        stroke="#78B82A"
                        stroke-width="1.7"
                        viewBox="0 0 24 24"
                    >

                        <rect
                            x="3"
                            y="4"
                            width="18"
                            height="15"
                            rx="2"
                        />

                        <path
                            d="M7 8h10M7 12h6M7 16h4"
                        />

                    </svg>

                </div>


                <h3
                    class="mt-7 text-xl font-bold text-darkgreen"
                >
                    Consultores
                </h3>


                <p
                    class="mt-3 text-textgray leading-7 text-sm"
                >
                    Especialistas que integran las herramientas
                    digitales dentro de sus servicios profesionales.
                </p>

            </article>



            <article
                class="partner-card bg-white rounded-[28px] p-8 border border-gray-100 reveal"
            >

                <div
                    class="w-14 h-14 bg-softgreen rounded-2xl flex items-center justify-center"
                >

                    <svg
                        width="28"
                        height="28"
                        fill="none"
                        stroke="#78B82A"
                        stroke-width="1.7"
                        viewBox="0 0 24 24"
                    >

                        <rect
                            x="3"
                            y="5"
                            width="18"
                            height="14"
                            rx="2"
                        />

                        <path
                            d="M7 9h10M7 13h4"
                        />

                    </svg>

                </div>


                <h3
                    class="mt-7 text-xl font-bold text-darkgreen"
                >
                    Empresas
                </h3>


                <p
                    class="mt-3 text-textgray leading-7 text-sm"
                >
                    Organizaciones que complementan sus productos
                    y servicios con tecnología agrícola.
                </p>

            </article>



            <article
                class="partner-card bg-white rounded-[28px] p-8 border border-gray-100 reveal"
            >

                <div
                    class="w-14 h-14 bg-softgreen rounded-2xl flex items-center justify-center"
                >

                    <svg
                        width="28"
                        height="28"
                        fill="none"
                        stroke="#78B82A"
                        stroke-width="1.7"
                        viewBox="0 0 24 24"
                    >

                        <circle
                            cx="12"
                            cy="12"
                            r="8"
                        />

                        <path
                            d="M4 12h16M12 4c2 2.5 3 5 3 8s-1 5.5-3 8c-2-2.5-3-5-3-8s1-5.5 3-8Z"
                        />

                    </svg>

                </div>


                <h3
                    class="mt-7 text-xl font-bold text-darkgreen"
                >
                    Distribuidores
                </h3>


                <p
                    class="mt-3 text-textgray leading-7 text-sm"
                >
                    Partners que ayudan a acercar Agricolus a
                    nuevos territorios y mercados.
                </p>

            </article>

        </div>

    </div>

</section>



<!-- =========================================================
     TESTIMONIALS
========================================================= -->

<section
    id="testimonios"
    x-data="testimonialSlider()"
    class="py-24 bg-white"
>

    <div class="max-w-6xl mx-auto px-6 lg:px-10">


        <div class="text-center reveal">

            <span
                class="text-agricolus uppercase tracking-[3px] text-sm font-bold"
            >
                Voces de nuestra red
            </span>


            <h2
                class="mt-4 text-4xl md:text-5xl font-bold text-darkgreen"
            >
                Lo dicen nuestros partners
            </h2>

        </div>


        <!-- TESTIMONIAL -->

        <div
            class="mt-14 relative"
        >

            <div
                class="bg-softgreen rounded-[40px] p-8 md:p-16 text-center min-h-[380px] flex flex-col justify-center"
            >


                <!-- QUOTE -->

                <div
                    class="text-agricolus text-6xl font-serif leading-none"
                >
                    “
                </div>


                <div
                    x-text="quotes[current].text"
                    class="testimonial mt-5 text-2xl md:text-3xl leading-[1.5] font-medium text-darkgreen max-w-4xl mx-auto"
                ></div>


                <div class="mt-9">

                    <div
                        x-text="quotes[current].name"
                        class="font-bold text-darkgreen"
                    ></div>

                    <div
                        x-text="quotes[current].company"
                        class="mt-1 text-agricolus text-sm font-semibold"
                    ></div>

                </div>

            </div>


            <!-- ARROWS -->

            <button
                @click="previous()"
                class="absolute left-3 md:left-[-25px] top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white shadow-lg border border-gray-100 flex items-center justify-center text-darkgreen hover:bg-agricolus hover:text-white transition"
            >

                <svg
                    width="20"
                    height="20"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        d="m15 18-6-6 6-6"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                </svg>

            </button>


            <button
                @click="next()"
                class="absolute right-3 md:right-[-25px] top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white shadow-lg border border-gray-100 flex items-center justify-center text-darkgreen hover:bg-agricolus hover:text-white transition"
            >

                <svg
                    width="20"
                    height="20"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        d="m9 18 6-6-6-6"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                </svg>

            </button>

        </div>


        <!-- DOTS -->

        <div class="flex justify-center gap-2 mt-8">

            <template
                x-for="(quote,index) in quotes"
                :key="index"
            >

                <button
                    @click="current = index"
                    class="h-2 rounded-full transition-all"
                    :class="current === index
                        ? 'w-8 bg-agricolus'
                        : 'w-2 bg-gray-300'"
                ></button>

            </template>

        </div>

    </div>

</section>



<!-- =========================================================
     PARTNERS LOGOS
========================================================= -->

<section
    class="py-16 bg-palegreen border-y border-gray-100"
>

    <div class="max-w-7xl mx-auto px-6 lg:px-10">


        <p
            class="text-center text-xs uppercase tracking-[3px] text-textgray font-bold mb-10"
        >
            Algunas organizaciones de nuestra red
        </p>


        <div
            class="grid grid-cols-2 md:grid-cols-4 gap-5"
        >

            <div
                class="h-28 rounded-2xl bg-white flex items-center justify-center"
            >

                <span
                    class="text-xl font-black text-gray-400"
                >
                    AGRIFILIERA
                </span>

            </div>


            <div
                class="h-28 rounded-2xl bg-white flex items-center justify-center"
            >

                <span
                    class="text-xl font-black text-gray-400"
                >
                    SOMASCHINI
                </span>

            </div>


            <div
                class="h-28 rounded-2xl bg-white flex items-center justify-center"
            >

                <span
                    class="text-xl font-black text-gray-400"
                >
                    SISTEMI TRE
                </span>

            </div>


            <div
                class="h-28 rounded-2xl bg-white flex items-center justify-center"
            >

                <span
                    class="text-xl font-black text-gray-400"
                >
                    GROUPE HECTARE
                </span>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     MARQUEE
========================================================= -->

<section class="bg-agricolus py-7 marquee">

    <div class="marquee-track">

        <div
            class="flex items-center gap-10 px-5 text-white text-2xl md:text-3xl font-bold"
        >

            <span>MAKING AGRITECH SUSTAINABLE</span>
            <span class="text-white/40">✦</span>
            <span>MAKING AGRITECH SUSTAINABLE</span>
            <span class="text-white/40">✦</span>
            <span>MAKING AGRITECH SUSTAINABLE</span>
            <span class="text-white/40">✦</span>

        </div>


        <div
            class="flex items-center gap-10 px-5 text-white text-2xl md:text-3xl font-bold"
        >

            <span>MAKING AGRITECH SUSTAINABLE</span>
            <span class="text-white/40">✦</span>
            <span>MAKING AGRITECH SUSTAINABLE</span>
            <span class="text-white/40">✦</span>
            <span>MAKING AGRITECH SUSTAINABLE</span>
            <span class="text-white/40">✦</span>

        </div>

    </div>

</section>



<!-- =========================================================
     CTA
========================================================= -->

<section
    id="contacto"
    class="py-24 bg-darkgreen text-white"
>

    <div class="max-w-5xl mx-auto px-6 text-center reveal">


        <span
            class="text-[#9BD44B] uppercase tracking-[3px] text-sm font-bold"
        >
            Programa Partner
        </span>


        <h2
            class="mt-5 text-4xl md:text-6xl font-bold leading-tight"
        >
            ¿Quieres formar parte
            de nuestra red?
        </h2>


        <p
            class="mt-6 text-lg md:text-xl text-white/60 leading-8 max-w-2xl mx-auto"
        >
            Descubre cómo colaborar con Agricolus y ofrecer nuevas
            oportunidades digitales a tus clientes.
        </p>


        <div class="mt-9 flex flex-wrap justify-center gap-4">

            <a
                href="<?= $base_url ?>contacto.php"
                class="bg-agricolus hover:bg-[#8ACA38] text-white px-8 py-4 rounded-full font-semibold transition"
            >
                Programa Partner
            </a>


            <a
                href="<?= $base_url ?>contacto.php"
                class="border border-white/30 hover:bg-white hover:text-darkgreen px-8 py-4 rounded-full font-semibold transition"
            >
                Contactar
            </a>

        </div>

    </div>

</section>



<!-- =========================================================
     NEWSLETTER
========================================================= -->

<section
    x-data="{
        submitted:false,
        email:''
    }"
    class="py-24 bg-softgreen"
>

    <div class="max-w-4xl mx-auto px-6 text-center">


        <div
            class="w-14 h-14 bg-agricolus rounded-full mx-auto flex items-center justify-center"
        >

            <span class="text-white font-black text-xl">
                A
            </span>

        </div>


        <h2
            class="mt-6 text-4xl md:text-5xl font-bold text-darkgreen"
        >
            ¿Quieres profundizar en el mundo
            de la agricultura de precisión?
        </h2>


        <p
            class="mt-5 text-lg text-textgray"
        >
            Mantente actualizado sobre agricultura digital,
            innovación y nuevas tecnologías.
        </p>


        <form
            @submit.prevent="submitted=true"
            class="mt-8 max-w-xl mx-auto"
        >

            <div
                x-show="!submitted"
                class="flex flex-col sm:flex-row gap-3"
            >

                <input
                    type="email"
                    required
                    x-model="email"
                    placeholder="Tu correo electrónico"
                    class="flex-1 px-6 py-4 rounded-full border border-gray-200 bg-white outline-none focus:border-agricolus"
                >


                <button
                    type="submit"
                    class="bg-darkgreen hover:bg-[#245337] text-white px-7 py-4 rounded-full font-semibold transition"
                >
                    Suscribirme
                </button>

            </div>


            <div
                x-show="submitted"
                x-transition
                class="bg-white rounded-2xl p-6"
            >

                <div class="text-agricolus text-3xl">
                    ✓
                </div>

                <p
                    class="mt-2 font-semibold text-darkgreen"
                >
                    ¡Gracias por suscribirte!
                </p>

            </div>

        </form>


        <label
            x-show="!submitted"
            class="mt-5 flex items-center justify-center gap-3 text-xs text-textgray"
        >

            <input
                type="checkbox"
                required
                class="accent-[#78B82A]"
            >

            <span>
                He leído y acepto la Política de Privacidad.
            </span>

        </label>

    </div>

</section>

<script>
function testimonialSlider() {
    return {
        current: 0,
        quotes: [
            {
                text: 'La colaboración entre el agrónomo y Agricolus es una apuesta ganadora: la elegí como herramienta de trabajo para ofrecer un valor añadido a las empresas agrícolas.',
                name: 'Federico Pasqualini',
                company: 'Agrifiliera'
            },
            {
                text: 'Con Agricolus, he encontrado la herramienta ideal para brindar un servicio de primera calidad a mis clientes.',
                name: 'Massimo Somaschini',
                company: 'Somaschini Consulting'
            },
            {
                text: 'Con la solución de gestión agronómica para viñedos de Agricolus, completamos la gama de herramientas para producir vinos excelentes y apoyar el crecimiento de las bodegas.',
                name: 'Silvia Cabutto',
                company: 'Area Sviluppo – Sistemi Tre'
            },
            {
                text: 'El software de agricultura de precisión Agricolus se posiciona como una solución sostenible y competitiva para nuestras empresas agrícolas y agroindustriales.',
                name: 'Guy Meli Momo',
                company: 'Groupe Hectare'
            }
        ],
        next() {
            this.current = (this.current + 1) % this.quotes.length;
        },
        previous() {
            this.current = (this.current - 1 + this.quotes.length) % this.quotes.length;
        }
    }
}

const revealElements = document.querySelectorAll('.reveal');
const revealObserver = new IntersectionObserver(
    entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
                revealObserver.unobserve(entry.target);
            }
        });
    },
    { threshold: .12 }
);
revealElements.forEach(element => { revealObserver.observe(element); });
</script>

<?php
include __DIR__ . '/../includes/footer.php';
?>

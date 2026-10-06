<?php
$page_title = "Empresa | Agricolus";
$base_url = "../";
$current_page = "empresa";
$body_class = "bg-white";

$page_styles = <<<'CSS'
        .hero-pattern {
            background-image:
                linear-gradient(
                    rgba(23,59,39,.045) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(23,59,39,.045) 1px,
                    transparent 1px
                );
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

        .value-card {
            transition:
                transform .3s ease,
                box-shadow .3s ease,
                border-color .3s ease;
        }

        .value-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 22px 45px rgba(23,59,39,.09);
            border-color: #78B82A;
        }

        .award-card {
            transition: all .3s ease;
        }

        .award-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 40px rgba(23,59,39,.08);
        }

        .member-card {
            transition: transform .3s ease;
        }

        .member-card:hover {
            transform: translateY(-5px);
        }

        .member-card img {
            filter: grayscale(100%);
            transition: filter .4s ease, transform .5s ease;
        }

        .member-card:hover img {
            filter: grayscale(0%);
            transform: scale(1.03);
        }

        .green-line {
            position: relative;
        }

        .green-line::before {
            content: "";
            position: absolute;
            left: 0;
            top: -16px;
            width: 45px;
            height: 4px;
            border-radius: 999px;
            background: #78B82A;
        }
CSS;

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/header.php';
?>

<!-- =========================================================
     HERO
========================================================= -->

<section
    class="pt-[82px] bg-softgreen hero-pattern overflow-hidden"
>

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="grid lg:grid-cols-2 min-h-[620px] items-center gap-14">


            <!-- LEFT -->

            <div class="py-20">

                <span
                    class="inline-flex items-center gap-2 bg-white rounded-full px-4 py-2 shadow-sm text-xs font-bold uppercase tracking-[2px]"
                >

                    <span class="w-2 h-2 bg-agricolus rounded-full"></span>

                    Quiénes somos

                </span>


                <h1
                    class="mt-7 text-5xl md:text-6xl lg:text-[70px] leading-[.98] tracking-[-3px] font-bold text-darkgreen"
                >

                    Innovación para una
                    <span class="text-agricolus">
                        agricultura sostenible
                    </span>

                </h1>


                <p
                    class="mt-7 max-w-xl text-lg md:text-xl leading-8 text-textgray"
                >
                    Somos una empresa innovadora que desarrolla herramientas
                    digitales para el sector agrícola y para todos los actores
                    de la cadena agroalimentaria.
                </p>


                <div class="mt-9 flex flex-wrap gap-4">

                    <a
                        href="#mision"
                        class="bg-darkgreen hover:bg-[#245337] text-white px-7 py-4 rounded-full font-semibold transition"
                    >
                        Conoce Agricolus
                    </a>

                    <a
                        href="#equipo"
                        class="border border-darkgreen hover:bg-darkgreen hover:text-white text-darkgreen px-7 py-4 rounded-full font-semibold transition"
                    >
                        Nuestro equipo
                    </a>

                </div>

            </div>


            <!-- RIGHT -->

            <div class="relative min-h-[540px] flex items-center justify-center">

                <!-- DECORATION -->

                <div
                    class="absolute w-[440px] h-[440px] rounded-full bg-[#DCEBC9] -right-20 bottom-0"
                ></div>

                <div
                    class="absolute w-[120px] h-[120px] rounded-full border-[18px] border-agricolus/20 top-16 left-0"
                ></div>


                <!-- IMAGE -->

                <div
                    class="relative z-10 image-hover w-full max-w-[570px]"
                >

                    <img
                        src="https://images.unsplash.com/photo-1492496913980-501348b61469?auto=format&fit=crop&w=1200&q=85"
                        alt="Agricultura sostenible"
                        class="w-full h-[500px] object-cover rounded-t-[190px] rounded-b-[35px] shadow-2xl"
                    >

                    <div
                        class="absolute left-6 bottom-6 bg-white rounded-[22px] p-5 shadow-xl"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="w-11 h-11 rounded-full bg-softgreen flex items-center justify-center"
                            >
                                <span class="text-agricolus text-xl">
                                    ✓
                                </span>
                            </div>

                            <div>

                                <p class="font-bold text-darkgreen">
                                    Making AgriTech
                                </p>

                                <p class="text-sm text-textgray">
                                    sustainable
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
     INTRO
========================================================= -->

<section class="py-24">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="grid lg:grid-cols-2 gap-16 items-center">


            <!-- IMAGE -->

            <div class="image-hover rounded-[35px] overflow-hidden">

                <img
                    src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=85"
                    alt="Campo agrícola"
                    class="w-full h-[540px] object-cover"
                >

            </div>


            <!-- TEXT -->

            <div>

                <span
                    class="text-agricolus uppercase tracking-[3px] text-sm font-bold"
                >
                    Agricolus
                </span>


                <h2
                    class="mt-5 text-4xl md:text-5xl font-bold text-darkgreen leading-tight"
                >
                    Tecnología al servicio
                    del mundo agrícola
                </h2>


                <p class="mt-6 text-lg text-textgray leading-8">

                    Agricolus es una empresa innovadora que desarrolla
                    herramientas digitales para el sector agrícola.
                    Nació en Perugia, Umbría, el «corazón verde» de Italia,
                    con el objetivo de apoyar a los actores de la cadena
                    alimentaria.

                </p>


                <p class="mt-5 text-lg text-textgray leading-8">

                    La idea nace de la pasión por el territorio, el campo
                    y, especialmente, por las personas que lo cultivan:
                    los agricultores.

                </p>


                <p class="mt-5 text-lg text-textgray leading-8">

                    Desde nuestra visión, la tecnología puede ayudar al
                    sector agrícola a enfrentarse a retos como el cambio
                    climático, el aumento de los costes de gestión y las
                    nuevas exigencias normativas.

                </p>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     MISION
========================================================= -->

<section
    id="mision"
    class="py-24 bg-darkgreen text-white relative overflow-hidden"
>

    <div
        class="absolute w-[500px] h-[500px] rounded-full border border-white/10 -right-40 -top-40"
    ></div>

    <div
        class="absolute w-[280px] h-[280px] rounded-full border border-agricolus/20 left-[-100px] bottom-[-100px]"
    ></div>


    <div class="max-w-5xl mx-auto px-6 relative z-10 text-center">

        <span
            class="text-[#9BD44B] uppercase tracking-[3px] text-sm font-bold"
        >
            Nuestra misión
        </span>


        <h2
            class="mt-5 text-5xl md:text-6xl font-bold tracking-tight"
        >
            Making AgriTech
            <span class="text-agricolus">
                sustainable
            </span>
        </h2>


        <div class="w-14 h-1 bg-agricolus rounded-full mx-auto mt-8"></div>


        <p
            class="mt-9 text-xl md:text-2xl leading-10 text-white/75"
        >
            Apoyar a agricultores y operadores del sector en la optimización
            de las prácticas agronómicas, haciendo que las tecnologías
            innovadoras sean accesibles y fáciles de usar.
        </p>


        <p
            class="mt-6 text-lg leading-8 text-white/60 max-w-3xl mx-auto"
        >
            Nuestro objetivo es contribuir a los objetivos de sostenibilidad
            ambiental y económica que requiere la agricultura moderna.
        </p>

    </div>

</section>



<!-- =========================================================
     EQUIPO / JUNTA
========================================================= -->

<section
    id="equipo"
    class="py-24 bg-palegreen"
>

    <div class="max-w-7xl mx-auto px-6 lg:px-10">


        <div class="flex flex-col md:flex-row justify-between md:items-end gap-8 mb-14">

            <div>

                <span
                    class="text-agricolus uppercase tracking-[3px] text-sm font-bold"
                >
                    Equipo
                </span>

                <h2
                    class="mt-4 text-4xl md:text-5xl font-bold text-darkgreen"
                >
                    Junta directiva
                </h2>

            </div>


            <p class="max-w-md text-textgray leading-7">
                Un equipo multidisciplinar unido por la tecnología,
                la innovación y la agricultura.
            </p>

        </div>


        <div class="grid md:grid-cols-3 gap-7">


            <!-- ANDREA -->

            <article class="member-card bg-white rounded-[30px] overflow-hidden">

                <div class="h-[390px] bg-[#DDE8D6] overflow-hidden">

                    <img
                        src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=800&q=85"
                        alt="Andrea Cruciani"
                        class="w-full h-full object-cover"
                    >

                </div>

                <div class="p-7">

                    <h3 class="text-2xl font-bold text-darkgreen">
                        Andrea Cruciani
                    </h3>

                    <p class="mt-2 text-agricolus font-semibold">
                        CEO
                    </p>

                </div>

            </article>


            <!-- ANTONIO -->

            <article class="member-card bg-white rounded-[30px] overflow-hidden">

                <div class="h-[390px] bg-[#DDE8D6] overflow-hidden">

                    <img
                        src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=800&q=85"
                        alt="Antonio Natale"
                        class="w-full h-full object-cover"
                    >

                </div>

                <div class="p-7">

                    <h3 class="text-2xl font-bold text-darkgreen">
                        Antonio Natale
                    </h3>

                    <p class="mt-2 text-agricolus font-semibold">
                        Director de RRHH
                    </p>

                </div>

            </article>


            <!-- LUIGI -->

            <article class="member-card bg-white rounded-[30px] overflow-hidden">

                <div class="h-[390px] bg-[#DDE8D6] overflow-hidden">

                    <img
                        src="https://images.unsplash.com/photo-1566492031773-4f4e44671d66?auto=format&fit=crop&w=800&q=85"
                        alt="Luigi Radaelli"
                        class="w-full h-full object-cover"
                    >

                </div>

                <div class="p-7">

                    <h3 class="text-2xl font-bold text-darkgreen">
                        Luigi Radaelli
                    </h3>

                    <p class="mt-2 text-agricolus font-semibold">
                        CMO
                    </p>

                </div>

            </article>

        </div>

    </div>

</section>



<!-- =========================================================
     VALORES
========================================================= -->

<section class="py-24 bg-white">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="max-w-3xl mb-14">

            <span
                class="text-agricolus uppercase tracking-[3px] text-sm font-bold"
            >
                Nuestros valores
            </span>

            <h2
                class="mt-4 text-4xl md:text-5xl font-bold text-darkgreen"
            >
                Lo que guía nuestro trabajo
            </h2>

        </div>


        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">


            <!-- VALUE 1 -->

            <article
                class="value-card border border-gray-200 rounded-[28px] p-8"
            >

                <div
                    class="w-14 h-14 bg-softgreen rounded-2xl flex items-center justify-center"
                >

                    <svg
                        width="27"
                        height="27"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#78B82A"
                        stroke-width="1.7"
                    >
                        <path d="M12 21s8-4 8-10V5l-8-3-8 3v6c0 6 8 10 8 10Z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>

                </div>


                <h3 class="mt-7 text-xl font-bold text-darkgreen">
                    Atención a las necesidades
                    de los agricultores
                </h3>

            </article>


            <!-- VALUE 2 -->

            <article
                class="value-card border border-gray-200 rounded-[28px] p-8"
            >

                <div
                    class="w-14 h-14 bg-softgreen rounded-2xl flex items-center justify-center"
                >

                    <svg
                        width="27"
                        height="27"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#78B82A"
                        stroke-width="1.7"
                    >
                        <path d="M12 3v18"/>
                        <path d="M3 12h18"/>
                        <circle cx="12" cy="12" r="8"/>
                    </svg>

                </div>


                <h3 class="mt-7 text-xl font-bold text-darkgreen">
                    Difusión de conocimientos
                    tecnológicos
                </h3>

            </article>


            <!-- VALUE 3 -->

            <article
                class="value-card border border-gray-200 rounded-[28px] p-8"
            >

                <div
                    class="w-14 h-14 bg-softgreen rounded-2xl flex items-center justify-center"
                >

                    <svg
                        width="27"
                        height="27"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#78B82A"
                        stroke-width="1.7"
                    >
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>

                </div>


                <h3 class="mt-7 text-xl font-bold text-darkgreen">
                    Calidad de las relaciones
                    con clientes y socios
                </h3>

            </article>


            <!-- VALUE 4 -->

            <article
                class="value-card border border-gray-200 rounded-[28px] p-8"
            >

                <div
                    class="w-14 h-14 bg-softgreen rounded-2xl flex items-center justify-center"
                >

                    <svg
                        width="27"
                        height="27"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#78B82A"
                        stroke-width="1.7"
                    >
                        <path d="M12 3c3 3 5 6 5 10a5 5 0 0 1-10 0c0-4 2-7 5-10Z"/>
                        <path d="M8 18c1.5-1 3-1 4 0"/>
                    </svg>

                </div>


                <h3 class="mt-7 text-xl font-bold text-darkgreen">
                    Sostenibilidad económica
                    y ambiental
                </h3>

            </article>

        </div>

    </div>

</section>



<!-- =========================================================
     RECONOCIMIENTOS
========================================================= -->

<section class="py-24 bg-palegreen">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">


        <div class="text-center max-w-3xl mx-auto mb-14">

            <span
                class="text-agricolus uppercase tracking-[3px] text-sm font-bold"
            >
                Reconocimientos
            </span>

            <h2
                class="mt-4 text-4xl md:text-5xl font-bold text-darkgreen"
            >
                Innovación reconocida
            </h2>

            <p class="mt-5 text-lg text-textgray leading-8">
                Algunos de los reconocimientos y selecciones recibidos
                por Agricolus y sus proyectos.
            </p>

        </div>


        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">


            <!-- AWARD 1 -->

            <article class="award-card bg-white rounded-[27px] p-7">

                <div
                    class="h-28 rounded-2xl bg-[#F7F3E7] flex items-center justify-center"
                >

                    <span class="font-bold text-2xl text-[#8D7443]">
                        FoodBytes
                    </span>

                </div>

                <p class="mt-6 text-textgray leading-7 text-sm">
                    Agricolus fue seleccionada como una de las empresas
                    destacadas en innovación de la cadena agroalimentaria.
                </p>

            </article>


            <!-- AWARD 2 -->

            <article class="award-card bg-white rounded-[27px] p-7">

                <div
                    class="h-28 rounded-2xl bg-[#EAF3E3] flex items-center justify-center"
                >

                    <span class="font-black text-2xl text-darkgreen">
                        FoodTech 500
                    </span>

                </div>

                <p class="mt-6 text-textgray leading-7 text-sm">
                    Agricolus aparece en el FoodTech 500 2025,
                    listado internacional de empresas del sector.
                </p>

            </article>


            <!-- AWARD 3 -->

            <article class="award-card bg-white rounded-[27px] p-7">

                <div
                    class="h-28 rounded-2xl bg-[#EAF1F5] flex items-center justify-center"
                >

                    <span class="font-bold text-xl text-[#32617A]">
                        DIGITAL SME
                    </span>

                </div>

                <p class="mt-6 text-textgray leading-7 text-sm">
                    Agricolus Academy recibió el premio Digital Skills
                    dentro de los DIGITAL SME Awards.
                </p>

            </article>


            <!-- AWARD 4 -->

            <article class="award-card bg-white rounded-[27px] p-7">

                <div
                    class="h-28 rounded-2xl bg-[#F0F1E7] flex items-center justify-center"
                >

                    <span class="font-black text-xl text-[#66702C]">
                        AGTECH
                    </span>

                </div>

                <p class="mt-6 text-textgray leading-7 text-sm">
                    Agricolus fue reconocida con el premio
                    Smart Agriculture Solution of the Year.
                </p>

            </article>

        </div>

    </div>

</section>



<!-- =========================================================
     INVESTIGACIÓN
========================================================= -->

<section class="py-24 bg-white">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="grid lg:grid-cols-2 gap-16 items-center">


            <div>

                <span
                    class="text-agricolus uppercase tracking-[3px] text-sm font-bold"
                >
                    Innovación
                </span>

                <h2
                    class="mt-4 text-4xl md:text-5xl font-bold text-darkgreen"
                >
                    Investigación
                    y desarrollo
                </h2>

                <p class="mt-6 text-lg text-textgray leading-8">
                    La investigación es una parte fundamental de nuestro
                    trabajo. Desarrollamos nuevas tecnologías y colaboramos
                    con actores del ecosistema agrícola para convertir
                    conocimiento científico en herramientas prácticas.
                </p>


                <div class="mt-8 space-y-4">

                    <div class="flex gap-4">

                        <div
                            class="flex-shrink-0 w-10 h-10 rounded-full bg-softgreen flex items-center justify-center text-agricolus font-bold"
                        >
                            01
                        </div>

                        <div>

                            <h3 class="font-bold text-darkgreen">
                                Nuevas tecnologías
                            </h3>

                            <p class="mt-1 text-textgray text-sm">
                                Investigación aplicada a la agricultura digital.
                            </p>

                        </div>

                    </div>


                    <div class="flex gap-4">

                        <div
                            class="flex-shrink-0 w-10 h-10 rounded-full bg-softgreen flex items-center justify-center text-agricolus font-bold"
                        >
                            02
                        </div>

                        <div>

                            <h3 class="font-bold text-darkgreen">
                                Colaboración
                            </h3>

                            <p class="mt-1 text-textgray text-sm">
                                Trabajo conjunto con empresas y centros de investigación.
                            </p>

                        </div>

                    </div>


                    <div class="flex gap-4">

                        <div
                            class="flex-shrink-0 w-10 h-10 rounded-full bg-softgreen flex items-center justify-center text-agricolus font-bold"
                        >
                            03
                        </div>

                        <div>

                            <h3 class="font-bold text-darkgreen">
                                Transferencia tecnológica
                            </h3>

                            <p class="mt-1 text-textgray text-sm">
                                Convertimos investigación en soluciones utilizables.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <div class="relative">

                <div
                    class="absolute -inset-5 rounded-[45px] bg-softgreen"
                ></div>

                <img
                    src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1100&q=85"
                    alt="Investigación y desarrollo"
                    class="relative w-full h-[500px] object-cover rounded-[35px]"
                >

                <div
                    class="absolute bottom-6 left-6 bg-white rounded-[20px] px-6 py-5 shadow-xl"
                >

                    <span class="text-agricolus font-bold text-3xl">
                        R&D
                    </span>

                    <p class="text-sm text-textgray mt-1">
                        Investigación aplicada
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     TRABAJA CON NOSOTROS
========================================================= -->

<section class="py-24 bg-darkgreen text-white">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="grid lg:grid-cols-2 gap-14 items-center">


            <div>

                <span
                    class="text-[#9BD44B] uppercase tracking-[3px] text-sm font-bold"
                >
                    Trabaja con nosotros
                </span>


                <h2
                    class="mt-5 text-4xl md:text-5xl font-bold leading-tight"
                >
                    Forma parte del futuro
                    de la agricultura
                </h2>


                <p
                    class="mt-6 text-white/65 text-lg leading-8 max-w-xl"
                >
                    Descubre nuestras oportunidades y los programas
                    pensados para construir nuevas colaboraciones
                    alrededor de la agricultura digital.
                </p>


                <div class="mt-8 flex flex-wrap gap-4">

                    <a
                        href="<?= $base_url ?>contacto.php"
                        class="bg-agricolus hover:bg-[#8ACA38] text-white px-7 py-4 rounded-full font-semibold transition"
                    >
                        Puestos de trabajo
                    </a>

                    <a
                        href="<?= $base_url ?>contacto.php"
                        class="border border-white/30 hover:bg-white hover:text-darkgreen text-white px-7 py-4 rounded-full font-semibold transition"
                    >
                        Programa Partner
                    </a>

                </div>

            </div>


            <div
                class="relative image-hover rounded-[35px] overflow-hidden"
            >

                <img
                    src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=1100&q=85"
                    alt="Equipo Agricolus"
                    class="w-full h-[430px] object-cover"
                >

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     NEWSLETTER
========================================================= -->

<section
    id="contacto"
    class="py-24 bg-softgreen"
>

    <div
        x-data="{
            submitted: false,
            email: ''
        }"
        class="max-w-4xl mx-auto px-6 text-center"
    >

        <div
            class="w-14 h-14 bg-agricolus rounded-full flex items-center justify-center mx-auto"
        >

            <span class="text-white text-xl font-bold">
                A
            </span>

        </div>


        <h2
            class="mt-6 text-4xl md:text-5xl font-bold text-darkgreen"
        >
            ¿Quieres profundizar en el mundo
            de la agricultura de precisión?
        </h2>


        <p class="mt-5 text-lg text-textgray">
            Mantente actualizado sobre innovación, tecnología
            y agricultura digital.
        </p>


        <form
            @submit.prevent="submitted = true"
            class="mt-8 max-w-xl mx-auto"
        >

            <div
                x-show="!submitted"
                class="flex flex-col sm:flex-row gap-3"
            >

                <input
                    type="email"
                    x-model="email"
                    required
                    placeholder="Tu correo electrónico"
                    class="flex-1 px-6 py-4 rounded-full border border-gray-200 outline-none focus:border-agricolus bg-white"
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
                class="bg-white rounded-2xl p-5 text-darkgreen"
            >

                <div class="text-agricolus text-2xl">
                    ✓
                </div>

                <p class="mt-2 font-semibold">
                    ¡Gracias por suscribirte!
                </p>

            </div>

        </form>


        <label
            x-show="!submitted"
            class="mt-5 flex items-start gap-3 justify-center text-xs text-textgray cursor-pointer"
        >

            <input
                type="checkbox"
                required
                class="mt-0.5 accent-[#78B82A]"
            >

            <span>
                He leído y acepto la Política de Privacidad.
            </span>

        </label>

    </div>

</section>

<?php
include __DIR__ . '/../includes/footer.php';
?>

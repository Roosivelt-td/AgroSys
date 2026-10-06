<?php
$page_title = "Agricolus — Agricultura digital";
$base_url = "./";
$current_page = "home";
$body_class = "bg-white text-darkgreen";

$page_styles = <<<'CSS'
        .grid-bg {
            background-image:
                linear-gradient(rgba(23,59,39,.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(23,59,39,.045) 1px, transparent 1px);
            background-size: 48px 48px;
        }
        .hero-image {
            border-radius: 180px 35px 35px 35px;
        }
        .reveal {
            opacity: 0;
            transform: translateY(35px);
            transition: opacity .8s ease, transform .8s ease;
        }
        .reveal.show {
            opacity: 1;
            transform: translateY(0);
        }
        .float {
            animation: float 5s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .solution-card {
            transition: transform .35s ease, box-shadow .35s ease;
        }
        .solution-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px rgba(23,59,39,.11);
        }
        .solution-card img {
            transition: transform .7s ease;
        }
        .solution-card:hover img {
            transform: scale(1.06);
        }
        .marquee {
            overflow: hidden;
            white-space: nowrap;
        }
        .marquee-track {
            display: inline-flex;
            animation: marquee 32s linear infinite;
        }
        @keyframes marquee {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }
        .green-line {
            position: relative;
        }
        .green-line::before {
            content: "";
            position: absolute;
            width: 55px;
            height: 4px;
            background: #78B82A;
            border-radius: 20px;
            top: -15px;
            left: 0;
        }
CSS;

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>

<!-- =====================================================
     HERO
====================================================== -->

<section
    class="
        pt-[82px]
        bg-cream
        grid-bg
        overflow-hidden
    "
>

    <div
        class="
            max-w-7xl
            mx-auto
            px-6
            lg:px-10
        "
    >

        <div
            class="
                min-h-[700px]
                grid
                lg:grid-cols-2
                gap-16
                items-center
            "
        >


            <!-- HERO TEXT -->

            <div
                class="
                    py-20
                    reveal
                "
            >

                <div
                    class="
                        inline-flex
                        items-center
                        gap-2
                        bg-white
                        rounded-full
                        px-4
                        py-2
                        shadow-sm
                    "
                >

                    <span
                        class="
                            w-2
                            h-2
                            rounded-full
                            bg-agricolus
                        "
                    ></span>

                    <span
                        class="
                            text-xs
                            font-bold
                            uppercase
                            tracking-[2px]
                        "
                    >
                        Agricolus
                    </span>

                </div>



                <h1
                    class="
                        mt-7
                        text-5xl
                        md:text-6xl
                        lg:text-[72px]
                        font-bold
                        leading-[.96]
                        tracking-[-4px]
                    "
                >

                    La tecnología
                    al servicio de
                    <span
                        class="
                            text-agricolus
                        "
                    >
                        la agricultura
                    </span>

                </h1>



                <p
                    class="
                        mt-7
                        max-w-xl
                        text-lg
                        md:text-xl
                        text-textgray
                        leading-8
                    "
                >

                    Soluciones digitales para gestionar
                    el campo de forma sencilla, precisa
                    y sostenible.

                </p>



                <div
                    class="
                        mt-9
                        flex
                        flex-wrap
                        gap-4
                    "
                >

                    <a
                        href="<?= $base_url ?>soluciones/todasSoluciones.php"
                        class="
                            bg-darkgreen
                            hover:bg-[#245337]
                            text-white
                            px-7
                            py-4
                            rounded-full
                            font-semibold
                            transition
                        "
                    >
                        Descubre Agricolus
                    </a>


                    <a
                        href="<?= $base_url ?>contacto.php"
                        class="
                            border
                            border-darkgreen
                            hover:bg-darkgreen
                            hover:text-white
                            px-7
                            py-4
                            rounded-full
                            font-semibold
                            transition
                        "
                    >
                        Solicita una demo
                    </a>

                </div>



                <!-- MINI STATS -->

                <div
                    class="
                        mt-12
                        flex
                        flex-wrap
                        gap-8
                    "
                >

                    <div>

                        <strong
                            class="
                                block
                                text-3xl
                                font-bold
                            "
                        >
                            4.0
                        </strong>

                        <span
                            class="
                                text-sm
                                text-textgray
                            "
                        >
                            Agricultura digital
                        </span>

                    </div>


                    <div>

                        <strong
                            class="
                                block
                                text-3xl
                                font-bold
                            "
                        >
                            360°
                        </strong>

                        <span
                            class="
                                text-sm
                                text-textgray
                            "
                        >
                            Gestión agrícola
                        </span>

                    </div>


                    <div>

                        <strong
                            class="
                                block
                                text-3xl
                                font-bold
                            "
                        >
                            +Data
                        </strong>

                        <span
                            class="
                                text-sm
                                text-textgray
                            "
                        >
                            Decisiones inteligentes
                        </span>

                    </div>

                </div>

            </div>



            <!-- HERO IMAGE -->

            <div
                class="
                    relative
                    min-h-[620px]
                    flex
                    items-center
                    justify-center
                    reveal
                "
            >

                <!-- GREEN CIRCLE -->

                <div
                    class="
                        absolute
                        right-[-80px]
                        bottom-[-40px]
                        w-[520px]
                        h-[520px]
                        rounded-full
                        bg-[#DDEBC9]
                    "
                ></div>



                <!-- SMALL CIRCLE -->

                <div
                    class="
                        absolute
                        left-0
                        top-20
                        w-28
                        h-28
                        rounded-full
                        border-[15px]
                        border-agricolus/20
                    "
                ></div>



                <!-- IMAGE -->

                <img
                    src="https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1300&q=90"
                    alt="Agricultura digital"
                    class="
                        hero-image
                        relative
                        z-10
                        w-full
                        max-w-[600px]
                        h-[560px]
                        object-cover
                        shadow-card
                    "
                >



                <!-- FLOATING DATA CARD -->

                <div
                    class="
                        float
                        absolute
                        z-20
                        left-0
                        bottom-12
                        bg-white
                        rounded-[24px]
                        p-5
                        shadow-card
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            gap-4
                        "
                    >

                        <div
                            class="
                                w-12
                                h-12
                                rounded-2xl
                                bg-softgreen
                                flex
                                items-center
                                justify-center
                                text-agricolus
                            "
                        >

                            <svg
                                width="25"
                                height="25"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                viewBox="0 0 24 24"
                            >

                                <path
                                    d="M4 18h16"
                                />

                                <path
                                    d="M6 15l4-5 3 3 5-7"
                                />

                            </svg>

                        </div>


                        <div>

                            <p
                                class="
                                    text-xs
                                    text-textgray
                                "
                            >
                                Datos agrícolas
                            </p>

                            <p
                                class="
                                    font-bold
                                "
                            >
                                En tiempo real
                            </p>

                        </div>

                    </div>

                </div>



                <!-- SECOND CARD -->

                <div
                    class="
                        float
                        absolute
                        z-20
                        right-[-15px]
                        top-20
                        bg-darkgreen
                        text-white
                        rounded-[22px]
                        px-5
                        py-4
                        shadow-xl
                    "
                    style="animation-delay:1.2s"
                >

                    <div
                        class="
                            text-xs
                            text-white/60
                        "
                    >
                        Precisión
                    </div>

                    <div
                        class="
                            text-xl
                            font-bold
                        "
                    >
                        Agricultura 4.0
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     INTRO
====================================================== -->

<section
    class="
        py-24
        bg-white
    "
>

    <div
        class="
            max-w-5xl
            mx-auto
            px-6
            text-center
            reveal
        "
    >

        <span
            class="
                text-agricolus
                uppercase
                tracking-[3px]
                text-sm
                font-bold
            "
        >
            Agricultura digital
        </span>


        <h2
            class="
                mt-5
                text-4xl
                md:text-5xl
                font-bold
                leading-tight
            "
        >

            Todo lo que necesitas
            para gestionar mejor tu campo

        </h2>


        <p
            class="
                mt-6
                text-lg
                md:text-xl
                text-textgray
                leading-8
                max-w-3xl
                mx-auto
            "
        >

            Agricolus integra datos, tecnologías y herramientas
            agronómicas en una única plataforma para ayudarte
            a conocer mejor tus cultivos y tomar decisiones
            basadas en información.

        </p>

    </div>

</section>



<!-- =====================================================
     SOLUCIONES
====================================================== -->

<section
    id="soluciones"
    class="
        py-24
        bg-cream
    "
>

    <div
        class="
            max-w-7xl
            mx-auto
            px-6
            lg:px-10
        "
    >


        <div
            class="
                max-w-3xl
                mb-14
                reveal
            "
        >

            <span
                class="
                    text-agricolus
                    uppercase
                    tracking-[3px]
                    text-sm
                    font-bold
                "
            >
                Nuestras soluciones
            </span>


            <h2
                class="
                    mt-4
                    text-4xl
                    md:text-5xl
                    font-bold
                "
            >

                Una plataforma.
                Muchas posibilidades.

            </h2>

        </div>



        <div
            class="
                grid
                md:grid-cols-2
                lg:grid-cols-3
                gap-6
            "
        >


            <!-- SATELLITE -->

            <article
                class="
                    solution-card
                    bg-white
                    rounded-[32px]
                    overflow-hidden
                    reveal
                "
            >

                <div
                    class="
                        h-[270px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1000&q=85"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                        alt="Monitoreo satelital"
                    >

                </div>


                <div class="p-7">

                    <span
                        class="
                            text-xs
                            uppercase
                            tracking-[2px]
                            text-agricolus
                            font-bold
                        "
                    >
                        Teledetección
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        Satélite
                    </h3>


                    <p
                        class="
                            mt-4
                            text-textgray
                            leading-7
                        "
                    >
                        Observa el estado de tus cultivos
                        mediante imágenes satelitales.
                    </p>


                    <a
                        href="<?= $base_url ?>soluciones/monitoreoSatelital.php"
                        class="
                            mt-6
                            inline-flex
                            font-semibold
                            text-darkgreen
                            hover:text-agricolus
                        "
                    >
                        Descubrir →
                    </a>

                </div>

            </article>



            <!-- IRRIGATION -->

            <article
                class="
                    solution-card
                    bg-white
                    rounded-[32px]
                    overflow-hidden
                    reveal
                "
            >

                <div
                    class="
                        h-[270px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1564419320461-6870880221ad?auto=format&fit=crop&w=1000&q=85"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                        alt="Riego agrícola"
                    >

                </div>


                <div class="p-7">

                    <span
                        class="
                            text-xs
                            uppercase
                            tracking-[2px]
                            text-agricolus
                            font-bold
                        "
                    >
                        Agua y nutrición
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        Riego y nutrición
                    </h3>


                    <p
                        class="
                            mt-4
                            text-textgray
                            leading-7
                        "
                    >
                        Planifica y optimiza el uso del agua
                        y los nutrientes.
                    </p>


                    <a
                        href="<?= $base_url ?>soluciones/gestionAgronomica.php"
                        class="
                            mt-6
                            inline-flex
                            font-semibold
                            text-darkgreen
                            hover:text-agricolus
                        "
                    >
                        Descubrir →
                    </a>

                </div>

            </article>



            <!-- PROTECTION -->

            <article
                class="
                    solution-card
                    bg-white
                    rounded-[32px]
                    overflow-hidden
                    reveal
                "
            >

                <div
                    class="
                        h-[270px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&w=1000&q=85"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                        alt="Protección de cultivos"
                    >

                </div>


                <div class="p-7">

                    <span
                        class="
                            text-xs
                            uppercase
                            tracking-[2px]
                            text-agricolus
                            font-bold
                        "
                    >
                        Agronomía
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        Protección de cultivos
                    </h3>


                    <p
                        class="
                            mt-4
                            text-textgray
                            leading-7
                        "
                    >
                        Anticipa riesgos y gestiona la defensa
                        de tus cultivos.
                    </p>


                    <a
                        href="<?= $base_url ?>soluciones/defensaCultivos.php"
                        class="
                            mt-6
                            inline-flex
                            font-semibold
                            text-darkgreen
                            hover:text-agricolus
                        "
                    >
                        Descubrir →
                    </a>

                </div>

            </article>



            <!-- APP -->

            <article
                class="
                    solution-card
                    bg-darkgreen
                    text-white
                    rounded-[32px]
                    overflow-hidden
                    reveal
                "
            >

                <div
                    class="
                        h-[270px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1000&q=85"
                        class="
                            w-full
                            h-full
                            object-cover
                            opacity-90
                        "
                        alt="Agricolus App"
                    >

                </div>


                <div class="p-7">

                    <span
                        class="
                            text-xs
                            uppercase
                            tracking-[2px]
                            text-[#9BD44B]
                            font-bold
                        "
                    >
                        Digital
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        La App de Agricolus
                    </h3>


                    <p
                        class="
                            mt-4
                            text-white/60
                            leading-7
                        "
                    >
                        Lleva la gestión de tus parcelas
                        directamente al campo.
                    </p>


                    <a
                        href="<?= $base_url ?>soluciones/exploracionCultivos.php"
                        class="
                            mt-6
                            inline-flex
                            font-semibold
                            text-[#9BD44B]
                        "
                    >
                        Descubrir →
                    </a>

                </div>

            </article>



            <!-- AGRITRACK -->

            <article
                class="
                    solution-card
                    bg-white
                    rounded-[32px]
                    overflow-hidden
                    reveal
                "
            >

                <div
                    class="
                        h-[270px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1586771107445-d3ca888129ff?auto=format&fit=crop&w=1000&q=85"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                        alt="AgriTrack"
                    >

                </div>


                <div class="p-7">

                    <span
                        class="
                            text-xs
                            uppercase
                            tracking-[2px]
                            text-agricolus
                            font-bold
                        "
                    >
                        Gestión
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        AgriTrack
                    </h3>


                    <p
                        class="
                            mt-4
                            text-textgray
                            leading-7
                        "
                    >
                        Gestiona operaciones, actividades
                        y recursos agrícolas desde una
                        plataforma digital.
                    </p>


                    <a
                        href="<?= $base_url ?>soluciones/controlCadenaAgroalimentaria.php"
                        class="
                            mt-6
                            inline-flex
                            font-semibold
                            text-darkgreen
                            hover:text-agricolus
                        "
                    >
                        Descubrir →
                    </a>

                </div>

            </article>



            <!-- WEATHER -->

            <article
                class="
                    solution-card
                    bg-white
                    rounded-[32px]
                    overflow-hidden
                    reveal
                "
            >

                <div
                    class="
                        h-[270px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1534088568595-a066f410bcda?auto=format&fit=crop&w=1000&q=85"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                        alt="Estación meteorológica"
                    >

                </div>


                <div class="p-7">

                    <span
                        class="
                            text-xs
                            uppercase
                            tracking-[2px]
                            text-agricolus
                            font-bold
                        "
                    >
                        Meteorología
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        Estaciones agrometeo
                    </h3>


                    <p
                        class="
                            mt-4
                            text-textgray
                            leading-7
                        "
                    >
                        Monitoriza las condiciones meteorológicas
                        de tus parcelas.
                    </p>


                    <a
                        href="<?= $base_url ?>soluciones/estacionesAgrometeo.php"
                        class="
                            mt-6
                            inline-flex
                            font-semibold
                            text-darkgreen
                            hover:text-agricolus
                        "
                    >
                        Descubrir →
                    </a>

                </div>

            </article>

        </div>

    </div>

</section>



<!-- =====================================================
     DATA SECTION
====================================================== -->

<section
    id="tecnologia"
    class="
        py-28
        bg-white
        overflow-hidden
    "
>

    <div
        class="
            max-w-7xl
            mx-auto
            px-6
            lg:px-10
        "
    >

        <div
            class="
                grid
                lg:grid-cols-2
                gap-16
                items-center
            "
        >


            <!-- IMAGE -->

            <div
                class="
                    relative
                    reveal
                "
            >

                <div
                    class="
                        absolute
                        inset-0
                        bg-agricolus
                        rounded-[45px]
                        rotate-3
                    "
                ></div>


                <img
                    src="https://images.unsplash.com/photo-1589923188900-85dae523342b?auto=format&fit=crop&w=1200&q=85"
                    alt="Tecnología agrícola"
                    class="
                        relative
                        w-full
                        h-[530px]
                        object-cover
                        rounded-[45px]
                        shadow-card
                    "
                >


                <!-- FLOAT -->

                <div
                    class="
                        absolute
                        right-5
                        bottom-5
                        bg-white
                        rounded-[24px]
                        px-6
                        py-5
                        shadow-xl
                    "
                >

                    <p
                        class="
                            text-xs
                            text-textgray
                        "
                    >
                        Información
                    </p>

                    <p
                        class="
                            mt-1
                            text-xl
                            font-bold
                        "
                    >
                        En un solo lugar
                    </p>

                </div>

            </div>



            <!-- TEXT -->

            <div
                class="reveal"
            >

                <span
                    class="
                        text-agricolus
                        uppercase
                        tracking-[3px]
                        text-sm
                        font-bold
                    "
                >
                    Tecnología
                </span>


                <h2
                    class="
                        mt-5
                        text-4xl
                        md:text-5xl
                        font-bold
                        leading-tight
                    "
                >

                    Datos que se convierten
                    en decisiones

                </h2>


                <p
                    class="
                        mt-6
                        text-lg
                        text-textgray
                        leading-8
                    "
                >

                    La plataforma combina datos meteorológicos,
                    información agronómica, imágenes satelitales
                    y tecnologías de agricultura de precisión.

                </p>



                <!-- FEATURES -->

                <div
                    class="
                        mt-9
                        space-y-5
                    "
                >

                    <div
                        class="
                            flex
                            gap-4
                            items-start
                        "
                    >

                        <div
                            class="
                                w-10
                                h-10
                                flex-shrink-0
                                rounded-full
                                bg-softgreen
                                text-agricolus
                                flex
                                items-center
                                justify-center
                                font-bold
                            "
                        >
                            ✓
                        </div>


                        <div>

                            <h3
                                class="
                                    font-bold
                                    text-lg
                                "
                            >
                                Datos conectados
                            </h3>

                            <p
                                class="
                                    mt-1
                                    text-sm
                                    text-textgray
                                "
                            >
                                Centraliza la información
                                de tus explotaciones.
                            </p>

                        </div>

                    </div>



                    <div
                        class="
                            flex
                            gap-4
                            items-start
                        "
                    >

                        <div
                            class="
                                w-10
                                h-10
                                flex-shrink-0
                                rounded-full
                                bg-softgreen
                                text-agricolus
                                flex
                                items-center
                                justify-center
                                font-bold
                            "
                        >
                            ✓
                        </div>


                        <div>

                            <h3
                                class="
                                    font-bold
                                    text-lg
                                "
                            >
                                Análisis inteligente
                            </h3>

                            <p
                                class="
                                    mt-1
                                    text-sm
                                    text-textgray
                                "
                            >
                                Convierte datos complejos
                                en información útil.
                            </p>

                        </div>

                    </div>



                    <div
                        class="
                            flex
                            gap-4
                            items-start
                        "
                    >

                        <div
                            class="
                                w-10
                                h-10
                                flex-shrink-0
                                rounded-full
                                bg-softgreen
                                text-agricolus
                                flex
                                items-center
                                justify-center
                                font-bold
                            "
                        >
                            ✓
                        </div>


                        <div>

                            <h3
                                class="
                                    font-bold
                                    text-lg
                                "
                            >
                                Gestión desde cualquier lugar
                            </h3>

                            <p
                                class="
                                    mt-1
                                    text-sm
                                    text-textgray
                                "
                            >
                                Accede a tus datos desde
                                ordenador, tablet o móvil.
                            </p>

                        </div>

                    </div>

                </div>



                <a
                    href="<?= $base_url ?>quieneSomos/tecnologia.php"
                    class="
                        mt-9
                        inline-flex
                        bg-darkgreen
                        hover:bg-[#245337]
                        text-white
                        px-7
                        py-4
                        rounded-full
                        font-semibold
                        transition
                    "
                >
                    Conoce nuestra tecnología
                </a>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     HOW IT WORKS
====================================================== -->

<section
    class="
        py-24
        bg-softgreen
    "
>

    <div
        class="
            max-w-7xl
            mx-auto
            px-6
            lg:px-10
        "
    >

        <div
            class="
                text-center
                max-w-3xl
                mx-auto
                reveal
            "
        >

            <span
                class="
                    text-agricolus
                    uppercase
                    tracking-[3px]
                    text-sm
                    font-bold
                "
            >
                Cómo funciona
            </span>


            <h2
                class="
                    mt-4
                    text-4xl
                    md:text-5xl
                    font-bold
                "
            >

                Del dato a la acción

            </h2>


            <p
                class="
                    mt-5
                    text-lg
                    text-textgray
                "
            >

                Una forma más sencilla de gestionar
                la información agronómica.

            </p>

        </div>



        <div
            class="
                mt-16
                grid
                md:grid-cols-3
                gap-6
            "
        >


            <!-- STEP 1 -->

            <div
                class="
                    bg-white
                    rounded-[32px]
                    p-8
                    reveal
                "
            >

                <div
                    class="
                        flex
                        justify-between
                        items-center
                    "
                >

                    <span
                        class="
                            text-5xl
                            font-bold
                            text-agricolus/30
                        "
                    >
                        01
                    </span>


                    <div
                        class="
                            w-14
                            h-14
                            rounded-2xl
                            bg-softgreen
                            flex
                            items-center
                            justify-center
                            text-agricolus
                        "
                    >

                        <svg
                            width="28"
                            height="28"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            viewBox="0 0 24 24"
                        >

                            <circle
                                cx="12"
                                cy="12"
                                r="8"
                            />

                            <path
                                d="M12 8v4l3 2"
                            />

                        </svg>

                    </div>

                </div>


                <h3
                    class="
                        mt-8
                        text-2xl
                        font-bold
                    "
                >
                    Recopila
                </h3>


                <p
                    class="
                        mt-3
                        text-textgray
                        leading-7
                    "
                >
                    Recoge datos del campo, estaciones,
                    satélites y actividades agrícolas.
                </p>

            </div>



            <!-- STEP 2 -->

            <div
                class="
                    bg-darkgreen
                    text-white
                    rounded-[32px]
                    p-8
                    reveal
                "
            >

                <div
                    class="
                        flex
                        justify-between
                        items-center
                    "
                >

                    <span
                        class="
                            text-5xl
                            font-bold
                            text-white/20
                        "
                    >
                        02
                    </span>


                    <div
                        class="
                            w-14
                            h-14
                            rounded-2xl
                            bg-white/10
                            flex
                            items-center
                            justify-center
                            text-[#9BD44B]
                        "
                    >

                        <svg
                            width="28"
                            height="28"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            viewBox="0 0 24 24"
                        >

                            <path
                                d="M4 18h16"
                            />

                            <path
                                d="M6 15l4-5 3 3 5-7"
                            />

                        </svg>

                    </div>

                </div>


                <h3
                    class="
                        mt-8
                        text-2xl
                        font-bold
                    "
                >
                    Analiza
                </h3>


                <p
                    class="
                        mt-3
                        text-white/60
                        leading-7
                    "
                >
                    Procesa y relaciona la información
                    para entender qué ocurre en tus parcelas.
                </p>

            </div>



            <!-- STEP 3 -->

            <div
                class="
                    bg-white
                    rounded-[32px]
                    p-8
                    reveal
                "
            >

                <div
                    class="
                        flex
                        justify-between
                        items-center
                    "
                >

                    <span
                        class="
                            text-5xl
                            font-bold
                            text-agricolus/30
                        "
                    >
                        03
                    </span>


                    <div
                        class="
                            w-14
                            h-14
                            rounded-2xl
                            bg-softgreen
                            flex
                            items-center
                            justify-center
                            text-agricolus
                        "
                    >

                        <svg
                            width="28"
                            height="28"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            viewBox="0 0 24 24"
                        >

                            <path
                                d="M5 12l4 4L19 6"
                            />

                        </svg>

                    </div>

                </div>


                <h3
                    class="
                        mt-8
                        text-2xl
                        font-bold
                    "
                >
                    Decide
                </h3>


                <p
                    class="
                        mt-3
                        text-textgray
                        leading-7
                    "
                >
                    Utiliza la información para actuar
                    de forma más precisa.
                </p>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     ACADEMY
====================================================== -->

<section
    id="academy"
    class="
        py-28
        bg-white
    "
>

    <div
        class="
            max-w-7xl
            mx-auto
            px-6
            lg:px-10
        "
    >

        <div
            class="
                grid
                lg:grid-cols-2
                gap-16
                items-center
            "
        >

            <div
                class="reveal"
            >

                <span
                    class="
                        text-agricolus
                        uppercase
                        tracking-[3px]
                        text-sm
                        font-bold
                    "
                >
                    Agricolus Academy
                </span>


                <h2
                    class="
                        mt-5
                        text-4xl
                        md:text-5xl
                        font-bold
                    "
                >

                    Aprende a sacar
                    todo el partido
                    a la tecnología agrícola

                </h2>


                <p
                    class="
                        mt-6
                        text-lg
                        text-textgray
                        leading-8
                    "
                >

                    Formación, contenidos y recursos para
                    profesionales que quieren avanzar hacia
                    una agricultura más digital y precisa.

                </p>


                <a
                    href="<?= $base_url ?>academy.php"
                    class="
                        mt-8
                        inline-flex
                        bg-agricolus
                        hover:bg-agricolusDark
                        text-white
                        px-7
                        py-4
                        rounded-full
                        font-semibold
                        transition
                    "
                >
                    Visita Academy
                </a>

            </div>



            <div
                class="
                    relative
                    reveal
                "
            >

                <img
                    src="https://images.unsplash.com/photo-1530267981375-f0de937f5f13?auto=format&fit=crop&w=1200&q=85"
                    alt="Agricolus Academy"
                    class="
                        w-full
                        h-[500px]
                        object-cover
                        rounded-[45px]
                    "
                >


                <div
                    class="
                        absolute
                        right-5
                        bottom-5
                        bg-white
                        rounded-[24px]
                        px-6
                        py-5
                        shadow-card
                    "
                >

                    <p
                        class="
                            text-xs
                            uppercase
                            tracking-wider
                            text-agricolus
                            font-bold
                        "
                    >
                        Formación
                    </p>


                    <p
                        class="
                            mt-1
                            font-bold
                            text-lg
                        "
                    >
                        Aprende. Aplica. Mejora.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     SUSTAINABILITY
====================================================== -->

<section
    id="sostenibilidad"
    class="
        py-24
        bg-cream
    "
>

    <div
        class="
            max-w-7xl
            mx-auto
            px-6
            lg:px-10
        "
    >

        <div
            class="
                max-w-3xl
                reveal
            "
        >

            <span
                class="
                    text-agricolus
                    uppercase
                    tracking-[3px]
                    text-sm
                    font-bold
                "
            >
                Sostenibilidad
            </span>


            <h2
                class="
                    mt-4
                    text-4xl
                    md:text-5xl
                    font-bold
                "
            >

                Tecnología para una
                agricultura más sostenible

            </h2>


            <p
                class="
                    mt-6
                    text-lg
                    text-textgray
                    leading-8
                "
            >

                La digitalización puede ayudar a optimizar
                recursos, reducir desperdicios y mejorar
                la trazabilidad de las actividades agrícolas.

            </p>

        </div>



        <div
            class="
                mt-14
                grid
                lg:grid-cols-3
                gap-6
            "
        >


            <div
                class="
                    bg-white
                    rounded-[30px]
                    p-8
                    reveal
                "
            >

                <div
                    class="
                        text-4xl
                        text-agricolus
                    "
                >
                    💧
                </div>


                <h3
                    class="
                        mt-6
                        text-xl
                        font-bold
                    "
                >
                    Optimización del agua
                </h3>


                <p
                    class="
                        mt-3
                        text-textgray
                        leading-7
                    "
                >
                    Mejora la planificación del riego
                    utilizando datos del cultivo y del clima.
                </p>

            </div>



            <div
                class="
                    bg-darkgreen
                    text-white
                    rounded-[30px]
                    p-8
                    reveal
                "
            >

                <div
                    class="
                        text-4xl
                    "
                >
                    🌱
                </div>


                <h3
                    class="
                        mt-6
                        text-xl
                        font-bold
                    "
                >
                    Uso eficiente de recursos
                </h3>


                <p
                    class="
                        mt-3
                        text-white/60
                        leading-7
                    "
                >
                    Apoya decisiones más precisas
                    sobre tratamientos y operaciones.
                </p>

            </div>



            <div
                class="
                    bg-white
                    rounded-[30px]
                    p-8
                    reveal
                "
            >

                <div
                    class="
                        text-4xl
                        text-agricolus
                    "
                >
                    📊
                </div>


                <h3
                    class="
                        mt-6
                        text-xl
                        font-bold
                    "
                >
                    Trazabilidad
                </h3>


                <p
                    class="
                        mt-3
                        text-textgray
                        leading-7
                    "
                >
                    Registra y consulta la información
                    de las actividades agrícolas.
                </p>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     COMPANY
====================================================== -->

<section
    id="empresa"
    class="
        py-28
        bg-white
    "
>

    <div
        class="
            max-w-7xl
            mx-auto
            px-6
            lg:px-10
        "
    >

        <div
            class="
                grid
                lg:grid-cols-2
                gap-16
                items-center
            "
        >


            <!-- TEXT -->

            <div
                class="reveal"
            >

                <span
                    class="
                        text-agricolus
                        uppercase
                        tracking-[3px]
                        text-sm
                        font-bold
                    "
                >
                    Agricolus
                </span>


                <h2
                    class="
                        mt-5
                        text-4xl
                        md:text-5xl
                        font-bold
                    "
                >

                    Tecnología nacida
                    para el campo

                </h2>


                <p
                    class="
                        mt-6
                        text-lg
                        text-textgray
                        leading-8
                    "
                >

                    Combinamos agronomía, tecnología y
                    experiencia para desarrollar herramientas
                    digitales pensadas para las necesidades
                    reales del sector agroalimentario.

                </p>


                <div
                    class="
                        mt-8
                        grid
                        grid-cols-2
                        gap-5
                    "
                >

                    <div>

                        <p
                            class="
                                text-4xl
                                font-bold
                                text-agricolus
                            "
                        >
                            4.0
                        </p>

                        <p
                            class="
                                mt-1
                                text-sm
                                text-textgray
                            "
                        >
                            Agricultura digital
                        </p>

                    </div>


                    <div>

                        <p
                            class="
                                text-4xl
                                font-bold
                                text-agricolus
                            "
                        >
                            Global
                        </p>

                        <p
                            class="
                                mt-1
                                text-sm
                                text-textgray
                            "
                        >
                            Red internacional
                        </p>

                    </div>

                </div>


                <a
                    href="<?= $base_url ?>quieneSomos/empresa.php"
                    class="
                        mt-9
                        inline-flex
                        border
                        border-darkgreen
                        hover:bg-darkgreen
                        hover:text-white
                        px-7
                        py-4
                        rounded-full
                        font-semibold
                        transition
                    "
                >
                    Conoce Agricolus
                </a>

            </div>



            <!-- IMAGE -->

            <div
                class="reveal"
            >

                <img
                    src="https://images.unsplash.com/photo-1523742815505-3c34f0c9c5f5?auto=format&fit=crop&w=1200&q=85"
                    alt="Agricultura"
                    class="
                        w-full
                        h-[560px]
                        object-cover
                        rounded-[45px]
                    "
                >

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     MARQUEE
====================================================== -->

<section
    class="
        bg-agricolus
        py-7
        marquee
    "
>

    <div
        class="
            marquee-track
        "
    >

        <div
            class="
                flex
                items-center
                gap-10
                px-5
                text-white
                text-2xl
                md:text-3xl
                font-bold
            "
        >

            <span>
                MAKING AGRITECH SUSTAINABLE
            </span>

            <span class="text-white/40">
                ✦
            </span>

            <span>
                AGRICULTURE 4.0
            </span>

            <span class="text-white/40">
                ✦
            </span>

            <span>
                DIGITAL AGRICULTURE
            </span>

            <span class="text-white/40">
                ✦
            </span>

        </div>


        <div
            class="
                flex
                items-center
                gap-10
                px-5
                text-white
                text-2xl
                md:text-3xl
                font-bold
            "
        >

            <span>
                MAKING AGRITECH SUSTAINABLE
            </span>

            <span class="text-white/40">
                ✦
            </span>

            <span>
                AGRICULTURE 4.0
            </span>

            <span class="text-white/40">
                ✦
            </span>

            <span>
                DIGITAL AGRICULTURE
            </span>

        </div>

    </div>

</section>



<!-- =====================================================
     CTA
====================================================== -->

<section
    id="contacto"
    class="
        py-28
        bg-darkgreen
        text-white
    "
>

    <div
        class="
            max-w-5xl
            mx-auto
            px-6
            text-center
            reveal
        "
    >

        <span
            class="
                text-[#9BD44B]
                uppercase
                tracking-[3px]
                text-sm
                font-bold
            "
        >
            Empieza ahora
        </span>


        <h2
            class="
                mt-5
                text-4xl
                md:text-6xl
                font-bold
                leading-tight
            "
        >

            Lleva tu agricultura
            al siguiente nivel

        </h2>


        <p
            class="
                mt-6
                max-w-2xl
                mx-auto
                text-lg
                text-white/60
                leading-8
            "
        >

            Descubre cómo Agricolus puede ayudarte
            a digitalizar y mejorar la gestión de
            tus explotaciones agrícolas.

        </p>


        <div
            class="
                mt-9
                flex
                flex-wrap
                justify-center
                gap-4
            "
        >

            <a
                href="<?= $base_url ?>contacto.php"
                class="
                    bg-agricolus
                    hover:bg-[#8ACA38]
                    text-white
                    px-8
                    py-4
                    rounded-full
                    font-semibold
                    transition
                "
            >
                Solicita una demo
            </a>


            <a
                href="<?= $base_url ?>contacto.php"
                class="
                    border
                    border-white/30
                    hover:bg-white
                    hover:text-darkgreen
                    px-8
                    py-4
                    rounded-full
                    font-semibold
                    transition
                "
            >
                Contacta con nosotros
            </a>

        </div>

    </div>

</section>



<!-- =====================================================
     NEWSLETTER
====================================================== -->

<section
    x-data="{
        submitted:false,
        email:''
    }"
    class="
        py-24
        bg-softgreen
    "
>

    <div
        class="
            max-w-4xl
            mx-auto
            px-6
            text-center
        "
    >

        <div
            class="
                w-14
                h-14
                bg-agricolus
                rounded-full
                mx-auto
                flex
                items-center
                justify-center
            "
        >

            <span
                class="
                    text-white
                    font-black
                    text-xl
                "
            >
                A
            </span>

        </div>


        <h2
            class="
                mt-6
                text-4xl
                md:text-5xl
                font-bold
            "
        >

            Mantente al día
            con la agricultura digital

        </h2>


        <p
            class="
                mt-5
                text-lg
                text-textgray
            "
        >

            Noticias, innovación, tecnología
            y contenidos agronómicos.

        </p>



        <form
            @submit.prevent="submitted=true"
            class="
                mt-8
                max-w-xl
                mx-auto
            "
        >

            <div
                x-show="!submitted"
                class="
                    flex
                    flex-col
                    sm:flex-row
                    gap-3
                "
            >

                <input
                    type="email"
                    required
                    x-model="email"
                    placeholder="Tu correo electrónico"
                    class="
                        flex-1
                        px-6
                        py-4
                        rounded-full
                        border
                        border-gray-200
                        bg-white
                        outline-none
                        focus:border-agricolus
                    "
                >


                <button
                    type="submit"
                    class="
                        bg-darkgreen
                        hover:bg-[#245337]
                        text-white
                        px-7
                        py-4
                        rounded-full
                        font-semibold
                    "
                >
                    Suscribirme
                </button>

            </div>


            <div
                x-show="submitted"
                x-transition
                class="
                    bg-white
                    rounded-2xl
                    p-6
                "
            >

                <div
                    class="
                        text-agricolus
                        text-3xl
                    "
                >
                    ✓
                </div>


                <p
                    class="
                        mt-2
                        font-semibold
                    "
                >
                    ¡Gracias por suscribirte!
                </p>

            </div>

        </form>


        <label
            x-show="!submitted"
            class="
                mt-5
                flex
                items-center
                justify-center
                gap-3
                text-xs
                text-textgray
            "
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

<?php
include __DIR__ . '/includes/footer.php';
?>

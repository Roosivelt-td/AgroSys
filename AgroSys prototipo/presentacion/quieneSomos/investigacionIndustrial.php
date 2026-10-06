<?php
$page_title = "Investigación industrial | Agricolus";
$base_url = "../";
$current_page = "empresa";
$body_class = "bg-white text-darkgreen";

$page_styles = <<<'CSS'
        .hero-grid {
            background-image:
                linear-gradient(rgba(23,59,39,.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(23,59,39,.045) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity .75s ease, transform .75s ease;
        }

        .reveal.show {
            opacity: 1;
            transform: translateY(0);
        }

        .project-card {
            transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
        }

        .project-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 25px 60px rgba(23,59,39,.10);
            border-color: rgba(120,184,42,.45);
        }

        .project-card img {
            transition: transform .7s ease;
        }

        .project-card:hover img {
            transform: scale(1.06);
        }

        .area-card {
            transition: transform .3s ease, background .3s ease;
        }

        .area-card:hover {
            transform: translateY(-6px);
        }

        .marquee {
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-track {
            display: inline-flex;
            animation: marquee 30s linear infinite;
        }

        @keyframes marquee {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        .floating {
            animation: floating 5s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .hero-image {
            border-radius: 200px 35px 35px 35px;
        }
CSS;

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/header.php';
?>

<!-- =========================================================
HERO
========================================================= -->

<section
    class="
        pt-[82px]
        bg-softgreen
        hero-grid
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
                min-h-[650px]
                grid
                lg:grid-cols-2
                gap-14
                items-center
            "
        >

            <!-- TEXT -->

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
                            uppercase
                            tracking-[2px]
                            font-bold
                        "
                    >
                        Investigación industrial
                    </span>

                </div>


                <h1
                    class="
                        mt-7
                        text-5xl
                        md:text-6xl
                        lg:text-[68px]
                        leading-[.98]
                        tracking-[-3px]
                        font-bold
                    "
                >

                    Investigación para
                    una agricultura

                    <span
                        class="
                            text-agricolus
                        "
                    >
                        más inteligente
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
                    Desarrollamos nuevas funciones y soluciones
                    para responder a las necesidades de la
                    Agricultura 4.0 y de todos los actores de la
                    cadena agroalimentaria.
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
                        href="#areas"
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
                        Nuestras áreas
                    </a>


                    <a
                        href="#proyectos"
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
                        Ver proyectos
                    </a>

                </div>

            </div>



            <!-- IMAGE -->

            <div
                class="
                    relative
                    min-h-[570px]
                    flex
                    items-center
                    justify-center
                    reveal
                "
            >

                <div
                    class="
                        absolute
                        w-[470px]
                        h-[470px]
                        bg-[#DCEBC9]
                        rounded-full
                        right-[-90px]
                        bottom-0
                    "
                ></div>


                <div
                    class="
                        absolute
                        w-28
                        h-28
                        border-[15px]
                        border-agricolus/20
                        rounded-full
                        left-2
                        top-20
                    "
                ></div>


                <div
                    class="
                        relative
                        z-10
                        w-full
                        max-w-[570px]
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1530267981375-f0de937f5f13?auto=format&fit=crop&w=1200&q=85"
                        alt="Investigación agrícola"
                        class="
                            hero-image
                            w-full
                            h-[520px]
                            object-cover
                            shadow-2xl
                        "
                    >


                    <!-- FLOAT CARD -->

                    <div
                        class="
                            floating
                            absolute
                            left-6
                            bottom-6
                            bg-white
                            rounded-[22px]
                            px-6
                            py-5
                            shadow-xl
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
                                    rounded-full
                                    bg-softgreen
                                    flex
                                    items-center
                                    justify-center
                                    text-agricolus
                                "
                            >

                                <svg
                                    width="26"
                                    height="26"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        d="M12 3v18M3 12h18"
                                    />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="7"
                                    />

                                </svg>

                            </div>


                            <div>

                                <p
                                    class="
                                        font-bold
                                        text-darkgreen
                                    "
                                >
                                    Agricultura 4.0
                                </p>

                                <p
                                    class="
                                        text-sm
                                        text-textgray
                                    "
                                >
                                    Innovación aplicada
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

<section
    class="
        py-24
        bg-white
    "
>

    <div
        class="
            max-w-4xl
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
            Investigación & innovación
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
            Convertimos la investigación
            en soluciones para el campo
        </h2>


        <p
            class="
                mt-6
                text-lg
                md:text-xl
                text-textgray
                leading-8
            "
        >
            Agricolus participa como socio tecnológico en
            proyectos nacionales e internacionales orientados
            al desarrollo de nuevas tecnologías para la
            agricultura digital.
        </p>


        <div
            class="
                mt-8
                flex
                flex-wrap
                justify-center
                gap-3
            "
        >

            <span
                class="
                    px-5
                    py-3
                    rounded-full
                    bg-softgreen
                    text-sm
                    font-semibold
                "
            >
                Horizon Europe
            </span>


            <span
                class="
                    px-5
                    py-3
                    rounded-full
                    bg-softgreen
                    text-sm
                    font-semibold
                "
            >
                EIT Food
            </span>


            <span
                class="
                    px-5
                    py-3
                    rounded-full
                    bg-softgreen
                    text-sm
                    font-semibold
                "
            >
                Fiware
            </span>


            <span
                class="
                    px-5
                    py-3
                    rounded-full
                    bg-softgreen
                    text-sm
                    font-semibold
                "
            >
                Organizaciones europeas
            </span>

        </div>

    </div>

</section>



<!-- =========================================================
3 AREAS
========================================================= -->

<section
    id="areas"
    class="
        py-24
        bg-palegreen
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
                Áreas de investigación
            </span>


            <h2
                class="
                    mt-4
                    text-4xl
                    md:text-5xl
                    font-bold
                "
            >
                Tres áreas para impulsar
                la innovación agrícola
            </h2>

        </div>



        <div
            class="
                grid
                md:grid-cols-3
                gap-6
            "
        >


            <!-- AREA 1 -->

            <article
                class="
                    area-card
                    bg-white
                    rounded-[32px]
                    p-8
                    border
                    border-gray-100
                    reveal
                "
            >

                <div
                    class="
                        w-16
                        h-16
                        bg-softgreen
                        rounded-2xl
                        flex
                        items-center
                        justify-center
                        text-agricolus
                    "
                >

                    <svg
                        width="32"
                        height="32"
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

                        <circle
                            cx="6"
                            cy="15"
                            r="1"
                        />

                        <circle
                            cx="10"
                            cy="10"
                            r="1"
                        />

                        <circle
                            cx="13"
                            cy="13"
                            r="1"
                        />

                        <circle
                            cx="18"
                            cy="6"
                            r="1"
                        />

                    </svg>

                </div>


                <p
                    class="
                        mt-8
                        text-xs
                        font-bold
                        tracking-[2px]
                        text-agricolus
                    "
                >
                    01
                </p>


                <h3
                    class="
                        mt-2
                        text-2xl
                        font-bold
                    "
                >
                    Modelos de predicción
                </h3>


                <p
                    class="
                        mt-4
                        text-textgray
                        leading-7
                    "
                >
                    Desarrollo de modelos capaces de anticipar
                    situaciones agronómicas y apoyar las decisiones
                    de agricultores y técnicos.
                </p>

            </article>



            <!-- AREA 2 -->

            <article
                class="
                    area-card
                    bg-darkgreen
                    text-white
                    rounded-[32px]
                    p-8
                    reveal
                "
            >

                <div
                    class="
                        w-16
                        h-16
                        bg-white/10
                        rounded-2xl
                        flex
                        items-center
                        justify-center
                        text-[#9BD44B]
                    "
                >

                    <svg
                        width="32"
                        height="32"
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
                            d="M4 12h16"
                        />

                        <path
                            d="M12 4c2 2 3 5 3 8s-1 6-3 8c-2-2-3-5-3-8s1-6 3-8Z"
                        />

                    </svg>

                </div>


                <p
                    class="
                        mt-8
                        text-xs
                        font-bold
                        tracking-[2px]
                        text-[#9BD44B]
                    "
                >
                    02
                </p>


                <h3
                    class="
                        mt-2
                        text-2xl
                        font-bold
                    "
                >
                    Observación de la Tierra
                </h3>


                <p
                    class="
                        mt-4
                        text-white/60
                        leading-7
                    "
                >
                    Utilización de teledetección e imágenes
                    satelitales para observar y analizar
                    las condiciones de los cultivos.
                </p>

            </article>



            <!-- AREA 3 -->

            <article
                class="
                    area-card
                    bg-white
                    rounded-[32px]
                    p-8
                    border
                    border-gray-100
                    reveal
                "
            >

                <div
                    class="
                        w-16
                        h-16
                        bg-softgreen
                        rounded-2xl
                        flex
                        items-center
                        justify-center
                        text-agricolus
                    "
                >

                    <svg
                        width="32"
                        height="32"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        viewBox="0 0 24 24"
                    >

                        <path
                            d="M12 3v18"
                        />

                        <path
                            d="M5 12c3-4 6-4 7 0"
                        />

                        <path
                            d="M19 12c-3-4-6-4-7 0"
                        />

                        <path
                            d="M6 18c2-2 4-2 6 0"
                        />

                        <path
                            d="M18 18c-2-2-4-2-6 0"
                        />

                    </svg>

                </div>


                <p
                    class="
                        mt-8
                        text-xs
                        font-bold
                        tracking-[2px]
                        text-agricolus
                    "
                >
                    03
                </p>


                <h3
                    class="
                        mt-2
                        text-2xl
                        font-bold
                    "
                >
                    Sostenibilidad
                </h3>


                <p
                    class="
                        mt-4
                        text-textgray
                        leading-7
                    "
                >
                    Investigación orientada a una gestión
                    más sostenible de los recursos y a una
                    cadena agroalimentaria más transparente.
                </p>

            </article>

        </div>

    </div>

</section>



<!-- =========================================================
PROJECTS INTRO
========================================================= -->

<section
    id="proyectos"
    class="
        py-24
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
                Proyectos internacionales
            </span>


            <h2
                class="
                    mt-4
                    text-4xl
                    md:text-5xl
                    font-bold
                "
            >
                Investigación aplicada
                a problemas reales
            </h2>


            <p
                class="
                    mt-6
                    text-lg
                    text-textgray
                    leading-8
                "
            >
                Participamos en proyectos que combinan
                investigación, datos, inteligencia artificial,
                teledetección y sostenibilidad para crear
                nuevas herramientas para el sector agrícola.
            </p>

        </div>

    </div>

</section>



<!-- =========================================================
OBSERVACIÓN DE LA TIERRA
========================================================= -->

<section
    class="
        pb-24
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
                flex
                items-center
                gap-4
                mb-10
            "
        >

            <span
                class="
                    w-3
                    h-3
                    rounded-full
                    bg-agricolus
                "
            ></span>


            <h2
                class="
                    text-3xl
                    font-bold
                "
            >
                Observación de la Tierra
            </h2>

        </div>


        <div
            class="
                grid
                md:grid-cols-2
                gap-6
            "
        >


            <!-- AGRITRACK -->

            <article
                class="
                    project-card
                    bg-palegreen
                    rounded-[32px]
                    overflow-hidden
                    border
                    border-gray-100
                    reveal
                "
            >

                <div
                    class="
                        h-[280px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1586771107445-d3ca888129ff?auto=format&fit=crop&w=1200&q=85"
                        alt="AgriTrack FullDSS Stack"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                    >

                </div>


                <div class="p-8">

                    <span
                        class="
                            text-xs
                            uppercase
                            tracking-[2px]
                            text-agricolus
                            font-bold
                        "
                    >
                        Observación de la Tierra
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        AgriTrack FullDSS Stack
                    </h3>


                    <p
                        class="
                            mt-4
                            text-textgray
                            leading-7
                        "
                    >
                        Desarrollo de un sistema avanzado de
                        soporte a la decisión capaz de combinar
                        modelos e información histórica procedente
                        de la teledetección satelital.
                    </p>


                    <div
                        class="
                            mt-6
                            inline-flex
                            px-4
                            py-2
                            bg-white
                            rounded-full
                            text-xs
                            font-bold
                        "
                    >
                        ESA Incubed
                    </div>

                </div>

            </article>



            <!-- METEO -->

            <article
                class="
                    project-card
                    bg-palegreen
                    rounded-[32px]
                    overflow-hidden
                    border
                    border-gray-100
                    reveal
                "
            >

                <div
                    class="
                        h-[280px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1504608524841-42fe6f032b4b?auto=format&fit=crop&w=1200&q=85"
                        alt="Meteo Map"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                    >

                </div>


                <div class="p-8">

                    <span
                        class="
                            text-xs
                            uppercase
                            tracking-[2px]
                            text-agricolus
                            font-bold
                        "
                    >
                        Observación de la Tierra
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        Meteo_Map
                    </h3>


                    <p
                        class="
                            mt-4
                            text-textgray
                            leading-7
                        "
                    >
                        Herramienta para visualizar datos
                        meteorológicos en tiempo real,
                        espacializando la información a
                        diferentes escalas.
                    </p>


                    <div
                        class="
                            mt-6
                            inline-flex
                            px-4
                            py-2
                            bg-white
                            rounded-full
                            text-xs
                            font-bold
                        "
                    >
                        NextGenerationEU
                    </div>

                </div>

            </article>

        </div>

    </div>

</section>



<!-- =========================================================
MODELOS DE PREDICCIÓN
========================================================= -->

<section
    class="
        py-24
        bg-palegreen
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
                flex
                items-center
                gap-4
                mb-10
            "
        >

            <span
                class="
                    w-3
                    h-3
                    rounded-full
                    bg-agricolus
                "
            ></span>


            <h2
                class="
                    text-3xl
                    font-bold
                "
            >
                Modelos de predicción
            </h2>

        </div>


        <div
            class="
                grid
                md:grid-cols-2
                gap-6
            "
        >


            <!-- CLEVER -->

            <article
                class="
                    project-card
                    bg-white
                    rounded-[32px]
                    overflow-hidden
                    border
                    border-gray-100
                    reveal
                "
            >

                <div
                    class="
                        h-[280px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1200&q=85"
                        alt="CLEVER"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                    >

                </div>


                <div class="p-8">

                    <span
                        class="
                            text-xs
                            uppercase
                            tracking-[2px]
                            text-agricolus
                            font-bold
                        "
                    >
                        Modelos de predicción
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        CLEVER
                    </h3>


                    <p
                        class="
                            mt-4
                            text-textgray
                            leading-7
                        "
                    >
                        Sistema de soporte a la decisión para
                        la recolección inteligente de naranjas,
                        utilizando reconocimiento de imágenes
                        mediante inteligencia artificial y
                        computación periférica.
                    </p>


                    <div
                        class="
                            mt-6
                            inline-flex
                            px-4
                            py-2
                            bg-softgreen
                            rounded-full
                            text-xs
                            font-bold
                        "
                    >
                        Horizon-KDT-JU
                    </div>

                </div>

            </article>



            <!-- VALPRO -->

            <article
                class="
                    project-card
                    bg-white
                    rounded-[32px]
                    overflow-hidden
                    border
                    border-gray-100
                    reveal
                "
            >

                <div
                    class="
                        h-[280px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1495107334309-fcf20504a5ab?auto=format&fit=crop&w=1200&q=85"
                        alt="VALPRO"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                    >

                </div>


                <div class="p-8">

                    <span
                        class="
                            text-xs
                            uppercase
                            tracking-[2px]
                            text-agricolus
                            font-bold
                        "
                    >
                        Modelos de predicción
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        VALPRO
                    </h3>


                    <p
                        class="
                            mt-4
                            text-textgray
                            leading-7
                        "
                    >
                        Proyecto orientado a mejorar la producción
                        de proteínas vegetales para alimentos y
                        piensos mediante nuevas innovaciones
                        y laboratorios vivientes.
                    </p>


                    <div
                        class="
                            mt-6
                            inline-flex
                            px-4
                            py-2
                            bg-softgreen
                            rounded-full
                            text-xs
                            font-bold
                        "
                    >
                        Horizon Europe
                    </div>

                </div>

            </article>

        </div>

    </div>

</section>



<!-- =========================================================
SOSTENIBILIDAD
========================================================= -->

<section
    class="
        py-24
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
                flex
                items-center
                gap-4
                mb-10
            "
        >

            <span
                class="
                    w-3
                    h-3
                    rounded-full
                    bg-agricolus
                "
            ></span>


            <h2
                class="
                    text-3xl
                    font-bold
                "
            >
                Sostenibilidad
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


            <!-- TITAN -->

            <article
                class="
                    project-card
                    bg-palegreen
                    rounded-[30px]
                    overflow-hidden
                    border
                    border-gray-100
                    reveal
                "
            >

                <div
                    class="
                        h-[230px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1492496913980-501348b61469?auto=format&fit=crop&w=900&q=85"
                        alt="TITAN"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                    >

                </div>


                <div class="p-7">

                    <span
                        class="
                            text-xs
                            text-agricolus
                            font-bold
                            tracking-[2px]
                            uppercase
                        "
                    >
                        Sostenibilidad
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        TITAN
                    </h3>


                    <p
                        class="
                            mt-4
                            text-sm
                            text-textgray
                            leading-6
                        "
                    >
                        Soluciones para aumentar la transparencia
                        de la cadena alimentaria y mejorar la
                        trazabilidad, sostenibilidad y seguridad.
                    </p>

                </div>

            </article>



            <!-- CEBUS -->

            <article
                class="
                    project-card
                    bg-palegreen
                    rounded-[30px]
                    overflow-hidden
                    border
                    border-gray-100
                    reveal
                "
            >

                <div
                    class="
                        h-[230px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&w=900&q=85"
                        alt="CEBUS"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                    >

                </div>


                <div class="p-7">

                    <span
                        class="
                            text-xs
                            text-agricolus
                            font-bold
                            tracking-[2px]
                            uppercase
                        "
                    >
                        Sostenibilidad
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        CEBUS
                    </h3>


                    <p
                        class="
                            mt-4
                            text-sm
                            text-textgray
                            leading-6
                        "
                    >
                        Modelo de balance húmico para estudiar
                        la variación de materia orgánica del suelo
                        y favorecer una gestión agrícola sostenible.
                    </p>

                </div>

            </article>



            <!-- AFCIC -->

            <article
                class="
                    project-card
                    bg-palegreen
                    rounded-[30px]
                    overflow-hidden
                    border
                    border-gray-100
                    reveal
                "
            >

                <div
                    class="
                        h-[230px]
                        overflow-hidden
                    "
                >

                    <img
                        src="https://images.unsplash.com/photo-1533130061792-64b345e4a833?auto=format&fit=crop&w=900&q=85"
                        alt="Action for Children in Conflict"
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                    >

                </div>


                <div class="p-7">

                    <span
                        class="
                            text-xs
                            text-agricolus
                            font-bold
                            tracking-[2px]
                            uppercase
                        "
                    >
                        Sostenibilidad
                    </span>


                    <h3
                        class="
                            mt-3
                            text-2xl
                            font-bold
                        "
                    >
                        Action for Children in Conflict
                    </h3>


                    <p
                        class="
                            mt-4
                            text-sm
                            text-textgray
                            leading-6
                        "
                    >
                        Proyecto desarrollado en Kenia para
                        aplicar herramientas AgriTech en
                        la agricultura rural.
                    </p>

                </div>

            </article>

        </div>

    </div>

</section>



<!-- =========================================================
PROCESS
========================================================= -->

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
                grid
                lg:grid-cols-2
                gap-16
                items-center
            "
        >


            <!-- LEFT -->

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
                    Del laboratorio al campo
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
                    Investigación que
                    se convierte en innovación
                </h2>


                <p
                    class="
                        mt-6
                        text-lg
                        text-textgray
                        leading-8
                    "
                >
                    La investigación industrial conecta
                    conocimientos científicos, tecnologías
                    digitales y necesidades reales del sector
                    agroalimentario.
                </p>


                <a
                    href="<?= $base_url ?>contacto.php"
                    class="
                        mt-8
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
                    Colabora con nosotros
                </a>

            </div>



            <!-- RIGHT -->

            <div
                class="space-y-5 reveal"
            >


                <!-- STEP -->

                <div
                    class="
                        bg-white
                        rounded-[25px]
                        p-6
                        flex
                        gap-5
                        items-start
                        shadow-sm
                    "
                >

                    <div
                        class="
                            flex-shrink-0
                            w-12
                            h-12
                            rounded-full
                            bg-softgreen
                            text-agricolus
                            flex
                            items-center
                            justify-center
                            font-bold
                        "
                    >
                        01
                    </div>


                    <div>

                        <h3
                            class="
                                text-xl
                                font-bold
                            "
                        >
                            Investigar
                        </h3>

                        <p
                            class="
                                mt-2
                                text-sm
                                text-textgray
                                leading-6
                            "
                        >
                            Identificamos nuevos retos y
                            oportunidades tecnológicas.
                        </p>

                    </div>

                </div>



                <!-- STEP -->

                <div
                    class="
                        bg-white
                        rounded-[25px]
                        p-6
                        flex
                        gap-5
                        items-start
                        shadow-sm
                    "
                >

                    <div
                        class="
                            flex-shrink-0
                            w-12
                            h-12
                            rounded-full
                            bg-softgreen
                            text-agricolus
                            flex
                            items-center
                            justify-center
                            font-bold
                        "
                    >
                        02
                    </div>


                    <div>

                        <h3
                            class="
                                text-xl
                                font-bold
                            "
                        >
                            Desarrollar
                        </h3>

                        <p
                            class="
                                mt-2
                                text-sm
                                text-textgray
                                leading-6
                            "
                        >
                            Convertimos los resultados de
                            investigación en herramientas digitales.
                        </p>

                    </div>

                </div>



                <!-- STEP -->

                <div
                    class="
                        bg-white
                        rounded-[25px]
                        p-6
                        flex
                        gap-5
                        items-start
                        shadow-sm
                    "
                >

                    <div
                        class="
                            flex-shrink-0
                            w-12
                            h-12
                            rounded-full
                            bg-softgreen
                            text-agricolus
                            flex
                            items-center
                            justify-center
                            font-bold
                        "
                    >
                        03
                    </div>


                    <div>

                        <h3
                            class="
                                text-xl
                                font-bold
                            "
                        >
                            Aplicar
                        </h3>

                        <p
                            class="
                                mt-2
                                text-sm
                                text-textgray
                                leading-6
                            "
                        >
                            Llevamos la innovación al terreno
                            para generar soluciones utilizables.
                        </p>

                    </div>

                </div>


            </div>

        </div>

    </div>

</section>



<!-- =========================================================
GREEN MARQUEE
========================================================= -->

<section
    class="
        bg-agricolus
        py-7
        marquee
    "
>

    <div class="marquee-track">


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
                RESEARCH & INNOVATION
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
                RESEARCH & INNOVATION
            </span>

            <span class="text-white/40">
                ✦
            </span>

            <span>
                AGRICULTURE 4.0
            </span>

        </div>

    </div>

</section>



<!-- =========================================================
CTA
========================================================= -->

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
            Investigación y desarrollo
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
            ¿Quieres desarrollar
            el futuro de la agricultura?
        </h2>


        <p
            class="
                mt-6
                text-lg
                md:text-xl
                text-white/60
                leading-8
                max-w-2xl
                mx-auto
            "
        >
            Conectamos empresas, centros de investigación
            y actores del sector agroalimentario para crear
            nuevas soluciones digitales.
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
                Contacta con nosotros
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
                Reserva una demo
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
            ¿Quieres profundizar
            en el mundo de la agricultura de precisión?
        </h2>


        <p
            class="
                mt-5
                text-lg
                text-textgray
            "
        >
            Mantente actualizado sobre innovación,
            tecnología y agricultura digital.
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
include __DIR__ . '/../includes/footer.php';
?>

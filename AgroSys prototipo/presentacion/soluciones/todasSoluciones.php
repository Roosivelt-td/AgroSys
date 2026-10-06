<?php
$page_title = "Soluciones | Agricolus";
$base_url = "../";
$current_page = "soluciones";
$body_class = "bg-white";

$page_styles = <<<'CSS'
        .hero-grid {
            background-image:
                linear-gradient(rgba(23,59,39,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(23,59,39,.05) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        .solution-card {
            transition: all .35s ease;
        }

        .solution-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 22px 50px rgba(23,59,39,.12);
        }

        .crop-card {
            transition: all .3s ease;
        }

        .crop-card:hover {
            transform: translateY(-5px);
        }

        .crop-card img {
            transition: transform .5s ease;
        }

        .crop-card:hover img {
            transform: scale(1.07);
        }
CSS;

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/header.php';
?>

<!-- =========================================================
     HERO
========================================================= -->

<section class="pt-[82px] bg-softgreen overflow-hidden">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="min-h-[600px] grid lg:grid-cols-2 gap-12 items-center">

            <!-- TEXT -->
            <div class="py-20 lg:py-28">

                <div class="inline-flex items-center gap-2 bg-white rounded-full px-4 py-2 mb-7 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-agricolus"></span>
                    <span class="text-sm font-semibold text-darkgreen">
                        SOLUCIONES AGRÍCOLAS
                    </span>
                </div>

                <h1 class="text-5xl md:text-6xl lg:text-[68px] leading-[1.02] font-bold tracking-[-2px] text-darkgreen">
                    Las mejores herramientas digitales
                    <span class="text-agricolus">
                        para tu agricultura
                    </span>
                </h1>

                <p class="mt-7 max-w-xl text-lg md:text-xl leading-8 text-textgray">
                    Todas las herramientas que necesitas para gestionar
                    tu explotación agrícola de forma precisa, sencilla
                    y eficiente, desde una única plataforma.
                </p>

                <div class="mt-9 flex flex-wrap gap-4">

                    <a
                        href="#explotacion"
                        class="bg-darkgreen hover:bg-[#245337] text-white px-7 py-4 rounded-full font-semibold transition"
                    >
                        Descubre las soluciones
                    </a>

                    <a
                        href="<?= $base_url ?>contacto.php"
                        class="border border-darkgreen text-darkgreen hover:bg-darkgreen hover:text-white px-7 py-4 rounded-full font-semibold transition"
                    >
                        Habla con nosotros
                    </a>

                </div>

            </div>


            <!-- IMAGE -->
            <div class="relative h-full min-h-[520px] flex items-end justify-center">

                <div class="absolute w-[470px] h-[470px] rounded-full bg-[#DCEBC9] bottom-[-80px] right-[-40px]"></div>

                <div class="relative z-10 w-full max-w-[580px]">

                    <img
                        src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=85"
                        class="w-full h-[500px] object-cover rounded-t-[180px] rounded-b-[35px] shadow-2xl"
                        alt="Agricultura de precisión"
                    >

                    <div class="absolute left-6 bottom-6 bg-white rounded-2xl p-5 shadow-xl max-w-[230px]">

                        <div class="text-3xl font-bold text-agricolus">
                            01
                        </div>

                        <p class="mt-1 text-sm font-semibold text-darkgreen">
                            Una plataforma para toda tu explotación
                        </p>

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

    <div class="max-w-5xl mx-auto px-6 text-center">

        <span class="text-agricolus uppercase tracking-[3px] text-sm font-bold">
            Una única plataforma
        </span>

        <h2 class="mt-5 text-4xl md:text-5xl font-bold tracking-tight text-darkgreen">
            Elige la solución que mejor se adapte
            a las necesidades de tu explotación
        </h2>

        <p class="mt-6 text-lg text-textgray leading-8 max-w-3xl mx-auto">
            Agricolus reúne herramientas digitales para el seguimiento,
            gestión y análisis de las actividades agrícolas, disponibles
            desde ordenador y smartphone.
        </p>

    </div>

</section>


<!-- =========================================================
     EXPLOTACIÓN AGRÍCOLA
========================================================= -->

<section
    id="explotacion"
    class="py-24 bg-[#F7F9F5]"
>

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="max-w-3xl mb-14">

            <span class="text-agricolus uppercase tracking-[3px] text-sm font-bold">
                Explotación agrícola
            </span>

            <h2 class="mt-4 text-4xl md:text-5xl font-bold text-darkgreen">
                Gestiona cada aspecto de tu explotación
            </h2>

            <p class="mt-5 text-lg text-textgray leading-8">
                Herramientas para monitorizar los cultivos, gestionar
                los recursos y tomar decisiones basadas en datos.
            </p>

        </div>


        <!-- MAIN SOLUTIONS -->

        <div class="grid md:grid-cols-3 gap-7">

            <!-- SATELLITE -->
            <article class="solution-card bg-white rounded-[28px] overflow-hidden">

                <div class="h-64 overflow-hidden">

                    <img
                        src="https://images.unsplash.com/photo-1574943320219-553eb213f72d?auto=format&fit=crop&w=900&q=80"
                        class="w-full h-full object-cover"
                        alt="Monitorización satelital"
                    >

                </div>

                <div class="p-8">

                    <span class="text-xs uppercase tracking-[2px] text-agricolus font-bold">
                        Módulo
                    </span>

                    <h3 class="mt-3 text-2xl font-bold text-darkgreen">
                        Satélite
                    </h3>

                    <p class="mt-4 text-textgray leading-7">
                        Monitorización satelital para conocer el estado
                        de los cultivos e identificar zonas críticas.
                    </p>

                    <a
                        href="<?= $base_url ?>soluciones/monitoreoSatelital.php"
                        class="inline-flex mt-7 items-center gap-2 font-semibold text-darkgreen hover:text-agricolus"
                    >
                        Descubrir
                        <span>→</span>
                    </a>

                </div>

            </article>


            <!-- IRRIGATION -->
            <article class="solution-card bg-white rounded-[28px] overflow-hidden">

                <div class="h-64 overflow-hidden">

                    <img
                        src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80"
                        class="w-full h-full object-cover"
                        alt="Riego y nutrición"
                    >

                </div>

                <div class="p-8">

                    <span class="text-xs uppercase tracking-[2px] text-agricolus font-bold">
                        Módulo
                    </span>

                    <h3 class="mt-3 text-2xl font-bold text-darkgreen">
                        Riego y Nutrición
                    </h3>

                    <p class="mt-4 text-textgray leading-7">
                        Optimiza el agua y los nutrientes aplicando
                        los insumos según las necesidades reales.
                    </p>

                    <a
                        href="<?= $base_url ?>soluciones/gestionAgronomica.php"
                        class="inline-flex mt-7 items-center gap-2 font-semibold text-darkgreen hover:text-agricolus"
                    >
                        Descubrir
                        <span>→</span>
                    </a>

                </div>

            </article>


            <!-- DEFENSE -->
            <article class="solution-card bg-white rounded-[28px] overflow-hidden">

                <div class="h-64 overflow-hidden">

                    <img
                        src="https://images.unsplash.com/photo-1592982537447-6f2a6a0a7c9a?auto=format&fit=crop&w=900&q=80"
                        class="w-full h-full object-cover"
                        alt="Defensa de cultivos"
                    >

                </div>

                <div class="p-8">

                    <span class="text-xs uppercase tracking-[2px] text-agricolus font-bold">
                        Módulo
                    </span>

                    <h3 class="mt-3 text-2xl font-bold text-darkgreen">
                        Defensa
                    </h3>

                    <p class="mt-4 text-textgray leading-7">
                        Sistemas de apoyo a la decisión para la defensa
                        y protección de cultivos.
                    </p>

                    <a
                        href="<?= $base_url ?>soluciones/defensaCultivos.php"
                        class="inline-flex mt-7 items-center gap-2 font-semibold text-darkgreen hover:text-agricolus"
                    >
                        Descubrir
                        <span>→</span>
                    </a>

                </div>

            </article>

        </div>

    </div>

</section>


<!-- =========================================================
     CULTIVOS
========================================================= -->

<section class="py-24 bg-white">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-12">

            <div>

                <span class="text-agricolus uppercase tracking-[3px] text-sm font-bold">
                    DSS específicos
                </span>

                <h2 class="mt-4 text-4xl md:text-5xl font-bold text-darkgreen">
                    Soluciones para diferentes cultivos
                </h2>

            </div>

            <p class="max-w-md text-textgray leading-7">
                Modelos y herramientas diseñados para responder
                a las necesidades específicas de cada cultivo.
            </p>

        </div>


        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <!-- OLIVO -->
            <div class="crop-card rounded-[24px] overflow-hidden relative h-[330px]">

                <img
                    src="https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=700&q=80"
                    class="absolute inset-0 w-full h-full object-cover"
                    alt="Olivo"
                >

                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                <div class="absolute bottom-0 left-0 p-7 text-white">
                    <h3 class="text-2xl font-bold">Olivo</h3>
                    <p class="mt-1 text-white/80 text-sm">
                        Gestión y protección
                    </p>
                </div>

            </div>


            <!-- VID -->
            <div class="crop-card rounded-[24px] overflow-hidden relative h-[330px]">

                <img
                    src="https://images.unsplash.com/photo-1473973266408-ed4e27abdd47?auto=format&fit=crop&w=700&q=80"
                    class="absolute inset-0 w-full h-full object-cover"
                    alt="Vid"
                >

                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                <div class="absolute bottom-0 left-0 p-7 text-white">
                    <h3 class="text-2xl font-bold">Vid</h3>
                    <p class="mt-1 text-white/80 text-sm">
                        DSS para viñedos
                    </p>
                </div>

            </div>


            <!-- MAÍZ -->
            <div class="crop-card rounded-[24px] overflow-hidden relative h-[330px]">

                <img
                    src="https://images.unsplash.com/photo-1601593768797-3f9e1f0a7e45?auto=format&fit=crop&w=700&q=80"
                    class="absolute inset-0 w-full h-full object-cover"
                    alt="Maíz"
                >

                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                <div class="absolute bottom-0 left-0 p-7 text-white">
                    <h3 class="text-2xl font-bold">Maíz</h3>
                    <p class="mt-1 text-white/80 text-sm">
                        Monitorización del cultivo
                    </p>
                </div>

            </div>


            <!-- TOMATE -->
            <div class="crop-card rounded-[24px] overflow-hidden relative h-[330px]">

                <img
                    src="https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=700&q=80"
                    class="absolute inset-0 w-full h-full object-cover"
                    alt="Tomate"
                >

                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                <div class="absolute bottom-0 left-0 p-7 text-white">
                    <h3 class="text-2xl font-bold">Tomate</h3>
                    <p class="mt-1 text-white/80 text-sm">
                        Protección y previsión
                    </p>
                </div>

            </div>


            <!-- TABACO -->
            <div class="crop-card rounded-[24px] overflow-hidden relative h-[250px]">

                <img
                    src="https://images.unsplash.com/photo-1531058020387-3be344556be6?auto=format&fit=crop&w=700&q=80"
                    class="absolute inset-0 w-full h-full object-cover"
                    alt="Tabaco"
                >

                <div class="absolute inset-0 bg-black/35"></div>

                <h3 class="absolute bottom-6 left-7 text-2xl font-bold text-white">
                    Tabaco
                </h3>

            </div>


            <!-- CEREALES -->
            <div class="crop-card rounded-[24px] overflow-hidden relative h-[250px]">

                <img
                    src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=700&q=80"
                    class="absolute inset-0 w-full h-full object-cover"
                    alt="Cereales"
                >

                <div class="absolute inset-0 bg-black/35"></div>

                <h3 class="absolute bottom-6 left-7 text-2xl font-bold text-white">
                    Cereales de invierno
                </h3>

            </div>


            <!-- CÍTRICOS -->
            <div class="crop-card rounded-[24px] overflow-hidden relative h-[250px]">

                <img
                    src="https://images.unsplash.com/photo-1547514701-42782101795e?auto=format&fit=crop&w=700&q=80"
                    class="absolute inset-0 w-full h-full object-cover"
                    alt="Cítricos"
                >

                <div class="absolute inset-0 bg-black/35"></div>

                <h3 class="absolute bottom-6 left-7 text-2xl font-bold text-white">
                    Cítricos
                </h3>

            </div>


            <!-- MELOCOTONERO -->
            <div class="crop-card rounded-[24px] overflow-hidden relative h-[250px]">

                <img
                    src="https://images.unsplash.com/photo-1498557850523-fd3d118b962e?auto=format&fit=crop&w=700&q=80"
                    class="absolute inset-0 w-full h-full object-cover"
                    alt="Melocotonero"
                >

                <div class="absolute inset-0 bg-black/35"></div>

                <h3 class="absolute bottom-6 left-7 text-2xl font-bold text-white">
                    Melocotonero
                </h3>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     AGRITRACK
========================================================= -->

<section class="py-24 bg-darkgreen text-white overflow-hidden">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <div>

                <span class="text-[#9AD34D] uppercase tracking-[3px] text-sm font-bold">
                    Cadena agroalimentaria
                </span>

                <h2 class="mt-5 text-4xl md:text-5xl font-bold leading-tight">
                    AgriTrack
                </h2>

                <p class="mt-6 text-white/70 text-lg leading-8 max-w-xl">
                    Herramientas digitales para las necesidades de toda
                    la cadena agroalimentaria, desde la producción hasta
                    la trazabilidad y gestión de los datos.
                </p>

                <a
                    href="<?= $base_url ?>soluciones/controlCadenaAgroalimentaria.php"
                    class="inline-flex mt-8 bg-agricolus hover:bg-[#8aca38] text-white px-7 py-4 rounded-full font-semibold transition"
                >
                    Descubre AgriTrack
                </a>

            </div>


            <div class="relative">

                <div class="absolute -inset-10 bg-agricolus/10 rounded-full blur-3xl"></div>

                <img
                    src="https://images.unsplash.com/photo-1492496913980-501348b61469?auto=format&fit=crop&w=1000&q=80"
                    class="relative rounded-[35px] w-full h-[430px] object-cover"
                    alt="Cadena agroalimentaria"
                >

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     SENSORES Y MAQUINARIA
========================================================= -->

<section class="py-24 bg-[#F7F9F5]">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="text-center max-w-3xl mx-auto mb-14">

            <span class="text-agricolus uppercase tracking-[3px] text-sm font-bold">
                Sensores y maquinaria
            </span>

            <h2 class="mt-4 text-4xl md:text-5xl font-bold text-darkgreen">
                Conecta tus herramientas de campo
            </h2>

            <p class="mt-5 text-lg text-textgray leading-8">
                Integra estaciones meteorológicas y maquinaria agrícola
                para disponer de una visión completa de tus operaciones.
            </p>

        </div>


        <div class="grid md:grid-cols-2 gap-8">

            <!-- WEATHER -->
            <article class="solution-card bg-white rounded-[30px] overflow-hidden">

                <div class="h-72">

                    <img
                        src="https://images.unsplash.com/photo-1534088568595-a066f410bcda?auto=format&fit=crop&w=1000&q=80"
                        class="w-full h-full object-cover"
                        alt="Estaciones agrometeorológicas"
                    >

                </div>

                <div class="p-9">

                    <h3 class="text-2xl font-bold text-darkgreen">
                        Estaciones agrometeorológicas
                    </h3>

                    <p class="mt-4 text-textgray leading-7">
                        Compra o alquila estaciones meteorológicas
                        integrables con Agricolus para recopilar datos
                        directamente desde el campo.
                    </p>

                    <a href="<?= $base_url ?>soluciones/estacionesAgrometeo.php"
                       class="inline-flex mt-7 font-semibold text-darkgreen hover:text-agricolus">
                        Descubrir →
                    </a>

                </div>

            </article>


            <!-- MACHINERY -->
            <article class="solution-card bg-white rounded-[30px] overflow-hidden">

                <div class="h-72">

                    <img
                        src="https://images.unsplash.com/photo-1592982537447-6f2a6a0a7c9a?auto=format&fit=crop&w=1000&q=80"
                        class="w-full h-full object-cover"
                        alt="Maquinaria agrícola"
                    >

                </div>

                <div class="p-9">

                    <h3 class="text-2xl font-bold text-darkgreen">
                        Interoperabilidad de maquinaria
                    </h3>

                    <p class="mt-4 text-textgray leading-7">
                        Conecta maquinaria agrícola de diferentes
                        fabricantes y comparte los datos de tus
                        operaciones de campo.
                    </p>

                    <a href="<?= $base_url ?>quieneSomos/tecnologia.php"
                       class="inline-flex mt-7 font-semibold text-darkgreen hover:text-agricolus">
                        Descubrir →
                    </a>

                </div>

            </article>

        </div>

    </div>

</section>


<!-- =========================================================
     INTEGRACIONES
========================================================= -->

<section class="py-24 bg-white">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="grid lg:grid-cols-[1fr_1.3fr] gap-16 items-center">

            <div>

                <span class="text-agricolus uppercase tracking-[3px] text-sm font-bold">
                    Integraciones
                </span>

                <h2 class="mt-4 text-4xl md:text-5xl font-bold text-darkgreen">
                    Lleva Agricolus a tus propios sistemas
                </h2>

                <p class="mt-6 text-lg text-textgray leading-8">
                    Integra servicios de previsión y datos de imágenes
                    con tus propias aplicaciones mediante nuestras APIs.
                </p>

            </div>


            <div class="grid sm:grid-cols-2 gap-5">

                <div class="border border-gray-200 rounded-[25px] p-8 hover:border-agricolus transition">

                    <div class="w-12 h-12 rounded-xl bg-softgreen flex items-center justify-center mb-6">

                        <span class="text-agricolus text-xl font-bold">
                            API
                        </span>

                    </div>

                    <h3 class="text-xl font-bold text-darkgreen">
                        Agricolus APIs Forecast
                    </h3>

                    <p class="mt-3 text-textgray leading-6">
                        Modelos de previsión para integraciones
                        y servicios digitales.
                    </p>

                    <a href="<?= $base_url ?>contacto.php" class="inline-block mt-5 font-semibold text-agricolus">
                        Más información →
                    </a>

                </div>


                <div class="border border-gray-200 rounded-[25px] p-8 hover:border-agricolus transition">

                    <div class="w-12 h-12 rounded-xl bg-softgreen flex items-center justify-center mb-6">

                        <span class="text-agricolus text-xl font-bold">
                            IMG
                        </span>

                    </div>

                    <h3 class="text-xl font-bold text-darkgreen">
                        Agricolus APIs Imagery
                    </h3>

                    <p class="mt-3 text-textgray leading-6">
                        Acceso e integración de información
                        e imágenes para agricultura.
                    </p>

                    <a href="<?= $base_url ?>contacto.php" class="inline-block mt-5 font-semibold text-agricolus">
                        Más información →
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     CTA
========================================================= -->

<section
    id="contacto"
    class="py-24 bg-softgreen"
>

    <div class="max-w-5xl mx-auto px-6 text-center">

        <div class="w-16 h-16 bg-agricolus rounded-full mx-auto flex items-center justify-center">

            <span class="text-white text-2xl font-bold">
                A
            </span>

        </div>

        <h2 class="mt-7 text-4xl md:text-5xl font-bold text-darkgreen">
            La solución integral para cada necesidad agronómica
        </h2>

        <p class="mt-6 text-lg text-textgray leading-8 max-w-2xl mx-auto">
            Simplifica la gestión agrícola y toma decisiones basadas
            en datos con las herramientas digitales de Agricolus.
        </p>

        <div class="mt-9 flex flex-wrap justify-center gap-4">

            <a
                href="<?= $base_url ?>contacto.php"
                class="bg-darkgreen hover:bg-[#245337] text-white px-8 py-4 rounded-full font-semibold transition"
            >
                Empieza ahora
            </a>

            <a
                href="<?= $base_url ?>contacto.php"
                class="border border-darkgreen text-darkgreen hover:bg-darkgreen hover:text-white px-8 py-4 rounded-full font-semibold transition"
            >
                Contacta con nosotros
            </a>

        </div>

    </div>

</section>

<?php
include __DIR__ . '/../includes/footer.php';
?>

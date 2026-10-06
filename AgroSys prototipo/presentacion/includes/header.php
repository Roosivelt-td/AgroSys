<?php
$base_url = $base_url ?? './';
$current_page = $current_page ?? '';
?>

<!-- =====================================================
     HEADER / NAV
====================================================== -->

<header
    id="header"
    x-data="{ open: false }"
    class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-b border-gray-100"
>

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="h-[82px] flex items-center justify-between">


            <!-- LOGO -->

            <a href="<?= $base_url ?>index.php" class="flex items-center gap-2">

                <div class="w-10 h-10 rounded-full bg-agricolus flex items-center justify-center">

                    <span class="text-white text-xl font-black">
                        A
                    </span>

                </div>

                <span class="text-2xl font-bold tracking-tight text-darkgreen">
                    AGRICOLUS
                </span>

            </a>


            <!-- DESKTOP NAV -->

            <nav class="hidden lg:flex items-center gap-8">

                <a
                    href="<?= $base_url ?>soluciones/todasSoluciones.php"
                    class="text-sm font-medium hover:text-agricolus transition <?= $current_page === 'soluciones' ? 'text-agricolus font-semibold' : 'text-darkgreen' ?>"
                >
                    Soluciones
                </a>

                <a
                    href="<?= $base_url ?>quieneSomos/tecnologia.php"
                    class="text-sm font-medium hover:text-agricolus transition <?= $current_page === 'tecnologia' ? 'text-agricolus font-semibold' : 'text-darkgreen' ?>"
                >
                    Tecnologías
                </a>

                <a
                    href="<?= $base_url ?>quieneSomos/empresa.php"
                    class="text-sm font-medium hover:text-agricolus transition <?= $current_page === 'empresa' ? 'text-agricolus font-semibold' : 'text-darkgreen' ?>"
                >
                    Quiénes somos
                </a>

                <a
                    href="<?= $base_url ?>academy.php"
                    class="text-sm font-medium hover:text-agricolus transition <?= $current_page === 'academy' ? 'text-agricolus font-semibold' : 'text-darkgreen' ?>"
                >
                    Academy
                </a>

                <a
                    href="<?= $base_url ?>sostenibilidad.php"
                    class="text-sm font-medium hover:text-agricolus transition <?= $current_page === 'sostenibilidad' ? 'text-agricolus font-semibold' : 'text-darkgreen' ?>"
                >
                    Sostenibilidad
                </a>

                <a
                    href="<?= $base_url ?>contacto.php"
                    class="text-sm font-medium hover:text-agricolus transition <?= $current_page === 'contacto' ? 'text-agricolus font-semibold' : 'text-darkgreen' ?>"
                >
                    Contactos
                </a>

            </nav>


            <!-- DESKTOP CTA -->

            <div class="hidden lg:flex items-center gap-5">

                <button class="text-sm font-semibold text-darkgreen">
                    ES
                </button>

                <a
                    href="<?= $base_url ?>contacto.php"
                    class="bg-agricolus hover:bg-agricolusDark text-white px-6 py-3 rounded-full font-semibold transition"
                >
                    Reserva una demo
                </a>

            </div>


            <!-- MOBILE BUTTON -->

            <button
                @click="open = !open"
                class="lg:hidden text-darkgreen"
            >

                <svg
                    x-show="!open"
                    xmlns="http://www.w3.org/2000/svg"
                    width="27"
                    height="27"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.7"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>

                <svg
                    x-show="open"
                    xmlns="http://www.w3.org/2000/svg"
                    width="27"
                    height="27"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.7"
                        d="M6 6l12 12M6 18L18 6"
                    />
                </svg>

            </button>

        </div>


        <!-- MOBILE MENU -->

        <div
            x-show="open"
            x-transition
            class="lg:hidden pb-7"
        >

            <nav class="flex flex-col gap-5">

                <a href="<?= $base_url ?>soluciones/todasSoluciones.php" class="font-medium text-darkgreen hover:text-agricolus">
                    Soluciones
                </a>

                <a href="<?= $base_url ?>quieneSomos/tecnologia.php" class="font-medium text-darkgreen hover:text-agricolus">
                    Tecnologías
                </a>

                <a href="<?= $base_url ?>quieneSomos/empresa.php" class="font-medium text-darkgreen hover:text-agricolus">
                    Quiénes somos
                </a>

                <a href="<?= $base_url ?>academy.php" class="font-medium text-darkgreen hover:text-agricolus">
                    Academy
                </a>

                <a href="<?= $base_url ?>sostenibilidad.php" class="font-medium text-darkgreen hover:text-agricolus">
                    Sostenibilidad
                </a>

                <a href="<?= $base_url ?>contacto.php" class="font-medium text-darkgreen hover:text-agricolus">
                    Contactos
                </a>

                <a
                    href="<?= $base_url ?>contacto.php"
                    class="bg-agricolus text-white text-center py-3 rounded-full font-semibold"
                >
                    Reserva una demo
                </a>

            </nav>

        </div>

    </div>

</header>

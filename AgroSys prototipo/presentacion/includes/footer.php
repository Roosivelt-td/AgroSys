<?php
$base_url = $base_url ?? './';
?>

<!-- =====================================================
     FOOTER
====================================================== -->

<footer class="bg-dark text-white">

    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16">

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12">


            <!-- BRAND -->

            <div>

                <a href="<?= $base_url ?>index.php" class="flex items-center gap-2">

                    <div class="w-9 h-9 rounded-full bg-agricolus flex items-center justify-center">

                        <span class="font-black text-white">
                            A
                        </span>

                    </div>

                    <span class="font-bold text-xl text-white">
                        AGRICOLUS
                    </span>

                </a>

                <p class="mt-5 max-w-xs text-sm text-white/50 leading-7">
                    Tecnología digital para una agricultura
                    más precisa y sostenible.
                </p>

            </div>


            <!-- SOLUCIONES -->

            <div>

                <h4 class="font-bold mb-5">
                    Soluciones
                </h4>

                <div class="space-y-3 text-sm text-white/50">

                    <a href="<?= $base_url ?>soluciones/gestionAgronomica.php" class="block hover:text-white">
                        Gestión Agronómica
                    </a>

                    <a href="<?= $base_url ?>soluciones/defensaCultivos.php" class="block hover:text-white">
                        Protección de Cultivos
                    </a>

                    <a href="<?= $base_url ?>soluciones/monitoreoSatelital.php" class="block hover:text-white">
                        Monitoreo Satelital
                    </a>

                    <a href="<?= $base_url ?>soluciones/estacionesAgrometeo.php" class="block hover:text-white">
                        Estaciones Agrometeo
                    </a>

                    <a href="<?= $base_url ?>soluciones/controlCadenaAgroalimentaria.php" class="block hover:text-white">
                        Control Cadena Agroalimentaria
                    </a>

                    <a href="<?= $base_url ?>soluciones/exploracionCultivos.php" class="block hover:text-white">
                        Exploración de Cultivos
                    </a>

                    <a href="<?= $base_url ?>soluciones/todasSoluciones.php" class="block hover:text-white font-semibold text-agricolus">
                        Todas las Soluciones
                    </a>

                </div>

            </div>


            <!-- QUIÉNES SOMOS -->

            <div>

                <h4 class="font-bold mb-5">
                    Quiénes somos
                </h4>

                <div class="space-y-3 text-sm text-white/50">

                    <a href="<?= $base_url ?>quieneSomos/empresa.php" class="block hover:text-white">
                        Empresa
                    </a>

                    <a href="<?= $base_url ?>quieneSomos/tecnologia.php" class="block hover:text-white">
                        Tecnología
                    </a>

                    <a href="<?= $base_url ?>quieneSomos/investigacionIndustrial.php" class="block hover:text-white">
                        Investigación Industrial
                    </a>

                    <a href="<?= $base_url ?>quieneSomos/nuestraRed.php" class="block hover:text-white">
                        Nuestra Red
                    </a>

                    <a href="<?= $base_url ?>academy.php" class="block hover:text-white">
                        Academy
                    </a>

                    <a href="<?= $base_url ?>sostenibilidad.php" class="block hover:text-white">
                        Sostenibilidad
                    </a>

                </div>

            </div>


            <!-- CONTACTO -->

            <div>

                <h4 class="font-bold mb-5">
                    Contacto
                </h4>

                <div class="space-y-3 text-sm text-white/50">

                    <p>Via Manna, 88 06132 Perugia (PG) - Italia</p>

                    <p>info@agricolus.com</p>

                    <a href="<?= $base_url ?>contacto.php" class="inline-block mt-2 bg-agricolus text-white px-5 py-2 rounded-full text-xs font-semibold hover:bg-agricolusDark transition">
                        Contacta con nosotros
                    </a>

                </div>

            </div>

        </div>


        <div class="mt-16 pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between text-xs text-white/40 gap-4">

            <p>
                © <?= date('Y') ?> Agricolus S.r.l. - Todos los derechos reservados.
            </p>

            <div class="flex gap-6">

                <a href="#" class="hover:text-white">
                    Política de Privacidad
                </a>

                <a href="#" class="hover:text-white">
                    Política de Cookies
                </a>

                <a href="#" class="hover:text-white">
                    Términos y Condiciones
                </a>

            </div>

        </div>

    </div>

</footer>

</body>
</html>

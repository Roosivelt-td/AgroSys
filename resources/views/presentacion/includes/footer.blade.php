<!-- =====================================================
     FOOTER
====================================================== -->
<footer class="bg-dark text-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12">
            <div>
                <div class="flex items-center gap-2">
                    <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-8 h-8 object-contain">
                    <span class="font-bold text-xl"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span></span>
                </div>
                <p class="mt-5 max-w-xs text-sm text-white/50 leading-7">
                    Tecnología digital para una agricultura más precisa y sostenible.
                </p>
            </div>

            <div>
                <h4 class="font-bold mb-5 text-agricolus">Soluciones</h4>
                <div class="space-y-3 text-sm text-white/50">
                    <a href="{{ route('soluciones.satelital') }}" class="block hover:text-white">Satélite</a>
                    <a href="{{ route('soluciones.agronomica') }}" class="block hover:text-white">Riego y nutrición</a>
                    <a href="{{ route('soluciones.defensa') }}" class="block hover:text-white">Protección de cultivos</a>
                    <a href="{{ route('soluciones.controlCadena') }}" class="block hover:text-white">AgriTrack</a>
                    <a href="{{ route('soluciones.agrometeo') }}" class="block hover:text-white">Estaciones agrometeo</a>
                </div>
            </div>

            <div>
                <h4 class="font-bold mb-5 text-agricolus">Empresa</h4>
                <div class="space-y-3 text-sm text-white/50">
                    <a href="{{ route('soluciones.empresa') }}" class="block hover:text-white">Empresa</a>
                    <a href="{{ route('soluciones.nuestraRed') }}" class="block hover:text-white">Nuestra red</a>
                    <a href="{{ route('soluciones.tecnologia') }}" class="block hover:text-white">Tecnologías</a>
                    <a href="{{ route('soluciones.investigacion') }}" class="block hover:text-white">Investigación industrial</a>
                    <a href="{{ route('soluciones.sostenibilidad') }}" class="block hover:text-white">Sostenibilidad</a>
                </div>
            </div>

            <div>
                <h4 class="font-bold mb-5 text-agricolus">Contacto</h4>
                <div class="space-y-3 text-sm text-white/50">
                    <p>Via Settevalli 320</p>
                    <p>06129 Perugia, Italia</p>
                    <p>+39.075.99.75.503</p>
                    <p>discover@agrosys.com</p>
                </div>
            </div>
        </div>

        <div class="mt-14 pt-7 border-t border-white/10 flex flex-col md:flex-row justify-between gap-4 text-sm text-white/35">
            <p>© {{ date('Y') }} AgroSys S.r.l.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white">Privacidad</a>
                <a href="#" class="hover:text-white">Cookies</a>
                <a href="#" class="hover:text-white">Legal</a>
            </div>
        </div>
    </div>
</footer>

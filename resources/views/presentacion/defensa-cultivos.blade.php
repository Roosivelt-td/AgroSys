<x-presentacion-guest>
  <div class="text-[#252925] bg-white font-sans">

    <style>
        .container-main {
          width: min(1180px, calc(100% - 40px));
          margin: auto;
        }

        .hero-overlay {
          background:
            linear-gradient(
              90deg,
              rgba(20, 45, 19, .78) 0%,
              rgba(20, 45, 19, .45) 48%,
              rgba(20, 45, 19, .12) 100%
            );
        }

        .shadow-soft {
          box-shadow: 0 15px 45px rgba(31, 54, 29, .10);
        }

        .green-line {
          position: relative;
        }

        .green-line::after {
          content: "";
          position: absolute;
          left: 0;
          bottom: -15px;
          width: 55px;
          height: 3px;
          background: #579d49;
        }

        .card-hover {
          transition: all .3s ease;
        }

        .card-hover:hover {
          transform: translateY(-6px);
          box-shadow: 0 20px 45px rgba(31, 54, 29, .15);
        }
    </style>

    <!-- =====================================================
         HERO
    ====================================================== -->
    <section
      class="relative min-h-[650px] flex items-center pt-12"
      style="
        background-image:url('https://images.unsplash.com/photo-1499529112087-3cb3b73cec95?auto=format&fit=crop&w=2000&q=85');
        background-size:cover;
        background-position:center;
      "
    >
      <div class="absolute inset-0 hero-overlay"></div>

      <div class="container-main relative z-10 text-white pt-24 pb-12">
        <div class="max-w-[760px]">
          <p class="uppercase tracking-[4px] text-green-300 text-sm font-semibold mb-7">
            Protección de cultivos
          </p>

          <h1 class="text-5xl md:text-6xl lg:text-[70px] leading-[1.05] font-bold">
            Protege tus cultivos
            <span class="block text-green-300">
              de forma inteligente
            </span>
          </h1>

          <p class="mt-8 text-lg md:text-xl leading-relaxed text-white/90 max-w-2xl">
            Monitoriza el estado de tus cultivos y toma decisiones oportunas gracias a los modelos predictivos y a los Sistemas de Apoyo a la Decisión de <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span>.
          </p>

          <div class="mt-10 flex flex-wrap gap-4">
            <a
              href="#dss"
              class="bg-[#579d49] hover:bg-[#43853a] px-7 py-4 rounded-full font-semibold transition text-white"
            >
              Descubre las soluciones
            </a>

            <a
              href="#contacto"
              class="border border-white/70 hover:bg-white hover:text-green-900 px-7 py-4 rounded-full font-semibold transition text-white"
            >
              Solicitar información
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         INTRO
    ====================================================== -->
    <section class="py-24 bg-white">
      <div class="container-main">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <p class="text-green-600 uppercase tracking-[3px] text-sm font-bold mb-5">
              Defensa integrada
            </p>

            <h2 class="green-line text-4xl md:text-5xl font-bold leading-tight">
              Una nueva forma de proteger tus cultivos
            </h2>

            <div class="mt-10 space-y-5 text-gray-600 text-lg leading-relaxed">
              <p>
                La defensa fitosanitaria de los cultivos está experimentando una importante evolución. Las condiciones meteorológicas pueden modificar rápidamente la aparición de enfermedades e infestaciones.
              </p>
              <p>
                La defensa programada deja paso a una estrategia integrada, basada en el monitoreo constante, las prácticas agrícolas sostenibles, el control biológico y el uso de tecnologías avanzadas.
              </p>
              <p>
                Los modelos de predicción ayudan a técnicos y agricultores a conocer el riesgo y elegir el momento adecuado para intervenir.
              </p>
            </div>
          </div>

          <div class="relative">
            <div class="rounded-[35px] overflow-hidden shadow-soft">
              <img
                src="https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&w=1200&q=85"
                class="w-full h-[520px] object-cover"
                alt="Agricultura de precisión"
              />
            </div>

            <div class="absolute -bottom-8 -left-8 bg-white rounded-2xl p-6 shadow-xl max-w-[270px]">
              <div class="text-[#579d49] text-3xl font-bold">
                DSS
              </div>
              <p class="mt-2 text-sm text-gray-600">
                Sistemas de apoyo a la decisión para una agricultura más precisa y sostenible.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         BENEFITS
    ====================================================== -->
    <section class="bg-[#f5f5ed] py-24">
      <div class="container-main">
        <div class="text-center max-w-3xl mx-auto">
          <p class="text-green-600 uppercase tracking-[3px] text-sm font-bold">
            Tecnología <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span>
          </p>

          <h2 class="mt-4 text-4xl md:text-5xl font-bold">
            Decide cuándo y dónde intervenir
          </h2>

          <p class="mt-6 text-gray-600 text-lg">
            Combina datos meteorológicos, información del cultivo, modelos predictivos y observaciones de campo.
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mt-16">
          <div class="bg-white rounded-3xl p-8 card-hover">
            <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center text-2xl">
              🌱
            </div>
            <h3 class="font-bold text-xl mt-7">
              Monitorización
            </h3>
            <p class="text-gray-600 mt-4 leading-relaxed">
              Controla el estado del cultivo y detecta anomalías antes de que se conviertan en problemas.
            </p>
          </div>

          <div class="bg-white rounded-3xl p-8 card-hover">
            <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center text-2xl">
              ☁️
            </div>
            <h3 class="font-bold text-xl mt-7">
              Datos meteorológicos
            </h3>
            <p class="text-gray-600 mt-4 leading-relaxed">
              Integra información meteorológica para comprender mejor las condiciones que afectan al cultivo.
            </p>
          </div>

          <div class="bg-white rounded-3xl p-8 card-hover">
            <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center text-2xl">
              📊
            </div>
            <h3 class="font-bold text-xl mt-7">
              Modelos predictivos
            </h3>
            <p class="text-gray-600 mt-4 leading-relaxed">
              Evalúa el riesgo de enfermedades e infestaciones mediante modelos específicos.
            </p>
          </div>

          <div class="bg-white rounded-3xl p-8 card-hover">
            <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center text-2xl">
              🎯
            </div>
            <h3 class="font-bold text-xl mt-7">
              Decisiones precisas
            </h3>
            <p class="text-gray-600 mt-4 leading-relaxed">
              Planifica las intervenciones cuando realmente son necesarias.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         DSS SECTION
    ====================================================== -->
    <section id="dss" class="py-24 bg-white">
      <div class="container-main">
        <div class="max-w-3xl">
          <p class="text-green-600 uppercase tracking-[3px] text-sm font-bold">
            Soluciones específicas
          </p>

          <h2 class="mt-5 text-4xl md:text-5xl font-bold leading-tight">
            Descubre nuestros DSS para cultivos
          </h2>

          <p class="mt-6 text-gray-600 text-lg">
            Herramientas diseñadas para diferentes cultivos, con modelos predictivos y funcionalidades específicas para acompañar la gestión de toda la campaña.
          </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-14">
          <a href="#" class="group relative h-[280px] overflow-hidden rounded-3xl">
            <img src="https://images.unsplash.com/photo-1601049676869-702ea24cfd58?auto=format&fit=crop&w=800&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Olivo"/>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
            <div class="absolute bottom-6 left-6 text-white">
              <span class="text-sm opacity-70">DSS</span>
              <h3 class="text-2xl font-bold mt-1">Olivo</h3>
            </div>
          </a>

          <a href="#" class="group relative h-[280px] overflow-hidden rounded-3xl">
            <img src="https://images.unsplash.com/photo-1537640538966-79f369143f8f?auto=format&fit=crop&w=800&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Vid"/>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
            <div class="absolute bottom-6 left-6 text-white">
              <span class="text-sm opacity-70">DSS</span>
              <h3 class="text-2xl font-bold mt-1">Vid</h3>
            </div>
          </a>

          <a href="#" class="group relative h-[280px] overflow-hidden rounded-3xl">
            <img src="https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=800&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Tomate"/>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
            <div class="absolute bottom-6 left-6 text-white">
              <span class="text-sm opacity-70">DSS</span>
              <h3 class="text-2xl font-bold mt-1">Tomate</h3>
            </div>
          </a>

          <a href="#" class="group relative h-[280px] overflow-hidden rounded-3xl">
            <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Cereales"/>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
            <div class="absolute bottom-6 left-6 text-white">
              <span class="text-sm opacity-70">DSS</span>
              <h3 class="text-2xl font-bold mt-1">Cereales</h3>
            </div>
          </a>

          <a href="#" class="group relative h-[280px] overflow-hidden rounded-3xl">
            <img src="https://images.unsplash.com/photo-1597362925123-77861d3fbac7?auto=format&fit=crop&w=800&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Melocotón"/>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
            <div class="absolute bottom-6 left-6 text-white">
              <span class="text-sm opacity-70">DSS</span>
              <h3 class="text-2xl font-bold mt-1">Melocotonero</h3>
            </div>
          </a>

          <a href="#" class="group relative h-[280px] overflow-hidden rounded-3xl">
            <img src="https://images.unsplash.com/photo-1601593768799-76c7b1a0c2d6?auto=format&fit=crop&w=800&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Cítricos"/>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
            <div class="absolute bottom-6 left-6 text-white">
              <span class="text-sm opacity-70">DSS</span>
              <h3 class="text-2xl font-bold mt-1">Cítricos</h3>
            </div>
          </a>

          <a href="#" class="group relative h-[280px] overflow-hidden rounded-3xl">
            <img src="https://images.unsplash.com/photo-1560693225-b8507d6f3aa9?auto=format&fit=crop&w=800&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Maíz"/>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
            <div class="absolute bottom-6 left-6 text-white">
              <span class="text-sm opacity-70">DSS</span>
              <h3 class="text-2xl font-bold mt-1">Maíz</h3>
            </div>
          </a>

          <a href="#" class="group relative h-[280px] overflow-hidden rounded-3xl">
            <img src="https://images.unsplash.com/photo-1598514982901-ae627b9c46f1?auto=format&fit=crop&w=800&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Tabaco"/>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
            <div class="absolute bottom-6 left-6 text-white">
              <span class="text-sm opacity-70">DSS</span>
              <h3 class="text-2xl font-bold mt-1">Tabaco</h3>
            </div>
          </a>
        </div>
      </div>
    </section>

    <!-- =====================================================
         DARK FEATURE SECTION
    ====================================================== -->
    <section class="bg-[#263b25] text-white py-24">
      <div class="container-main">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <p class="text-green-300 uppercase tracking-[3px] text-sm font-bold">
              Agricultura de precisión
            </p>

            <h2 class="mt-5 text-4xl md:text-5xl font-bold leading-tight">
              De los datos a la decisión
            </h2>

            <p class="mt-7 text-white/75 text-lg leading-relaxed">
              <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> integra diferentes fuentes de información para ofrecer una visión completa de la explotación y ayudarte a gestionar el cultivo durante toda la campaña.
            </p>

            <div class="mt-10 space-y-6">
              <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center shrink-0">✓</div>
                <div>
                  <h3 class="font-bold text-lg">Predicción</h3>
                  <p class="text-white/65 mt-1">Evalúa anticipadamente el riesgo de enfermedades e infestaciones.</p>
                </div>
              </div>

              <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center shrink-0">✓</div>
                <div>
                  <h3 class="font-bold text-lg">Monitorización</h3>
                  <p class="text-white/65 mt-1">Combina datos de campo, meteorología y tecnología.</p>
                </div>
              </div>

              <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center shrink-0">✓</div>
                <div>
                  <h3 class="font-bold text-lg">Intervención</h3>
                  <p class="text-white/65 mt-1">Actúa en el momento adecuado y utiliza mejor tus recursos.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="relative">
            <div class="rounded-[35px] overflow-hidden">
              <img
                src="https://images.unsplash.com/photo-1625246333195-78d9c38ad449?auto=format&fit=crop&w=1400&q=85"
                class="w-full h-[550px] object-cover"
                alt="Agricultura inteligente"
              />
            </div>

            <div class="absolute -bottom-7 -right-7 bg-green-500 rounded-3xl p-7 w-[230px]">
              <div class="text-4xl font-bold">360°</div>
              <p class="mt-2 text-sm text-white/90">Visión integral de la gestión del cultivo.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         CTA
    ====================================================== -->
    <section id="contacto" class="py-24">
      <div class="container-main">
        <div class="rounded-[35px] overflow-hidden relative bg-green-700">
          <div
            class="absolute inset-0 opacity-20"
            style="
              background-image:url('https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1800&q=80');
              background-size:cover;
              background-position:center;
            "
          ></div>

          <div class="relative z-10 px-8 md:px-16 py-20 text-center text-white">
            <p class="uppercase tracking-[3px] text-green-200 text-sm font-bold">
              <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span>
            </p>

            <h2 class="mt-5 text-4xl md:text-5xl font-bold">
              ¿Quieres proteger mejor tus cultivos?
            </h2>

            <p class="mt-6 max-w-2xl mx-auto text-white/85 text-lg">
              Descubre cómo las herramientas de agricultura de precisión de <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> pueden ayudarte a monitorizar y gestionar tu explotación.
            </p>

            <div class="mt-9">
              <a
                href="#"
                class="inline-flex bg-white text-green-800 hover:bg-green-50 px-8 py-4 rounded-full font-bold transition"
              >
                Solicitar información
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</x-presentacion-guest>

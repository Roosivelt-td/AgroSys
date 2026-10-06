<x-presentacion-guest>
  <div class="text-[#272b27] bg-white font-sans">

    <style>
        .container-main {
          width: min(1180px, calc(100% - 40px));
          margin: auto;
        }

        .hero-overlay {
          background:
            linear-gradient(
              90deg,
              rgba(29,61,27,.96) 0%,
              rgba(29,61,27,.82) 42%,
              rgba(29,61,27,.22) 100%
            );
        }

        .section-line {
          position: relative;
        }

        .section-line::after {
          content: "";
          position: absolute;
          width: 55px;
          height: 3px;
          left: 0;
          bottom: -16px;
          background: #5da44d;
        }

        .feature-card {
          transition:
            transform .35s ease,
            box-shadow .35s ease;
        }

        .feature-card:hover {
          transform: translateY(-8px);
          box-shadow: 0 20px 55px rgba(34,70,31,.13);
        }

        .sensor-pulse {
          animation: pulseSensor 2.5s infinite;
        }

        @keyframes pulseSensor {
          0% {
            box-shadow: 0 0 0 0 rgba(93,164,77,.35);
          }
          70% {
            box-shadow: 0 0 0 20px rgba(93,164,77,0);
          }
          100% {
            box-shadow: 0 0 0 0 rgba(93,164,77,0);
          }
        }

        .weather-line {
          stroke-dasharray: 500;
          stroke-dashoffset: 500;
          animation: drawLine 2s ease forwards;
        }

        @keyframes drawLine {
          to {
            stroke-dashoffset: 0;
          }
        }

        @media(max-width:768px) {
          .hero-overlay {
            background: rgba(29,61,27,.84);
          }
        }
    </style>

    <!-- =====================================================
         HERO
    ====================================================== -->
    <section
      class="relative min-h-[720px] flex items-center overflow-hidden pt-12"
      style="
        background-image:
          url('https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&w=2200&q=90');
        background-size:cover;
        background-position:center;
      "
    >
      <div class="absolute inset-0 hero-overlay"></div>

      <div class="container-main relative z-10 pt-24 pb-12">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div class="text-white">
            <div class="uppercase tracking-[4px] text-[#abd09f] text-sm font-bold">
              Sensores y tecnología
            </div>

            <h1 class="mt-7 text-5xl md:text-6xl lg:text-[64px] font-bold leading-[1.04]">
              Estaciones
              <span class="text-[#abd09f]">
                agrometeo
              </span>
            </h1>

            <p class="mt-8 text-lg md:text-xl text-white/95 leading-relaxed max-w-xl">
              Datos meteorológicos precisos directamente desde el campo para conocer las condiciones de tus cultivos y apoyar tus decisiones agronómicas.
            </p>

            <div class="mt-8 flex flex-wrap gap-4">
              <a
                href="#estaciones"
                class="bg-[#5da44d] hover:bg-[#83bb74] px-7 py-4 rounded-full font-bold transition text-white"
              >
                Descubrir las estaciones
              </a>

              <a
                href="#contacto"
                class="border border-white/60 hover:bg-white hover:text-[#2d5c29] px-7 py-4 rounded-full font-bold transition text-white"
              >
                Solicitar información
              </a>
            </div>
          </div>

          <div class="hidden md:block relative">
            <div class="bg-white rounded-[35px] p-3 shadow-soft rotate-2">
              <div class="rounded-[27px] overflow-hidden relative">
                <img
                  src="https://images.unsplash.com/photo-1586771107445-d3ca888129ce?auto=format&fit=crop&w=1300&q=85"
                  class="w-full h-[470px] object-cover"
                  alt="Estación meteorológica agrícola"
                >

                <div class="absolute left-[48%] top-[34%] w-14 h-14 rounded-full bg-[#5da44d] border-4 border-white shadow-xl sensor-pulse flex items-center justify-center text-xl text-white">
                  ☁
                </div>
              </div>
            </div>

            <div class="absolute -left-10 bottom-10 bg-white rounded-2xl shadow-xl p-6 w-[250px]">
              <div class="flex items-center justify-between">
                <span class="text-gray-500 text-sm">Estación activa</span>
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
              </div>
              <div class="text-3xl font-bold text-[#397331] mt-3">
                18.6 °C
              </div>
              <div class="grid grid-cols-2 gap-3 mt-4 text-xs">
                <div>
                  <span class="text-gray-400">Humedad</span>
                  <strong class="block mt-1">72%</strong>
                </div>
                <div>
                  <span class="text-gray-400">Lluvia</span>
                  <strong class="block mt-1">2.4 mm</strong>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         INTRO
    ====================================================== -->
    <section id="estaciones" class="py-24">
      <div class="container-main">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <div class="rounded-[35px] overflow-hidden shadow-soft">
              <img
                src="https://images.unsplash.com/photo-1563514227147-6d2ff665a6a7?auto=format&fit=crop&w=1400&q=85"
                class="w-full h-[520px] object-cover"
                alt="Estación agrometeorológica"
              >
            </div>
          </div>

          <div>
            <p class="uppercase tracking-[3px] text-[#478d3c] text-sm font-bold">
              Estaciones meteorológicas
            </p>

            <h2 class="section-line mt-5 text-4xl md:text-5xl font-bold leading-tight">
              Conoce el microclima de tus cultivos
            </h2>

            <p class="mt-8 text-gray-600 text-lg leading-relaxed">
              Las estaciones agrometeorológicas recogen datos directamente en campo para ofrecer una visión precisa de las condiciones que afectan al desarrollo de los cultivos.
            </p>

            <p class="mt-5 text-gray-600 leading-relaxed">
              <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> permite integrar estaciones físicas y virtuales dentro de la plataforma, centralizando la información meteorológica y poniéndola a disposición de los modelos de predicción.
            </p>

            <div class="mt-9 flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-[#e5f0e1] flex items-center justify-center text-xl">
                🌦️
              </div>
              <div>
                <div class="font-bold">Datos directamente del campo</div>
                <div class="text-gray-500 text-sm mt-1">Información meteorológica disponible en la plataforma.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         PARAMETERS
    ====================================================== -->
    <section class="bg-[#f4f8f2] py-24">
      <div class="container-main">
        <div class="max-w-3xl">
          <p class="uppercase tracking-[3px] text-[#478d3c] text-sm font-bold">
            Datos meteorológicos
          </p>

          <h2 class="section-line mt-5 text-4xl md:text-5xl font-bold">
            Todos los parámetros que necesitas
          </h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-16">
          <div class="feature-card bg-white rounded-[30px] p-8">
            <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center text-3xl">🌡️</div>
            <h3 class="text-2xl font-bold mt-7">Temperatura</h3>
            <p class="mt-4 text-gray-600 leading-relaxed">
              Consulta la temperatura del entorno para conocer las condiciones térmicas del cultivo.
            </p>
            <div class="mt-6 text-[#478d3c] font-bold">Datos en tiempo real →</div>
          </div>

          <div class="feature-card bg-white rounded-[30px] p-8">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-3xl">💧</div>
            <h3 class="text-2xl font-bold mt-7">Humedad</h3>
            <p class="mt-4 text-gray-600 leading-relaxed">
              Monitoriza la humedad del aire y otros parámetros relacionados con las condiciones ambientales.
            </p>
            <div class="mt-6 text-[#478d3c] font-bold">Monitorización →</div>
          </div>

          <div class="feature-card bg-white rounded-[30px] p-8">
            <div class="w-16 h-16 rounded-2xl bg-cyan-50 flex items-center justify-center text-3xl">🌧️</div>
            <h3 class="text-2xl font-bold mt-7">Precipitaciones</h3>
            <p class="mt-4 text-gray-600 leading-relaxed">
              Registra las precipitaciones y utiliza estos datos para comprender mejor las condiciones del campo.
            </p>
            <div class="mt-6 text-[#478d3c] font-bold">Pluviometría →</div>
          </div>

          <div class="feature-card bg-white rounded-[30px] p-8">
            <div class="w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center text-3xl">💨</div>
            <h3 class="text-2xl font-bold mt-7">Viento</h3>
            <p class="mt-4 text-gray-600 leading-relaxed">
              Conoce la velocidad y dirección del viento para interpretar mejor las condiciones meteorológicas.
            </p>
            <div class="mt-6 text-[#478d3c] font-bold">Datos del viento →</div>
          </div>

          <div class="feature-card bg-white rounded-[30px] p-8">
            <div class="w-16 h-16 rounded-2xl bg-green-50 flex items-center justify-center text-3xl">🍃</div>
            <h3 class="text-2xl font-bold mt-7">Humedad foliar</h3>
            <p class="mt-4 text-gray-600 leading-relaxed">
              Utiliza sensores específicos para registrar la humedad presente en las hojas.
            </p>
            <div class="mt-6 text-[#478d3c] font-bold">Sensor foliar →</div>
          </div>

          <div class="feature-card bg-white rounded-[30px] p-8">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center text-3xl">🌱</div>
            <h3 class="text-2xl font-bold mt-7">Humedad del suelo</h3>
            <p class="mt-4 text-gray-600 leading-relaxed">
              Integra sensores de suelo para complementar la información meteorológica del campo.
            </p>
            <div class="mt-6 text-[#478d3c] font-bold">Sensores de suelo →</div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         DASHBOARD
    ====================================================== -->
    <section class="bg-[#243923] text-white py-24">
      <div class="container-main">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <p class="uppercase tracking-[3px] text-[#abd09f] text-sm font-bold">
              Datos en <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span>
            </p>

            <h2 class="mt-5 text-4xl md:text-5xl font-bold leading-tight">
              Convierte los datos meteorológicos en información útil
            </h2>

            <p class="mt-7 text-white/70 text-lg leading-relaxed">
              Los datos recopilados por las estaciones pueden integrarse con la plataforma <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> y utilizarse junto a otros datos agronómicos y modelos predictivos.
            </p>

            <div class="mt-10 space-y-5">
              <div class="flex gap-4">
                <div class="w-11 h-11 rounded-full bg-[#5da44d] flex items-center justify-center shrink-0">✓</div>
                <div>
                  <h3 class="font-bold text-lg">Datos centralizados</h3>
                  <p class="text-white/60 mt-1">Consulta la información meteorológica desde la plataforma.</p>
                </div>
              </div>

              <div class="flex gap-4">
                <div class="w-11 h-11 rounded-full bg-[#5da44d] flex items-center justify-center shrink-0">✓</div>
                <div>
                  <h3 class="font-bold text-lg">Modelos de predicción</h3>
                  <p class="text-white/60 mt-1">Utiliza los datos como fuente para modelos agronómicos.</p>
                </div>
              </div>

              <div class="flex gap-4">
                <div class="w-11 h-11 rounded-full bg-[#5da44d] flex items-center justify-center shrink-0">✓</div>
                <div>
                  <h3 class="font-bold text-lg">Seguimiento continuo</h3>
                  <p class="text-white/60 mt-1">Analiza la evolución de las variables meteorológicas.</p>
                </div>
              </div>
            </div>
          </div>

          <div>
            <div class="bg-white rounded-[25px] p-3 shadow-soft">
              <div class="bg-[#f5f7f4] rounded-[18px] overflow-hidden">
                <div class="h-14 bg-white border-b flex items-center justify-between px-5">
                  <div class="font-bold text-gray-800">Estaciones</div>
                  <div class="flex items-center gap-2">
                    <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-4 h-4 object-contain inline-block mr-1 align-middle">
                    <span class="text-xs text-gray-500">Sincronizado</span>
                  </div>
                </div>

                <div class="p-5">
                  <div class="bg-white rounded-xl p-4">
                    <div class="flex items-center justify-between">
                      <div>
                        <div class="text-xs text-gray-400">ESTACIÓN</div>
                        <div class="font-bold text-gray-800 mt-1">Bovolone</div>
                      </div>
                      <div class="bg-[#e5f0e1] text-[#397331] text-xs px-3 py-1 rounded-full">Física</div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mt-5">
                      <div class="bg-red-50 rounded-xl p-3">
                        <div class="text-xs text-gray-400">Temperatura</div>
                        <div class="font-bold text-gray-800 mt-1">18.6°C</div>
                      </div>
                      <div class="bg-blue-50 rounded-xl p-3">
                        <div class="text-xs text-gray-400">Humedad</div>
                        <div class="font-bold text-gray-800 mt-1">72%</div>
                      </div>
                      <div class="bg-cyan-50 rounded-xl p-3">
                        <div class="text-xs text-gray-400">Lluvia</div>
                        <div class="font-bold text-gray-800 mt-1">2.4mm</div>
                      </div>
                    </div>
                  </div>

                  <div class="bg-white rounded-xl mt-4 p-5">
                    <div class="flex justify-between items-center">
                      <div class="text-sm font-bold text-gray-700">Evolución meteorológica</div>
                      <div class="text-xs text-[#478d3c]">24 horas</div>
                    </div>

                    <svg viewBox="0 0 500 190" class="w-full mt-4">
                      <line x1="0" y1="30" x2="500" y2="30" stroke="#edf0ec"/>
                      <line x1="0" y1="80" x2="500" y2="80" stroke="#edf0ec"/>
                      <line x1="0" y1="130" x2="500" y2="130" stroke="#edf0ec"/>
                      <line x1="0" y1="180" x2="500" y2="180" stroke="#edf0ec"/>

                      <polyline
                        points="0,135 60,120 110,130 160,90 220,100 280,65 340,75 400,45 450,60 500,35"
                        fill="none" stroke="#ef6c5b" stroke-width="4"
                        class="weather-line"
                      />
                      <polyline
                        points="0,70 60,80 110,72 160,100 220,90 280,115 340,105 400,120 450,110 500,130"
                        fill="none" stroke="#5da44d" stroke-width="3"
                        class="weather-line"
                      />
                      <circle cx="500" cy="35" r="5" fill="#ef6c5b"/>
                    </svg>

                    <div class="flex gap-5 text-xs text-gray-400">
                      <span><i class="inline-block w-2 h-2 bg-red-400 rounded-full mr-1"></i> Temperatura</span>
                      <span><i class="inline-block w-2 h-2 bg-[#5da44d] rounded-full mr-1"></i> Humedad</span>
                    </div>
                  </div>

                  <div class="text-xs text-gray-400 mt-4 text-right">
                    Última sincronización: hoy, 10:30
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         PHYSICAL / VIRTUAL
    ====================================================== -->
    <section class="py-24">
      <div class="container-main">
        <div class="text-center max-w-3xl mx-auto">
          <p class="uppercase tracking-[3px] text-[#478d3c] text-sm font-bold">
            Tipos de estaciones
          </p>

          <h2 class="mt-5 text-4xl md:text-5xl font-bold">
            Físicas o virtuales, integradas en <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span>
          </h2>

          <p class="mt-5 text-gray-600 text-lg">
            Elige la solución que mejor se adapte a las características de tu explotación y conecta los datos con tu plataforma.
          </p>
        </div>

        <div class="grid md:grid-cols-2 gap-7 mt-16">
          <div class="feature-card rounded-[35px] overflow-hidden border border-gray-100 bg-white">
            <div class="h-[280px] overflow-hidden">
              <img
                src="https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&w=1200&q=85"
                class="w-full h-full object-cover"
                alt="Estación meteorológica física"
              >
            </div>

            <div class="p-8">
              <div class="inline-flex px-4 py-2 bg-[#e5f0e1] text-[#397331] rounded-full text-sm font-bold">
                Estaciones físicas
              </div>

              <h3 class="text-3xl font-bold mt-5">
                Sensores directamente en el campo
              </h3>

              <p class="mt-4 text-gray-600 leading-relaxed">
                Instala estaciones y sensores en tus parcelas para recopilar información meteorológica localizada.
              </p>

              <div class="mt-7 space-y-3 text-sm">
                <div class="flex items-center gap-3">
                  <span class="w-7 h-7 rounded-full bg-[#e5f0e1] text-[#397331] flex items-center justify-center">✓</span>
                  Temperatura
                </div>
                <div class="flex items-center gap-3">
                  <span class="w-7 h-7 rounded-full bg-[#e5f0e1] text-[#397331] flex items-center justify-center">✓</span>
                  Humedad
                </div>
                <div class="flex items-center gap-3">
                  <span class="w-7 h-7 rounded-full bg-[#e5f0e1] text-[#397331] flex items-center justify-center">✓</span>
                  Precipitación
                </div>
                <div class="flex items-center gap-3">
                  <span class="w-7 h-7 rounded-full bg-[#e5f0e1] text-[#397331] flex items-center justify-center">✓</span>
                  Viento y humedad foliar
                </div>
              </div>
            </div>
          </div>

          <div class="feature-card rounded-[35px] overflow-hidden border border-gray-100 bg-white">
            <div class="h-[280px] overflow-hidden bg-[#244b21] relative">
              <img
                src="https://images.unsplash.com/photo-1504608524841-42fe6f032b4b?auto=format&fit=crop&w=1200&q=85"
                class="absolute inset-0 w-full h-full object-cover opacity-60"
                alt="Datos meteorológicos"
              >
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="bg-white rounded-3xl shadow-xl p-7 w-[250px]">
                  <div class="text-xs text-gray-400">ESTACIÓN VIRTUAL</div>
                  <div class="text-4xl font-bold text-[#397331] mt-2">17.8°C</div>
                  <div class="flex justify-between mt-4 text-xs text-gray-500">
                    <span>💧 68%</span>
                    <span>💨 2.1 m/s</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="p-8">
              <div class="inline-flex px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-bold">
                Estaciones virtuales
              </div>

              <h3 class="text-3xl font-bold mt-5">
                Información meteorológica sin instalación física
              </h3>

              <p class="mt-4 text-gray-600 leading-relaxed">
                Utiliza información meteorológica virtual integrada en la plataforma para complementar el análisis de tus cultivos.
              </p>

              <div class="mt-7 flex items-center gap-4">
                <div class="w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center">🌐</div>
                <div>
                  <div class="font-bold">Integración digital</div>
                  <div class="text-sm text-gray-500">Disponible dentro de <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span>.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         PARTNERS
    ====================================================== -->
    <section class="bg-[#f3f6ef] py-24">
      <div class="container-main">
        <div class="text-center max-w-3xl mx-auto">
          <p class="uppercase tracking-[3px] text-[#478d3c] text-sm font-bold">
            Integraciones
          </p>

          <h2 class="mt-5 text-4xl md:text-5xl font-bold">
            Conecta las estaciones que ya utilizas
          </h2>

          <p class="mt-5 text-gray-600 text-lg">
            <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> trabaja con diferentes tecnologías y proveedores de estaciones agrometeorológicas.
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mt-14">
          <div class="h-28 bg-white rounded-2xl flex items-center justify-center text-gray-500 font-bold shadow-sm">PESSL</div>
          <div class="h-28 bg-white rounded-2xl flex items-center justify-center text-gray-500 font-bold shadow-sm">NETSENS</div>
          <div class="h-28 bg-white rounded-2xl flex items-center justify-center text-gray-500 font-bold shadow-sm">iFarming</div>
          <div class="h-28 bg-white rounded-2xl flex items-center justify-center text-gray-500 font-bold shadow-sm">DAVIS</div>
          <div class="h-28 bg-white rounded-2xl flex items-center justify-center text-gray-500 font-bold shadow-sm">CORDULUS</div>
          <div class="h-28 bg-white rounded-2xl flex items-center justify-center text-gray-500 font-bold shadow-sm">SENCROP</div>
        </div>

        <div class="mt-8 text-center text-gray-500 text-sm">
          ¿Tienes una estación que no aparece aquí? Podemos estudiar sus posibilidades de integración.
        </div>
      </div>
    </section>

    <!-- =====================================================
         HOW IT WORKS
    ====================================================== -->
    <section class="py-24">
      <div class="container-main">
        <div class="max-w-3xl">
          <p class="uppercase tracking-[3px] text-[#478d3c] text-sm font-bold">
            Cómo funciona
          </p>

          <h2 class="section-line mt-5 text-4xl md:text-5xl font-bold">
            Del sensor a la decisión
          </h2>
        </div>

        <div class="grid md:grid-cols-4 gap-6 mt-16">
          <div class="relative feature-card border border-gray-100 rounded-3xl p-7">
            <div class="w-12 h-12 rounded-full bg-[#478d3c] text-white flex items-center justify-center font-bold text-lg">01</div>
            <h3 class="text-xl font-bold mt-6">Medición</h3>
            <p class="text-gray-500 mt-3 leading-relaxed">Los sensores recopilan información meteorológica y ambiental.</p>
          </div>

          <div class="relative feature-card border border-gray-100 rounded-3xl p-7">
            <div class="w-12 h-12 rounded-full bg-[#478d3c] text-white flex items-center justify-center font-bold text-lg">02</div>
            <h3 class="text-xl font-bold mt-6">Transmisión</h3>
            <p class="text-gray-500 mt-3 leading-relaxed">Los datos son transmitidos y sincronizados con la plataforma.</p>
          </div>

          <div class="relative feature-card border border-gray-100 rounded-3xl p-7">
            <div class="w-12 h-12 rounded-full bg-[#478d3c] text-white flex items-center justify-center font-bold text-lg">03</div>
            <h3 class="text-xl font-bold mt-6">Análisis</h3>
            <p class="text-gray-500 mt-3 leading-relaxed"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> combina los datos con información agronómica y modelos.</p>
          </div>

          <div class="relative feature-card border border-gray-100 rounded-3xl p-7">
            <div class="w-12 h-12 rounded-full bg-[#478d3c] text-white flex items-center justify-center font-bold text-lg">04</div>
            <h3 class="text-xl font-bold mt-6">Decisión</h3>
            <p class="text-gray-500 mt-3 leading-relaxed">Obtén información que ayuda a interpretar las condiciones del cultivo.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         AGRONOMIC SECTION
    ====================================================== -->
    <section class="bg-[#f4f8f2] py-24">
      <div class="container-main">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div class="order-2 lg:order-1">
            <div class="relative rounded-[35px] overflow-hidden shadow-soft">
              <img
                src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1400&q=85"
                class="w-full h-[560px] object-cover"
                alt="Agricultura de precisión"
              >

              <div class="absolute top-7 left-7 bg-white rounded-2xl shadow-xl p-5 w-[230px]">
                <div class="text-xs text-gray-400">CONDICIONES</div>
                <div class="flex items-center justify-between mt-3">
                  <div>
                    <div class="text-3xl font-bold text-[#397331]">18.6°</div>
                    <div class="text-xs text-gray-500">Temperatura</div>
                  </div>
                  <div class="text-4xl">☀️</div>
                </div>
                <div class="border-t mt-4 pt-4 grid grid-cols-2 gap-4 text-xs">
                  <div>
                    <span class="text-gray-400">Humedad</span>
                    <strong class="block mt-1">72%</strong>
                  </div>
                  <div>
                    <span class="text-gray-400">Viento</span>
                    <strong class="block mt-1">2.1 m/s</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="order-1 lg:order-2">
            <p class="uppercase tracking-[3px] text-[#478d3c] text-sm font-bold">
              Agricultura de precisión
            </p>

            <h2 class="section-line mt-5 text-4xl md:text-5xl font-bold leading-tight">
              Meteorología conectada con la agronomía
            </h2>

            <p class="mt-8 text-gray-600 text-lg leading-relaxed">
              Los datos de las estaciones no están aislados. Pueden combinarse con información procedente de sensores, previsiones meteorológicas, imágenes satelitales y actividades de campo.
            </p>

            <div class="mt-9 space-y-5">
              <div class="flex gap-4">
                <div class="w-11 h-11 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0">🌱</div>
                <div>
                  <h3 class="font-bold text-lg">Estado del cultivo</h3>
                  <p class="text-gray-500 mt-1">Relaciona las condiciones meteorológicas con el desarrollo de las plantas.</p>
                </div>
              </div>

              <div class="flex gap-4">
                <div class="w-11 h-11 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0">💧</div>
                <div>
                  <h3 class="font-bold text-lg">Gestión del riego</h3>
                  <p class="text-gray-500 mt-1">Utiliza la información meteorológica junto con los modelos de riego.</p>
                </div>
              </div>

              <div class="flex gap-4">
                <div class="w-11 h-11 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0">🛡️</div>
                <div>
                  <h3 class="font-bold text-lg">Modelos de defensa</h3>
                  <p class="text-gray-500 mt-1">Los parámetros meteorológicos pueden alimentar modelos de predicción.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         INSTALLATION
    ====================================================== -->
    <section class="py-24">
      <div class="container-main">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <p class="uppercase tracking-[3px] text-[#478d3c] text-sm font-bold">
              Instalación
            </p>

            <h2 class="section-line mt-5 text-4xl md:text-5xl font-bold">
              Una estación adaptada al campo
            </h2>

            <p class="mt-8 text-gray-600 text-lg leading-relaxed">
              La ubicación de una estación meteorológica debe considerar el cultivo, las características geomorfológicas de la parcela y las condiciones de trabajo de los operadores.
            </p>

            <div class="mt-8 bg-[#f4f8f2] rounded-3xl p-6">
              <div class="flex gap-4">
                <div class="text-2xl">📍</div>
                <div>
                  <h3 class="font-bold">Ubicación adecuada</h3>
                  <p class="text-gray-500 text-sm mt-2 leading-relaxed">
                    La instalación debe tener en cuenta las necesidades del cultivo y las condiciones específicas del terreno.
                  </p>
                </div>
              </div>
            </div>

            <div class="mt-5 bg-[#f4f8f2] rounded-3xl p-6">
              <div class="flex gap-4">
                <div class="text-2xl">🚜</div>
                <div>
                  <h3 class="font-bold">Operaciones agrícolas</h3>
                  <p class="text-gray-500 text-sm mt-2 leading-relaxed">
                    También es importante considerar el paso de maquinaria y las actividades habituales de los trabajadores.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="relative">
            <div class="rounded-[35px] overflow-hidden shadow-soft">
              <img
                src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1400&q=85"
                class="w-full h-[560px] object-cover"
                alt="Campo agrícola"
              >
            </div>

            <div class="absolute right-7 bottom-7 bg-white rounded-3xl shadow-xl p-6 w-[250px]">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-[#e5f0e1] flex items-center justify-center text-xl">📡</div>
                <div>
                  <div class="font-bold">Sensor conectado</div>
                  <div class="text-xs text-green-600 mt-1">● Online</div>
                </div>
              </div>
              <div class="mt-5 border-t pt-4 grid grid-cols-2 gap-4 text-sm">
                <div>
                  <span class="text-gray-400 text-xs">Temp.</span>
                  <strong class="block mt-1">18.6°C</strong>
                </div>
                <div>
                  <span class="text-gray-400 text-xs">Lluvia</span>
                  <strong class="block mt-1">2.4 mm</strong>
                </div>
              </div>
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
        <div class="relative overflow-hidden rounded-[40px] bg-[#478d3c] text-white">
          <div
            class="absolute inset-0 opacity-15"
            style="
              background-image:
                url('https://images.unsplash.com/photo-1560493676-04071c5f467b?auto=format&fit=crop&w=1800&q=80');
              background-size:cover;
              background-position:center;
            "
          ></div>

          <div class="relative z-10 px-8 md:px-16 py-20 text-center">
            <p class="uppercase tracking-[3px] text-[#cce1c5] text-sm font-bold">
              <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span>
            </p>

            <h2 class="mt-5 text-4xl md:text-5xl font-bold">
              Lleva los datos meteorológicos a tu campo
            </h2>

            <p class="mt-6 max-w-2xl mx-auto text-white/80 text-lg">
              Integra estaciones agrometeorológicas físicas o virtuales con <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> y centraliza la información que necesitas para monitorizar tus cultivos.
            </p>

            <div class="mt-9 flex flex-wrap justify-center gap-4">
              <a
                href="#"
                class="bg-white text-[#2d5c29] px-8 py-4 rounded-full font-bold hover:bg-[#f4f8f2] transition"
              >
                Solicitar información
              </a>

              <a
                href="#"
                class="border border-white/60 px-8 py-4 rounded-full font-bold hover:bg-white hover:text-[#2d5c29] transition"
              >
                Contactar
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</x-presentacion-guest>

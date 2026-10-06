<x-presentacion-guest>
  <div class="text-[#263d20] bg-white font-sans" x-data="{ mobileMenu: false, modal: false, activeFeature: null, submitted: false }">

    <style>
        .container-agri {
          width: min(1180px, calc(100% - 40px));
          margin: 0 auto;
        }

        .hero {
          background:
            radial-gradient(
              circle at 80% 20%,
              rgba(120,169,74,.18),
              transparent 28%
            ),
            linear-gradient(
              135deg,
              #f4f8ef 0%,
              #ffffff 65%
            );
        }

        .satellite-hero {
          background:
            linear-gradient(
              90deg,
              rgba(20,45,24,.82),
              rgba(20,45,24,.25)
            ),
            url(
              'https://images.unsplash.com/photo-1511497584788-876760111969?auto=format&fit=crop&w=2000&q=90'
            );

          background-size: cover;
          background-position: center;
        }

        .field-image {
          background:
            linear-gradient(
              rgba(20,50,25,.12),
              rgba(20,50,25,.12)
            ),
            url(
              'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1600&q=90'
            );

          background-size: cover;
          background-position: center;
        }

        .farmer-image {
          background:
            linear-gradient(
              rgba(30,55,28,.10),
              rgba(30,55,28,.10)
            ),
            url(
              'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?auto=format&fit=crop&w=1600&q=90'
            );

          background-size: cover;
          background-position: center;
        }

        .map-image {
          background:
            linear-gradient(
              rgba(20,50,25,.12),
              rgba(20,50,25,.12)
            ),
            url(
              'https://images.unsplash.com/photo-1586771107445-d3ca888129ce?auto=format&fit=crop&w=1600&q=90'
            );

          background-size: cover;
          background-position: center;
        }

        .glass {
          background: rgba(255,255,255,.78);
          backdrop-filter: blur(15px);
          -webkit-backdrop-filter: blur(15px);
        }

        .soft-shadow {
          box-shadow:
            0 25px 70px rgba(38,61,32,.10);
        }

        .card {
          transition:
            transform .35s ease,
            box-shadow .35s ease;
        }

        .card:hover {
          transform: translateY(-8px);
          box-shadow:
            0 25px 60px rgba(38,61,32,.14);
        }

        .grid-pattern {
          background-image:
            linear-gradient(
              rgba(120,169,74,.10) 1px,
              transparent 1px
            ),
            linear-gradient(
              90deg,
              rgba(120,169,74,.10) 1px,
              transparent 1px
            );

          background-size: 40px 40px;
        }
    </style>

    <!-- =====================================================
         HERO
    ===================================================== -->
    <section class="hero pt-12 overflow-hidden" id="solucion">
      <div class="container-agri">
        <div class="min-h-[700px] grid lg:grid-cols-2 gap-14 items-center py-20">
          <div>
            <div class="inline-flex items-center gap-3 bg-[#e8f1dc] text-[#4d762d] px-4 py-2 rounded-full text-sm font-bold">
              <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-4 h-4 object-contain inline-block mr-1 align-middle">
              MÓDULO SATÉLITE
            </div>

            <h1 class="mt-7 text-5xl md:text-6xl lg:text-[70px] leading-[.94] font-black tracking-tight text-[#263d20]">
              Monitoriza tus cultivos <span class="text-[#619037]">desde el espacio.</span>
            </h1>

            <p class="mt-8 max-w-xl text-lg md:text-xl leading-8 text-gray-600">
              Utiliza imágenes satelitales Sentinel-2 e índices de vegetación para detectar la variabilidad del campo y tomar decisiones agronómicas más precisas.
            </p>

            <div class="mt-9 flex flex-wrap gap-4">
              <button @click="modal = true" class="bg-[#619037] hover:bg-[#4d762d] text-white px-8 py-4 rounded-full font-black transition shadow-lg">
                SOLICITAR INFORMACIÓN
              </button>
              <a href="#funciones" class="border border-[#619037] text-[#4d762d] px-8 py-4 rounded-full font-bold hover:bg-[#f4f8ef] transition">
                VER FUNCIONALIDADES
              </a>
            </div>

            <div class="mt-9 flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-white shadow flex items-center justify-center text-xl">🛰️</div>
              <div>
                <div class="text-xs uppercase tracking-[.2em] text-gray-500 font-bold">Desde</div>
                <div class="text-2xl font-black text-[#263d20]">150 €/año</div>
              </div>
            </div>
          </div>

          <div class="relative h-[550px] lg:h-[620px]">
            <div class="absolute inset-0 rounded-[45px] overflow-hidden satellite-hero shadow-2xl"></div>

            <div class="absolute top-8 right-[-15px] md:right-[-35px] glass rounded-3xl p-5 shadow-xl w-64">
              <div class="flex justify-between items-center">
                <span class="text-xs uppercase tracking-widest font-bold text-gray-500">Sentinel-2</span>
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
              </div>
              <div class="mt-4 h-28 rounded-2xl bg-gradient-to-br from-green-800 via-green-500 to-yellow-300 relative overflow-hidden">
                <div class="absolute inset-0 opacity-30 grid-pattern"></div>
                <div class="absolute top-7 left-10 w-24 h-12 bg-green-200/50 rounded-full blur-xl"></div>
              </div>
              <div class="mt-4 flex justify-between text-xs">
                <span class="text-gray-500">Resolución</span>
                <strong>10 m</strong>
              </div>
              <div class="mt-2 flex justify-between text-xs">
                <span class="text-gray-500">Frecuencia</span>
                <strong>~5 días</strong>
              </div>
            </div>

            <div class="absolute bottom-10 left-[-15px] md:left-[-35px] bg-white rounded-3xl p-6 shadow-2xl w-72">
              <div class="text-xs uppercase tracking-widest font-bold text-gray-400">Índice de vigor</div>
              <div class="mt-4 flex items-end gap-2">
                <span class="text-4xl font-black text-[#4d762d]">0.82</span>
                <span class="text-sm text-green-600 font-bold mb-2">+8.4%</span>
              </div>
              <div class="mt-4 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full w-[82%] bg-gradient-to-r from-yellow-400 to-green-600 rounded-full"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         INTRO STATS
    ===================================================== -->
    <section class="bg-[#263d20] text-white py-14">
      <div class="container-agri grid grid-cols-2 md:grid-cols-4 gap-8">
        <div class="text-center">
          <div class="text-4xl font-black">10 m</div>
          <div class="mt-2 text-sm text-green-200">Resolución espacial</div>
        </div>
        <div class="text-center">
          <div class="text-4xl font-black">5 días</div>
          <div class="mt-2 text-sm text-green-200">Actualización aproximada</div>
        </div>
        <div class="text-center">
          <div class="text-4xl font-black">Sentinel-2</div>
          <div class="mt-2 text-sm text-green-200">Imágenes satelitales</div>
        </div>
        <div class="text-center">
          <div class="text-4xl font-black">8+</div>
          <div class="mt-2 text-sm text-green-200">Índices disponibles</div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         WHY SATELLITE
    ===================================================== -->
    <section class="py-28 bg-white">
      <div class="container-agri">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <div class="text-[#619037] text-sm uppercase tracking-[.3em] font-black">¿Por qué utilizar el satélite?</div>
            <h2 class="mt-5 text-4xl md:text-5xl font-black leading-tight text-[#263d20]">
              Comprende lo que sucede dentro de cada parcela.
            </h2>
            <p class="mt-7 text-lg leading-8 text-gray-600">
              No todos los problemas del campo son visibles de forma uniforme desde tierra. Las imágenes satelitales permiten descubrir diferencias entre zonas y orientar las inspecciones donde realmente hacen falta.
            </p>
            <div class="mt-9 space-y-5">
              <div class="flex gap-4">
                <div class="flex-none w-11 h-11 rounded-full bg-[#e8f1dc] flex items-center justify-center text-[#4d762d] font-black">✓</div>
                <div>
                  <h3 class="font-black text-lg">Detecta variabilidad</h3>
                  <p class="mt-1 text-gray-500">Identifica diferencias de vigor dentro de una misma parcela.</p>
                </div>
              </div>
              <div class="flex gap-4">
                <div class="flex-none w-11 h-11 rounded-full bg-[#e8f1dc] flex items-center justify-center text-[#4d762d] font-black">✓</div>
                <div>
                  <h3 class="font-black text-lg">Localiza zonas críticas</h3>
                  <p class="mt-1 text-gray-500">Detecta señales relacionadas con estrés hídrico o clorosis.</p>
                </div>
              </div>
              <div class="flex gap-4">
                <div class="flex-none w-11 h-11 rounded-full bg-[#e8f1dc] flex items-center justify-center text-[#4d762d] font-black">✓</div>
                <div>
                  <h3 class="font-black text-lg">Actúa por zonas</h3>
                  <p class="mt-1 text-gray-500">Orienta las intervenciones y las inspecciones de forma específica.</p>
                </div>
              </div>
            </div>
          </div>
          <div class="field-image h-[550px] rounded-[45px] relative overflow-hidden soft-shadow">
            <div class="absolute bottom-7 left-7 right-7 bg-white/95 backdrop-blur rounded-3xl p-6">
              <div class="text-xs uppercase tracking-widest font-bold text-gray-400">Agricultura de precisión</div>
              <div class="mt-2 text-xl font-black text-[#263d20]">Datos para decidir dónde actuar.</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         WHAT CAN YOU DO
    ===================================================== -->
    <section id="funciones" class="py-28 bg-[#f4f8ef]">
      <div class="container-agri">
        <div class="max-w-3xl">
          <div class="text-[#619037] text-sm uppercase tracking-[.3em] font-black">Qué puedes hacer</div>
          <h2 class="mt-5 text-4xl md:text-5xl font-black text-[#263d20]">Del mapa satelital a la acción.</h2>
          <p class="mt-6 text-lg text-gray-600 leading-8">Convierte la información de las imágenes en datos que puedas utilizar en tu gestión diaria del cultivo.</p>
        </div>
        <div class="mt-14 grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          <article class="card bg-white rounded-[30px] p-7 border border-[#e8f1dc]">
            <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center text-2xl">🌱</div>
            <h3 class="mt-7 text-xl font-black text-[#263d20]">Monitoriza el cultivo</h3>
            <p class="mt-3 text-gray-500 leading-7 text-sm">Evalúa el vigor de las plantas y observa diferencias entre las distintas áreas del campo.</p>
          </article>
          <article class="card bg-white rounded-[30px] p-7 border border-[#e8f1dc]">
            <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl">💧</div>
            <h3 class="mt-7 text-xl font-black text-[#263d20]">Detecta estrés hídrico</h3>
            <p class="mt-3 text-gray-500 leading-7 text-sm">Localiza zonas que pueden presentar una mayor deficiencia de agua.</p>
          </article>
          <article class="card bg-white rounded-[30px] p-7 border border-[#e8f1dc]">
            <div class="w-14 h-14 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl">🍃</div>
            <h3 class="mt-7 text-xl font-black text-[#263d20]">Detecta clorosis</h3>
            <p class="mt-3 text-gray-500 leading-7 text-sm">Identifica áreas que requieren un análisis agronómico más detallado.</p>
          </article>
          <article class="card bg-white rounded-[30px] p-7 border border-[#e8f1dc]">
            <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center text-2xl">🗺️</div>
            <h3 class="mt-7 text-xl font-black text-[#263d20]">Fertiliza a dosis variable</h3>
            <p class="mt-3 text-gray-500 leading-7 text-sm">Genera mapas de prescripción para optimizar el uso de fertilizantes.</p>
          </article>
        </div>
      </div>
    </section>

    <!-- =====================================================
         SATELLITE MAP SECTION
    ===================================================== -->
    <section class="py-28 bg-white overflow-hidden" id="como-funciona">
      <div class="container-agri">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div class="relative order-2 lg:order-1">
            <div class="map-image h-[520px] rounded-[40px] overflow-hidden soft-shadow"></div>
            <div class="absolute top-6 left-6 bg-white rounded-2xl shadow-xl p-4 w-56">
              <div class="text-xs uppercase tracking-widest font-bold text-gray-400">Campo</div>
              <div class="mt-1 font-black text-[#263d20]">Parcela 024</div>
              <div class="mt-4 space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-gray-500">NDVI</span><strong class="text-green-600">0.82</strong></div>
                <div class="flex justify-between"><span class="text-gray-500">Clorofila</span><strong class="text-green-600">0.74</strong></div>
                <div class="flex justify-between"><span class="text-gray-500">Agua</span><strong class="text-blue-600">0.69</strong></div>
              </div>
            </div>
            <div class="absolute bottom-6 right-6 bg-white rounded-2xl shadow-xl p-4">
              <div class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-2">Vigor</div>
              <div class="w-32 h-3 rounded-full bg-gradient-to-r from-red-500 via-yellow-400 to-green-600"></div>
              <div class="flex justify-between mt-1 text-[9px] text-gray-400"><span>Bajo</span><span>Alto</span></div>
            </div>
          </div>
          <div class="order-1 lg:order-2">
            <div class="text-[#619037] text-sm uppercase tracking-[.3em] font-black">Cómo funciona</div>
            <h2 class="mt-5 text-4xl md:text-5xl font-black leading-tight text-[#263d20]">Una visión completa de cada parcela.</h2>
            <p class="mt-7 text-lg leading-8 text-gray-600"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span> procesa las imágenes satelitales y las transforma en índices que puedes consultar directamente en la plataforma.</p>
            <div class="mt-9 space-y-7">
              <div class="flex gap-5">
                <div class="flex-none w-12 h-12 rounded-2xl bg-[#619037] text-white flex items-center justify-center font-black">01</div>
                <div>
                  <h3 class="font-black text-lg text-[#263d20]">Selecciona tu campo</h3>
                  <p class="mt-1 text-gray-500 leading-6">Geolocaliza y organiza tus parcelas dentro de la plataforma.</p>
                </div>
              </div>
              <div class="flex gap-5">
                <div class="flex-none w-12 h-12 rounded-2xl bg-[#619037] text-white flex items-center justify-center font-black">02</div>
                <div>
                  <h3 class="font-black text-lg text-[#263d20]">Analiza las imágenes</h3>
                  <p class="mt-1 text-gray-500 leading-6">Compara fechas e índices para comprender la evolución del cultivo.</p>
                </div>
              </div>
              <div class="flex gap-5">
                <div class="flex-none w-12 h-12 rounded-2xl bg-[#619037] text-white flex items-center justify-center font-black">03</div>
                <div>
                  <h3 class="font-black text-lg text-[#263d20]">Decide dónde intervenir</h3>
                  <p class="mt-1 text-gray-500 leading-6">Utiliza los datos para orientar las comprobaciones y actuaciones.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         FEATURES
    ===================================================== -->
    <section class="py-28 bg-[#f4f8ef]">
      <div class="container-agri">
        <div class="text-center max-w-3xl mx-auto">
          <div class="text-[#619037] text-sm uppercase tracking-[.3em] font-black">Funcionalidades</div>
          <h2 class="mt-5 text-4xl md:text-5xl font-black text-[#263d20]">Mucho más que imágenes.</h2>
          <p class="mt-6 text-lg text-gray-600 leading-8">El módulo Satélite forma parte de un ecosistema completo para gestionar tu explotación agrícola.</p>
        </div>
        <div class="mt-14 grid md:grid-cols-2 lg:grid-cols-3 gap-5">
          <template x-for="feature in [
            {icon:'🗺️',title:'Campos',text:'Crea y geolocaliza parcelas con diferentes cultivos y variedades.'},
            {icon:'🛰️',title:'Imágenes satelitales',text:'Consulta imágenes Sentinel-2 e índices de vigor, estrés hídrico y clorofila.'},
            {icon:'📋',title:'Mapas de prescripción',text:'Genera mapas para realizar fertilización a dosis variable.'},
            {icon:'🌱',title:'Plan de cultivos',text:'Registra y visualiza tu planificación agrícola.'},
            {icon:'🚜',title:'Actividades',text:'Registra las operaciones realizadas en campo.'},
            {icon:'☁️',title:'Previsión meteorológica',text:'Consulta previsiones profesionales de hasta siete días.'},
            {icon:'🔎',title:'Monitoreo de cultivos',text:'Registra fenología, plagas, enfermedades y anomalías.'},
            {icon:'📝',title:'Nota rápida',text:'Añade fotos, notas y notas de voz asociadas a tus campos.'},
            {icon:'♻️',title:'Sostenibilidad',text:'Monitoriza indicadores económicos y ambientales.'}
          ]" :key="feature.title">
            <article class="bg-white rounded-3xl p-7 border border-gray-100 hover:border-[#d4e5bd] transition">
              <div class="w-12 h-12 rounded-2xl bg-[#e8f1dc] flex items-center justify-center text-xl" x-text="feature.icon"></div>
              <h3 class="mt-5 text-lg font-black text-[#263d20]" x-text="feature.title"></h3>
              <p class="mt-2 text-sm text-gray-500 leading-6" x-text="feature.text"></p>
            </article>
          </template>
        </div>
      </div>
    </section>

    <!-- =====================================================
         DATA TO DECISION
    ===================================================== -->
    <section id="ventajas" class="py-28 bg-white">
      <div class="container-agri">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <div class="text-[#619037] text-sm uppercase tracking-[.3em] font-black">De los datos a las decisiones</div>
            <h2 class="mt-5 text-4xl md:text-5xl font-black text-[#263d20] leading-tight">Convierte mapas en decisiones agronómicas.</h2>
            <p class="mt-7 text-lg leading-8 text-gray-600">El valor de la monitorización satelital no está solo en visualizar una imagen, sino en utilizarla para entender mejor el campo y actuar de forma específica.</p>
            <div class="mt-9 grid gap-4">
              <div class="flex items-center gap-4 p-4 rounded-2xl bg-[#f4f8ef]"><span class="w-9 h-9 rounded-full bg-[#619037] text-white flex items-center justify-center font-black">1</span><span class="font-bold">Comprender la variabilidad del campo</span></div>
              <div class="flex items-center gap-4 p-4 rounded-2xl bg-[#f4f8ef]"><span class="w-9 h-9 rounded-full bg-[#619037] text-white flex items-center justify-center font-black">2</span><span class="font-bold">Evaluar diferencias en el cultivo</span></div>
              <div class="flex items-center gap-4 p-4 rounded-2xl bg-[#f4f8ef]"><span class="w-9 h-9 rounded-full bg-[#619037] text-white flex items-center justify-center font-black">3</span><span class="font-bold">Utilizar los fertilizantes de forma eficiente</span></div>
              <div class="flex items-center gap-4 p-4 rounded-2xl bg-[#f4f8ef]"><span class="w-9 h-9 rounded-full bg-[#619037] text-white flex items-center justify-center font-black">4</span><span class="font-bold">Conectar satélite y observaciones de campo</span></div>
            </div>
          </div>
          <div class="farmer-image h-[600px] rounded-[45px] overflow-hidden soft-shadow relative">
            <div class="absolute inset-x-6 bottom-6 bg-white/95 backdrop-blur rounded-3xl p-6">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#619037] text-white flex items-center justify-center text-xl">✓</div>
                <div>
                  <div class="font-black text-[#263d20]">Agricultura de precisión</div>
                  <div class="text-sm text-gray-500">Información útil para actuar.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         CTA
    ===================================================== -->
    <section id="contacto" class="py-24 bg-[#263d20] text-white">
      <div class="container-agri text-center">
        <div class="max-w-4xl mx-auto">
          <div class="text-[#b8d497] text-sm uppercase tracking-[.35em] font-black">Módulo Satélite</div>
          <h2 class="mt-6 text-4xl md:text-6xl font-black leading-tight">Empieza a ver tu explotación de otra manera.</h2>
          <p class="mt-7 text-lg md:text-xl text-green-100 leading-8 max-w-2xl mx-auto">
            Descubre cómo la monitorización satelital puede ayudarte a comprender mejor tus cultivos y orientar tus decisiones.
          </p>
          <div class="mt-9 flex justify-center flex-wrap gap-4">
            <button @click="modal = true" class="bg-white text-[#385a27] hover:bg-[#f4f8ef] px-9 py-4 rounded-full font-black transition">
              SOLICITAR MÁS INFORMACIÓN
            </button>
            <a href="#" class="border border-white/40 hover:bg-white/10 px-9 py-4 rounded-full font-bold transition">
              DESCARGA LA APP
            </a>
          </div>
          <div class="mt-10 text-sm text-green-200">Desde 150 €/año</div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         CONTACT MODAL
    ===================================================== -->
    <div x-show="modal" x-transition.opacity class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-5" @keydown.escape.window="modal = false" style="display: none;">
      <div x-show="modal" x-transition @click.outside="modal = false" class="bg-white rounded-[35px] max-w-xl w-full p-8 md:p-10 shadow-2xl">
        <div class="flex justify-between items-start">
          <div>
            <div class="text-[#619037] text-xs uppercase tracking-[.3em] font-black"><span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span></div>
            <h2 class="mt-3 text-3xl font-black text-[#263d20]">Solicita información</h2>
          </div>
          <button @click="modal = false" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200">✕</button>
        </div>
        <p class="mt-5 text-gray-500 leading-7">Déjanos tus datos y te contactaremos para explicarte el funcionamiento del módulo Satélite.</p>
        <form class="mt-7" @submit.prevent="submitted = true; setTimeout(() => { modal = false; submitted = false; }, 1800)">
          <div class="grid md:grid-cols-2 gap-4">
            <input required type="text" placeholder="Nombre" class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a]">
            <input required type="text" placeholder="Apellidos" class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a]">
          </div>
          <input required type="email" placeholder="Correo electrónico" class="mt-4 w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a]">
          <input required type="text" placeholder="Empresa / explotación" class="mt-4 w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a]">
          <textarea rows="4" placeholder="Cuéntanos qué necesitas..." class="mt-4 w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-[#78a94a] resize-none"></textarea>
          <label class="mt-4 flex gap-3 text-sm text-gray-500">
            <input type="checkbox" required class="mt-1 accent-green-600">
            <span>He leído y acepto la Política de Privacidad.</span>
          </label>
          <button type="submit" class="mt-6 w-full bg-[#619037] hover:bg-[#4d762d] text-white py-4 rounded-full font-black">
            <span x-show="!submitted">ENVIAR SOLICITUD</span>
            <span x-show="submitted" style="display: none;">✓ SOLICITUD ENVIADA</span>
          </button>
        </form>
      </div>
    </div>

  </div>
</x-presentacion-guest>

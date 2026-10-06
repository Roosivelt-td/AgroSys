<?php
$page_title = "AgriTrack | Herramientas digitales para la cadena agroalimentaria";
$base_url = "../";
$current_page = "soluciones";
$body_class = "bg-white";

$page_styles = <<<'CSS'
    .container-main {
      width: min(1180px, calc(100% - 40px));
      margin: auto;
    }

    .hero-overlay {
      background:
        linear-gradient(
          90deg,
          rgba(29, 61, 27, .94) 0%,
          rgba(29, 61, 27, .78) 43%,
          rgba(29, 61, 27, .25) 100%
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
      background: #5da44d;
      left: 0;
      bottom: -16px;
    }

    .feature-card {
      transition:
        transform .35s ease,
        box-shadow .35s ease;
    }

    .feature-card:hover {
      transform: translateY(-7px);
      box-shadow: 0 20px 50px rgba(34, 70, 31, .13);
    }

    .ecosystem-line {
      position: absolute;
      top: 42px;
      left: 10%;
      right: 10%;
      height: 2px;
      background: #cce1c5;
    }

    @media(max-width: 768px) {
      .ecosystem-line {
        display:none;
      }
    }
CSS;

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/header.php';
?>

<!-- =========================================================
     HERO
========================================================== -->

<section
  class="relative
         min-h-[720px]
         flex items-center
         overflow-hidden"
  style="
    background-image:
      url('https://images.unsplash.com/photo-1492496913980-501348b61469?auto=format&fit=crop&w=2200&q=90');
    background-size:cover;
    background-position:center;
  "
>

  <div
    class="absolute inset-0
           hero-overlay"
  ></div>


  <div
    class="container-main
           relative z-10
           pt-24"
  >

    <div
      class="grid lg:grid-cols-2
             gap-16
             items-center"
    >


      <!-- TEXT -->

      <div class="text-white">

        <div
          class="uppercase
                 tracking-[4px]
                 text-agricolus-300
                 text-sm
                 font-bold"
        >
          Cadena agroalimentaria
        </div>


        <h1
          class="mt-7
                 text-5xl md:text-6xl
                 lg:text-[64px]
                 font-bold
                 leading-[1.04]"
        >

          Herramientas digitales
          para la

          <span
            class="text-agricolus-300"
          >
            cadena agroalimentaria
          </span>

        </h1>


        <p
          class="mt-8
                 text-lg md:text-xl
                 text-white/90
                 leading-relaxed
                 max-w-xl"
        >

          AgriTrack ayuda a las organizaciones
          del sector agrícola a gestionar de manera
          eficiente e innovadora la relación con
          todos los actores de la cadena.

        </p>


        <div
          class="mt-8
                 flex flex-wrap
                 gap-4"
        >

          <a
            href="<?= $base_url ?>contacto.php"
            class="bg-agricolus-500
                   hover:bg-agricolus-400
                   px-7 py-4
                   rounded-full
                   font-bold
                   transition"
          >
            Solicitar información
          </a>


          <a
            href="#funcionalidades"
            class="border
                   border-white/60
                   hover:bg-white
                   hover:text-agricolus-800
                   px-7 py-4
                   rounded-full
                   font-bold
                   transition"
          >
            Descubrir AgriTrack
          </a>

        </div>

      </div>


      <!-- HERO VISUAL -->

      <div
        class="hidden md:block
               relative"
      >

        <div
          class="bg-white
                 rounded-[35px]
                 p-3
                 shadow-soft
                 rotate-2"
        >

          <img
            src="https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1300&q=85"
            class="w-full
                   h-[470px]
                   object-cover
                   rounded-[27px]"
            alt="AgriTrack"
          >

        </div>


        <div
          class="absolute
                 -left-10
                 bottom-12
                 bg-white
                 rounded-2xl
                 shadow-xl
                 p-6
                 w-[235px]"
        >

          <div
            class="text-agricolus-600
                   text-3xl
                   font-bold"
          >
            AgriTrack
          </div>

          <p
            class="text-gray-500
                   text-sm
                   mt-2"
          >
            Un ecosistema digital para
            toda la cadena.
          </p>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =========================================================
     INTRO
========================================================== -->

<section class="py-24">

  <div class="container-main">

    <div
      class="grid lg:grid-cols-2
             gap-16
             items-center"
    >


      <!-- IMAGE -->

      <div>

        <div
          class="rounded-[35px]
                 overflow-hidden
                 shadow-soft"
        >

          <img
            src="https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1400&q=85"
            class="w-full
                   h-[520px]
                   object-cover"
            alt="Cadena agroalimentaria"
          >

        </div>

      </div>


      <!-- TEXT -->

      <div>

        <p
          class="uppercase
                 tracking-[3px]
                 text-agricolus-600
                 text-sm
                 font-bold"
        >
          AgriTrack
        </p>


        <h2
          class="section-line
                 mt-5
                 text-4xl md:text-5xl
                 font-bold
                 leading-tight"
        >

          Conecta a todos
          los actores de la cadena

        </h2>


        <p
          class="mt-8
                 text-gray-600
                 text-lg
                 leading-relaxed"
        >

          Las organizaciones agrícolas necesitan
          coordinar grandes cantidades de información:
          explotaciones, cultivos, operaciones,
          documentos, comunicaciones y datos
          agronómicos.

        </p>


        <p
          class="mt-5
                 text-gray-600
                 leading-relaxed"
        >

          AgriTrack reúne estos elementos en un
          único ecosistema digital para facilitar
          la trazabilidad, la comunicación y la
          toma de decisiones.

        </p>


        <div
          class="mt-9
                 flex items-center
                 gap-4"
        >

          <div
            class="w-12 h-12
                   rounded-full
                   bg-agricolus-100
                   flex items-center
                   justify-center
                   text-xl"
          >
            🔗
          </div>

          <div>

            <div class="font-bold">
              Un ecosistema conectado
            </div>

            <div
              class="text-gray-500
                     text-sm"
            >
              Empresas, técnicos y explotaciones
              trabajando con los mismos datos.
            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =========================================================
     3 PILLARS
========================================================== -->

<section
  class="bg-agricolus-50
         py-24"
>

  <div class="container-main">

    <div
      class="max-w-3xl"
    >

      <p
        class="uppercase
               tracking-[3px]
               text-agricolus-600
               text-sm
               font-bold"
      >
        Una solución integral
      </p>


      <h2
        class="section-line
               mt-5
               text-4xl md:text-5xl
               font-bold"
      >

        Tres pilares para
        gestionar la cadena

      </h2>

    </div>


    <div
      class="grid md:grid-cols-3
             gap-6
             mt-16"
    >


      <!-- PILLAR -->

      <div
        class="feature-card
               bg-white
               rounded-[30px]
               p-8"
      >

        <div
          class="w-16 h-16
                 rounded-2xl
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-3xl"
        >
          🔍
        </div>


        <h3
          class="text-2xl
                 font-bold
                 mt-7"
        >
          Innovación
          y trazabilidad
        </h3>


        <p
          class="mt-4
                 text-gray-600
                 leading-relaxed"
        >

          Organiza los procesos de la cadena
          y gestiona los datos de trazabilidad
          de las materias primas.

        </p>


        <div
          class="mt-7
                 text-agricolus-600
                 font-bold"
        >
          Trazabilidad →
        </div>

      </div>


      <!-- PILLAR -->

      <div
        class="feature-card
               bg-white
               rounded-[30px]
               p-8"
      >

        <div
          class="w-16 h-16
                 rounded-2xl
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-3xl"
        >
          📣
        </div>


        <h3
          class="text-2xl
                 font-bold
                 mt-7"
        >
          Marca
          y comunicación
        </h3>


        <p
          class="mt-4
                 text-gray-600
                 leading-relaxed"
        >

          Mantén una comunicación constante
          con las explotaciones mediante
          noticias, documentos y contenidos.

        </p>


        <div
          class="mt-7
                 text-agricolus-600
                 font-bold"
        >
          Comunicación →
        </div>

      </div>


      <!-- PILLAR -->

      <div
        class="feature-card
               bg-white
               rounded-[30px]
               p-8"
      >

        <div
          class="w-16 h-16
                 rounded-2xl
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-3xl"
        >
          📊
        </div>


        <h3
          class="text-2xl
                 font-bold
                 mt-7"
        >
          Soporte a la
          decisión
        </h3>


        <p
          class="mt-4
                 text-gray-600
                 leading-relaxed"
        >

          Gestiona múltiples empresas agrícolas
          y utiliza herramientas agronómicas
          para apoyar las decisiones.

        </p>


        <div
          class="mt-7
                 text-agricolus-600
                 font-bold"
        >
          Gestión agronómica →
        </div>

      </div>

    </div>

  </div>

</section>



<!-- =========================================================
     FUNCTIONALITIES
========================================================== -->

<section
  id="funcionalidades"
  class="py-24"
>

  <div class="container-main">

    <div
      class="text-center
             max-w-3xl
             mx-auto"
    >

      <p
        class="uppercase
               tracking-[3px]
               text-agricolus-600
               text-sm
               font-bold"
      >
        Funcionalidades
      </p>


      <h2
        class="mt-5
               text-4xl md:text-5xl
               font-bold"
      >
        Todo lo que necesitas
        en un único ecosistema
      </h2>


      <p
        class="mt-5
               text-gray-600
               text-lg"
      >
        Herramientas digitales para conectar
        información, personas y procesos.
      </p>

    </div>


    <!-- FUNCTION GRID -->

    <div
      class="grid sm:grid-cols-2
             lg:grid-cols-3
             gap-5
             mt-16"
    >


      <!-- 1 -->

      <div
        class="feature-card
               border border-gray-100
               rounded-3xl
               p-7"
      >

        <div class="text-3xl">
          📊
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Panel de control
        </h3>

        <p
          class="text-gray-500
                 mt-3
                 leading-relaxed"
        >
          Visualiza los principales indicadores
          de la cadena desde una única interfaz.
        </p>

      </div>


      <!-- 2 -->

      <div
        class="feature-card
               border border-gray-100
               rounded-3xl
               p-7"
      >

        <div class="text-3xl">
          🌐
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Gestión de ecosistemas
        </h3>

        <p
          class="text-gray-500
                 mt-3
                 leading-relaxed"
        >
          Gestiona organizaciones, empresas
          agrícolas y diferentes actores.
        </p>

      </div>


      <!-- 3 -->

      <div
        class="feature-card
               border border-gray-100
               rounded-3xl
               p-7"
      >

        <div class="text-3xl">
          🏷️
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Marca
        </h3>

        <p
          class="text-gray-500
                 mt-3
                 leading-relaxed"
        >
          Refuerza la identidad y comunicación
          de tu organización.
        </p>

      </div>


      <!-- 4 -->

      <div
        class="feature-card
               border border-gray-100
               rounded-3xl
               p-7"
      >

        <div class="text-3xl">
          💬
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Comunicación
        </h3>

        <p
          class="text-gray-500
                 mt-3
                 leading-relaxed"
        >
          Envía noticias, documentos y banners
          directamente a las explotaciones.
        </p>

      </div>


      <!-- 5 -->

      <div
        class="feature-card
               border border-gray-100
               rounded-3xl
               p-7"
      >

        <div class="text-3xl">
          📁
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Documentos
        </h3>

        <p
          class="text-gray-500
                 mt-3
                 leading-relaxed"
        >
          Comparte y recibe información
          importante para toda la cadena.
        </p>

      </div>


      <!-- 6 -->

      <div
        class="feature-card
               border border-gray-100
               rounded-3xl
               p-7"
      >

        <div class="text-3xl">
          🚜
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Gestión de operaciones
        </h3>

        <p
          class="text-gray-500
                 mt-3
                 leading-relaxed"
        >
          Controla operaciones agrícolas,
          tratamientos y actividades.
        </p>

      </div>


      <!-- 7 -->

      <div
        class="feature-card
               border border-gray-100
               rounded-3xl
               p-7"
      >

        <div class="text-3xl">
          🛰️
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Imágenes de satélite
        </h3>

        <p
          class="text-gray-500
                 mt-3
                 leading-relaxed"
        >
          Consulta información satelital
          para monitorizar cultivos.
        </p>

      </div>


      <!-- 8 -->

      <div
        class="feature-card
               border border-gray-100
               rounded-3xl
               p-7"
      >

        <div class="text-3xl">
          🌱
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Gestión agronómica
        </h3>

        <p
          class="text-gray-500
                 mt-3
                 leading-relaxed"
        >
          Apoya la gestión de múltiples
          explotaciones agrícolas.
        </p>

      </div>


      <!-- 9 -->

      <div
        class="feature-card
               border border-gray-100
               rounded-3xl
               p-7"
      >

        <div class="text-3xl">
          🚨
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Boletines de alerta
        </h3>

        <p
          class="text-gray-500
                 mt-3
                 leading-relaxed"
        >
          Envía alertas y reportes para apoyar
          el monitoreo de los cultivos.
        </p>

      </div>


      <!-- 10 -->

      <div
        class="feature-card
               border border-gray-100
               rounded-3xl
               p-7"
      >

        <div class="text-3xl">
          🎓
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Academy
        </h3>

        <p
          class="text-gray-500
                 mt-3
                 leading-relaxed"
        >
          Proporciona formación online mediante
          cursos dedicados.
        </p>

      </div>

    </div>

  </div>

</section>



<!-- =========================================================
     DASHBOARD SECTION
========================================================== -->

<section
  class="bg-[#243923]
         text-white
         py-24"
>

  <div class="container-main">

    <div
      class="grid lg:grid-cols-2
             gap-16
             items-center"
    >


      <!-- TEXT -->

      <div>

        <p
          class="uppercase
                 tracking-[3px]
                 text-agricolus-300
                 text-sm
                 font-bold"
        >
          Gestión centralizada
        </p>


        <h2
          class="mt-5
                 text-4xl md:text-5xl
                 font-bold
                 leading-tight"
        >
          Toda la información
          de la cadena en una sola interfaz
        </h2>


        <p
          class="mt-7
                 text-white/70
                 text-lg
                 leading-relaxed"
        >

          Consulta datos de campos, cultivos,
          actividades realizadas, monitoreo y
          sensores desde una visión centralizada.

        </p>


        <div
          class="mt-10
                 space-y-5"
        >

          <div class="flex gap-4">

            <div
              class="w-11 h-11
                     rounded-full
                     bg-agricolus-500
                     flex items-center
                     justify-center
                     shrink-0"
            >
              ✓
            </div>

            <div>

              <h3
                class="font-bold
                       text-lg"
              >
                Información centralizada
              </h3>

              <p
                class="text-white/60
                       mt-1"
              >
                Accede a la información
                relevante de cada organización.
              </p>

            </div>

          </div>


          <div class="flex gap-4">

            <div
              class="w-11 h-11
                     rounded-full
                     bg-agricolus-500
                     flex items-center
                     justify-center
                     shrink-0"
            >
              ✓
            </div>

            <div>

              <h3
                class="font-bold
                       text-lg"
              >
                Informes
              </h3>

              <p
                class="text-white/60
                       mt-1"
              >
                Obtén reportes de operaciones,
                productos e insumos.
              </p>

            </div>

          </div>


          <div class="flex gap-4">

            <div
              class="w-11 h-11
                     rounded-full
                     bg-agricolus-500
                     flex items-center
                     justify-center
                     shrink-0"
            >
              ✓
            </div>

            <div>

              <h3
                class="font-bold
                       text-lg"
              >
                Trazabilidad
              </h3>

              <p
                class="text-white/60
                       mt-1"
              >
                Sigue el recorrido de las materias
                primas dentro de la cadena.
              </p>

            </div>

          </div>

        </div>

      </div>


      <!-- DASHBOARD MOCKUP -->

      <div>

        <div
          class="bg-white
                 rounded-[25px]
                 p-3
                 shadow-soft"
        >

          <div
            class="bg-[#f5f7f4]
                   rounded-[18px]
                   overflow-hidden"
          >

            <!-- TOP -->

            <div
              class="h-14
                     bg-white
                     border-b
                     flex items-center
                     justify-between
                     px-5"
            >

              <div
                class="font-bold
                       text-gray-800"
              >
                AgriTrack
              </div>


              <div
                class="flex gap-2"
              >

                <div
                  class="w-7 h-7
                         rounded-full
                         bg-agricolus-100"
                ></div>

                <div
                  class="w-7 h-7
                         rounded-full
                         bg-agricolus-100"
                ></div>

              </div>

            </div>


            <!-- DASHBOARD -->

            <div
              class="p-5"
            >

              <div
                class="grid
                       grid-cols-3
                       gap-3"
              >

                <div
                  class="bg-white
                         rounded-xl
                         p-4"
                >

                  <div
                    class="text-xs
                           text-gray-400"
                  >
                    Empresas
                  </div>

                  <div
                    class="text-2xl
                           font-bold
                           mt-2
                           text-agricolus-700"
                  >
                    128
                  </div>

                </div>


                <div
                  class="bg-white
                         rounded-xl
                         p-4"
                >

                  <div
                    class="text-xs
                           text-gray-400"
                  >
                    Campos
                  </div>

                  <div
                    class="text-2xl
                           font-bold
                           mt-2
                           text-agricolus-700"
                  >
                    2.430
                  </div>

                </div>


                <div
                  class="bg-white
                         rounded-xl
                         p-4"
                >

                  <div
                    class="text-xs
                           text-gray-400"
                  >
                    Hectáreas
                  </div>

                  <div
                    class="text-2xl
                           font-bold
                           mt-2
                           text-agricolus-700"
                  >
                    8.640
                  </div>

                </div>

              </div>


              <!-- CHART -->

              <div
                class="bg-white
                       rounded-xl
                       mt-4
                       p-5
                       h-[230px]"
              >

                <div
                  class="text-sm
                         font-bold
                         text-gray-700"
                >
                  Actividad de la cadena
                </div>


                <div
                  class="flex
                         items-end
                         gap-3
                         h-[160px]
                         mt-3"
                >

                  <div
                    class="flex-1
                           bg-agricolus-200
                           rounded-t
                           h-[45%]"
                  ></div>

                  <div
                    class="flex-1
                           bg-agricolus-300
                           rounded-t
                           h-[60%]"
                  ></div>

                  <div
                    class="flex-1
                           bg-agricolus-400
                           rounded-t
                           h-[75%]"
                  ></div>

                  <div
                    class="flex-1
                           bg-agricolus-500
                           rounded-t
                           h-[55%]"
                  ></div>

                  <div
                    class="flex-1
                           bg-agricolus-600
                           rounded-t
                           h-[90%]"
                  ></div>

                  <div
                    class="flex-1
                           bg-agricolus-700
                           rounded-t
                           h-[72%]"
                  ></div>

                </div>

              </div>


              <!-- MAP -->

              <div
                class="bg-[#dfe9d9]
                       rounded-xl
                       mt-4
                       h-[130px]
                       relative
                       overflow-hidden"
              >

                <div
                  class="absolute
                         w-20 h-20
                         bg-agricolus-500/30
                         rounded-full
                         left-[20%]
                         top-[25%]"
                ></div>

                <div
                  class="absolute
                         w-28 h-20
                         bg-agricolus-600/30
                         rounded-full
                         right-[15%]
                         top-[20%]"
                ></div>

                <div
                  class="absolute
                         w-10 h-10
                         bg-agricolus-700
                         rounded-full
                         left-[47%]
                         top-[42%]
                         border-4
                         border-white"
                ></div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =========================================================
     WHAT YOU CAN DO
========================================================== -->

<section class="py-24">

  <div class="container-main">

    <div
      class="max-w-3xl"
    >

      <p
        class="uppercase
               tracking-[3px]
               text-agricolus-600
               text-sm
               font-bold"
      >
        Qué puedes hacer
      </p>


      <h2
        class="section-line
               mt-5
               text-4xl md:text-5xl
               font-bold"
      >
        Convierte los datos
        en decisiones
      </h2>

    </div>


    <div
      class="grid lg:grid-cols-2
             gap-6
             mt-16"
    >


      <!-- ITEM -->

      <div
        class="border
               border-gray-100
               rounded-3xl
               p-8
               flex gap-6"
      >

        <div
          class="w-14 h-14
                 shrink-0
                 rounded-2xl
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          🌾
        </div>

        <div>

          <h3
            class="text-xl
                   font-bold"
          >
            Organiza los procesos
          </h3>

          <p
            class="text-gray-600
                   mt-3
                   leading-relaxed"
          >
            Gestiona los datos de trazabilidad
            y controla el cumplimiento de los
            estándares de calidad.
          </p>

        </div>

      </div>


      <!-- ITEM -->

      <div
        class="border
               border-gray-100
               rounded-3xl
               p-8
               flex gap-6"
      >

        <div
          class="w-14 h-14
                 shrink-0
                 rounded-2xl
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          📄
        </div>

        <div>

          <h3
            class="text-xl
                   font-bold"
          >
            Gestiona documentos
          </h3>

          <p
            class="text-gray-600
                   mt-3
                   leading-relaxed"
          >
            Envía y recibe documentos e información
            relevante para los diferentes actores.
          </p>

        </div>

      </div>


      <!-- ITEM -->

      <div
        class="border
               border-gray-100
               rounded-3xl
               p-8
               flex gap-6"
      >

        <div
          class="w-14 h-14
                 shrink-0
                 rounded-2xl
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          📈
        </div>

        <div>

          <h3
            class="text-xl
                   font-bold"
          >
            Genera informes
          </h3>

          <p
            class="text-gray-600
                   mt-3
                   leading-relaxed"
          >
            Consulta operaciones, productos,
            cantidades de insumos y recursos
            utilizados.
          </p>

        </div>

      </div>


      <!-- ITEM -->

      <div
        class="border
               border-gray-100
               rounded-3xl
               p-8
               flex gap-6"
      >

        <div
          class="w-14 h-14
                 shrink-0
                 rounded-2xl
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          📢
        </div>

        <div>

          <h3
            class="text-xl
                   font-bold"
          >
            Comunícate con las fincas
          </h3>

          <p
            class="text-gray-600
                   mt-3
                   leading-relaxed"
          >
            Envía noticias, banners y documentos
            directamente a las explotaciones.
          </p>

        </div>

      </div>


      <!-- ITEM -->

      <div
        class="border
               border-gray-100
               rounded-3xl
               p-8
               flex gap-6"
      >

        <div
          class="w-14 h-14
                 shrink-0
                 rounded-2xl
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          🎓
        </div>

        <div>

          <h3
            class="text-xl
                   font-bold"
          >
            Forma a tus equipos
          </h3>

          <p
            class="text-gray-600
                   mt-3
                   leading-relaxed"
          >
            Pon a disposición de las organizaciones
            cursos y formación online.
          </p>

        </div>

      </div>


      <!-- ITEM -->

      <div
        class="border
               border-gray-100
               rounded-3xl
               p-8
               flex gap-6"
      >

        <div
          class="w-14 h-14
                 shrink-0
                 rounded-2xl
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          🚨
        </div>

        <div>

          <h3
            class="text-xl
                   font-bold"
          >
            Envía alertas
          </h3>

          <p
            class="text-gray-600
                   mt-3
                   leading-relaxed"
          >
            Apoya a las fincas con reportes
            y boletines de alerta.
          </p>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =========================================================
     DSS SECTION
========================================================== -->

<section
  class="bg-[#f3f6ef]
         py-24"
>

  <div class="container-main">

    <div
      class="grid lg:grid-cols-2
             gap-16
             items-center"
    >


      <!-- IMAGE -->

      <div
        class="relative"
      >

        <div
          class="rounded-[35px]
                 overflow-hidden
                 shadow-soft"
        >

          <img
            src="https://images.unsplash.com/photo-1586771107445-d3ca888129ce?auto=format&fit=crop&w=1400&q=85"
            class="w-full
                   h-[560px]
                   object-cover"
            alt="Gestión agrícola"
          >

        </div>


        <div
          class="absolute
                 bottom-7
                 right-7
                 bg-white
                 rounded-2xl
                 shadow-xl
                 p-6
                 w-[230px]"
        >

          <div
            class="text-agricolus-600
                   text-3xl
                   font-bold"
          >
            DSS
          </div>

          <p
            class="text-gray-500
                   text-sm
                   mt-2"
          >
            Sistema de soporte a la decisión
          </p>

        </div>

      </div>


      <!-- TEXT -->

      <div>

        <p
          class="uppercase
                 tracking-[3px]
                 text-agricolus-600
                 text-sm
                 font-bold"
        >
          Soporte a la decisión
        </p>


        <h2
          class="section-line
                 mt-5
                 text-4xl md:text-5xl
                 font-bold
                 leading-tight"
        >
          Una gestión agronómica
          basada en datos
        </h2>


        <p
          class="mt-8
                 text-gray-600
                 text-lg
                 leading-relaxed"
        >

          AgriTrack permite consultar sistemas de
          soporte a la decisión para defensa,
          fertilización, gestión del agua y
          seguimiento del estado fenológico.

        </p>


        <div
          class="mt-9
                 grid sm:grid-cols-2
                 gap-4"
        >

          <div
            class="bg-white
                   rounded-2xl
                   p-5"
          >

            <div class="text-2xl">
              🛡️
            </div>

            <h3
              class="font-bold
                     mt-3"
            >
              Defensa
            </h3>

            <p
              class="text-sm
                     text-gray-500
                     mt-1"
            >
              Apoyo al monitoreo
              fitosanitario.
            </p>

          </div>


          <div
            class="bg-white
                   rounded-2xl
                   p-5"
          >

            <div class="text-2xl">
              💧
            </div>

            <h3
              class="font-bold
                     mt-3"
            >
              Gestión hídrica
            </h3>

            <p
              class="text-sm
                     text-gray-500
                     mt-1"
            >
              Información para
              gestionar el agua.
            </p>

          </div>


          <div
            class="bg-white
                   rounded-2xl
                   p-5"
          >

            <div class="text-2xl">
              🧪
            </div>

            <h3
              class="font-bold
                     mt-3"
            >
              Fertilización
            </h3>

            <p
              class="text-sm
                     text-gray-500
                     mt-1"
            >
              Gestión precisa
              de nutrientes.
            </p>

          </div>


          <div
            class="bg-white
                   rounded-2xl
                   p-5"
          >

            <div class="text-2xl">
              🌱
            </div>

            <h3
              class="font-bold
                     mt-3"
            >
              Fenología
            </h3>

            <p
              class="text-sm
                     text-gray-500
                     mt-1"
            >
              Seguimiento del
              desarrollo del cultivo.
            </p>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =========================================================
     ECOSYSTEM
========================================================== -->

<section class="py-24">

  <div class="container-main">

    <div
      class="text-center
             max-w-3xl
             mx-auto"
    >

      <p
        class="uppercase
               tracking-[3px]
               text-agricolus-600
               text-sm
               font-bold"
      >
        ¿Para quién es AgriTrack?
      </p>


      <h2
        class="mt-5
               text-4xl md:text-5xl
               font-bold"
      >
        Una plataforma para
        toda la cadena
      </h2>

    </div>


    <div
      class="relative
             grid sm:grid-cols-2
             lg:grid-cols-3
             gap-6
             mt-16"
    >

      <div class="ecosystem-line"></div>


      <!-- CARD -->

      <div
        class="relative z-10
               bg-white
               border
               border-gray-100
               rounded-3xl
               p-7
               text-center
               feature-card"
      >

        <div
          class="mx-auto
                 w-16 h-16
                 rounded-full
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          🏛️
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Consorcios
        </h3>

        <p
          class="text-gray-500
                 text-sm
                 mt-3"
        >
          Coordina múltiples organizaciones
          y productores.
        </p>

      </div>


      <!-- CARD -->

      <div
        class="relative z-10
               bg-white
               border
               border-gray-100
               rounded-3xl
               p-7
               text-center
               feature-card"
      >

        <div
          class="mx-auto
                 w-16 h-16
                 rounded-full
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          👥
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Asociaciones
          de agricultores
        </h3>

        <p
          class="text-gray-500
                 text-sm
                 mt-3"
        >
          Gestiona relaciones
          con agricultores asociados.
        </p>

      </div>


      <!-- CARD -->

      <div
        class="relative z-10
               bg-white
               border
               border-gray-100
               rounded-3xl
               p-7
               text-center
               feature-card"
      >

        <div
          class="mx-auto
                 w-16 h-16
                 rounded-full
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          🤝
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Cooperativas
        </h3>

        <p
          class="text-gray-500
                 text-sm
                 mt-3"
        >
          Centraliza la información
          de las explotaciones.
        </p>

      </div>


      <!-- CARD -->

      <div
        class="relative z-10
               bg-white
               border
               border-gray-100
               rounded-3xl
               p-7
               text-center
               feature-card"
      >

        <div
          class="mx-auto
                 w-16 h-16
                 rounded-full
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          🏭
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Empresas
          procesadoras
        </h3>

        <p
          class="text-gray-500
                 text-sm
                 mt-3"
        >
          Mejora la trazabilidad de las
          materias primas.
        </p>

      </div>


      <!-- CARD -->

      <div
        class="relative z-10
               bg-white
               border
               border-gray-100
               rounded-3xl
               p-7
               text-center
               feature-card"
      >

        <div
          class="mx-auto
                 w-16 h-16
                 rounded-full
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          📦
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Distribuidores
        </h3>

        <p
          class="text-gray-500
                 text-sm
                 mt-3"
        >
          Coordina productos, recursos
          y comunicaciones.
        </p>

      </div>


      <!-- CARD -->

      <div
        class="relative z-10
               bg-white
               border
               border-gray-100
               rounded-3xl
               p-7
               text-center
               feature-card"
      >

        <div
          class="mx-auto
                 w-16 h-16
                 rounded-full
                 bg-agricolus-100
                 flex items-center
                 justify-center
                 text-2xl"
        >
          🏢
        </div>

        <h3
          class="font-bold
                 text-xl
                 mt-5"
        >
          Almacenes
        </h3>

        <p
          class="text-gray-500
                 text-sm
                 mt-3"
        >
          Controla operaciones
          y recursos almacenados.
        </p>

      </div>

    </div>

  </div>

</section>



<!-- =========================================================
     COMMUNICATION
========================================================== -->

<section
  class="relative
         py-24
         overflow-hidden"
>

  <div
    class="absolute
           inset-0
           bg-agricolus-900"
  ></div>


  <div
    class="absolute
           -right-40
           -top-40
           w-[500px]
           h-[500px]
           rounded-full
           bg-agricolus-600/20"
  ></div>


  <div
    class="container-main
           relative z-10"
  >

    <div
      class="grid lg:grid-cols-2
             gap-16
             items-center"
    >


      <div class="text-white">

        <p
          class="uppercase
                 tracking-[3px]
                 text-agricolus-300
                 text-sm
                 font-bold"
        >
          Comunicación
        </p>


        <h2
          class="mt-5
                 text-4xl md:text-5xl
                 font-bold"
        >
          Mantén conectada
          a toda la cadena
        </h2>


        <p
          class="mt-7
                 text-white/70
                 text-lg
                 leading-relaxed"
        >

          Envía información directamente
          a las explotaciones: noticias,
          documentos, banners, alertas
          y contenidos formativos.

        </p>


        <div
          class="mt-9
                 space-y-4"
        >

          <div
            class="flex items-center
                   gap-4"
          >

            <span
              class="w-9 h-9
                     rounded-full
                     bg-agricolus-500
                     flex items-center
                     justify-center"
            >
              ✓
            </span>

            Noticias

          </div>


          <div
            class="flex items-center
                   gap-4"
          >

            <span
              class="w-9 h-9
                     rounded-full
                     bg-agricolus-500
                     flex items-center
                     justify-center"
            >
              ✓
            </span>

            Documentos

          </div>


          <div
            class="flex items-center
                   gap-4"
          >

            <span
              class="w-9 h-9
                     rounded-full
                     bg-agricolus-500
                     flex items-center
                     justify-center"
            >
              ✓
            </span>

            Alertas y boletines

          </div>


          <div
            class="flex items-center
                   gap-4"
          >

            <span
              class="w-9 h-9
                     rounded-full
                     bg-agricolus-500
                     flex items-center
                     justify-center"
            >
              ✓
            </span>

            Formación online

          </div>

        </div>

      </div>


      <!-- PHONE MOCKUP -->

      <div class="flex justify-center">

        <div
          class="w-[290px]
                 bg-[#171d17]
                 rounded-[40px]
                 p-3
                 shadow-2xl"
        >

          <div
            class="bg-white
                   rounded-[30px]
                   overflow-hidden"
          >

            <div
              class="bg-agricolus-700
                     h-16
                     flex items-center
                     px-5
                     text-white
                     font-bold"
            >
              AgriTrack
            </div>


            <div class="p-5">

              <div
                class="text-xs
                       text-gray-400"
              >
                COMUNICACIÓN
              </div>


              <h3
                class="text-xl
                       font-bold
                       mt-2"
              >
                Novedades
              </h3>


              <div
                class="mt-5
                       space-y-4"
              >

                <div
                  class="border
                         rounded-2xl
                         p-4"
                >

                  <div
                    class="w-full
                           h-20
                           rounded-xl
                           bg-agricolus-100
                           flex items-center
                           justify-center
                           text-3xl"
                  >
                    🌾
                  </div>

                  <div
                    class="font-bold
                           mt-3"
                  >
                    Nuevas recomendaciones
                  </div>

                  <p
                    class="text-xs
                           text-gray-500
                           mt-1"
                  >
                    Información para las
                    explotaciones.
                  </p>

                </div>


                <div
                  class="border
                         rounded-2xl
                         p-4"
                >

                  <div
                    class="font-bold"
                  >
                    Boletín de alerta
                  </div>

                  <p
                    class="text-xs
                           text-gray-500
                           mt-2"
                  >
                    Revisa las condiciones
                    de tus cultivos.
                  </p>

                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =========================================================
     CTA
========================================================== -->

<section
  id="contacto"
  class="py-24"
>

  <div class="container-main">

    <div
      class="relative
             overflow-hidden
             rounded-[40px]
             bg-agricolus-600
             text-white"
    >

      <div
        class="absolute
               inset-0
               opacity-15"
        style="
          background-image:
            url('https://images.unsplash.com/photo-1523742817394-9f5f9c1b4e9a?auto=format&fit=crop&w=1800&q=80');
          background-size:cover;
          background-position:center;
        "
      ></div>


      <div
        class="relative z-10
               px-8 md:px-16
               py-20
               text-center"
      >

        <p
          class="uppercase
                 tracking-[3px]
                 text-agricolus-200
                 text-sm
                 font-bold"
        >
          AgriTrack
        </p>


        <h2
          class="mt-5
                 text-4xl md:text-5xl
                 font-bold"
        >
          ¿Quieres digitalizar
          tu cadena agroalimentaria?
        </h2>


        <p
          class="mt-6
                 max-w-2xl
                 mx-auto
                 text-white/80
                 text-lg"
        >

          Descubre cómo AgriTrack puede ayudarte
          a gestionar organizaciones, trazabilidad,
          comunicación y procesos agronómicos.

        </p>


        <div
          class="mt-9
                 flex flex-wrap
                 justify-center
                 gap-4"
        >

          <a
            href="<?= $base_url ?>contacto.php"
            class="bg-white
                   text-agricolus-800
                   px-8 py-4
                   rounded-full
                   font-bold
                   hover:bg-agricolus-50
                   transition"
          >
            Solicitar más información
          </a>


          <a
            href="<?= $base_url ?>contacto.php"
            class="border
                   border-white/60
                   px-8 py-4
                   rounded-full
                   font-bold
                   hover:bg-white
                   hover:text-agricolus-800
                   transition"
          >
            Contactar
          </a>

        </div>

      </div>

    </div>

  </div>

</section>

<?php
include __DIR__ . '/../includes/footer.php';
?>

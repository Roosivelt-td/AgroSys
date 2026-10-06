<?php
$page_title = "Sostenibilidad de la cadena agroalimentaria | Agricolus";
$base_url = "./";
$current_page = "sostenibilidad";
$body_attrs = 'x-data="{ mobileMenu: false, activeChallenge: null, newsletterSent: false }"';
$body_class = "bg-white";

$page_styles = <<<'CSS'
    .container-agri {
      width: min(1180px, calc(100% - 40px));
      margin: auto;
    }

    .hero-bg {
      background:
        radial-gradient(
          circle at 80% 20%,
          rgba(120,169,74,.18),
          transparent 30%
        ),
        linear-gradient(
          180deg,
          #f4f8ef 0%,
          #ffffff 100%
        );
    }

    .hero-image {
      background-image:
        linear-gradient(
          90deg,
          rgba(38,61,32,.05),
          rgba(38,61,32,0)
        ),
        url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1500&q=85');
      background-size: cover;
      background-position: center;
    }

    .green-image {
      background-image:
        linear-gradient(
          rgba(38,61,32,.68),
          rgba(38,61,32,.68)
        ),
        url('https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1800&q=85');
      background-size: cover;
      background-position: center;
    }

    .shadow-agri {
      box-shadow:
        0 25px 70px rgba(38,61,32,.12);
    }

    .card-hover {
      transition:
        transform .3s ease,
        box-shadow .3s ease;
    }

    .card-hover:hover {
      transform: translateY(-7px);
      box-shadow:
        0 25px 50px rgba(38,61,32,.12);
    }

    .number {
      font-variant-numeric: tabular-nums;
    }
CSS;

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>

<!-- ==========================================================
     HERO
========================================================== -->

<main id="sostenibilidad">

<section
  class="hero-bg
         pt-[82px]
         overflow-hidden"
>

  <div class="container-agri">

    <div
      class="grid
             lg:grid-cols-2
             min-h-[680px]
             items-center
             gap-12
             py-20"
    >


      <!-- TEXT -->

      <div>

        <div
          class="inline-flex
                 items-center
                 gap-2
                 bg-agri-100
                 text-agri-700
                 px-4 py-2
                 rounded-full
                 text-sm
                 font-bold"
        >

          <span
            class="w-2 h-2
                   bg-agri-500
                   rounded-full"
          ></span>

          Agricultura sostenible

        </div>


        <h1
          class="mt-7
                 text-5xl
                 md:text-6xl
                 lg:text-[67px]
                 leading-[.98]
                 font-black
                 tracking-tight
                 text-agri-900"
        >

          Sostenibilidad
          de la cadena

          <span class="text-agri-600">
            agroalimentaria
          </span>

        </h1>


        <p
          class="mt-8
                 max-w-xl
                 text-lg
                 leading-8
                 text-gray-600"
        >

          La transformación digital puede ayudar a todos los
          actores de la cadena agroalimentaria a afrontar los
          grandes retos de la agricultura actual.

        </p>


        <p
          class="mt-5
                 max-w-xl
                 text-lg
                 leading-8
                 text-gray-600"
        >

          Datos, trazabilidad y herramientas digitales para
          tomar decisiones más eficientes y sostenibles.

        </p>


        <div class="mt-9 flex flex-wrap gap-4">

          <a
            href="#retos"
            class="bg-agri-600
                   hover:bg-agri-700
                   text-white
                   px-7 py-4
                   rounded-full
                   font-bold
                   transition"
          >
            Descubre más
          </a>

          <a
            href="#resultados"
            class="border
                   border-agri-600
                   text-agri-700
                   px-7 py-4
                   rounded-full
                   font-bold
                   hover:bg-agri-50
                   transition"
          >
            Ver resultados
          </a>

        </div>

      </div>



      <!-- IMAGE -->

      <div class="relative">

        <div
          class="absolute
                 -top-10
                 -right-5
                 w-36 h-36
                 bg-agri-200
                 rounded-full
                 opacity-70"
        ></div>

        <div
          class="absolute
                 -bottom-8
                 -left-8
                 w-44 h-44
                 bg-agri-100
                 rounded-full"
        ></div>


        <div
          class="relative
                 overflow-hidden
                 rounded-[45px]
                 rounded-bl-[120px]
                 shadow-agri"
        >

          <img
            src="https://images.unsplash.com/photo-1523742810-5a0a9b3b5a9e?auto=format&fit=crop&w=1400&q=85"
            alt="Agricultura sostenible"
            class="w-full
                   h-[560px]
                   object-cover"
          >


          <!-- floating card -->

          <div
            class="absolute
                   bottom-7
                   left-7
                   right-7
                   bg-white/95
                   backdrop-blur
                   rounded-2xl
                   p-5
                   shadow-xl"
          >

            <div class="flex items-center gap-4">

              <div
                class="w-12 h-12
                       rounded-xl
                       bg-agri-100
                       flex items-center
                       justify-center
                       text-2xl"
              >
                🌱
              </div>

              <div>

                <div
                  class="font-bold
                         text-agri-900"
                >
                  Making AgriTech Sustainable
                </div>

                <div
                  class="text-sm
                         text-gray-500"
                >
                  Tecnología para una agricultura más eficiente
                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- ==========================================================
     INTRO
========================================================== -->

<section class="py-24 bg-white">

  <div class="container-agri">

    <div
      class="max-w-3xl"
    >

      <span
        class="text-agri-600
               uppercase
               tracking-[.25em]
               text-sm
               font-bold"
      >
        El desafío
      </span>


      <h2
        class="mt-5
               text-4xl
               md:text-5xl
               font-black
               text-agri-900
               leading-tight"
      >

        Una cadena agroalimentaria
        preparada para el futuro

      </h2>


      <p
        class="mt-6
               text-lg
               text-gray-600
               leading-8"
      >

        El sector agroalimentario mundial se enfrenta a una
        serie de preocupaciones impulsadas por grandes
        tendencias económicas, ambientales y sociales.

      </p>

    </div>

  </div>

</section>



<!-- ==========================================================
     CHALLENGES
========================================================== -->

<section
  id="retos"
  class="py-24
         bg-agri-50"
>

  <div class="container-agri">

    <div class="text-center">

      <span
        class="text-agri-600
               uppercase
               tracking-[.25em]
               text-sm
               font-bold"
      >
        Los retos
      </span>


      <h2
        class="mt-4
               text-4xl
               md:text-5xl
               font-black
               text-agri-900"
      >
        Los grandes desafíos
      </h2>


      <p
        class="max-w-2xl
               mx-auto
               mt-5
               text-gray-600
               text-lg"
      >
        Cinco factores están transformando la manera en que
        producimos, distribuimos y consumimos alimentos.
      </p>

    </div>


    <div
      class="mt-16
             grid
             md:grid-cols-2
             lg:grid-cols-5
             gap-5"
    >


      <!-- CARD 1 -->

      <button
        @click="activeChallenge = activeChallenge === 1 ? null : 1"
        class="card-hover
               text-left
               bg-white
               rounded-[28px]
               p-7
               border
               border-gray-100"
      >

        <div
          class="w-14 h-14
                 rounded-2xl
                 bg-orange-50
                 flex items-center
                 justify-center
                 text-2xl"
        >
          ☀️
        </div>

        <h3
          class="mt-6
                 font-extrabold
                 text-agri-900"
        >
          Cambio climático
        </h3>

        <p
          class="mt-3
                 text-sm
                 leading-6
                 text-gray-500"
        >
          Fenómenos extremos y variabilidad climática.
        </p>

        <div
          x-show="activeChallenge === 1"
          x-transition
          class="mt-4
                 text-xs
                 text-agri-600
                 font-semibold"
        >
          Monitorización y datos para anticiparse a las
          variaciones del campo.
        </div>

      </button>



      <!-- CARD 2 -->

      <button
        @click="activeChallenge = activeChallenge === 2 ? null : 2"
        class="card-hover
               text-left
               bg-white
               rounded-[28px]
               p-7
               border
               border-gray-100"
      >

        <div
          class="w-14 h-14
                 rounded-2xl
                 bg-blue-50
                 flex items-center
                 justify-center
                 text-2xl"
        >
          📈
        </div>

        <h3
          class="mt-6
                 font-extrabold
                 text-agri-900"
        >
          Rentabilidad
        </h3>

        <p
          class="mt-3
                 text-sm
                 leading-6
                 text-gray-500"
        >
          Competitividad y rendimiento de las explotaciones.
        </p>

        <div
          x-show="activeChallenge === 2"
          x-transition
          class="mt-4
                 text-xs
                 text-agri-600
                 font-semibold"
        >
          Optimización de recursos y seguimiento de costes.
        </div>

      </button>



      <!-- CARD 3 -->

      <button
        @click="activeChallenge = activeChallenge === 3 ? null : 3"
        class="card-hover
               text-left
               bg-white
               rounded-[28px]
               p-7
               border
               border-gray-100"
      >

        <div
          class="w-14 h-14
                 rounded-2xl
                 bg-purple-50
                 flex items-center
                 justify-center
                 text-2xl"
        >
          👨‍🌾
        </div>

        <h3
          class="mt-6
                 font-extrabold
                 text-agri-900"
        >
          Mano de obra
        </h3>

        <p
          class="mt-3
                 text-sm
                 leading-6
                 text-gray-500"
        >
          Disponibilidad y gestión del trabajo agrícola.
        </p>

        <div
          x-show="activeChallenge === 3"
          x-transition
          class="mt-4
                 text-xs
                 text-agri-600
                 font-semibold"
        >
          Digitalización para facilitar la planificación
          y coordinación.
        </div>

      </button>



      <!-- CARD 4 -->

      <button
        @click="activeChallenge = activeChallenge === 4 ? null : 4"
        class="card-hover
               text-left
               bg-white
               rounded-[28px]
               p-7
               border
               border-gray-100"
      >

        <div
          class="w-14 h-14
                 rounded-2xl
                 bg-yellow-50
                 flex items-center
                 justify-center
                 text-2xl"
        >
          💶
        </div>

        <h3
          class="mt-6
                 font-extrabold
                 text-agri-900"
        >
          Volatilidad
        </h3>

        <p
          class="mt-3
                 text-sm
                 leading-6
                 text-gray-500"
        >
          Precios y costes de las materias primas.
        </p>

        <div
          x-show="activeChallenge === 4"
          x-transition
          class="mt-4
                 text-xs
                 text-agri-600
                 font-semibold"
        >
          Datos integrados para mejorar la toma de decisiones.
        </div>

      </button>



      <!-- CARD 5 -->

      <button
        @click="activeChallenge = activeChallenge === 5 ? null : 5"
        class="card-hover
               text-left
               bg-agri-600
               rounded-[28px]
               p-7
               text-white
               border
               border-agri-600"
      >

        <div
          class="w-14 h-14
                 rounded-2xl
                 bg-white/15
                 flex items-center
                 justify-center
                 text-2xl"
        >
          🌍
        </div>

        <h3
          class="mt-6
                 font-extrabold"
        >
          Sostenibilidad
        </h3>

        <p
          class="mt-3
                 text-sm
                 leading-6
                 text-green-100"
        >
          Reducir el impacto ambiental de la producción.
        </p>

        <div
          x-show="activeChallenge === 5"
          x-transition
          class="mt-4
                 text-xs
                 text-green-100
                 font-semibold"
        >
          Control de agua, insumos, energía y biodiversidad.
        </div>

      </button>

    </div>

  </div>

</section>



<!-- ==========================================================
     RESULTS
========================================================== -->

<section
  id="resultados"
  class="py-24 bg-white"
>

  <div class="container-agri">

    <div
      class="grid
             lg:grid-cols-2
             gap-16
             items-center"
    >


      <!-- IMAGE -->

      <div class="relative">

        <div
          class="absolute
                 -top-7
                 -left-7
                 w-32 h-32
                 rounded-full
                 bg-agri-100"
        ></div>


        <div
          class="relative
                 rounded-[45px]
                 overflow-hidden
                 shadow-agri"
        >

          <img
            src="https://images.unsplash.com/photo-1495107334309-fcf20504a5ab?auto=format&fit=crop&w=1400&q=85"
            alt="Campo sostenible"
            class="w-full
                   h-[560px]
                   object-cover"
          >

        </div>

      </div>



      <!-- CONTENT -->

      <div>

        <span
          class="text-agri-600
                 uppercase
                 tracking-[.25em]
                 text-sm
                 font-bold"
        >
          Datos que importan
        </span>


        <h2
          class="mt-5
                 text-4xl
                 md:text-5xl
                 font-black
                 text-agri-900
                 leading-tight"
        >
          Digitalización para
          medir y mejorar
        </h2>


        <p
          class="mt-6
                 text-lg
                 leading-8
                 text-gray-600"
        >

          Las herramientas digitales permiten recopilar
          información de las explotaciones y transformarla
          en indicadores útiles para la toma de decisiones.

        </p>


        <!-- METRICS -->

        <div
          class="mt-10
                 grid
                 grid-cols-2
                 gap-4"
        >

          <div
            class="rounded-3xl
                   bg-agri-50
                   p-6"
          >

            <div
              class="text-4xl
                     font-black
                     text-agri-600
                     number"
            >
              -25%
            </div>

            <div
              class="mt-2
                     text-sm
                     text-gray-600"
            >
              Uso de inputs
            </div>

          </div>


          <div
            class="rounded-3xl
                   bg-blue-50
                   p-6"
          >

            <div
              class="text-4xl
                     font-black
                     text-blue-600
                     number"
            >
              -30%
            </div>

            <div
              class="mt-2
                     text-sm
                     text-gray-600"
            >
              Consumo de agua
            </div>

          </div>


          <div
            class="rounded-3xl
                   bg-emerald-50
                   p-6"
          >

            <div
              class="text-4xl
                     font-black
                     text-emerald-600"
            >
              ↓
            </div>

            <div
              class="mt-2
                     text-sm
                     text-gray-600"
            >
              Emisiones
            </div>

          </div>


          <div
            class="rounded-3xl
                   bg-yellow-50
                   p-6"
          >

            <div
              class="text-4xl
                     font-black
                     text-yellow-600"
            >
              ↑
            </div>

            <div
              class="mt-2
                     text-sm
                     text-gray-600"
            >
              Rendimiento
            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- ==========================================================
     SUSTAINABILITY INDICATORS
========================================================== -->

<section
  class="py-24
         bg-agri-50"
>

  <div class="container-agri">

    <div class="text-center max-w-3xl mx-auto">

      <span
        class="text-agri-600
               uppercase
               tracking-[.25em]
               text-sm
               font-bold"
      >
        Indicadores
      </span>


      <h2
        class="mt-4
               text-4xl
               md:text-5xl
               font-black
               text-agri-900"
      >
        Una visión completa
        de la sostenibilidad
      </h2>


      <p
        class="mt-5
               text-gray-600
               text-lg
               leading-8"
      >

        Evalúa los principales factores económicos y ambientales
        de las explotaciones y realiza un seguimiento de tus
        objetivos.

      </p>

    </div>



    <div
      class="mt-16
             grid
             md:grid-cols-3
             gap-6"
    >

      <!-- ECONOMIC -->

      <div
        class="card-hover
               bg-white
               rounded-[32px]
               p-8"
      >

        <div
          class="w-16 h-16
                 rounded-2xl
                 bg-blue-50
                 flex items-center
                 justify-center
                 text-3xl"
        >
          📊
        </div>

        <h3
          class="mt-7
                 text-2xl
                 font-black
                 text-agri-900"
        >
          Económica
        </h3>

        <p
          class="mt-3
                 text-gray-500
                 leading-7"
        >
          Indicadores relacionados con el rendimiento y
          producción de los cultivos.
        </p>

        <div
          class="mt-6
                 space-y-3"
        >

          <div
            class="flex justify-between
                   text-sm"
          >
            <span>Rendimiento</span>
            <strong>82%</strong>
          </div>

          <div
            class="h-2
                   bg-gray-100
                   rounded-full
                   overflow-hidden"
          >

            <div
              class="h-full
                     bg-blue-500
                     rounded-full"
              style="width:82%"
            ></div>

          </div>

        </div>

      </div>


      <!-- ENVIRONMENTAL -->

      <div
        class="card-hover
               bg-white
               rounded-[32px]
               p-8"
      >

        <div
          class="w-16 h-16
                 rounded-2xl
                 bg-green-50
                 flex items-center
                 justify-center
                 text-3xl"
        >
          🌱
        </div>

        <h3
          class="mt-7
                 text-2xl
                 font-black
                 text-agri-900"
        >
          Ambiental
        </h3>

        <p
          class="mt-3
                 text-gray-500
                 leading-7"
        >
          Seguimiento del agua, fertilizantes, productos
          fitosanitarios y biodiversidad.
        </p>

        <div
          class="mt-6
                 space-y-3"
        >

          <div
            class="flex justify-between
                   text-sm"
          >
            <span>Objetivo ambiental</span>
            <strong>76%</strong>
          </div>

          <div
            class="h-2
                   bg-gray-100
                   rounded-full
                   overflow-hidden"
          >

            <div
              class="h-full
                     bg-agri-500
                     rounded-full"
              style="width:76%"
            ></div>

          </div>

        </div>

      </div>



      <!-- OBJECTIVES -->

      <div
        class="card-hover
               bg-agri-600
               rounded-[32px]
               p-8
               text-white"
      >

        <div
          class="w-16 h-16
                 rounded-2xl
                 bg-white/15
                 flex items-center
                 justify-center
                 text-3xl"
        >
          🎯
        </div>

        <h3
          class="mt-7
                 text-2xl
                 font-black"
        >
          Objetivos
        </h3>

        <p
          class="mt-3
                 text-green-100
                 leading-7"
        >
          Define objetivos y monitoriza su evolución
          durante la campaña agrícola.
        </p>

        <div
          class="mt-6"
        >

          <div
            class="flex justify-between
                   text-sm
                   text-green-100"
          >
            <span>Progreso</span>
            <strong>68%</strong>
          </div>

          <div
            class="mt-3
                   h-2
                   bg-white/20
                   rounded-full
                   overflow-hidden"
          >

            <div
              class="h-full
                     bg-white
                     rounded-full"
              style="width:68%"
            ></div>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- ==========================================================
     TRACEABILITY
========================================================== -->

<section class="py-24 bg-white">

  <div class="container-agri">

    <div
      class="grid
             lg:grid-cols-2
             gap-16
             items-center"
    >

      <div>

        <span
          class="text-agri-600
                 uppercase
                 tracking-[.25em]
                 text-sm
                 font-bold"
        >
          Trazabilidad
        </span>


        <h2
          class="mt-5
                 text-4xl
                 md:text-5xl
                 font-black
                 text-agri-900"
        >
          Una cadena
          más transparente
        </h2>


        <p
          class="mt-6
                 text-lg
                 leading-8
                 text-gray-600"
        >

          La trazabilidad permite seguir el producto desde
          la producción hasta el consumidor y disponer de
          información en cada etapa del proceso.

        </p>


        <div class="mt-8 space-y-5">

          <div class="flex gap-4">

            <div
              class="w-10 h-10
                     flex-shrink-0
                     rounded-full
                     bg-agri-100
                     text-agri-600
                     flex items-center
                     justify-center
                     font-bold"
            >
              1
            </div>

            <div>

              <h3 class="font-bold text-agri-900">
                Producción
              </h3>

              <p class="text-gray-500 text-sm mt-1">
                Datos y operaciones realizadas en el campo.
              </p>

            </div>

          </div>


          <div class="flex gap-4">

            <div
              class="w-10 h-10
                     flex-shrink-0
                     rounded-full
                     bg-agri-100
                     text-agri-600
                     flex items-center
                     justify-center
                     font-bold"
            >
              2
            </div>

            <div>

              <h3 class="font-bold text-agri-900">
                Transformación
              </h3>

              <p class="text-gray-500 text-sm mt-1">
                Información de los procesos de transformación.
              </p>

            </div>

          </div>


          <div class="flex gap-4">

            <div
              class="w-10 h-10
                     flex-shrink-0
                     rounded-full
                     bg-agri-100
                     text-agri-600
                     flex items-center
                     justify-center
                     font-bold"
            >
              3
            </div>

            <div>

              <h3 class="font-bold text-agri-900">
                Distribución
              </h3>

              <p class="text-gray-500 text-sm mt-1">
                Seguimiento hasta la llegada del producto.
              </p>

            </div>

          </div>

        </div>

      </div>



      <!-- TRACEABILITY VISUAL -->

      <div
        class="relative
               bg-agri-50
               rounded-[40px]
               p-8
               md:p-12"
      >

        <div class="relative">

          <div
            class="absolute
                   top-10
                   left-10
                   right-10
                   h-1
                   bg-agri-300"
          ></div>


          <div
            class="grid
                   grid-cols-3
                   gap-5
                   relative"
          >

            <div class="text-center">

              <div
                class="mx-auto
                       w-20 h-20
                       rounded-full
                       bg-agri-600
                       text-white
                       flex items-center
                       justify-center
                       text-3xl
                       shadow-lg"
              >
                🌾
              </div>

              <div
                class="mt-4
                       font-bold
                       text-agri-900"
              >
                Campo
              </div>

            </div>


            <div class="text-center">

              <div
                class="mx-auto
                       w-20 h-20
                       rounded-full
                       bg-agri-600
                       text-white
                       flex items-center
                       justify-center
                       text-3xl
                       shadow-lg"
              >
                🏭
              </div>

              <div
                class="mt-4
                       font-bold
                       text-agri-900"
              >
                Industria
              </div>

            </div>


            <div class="text-center">

              <div
                class="mx-auto
                       w-20 h-20
                       rounded-full
                       bg-agri-600
                       text-white
                       flex items-center
                       justify-center
                       text-3xl
                       shadow-lg"
              >
                🛒
              </div>

              <div
                class="mt-4
                       font-bold
                       text-agri-900"
              >
                Consumidor
              </div>

            </div>

          </div>


          <div
            class="mt-14
                   bg-white
                   rounded-3xl
                   p-6
                   shadow-lg"
          >

            <div
              class="flex
                     items-center
                     justify-between"
            >

              <div>

                <div
                  class="text-xs
                         uppercase
                         tracking-wider
                         text-gray-400"
                >
                  Lote de producción
                </div>

                <div
                  class="mt-1
                         font-bold
                         text-agri-900"
                >
                  LOT-2026-00184
                </div>

              </div>

              <div
                class="w-11 h-11
                       rounded-xl
                       bg-green-100
                       flex items-center
                       justify-center"
              >
                ✓
              </div>

            </div>


            <div
              class="mt-5
                     grid
                     grid-cols-3
                     gap-3
                     text-center"
            >

              <div
                class="bg-gray-50
                       rounded-xl
                       py-3"
              >

                <div
                  class="text-xs
                         text-gray-400"
                >
                  Cultivo
                </div>

                <div
                  class="font-bold
                         text-sm
                         mt-1"
                >
                  Trigo
                </div>

              </div>


              <div
                class="bg-gray-50
                       rounded-xl
                       py-3"
              >

                <div
                  class="text-xs
                         text-gray-400"
                >
                  Estado
                </div>

                <div
                  class="font-bold
                         text-sm
                         mt-1
                         text-green-600"
                >
                  Trazado
                </div>

              </div>


              <div
                class="bg-gray-50
                       rounded-xl
                       py-3"
              >

                <div
                  class="text-xs
                         text-gray-400"
                >
                  Datos
                </div>

                <div
                  class="font-bold
                         text-sm
                         mt-1"
                >
                  100%
                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- ==========================================================
     PARTNERS
========================================================== -->

<section
  class="py-24
         bg-agri-50"
>

  <div class="container-agri">

    <div class="text-center">

      <span
        class="text-agri-600
               uppercase
               tracking-[.25em]
               text-sm
               font-bold"
      >
        Colaboraciones
      </span>


      <h2
        class="mt-4
               text-4xl
               md:text-5xl
               font-black
               text-agri-900"
      >
        Juntos por una cadena
        más sostenible
      </h2>


      <p
        class="mt-5
               max-w-2xl
               mx-auto
               text-gray-600
               text-lg"
      >
        La innovación necesita colaboración entre empresas,
        profesionales y organizaciones del sector.
      </p>

    </div>


    <div
      class="mt-14
             grid
             grid-cols-2
             md:grid-cols-4
             gap-5"
    >

      <div
        class="h-28
               bg-white
               rounded-2xl
               flex items-center
               justify-center
               font-black
               text-2xl
               text-gray-400"
      >
        SATA
      </div>

      <div
        class="h-28
               bg-white
               rounded-2xl
               flex items-center
               justify-center
               font-black
               text-xl
               text-gray-400"
      >
        PARTNER
      </div>

      <div
        class="h-28
               bg-white
               rounded-2xl
               flex items-center
               justify-center
               font-black
               text-xl
               text-gray-400"
      >
        AGRIFOOD
      </div>

      <div
        class="h-28
               bg-white
               rounded-2xl
               flex items-center
               justify-center
               font-black
               text-xl
               text-gray-400"
      >
        INNOVATION
      </div>

    </div>

  </div>

</section>



<!-- ==========================================================
     BIG CTA
========================================================== -->

<section class="green-image py-28">

  <div
    class="container-agri
           text-center
           text-white"
  >

    <div
      class="uppercase
             tracking-[.4em]
             text-sm
             font-bold
             text-green-200"
    >
      Agricolus
    </div>


    <h2
      class="mt-6
             text-4xl
             md:text-6xl
             font-black"
    >
      Making AgriTech
      Sustainable
    </h2>


    <p
      class="max-w-2xl
             mx-auto
             mt-6
             text-lg
             leading-8
             text-green-100"
    >

      Descubre cómo las herramientas digitales pueden ayudarte
      a gestionar de forma más eficiente y sostenible tu cadena
      agroalimentaria.

    </p>


    <div class="mt-9">

      <a
        href="<?= $base_url ?>contacto.php"
        class="inline-flex
               bg-white
               text-agri-900
               px-8 py-4
               rounded-full
               font-black
               hover:bg-agri-50
               transition"
      >
        SOLICITA INFORMACIÓN
      </a>

    </div>

  </div>

</section>



<!-- ==========================================================
     NEWSLETTER
========================================================== -->

<section
  id="contacto"
  class="py-24
         bg-agri-900
         text-white"
>

  <div class="container-agri">

    <div
      class="grid
             lg:grid-cols-2
             gap-16
             items-center"
    >

      <div>

        <span
          class="text-agri-300
                 uppercase
                 tracking-[.25em]
                 text-sm
                 font-bold"
        >
          Mantente informado
        </span>


        <h2
          class="mt-5
                 text-4xl
                 md:text-5xl
                 font-black"
        >
          Agricultura,
          tecnología y
          sostenibilidad.
        </h2>


        <p
          class="mt-6
                 text-green-100
                 text-lg
                 leading-8"
        >
          Recibe novedades sobre agricultura de precisión,
          innovación y herramientas digitales.
        </p>

      </div>


      <form
        @submit.prevent="newsletterSent = true"
        class="bg-white
               rounded-[30px]
               p-8
               text-gray-800"
      >

        <div
          x-show="!newsletterSent"
        >

          <label
            class="block
                   text-sm
                   font-bold
                   mb-2"
          >
            Nombre
          </label>

          <input
            type="text"
            required
            placeholder="Tu nombre"
            class="w-full
                   px-5 py-4
                   rounded-xl
                   border
                   border-gray-200
                   outline-none
                   focus:ring-2
                   focus:ring-agri-500"
          >


          <label
            class="block
                   text-sm
                   font-bold
                   mt-5
                   mb-2"
          >
            Email
          </label>

          <input
            type="email"
            required
            placeholder="tu@email.com"
            class="w-full
                   px-5 py-4
                   rounded-xl
                   border
                   border-gray-200
                   outline-none
                   focus:ring-2
                   focus:ring-agri-500"
          >


          <label
            class="flex
                   gap-3
                   mt-5
                   text-sm
                   text-gray-500"
          >

            <input
              type="checkbox"
              required
              class="mt-1 accent-green-600"
            >

            <span>
              Acepto la política de privacidad.
            </span>

          </label>


          <button
            type="submit"
            class="w-full
                   mt-6
                   bg-agri-600
                   hover:bg-agri-700
                   text-white
                   py-4
                   rounded-full
                   font-black
                   transition"
          >
            SUSCRIBIRME
          </button>

        </div>


        <div
          x-show="newsletterSent"
          x-transition
          class="text-center
                 py-10"
        >

          <div
            class="w-16 h-16
                   mx-auto
                   rounded-full
                   bg-green-100
                   text-green-600
                   flex items-center
                   justify-center
                   text-3xl"
          >
            ✓
          </div>

          <h3
            class="mt-5
                   text-2xl
                   font-black
                   text-agri-900"
          >
            ¡Gracias!
          </h3>

          <p
            class="mt-2
                   text-gray-500"
          >
            Tu solicitud ha sido registrada.
          </p>

        </div>

      </form>

    </div>

  </div>

</section>

</main>

<?php
include __DIR__ . '/includes/footer.php';
?>

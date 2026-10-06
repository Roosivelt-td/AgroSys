<?php
$page_title = "Riego y Nutrición | Agricolus";
$base_url = "../";
$current_page = "soluciones";
$body_attrs = 'x-data="{ modal: false }"';

$page_styles = <<<'CSS'
    .container-agri {
      width: min(1180px, calc(100% - 40px));
      margin: 0 auto;
    }

    .hero {
      background:
        radial-gradient(
          circle at 82% 15%,
          rgba(120,169,74,.18),
          transparent 30%
        ),
        linear-gradient(
          135deg,
          #f4f8ef 0%,
          #ffffff 70%
        );
    }

    .hero-image {
      background:
        linear-gradient(
          90deg,
          rgba(26,52,28,.78),
          rgba(26,52,28,.12)
        ),
        url(
          'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=2000&q=90'
        );
      background-size: cover;
      background-position: center;
    }

    .water-image {
      background:
        linear-gradient(
          rgba(20,70,45,.08),
          rgba(20,70,45,.08)
        ),
        url(
          'https://images.unsplash.com/photo-1560493676-04071c5f467b?auto=format&fit=crop&w=1600&q=90'
        );
      background-size: cover;
      background-position: center;
    }

    .farm-image {
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

    .weather-image {
      background:
        linear-gradient(
          rgba(25,55,45,.18),
          rgba(25,55,45,.18)
        ),
        url(
          'https://images.unsplash.com/photo-1504608524841-42fe6f032b4b?auto=format&fit=crop&w=1600&q=90'
        );
      background-size: cover;
      background-position: center;
    }

    .glass {
      background: rgba(255,255,255,.82);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
    }

    .soft-shadow {
      box-shadow: 0 25px 70px rgba(38,61,32,.11);
    }

    .card {
      transition:
        transform .35s ease,
        box-shadow .35s ease,
        border-color .35s ease;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 60px rgba(38,61,32,.13);
      border-color: #d4e5bd;
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
      background-size: 35px 35px;
    }

    .water-gradient {
      background:
        linear-gradient(
          135deg,
          #0e6655,
          #51a878
        );
    }
CSS;

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/header.php';
?>

<!-- =====================================================
     HERO
===================================================== -->

<section
  id="solucion"
  class="
    hero
    pt-[82px]
    overflow-hidden
  "
>

  <div class="container-agri">

    <div
      class="
        min-h-[700px]
        grid
        lg:grid-cols-2
        gap-14
        items-center
        py-20
      "
    >

      <!-- TEXT -->

      <div>

        <div
          class="
            inline-flex
            items-center
            gap-3
            bg-agri-100
            text-agri-700
            px-4
            py-2
            rounded-full
            text-sm
            font-bold
          "
        >

          <span
            class="
              w-2
              h-2
              rounded-full
              bg-agri-500
            "
          ></span>

          RIEGO Y NUTRICIÓN

        </div>


        <h1
          class="
            mt-7
            text-5xl
            md:text-6xl
            lg:text-[68px]
            leading-[.95]
            font-black
            tracking-tight
            text-agri-900
          "
        >

          Gestiona el riego
          y la nutrición
          <span class="text-agri-600">
            de forma eficiente.
          </span>

        </h1>


        <p
          class="
            mt-8
            max-w-xl
            text-lg
            md:text-xl
            leading-8
            text-gray-600
          "
        >

          Utiliza modelos predictivos, datos
          meteorológicos y estaciones de campo
          para aplicar agua y fertilizantes según
          las necesidades reales de tus cultivos.

        </p>


        <div
          class="
            mt-9
            flex
            flex-wrap
            gap-4
          "
        >

          <button
            @click="modal = true"
            class="
              bg-agri-600
              hover:bg-agri-700
              text-white
              px-8
              py-4
              rounded-full
              font-black
              transition
              shadow-lg
              shadow-agri-600/20
            "
          >
            SOLICITAR INFORMACIÓN
          </button>


          <a
            href="#modelos"
            class="
              border
              border-agri-600
              text-agri-700
              px-8
              py-4
              rounded-full
              font-bold
              hover:bg-agri-50
              transition
            "
          >
            DESCUBRE LOS MODELOS
          </a>

        </div>


        <!-- PRICE -->

        <div
          class="
            mt-9
            flex
            items-center
            gap-4
          "
        >

          <div
            class="
              w-12
              h-12
              rounded-2xl
              bg-white
              shadow
              flex
              items-center
              justify-center
              text-xl
            "
          >
            💧
          </div>

          <div>

            <div
              class="
                text-xs
                uppercase
                tracking-[.2em]
                text-gray-500
                font-bold
              "
            >
              Desde
            </div>

            <div
              class="
                text-2xl
                font-black
                text-agri-900
              "
            >
              50 €/año
            </div>

          </div>

        </div>

      </div>


      <!-- HERO VISUAL -->

      <div
        class="
          relative
          h-[550px]
          lg:h-[620px]
        "
      >

        <div
          class="
            absolute
            inset-0
            rounded-[45px]
            overflow-hidden
            hero-image
            shadow-2xl
          "
        ></div>


        <!-- WATER CARD -->

        <div
          class="
            absolute
            top-8
            right-[-15px]
            md:right-[-35px]
            glass
            rounded-3xl
            p-5
            shadow-xl
            w-64
          "
        >

          <div
            class="
              flex
              items-center
              justify-between
            "
          >

            <div>

              <div
                class="
                  text-[10px]
                  uppercase
                  tracking-widest
                  font-bold
                  text-gray-400
                "
              >
                Necesidad hídrica
              </div>

              <div
                class="
                  mt-1
                  text-2xl
                  font-black
                  text-agri-900
                "
              >
                18.4 mm
              </div>

            </div>


            <div
              class="
                w-11
                h-11
                rounded-full
                bg-blue-100
                flex
                items-center
                justify-center
                text-xl
              "
            >
              💧
            </div>

          </div>


          <div
            class="
              mt-5
              h-24
              rounded-2xl
              water-gradient
              relative
              overflow-hidden
            "
          >

            <div
              class="
                absolute
                inset-0
                grid-pattern
                opacity-30
              "
            ></div>


            <div
              class="
                absolute
                bottom-3
                left-4
                text-white
                text-xs
                font-bold
              "
            >
              Balance hídrico
            </div>

          </div>


          <div
            class="
              mt-4
              flex
              justify-between
              text-xs
            "
          >

            <span class="text-gray-500">
              Estado
            </span>

            <strong class="text-blue-600">
              Intervención recomendada
            </strong>

          </div>

        </div>


        <!-- NUTRITION CARD -->

        <div
          class="
            absolute
            bottom-8
            left-[-15px]
            md:left-[-35px]
            bg-white
            rounded-3xl
            p-6
            shadow-2xl
            w-72
          "
        >

          <div
            class="
              text-xs
              uppercase
              tracking-widest
              font-bold
              text-gray-400
            "
          >
            Nutrición
          </div>


          <div
            class="
              mt-4
              grid
              grid-cols-3
              gap-2
            "
          >

            <div
              class="
                bg-blue-50
                rounded-2xl
                p-3
                text-center
              "
            >

              <div
                class="
                  text-xl
                  font-black
                  text-blue-700
                "
              >
                N
              </div>

              <div
                class="
                  mt-1
                  text-xs
                  text-gray-500
                "
              >
                82 kg
              </div>

            </div>


            <div
              class="
                bg-purple-50
                rounded-2xl
                p-3
                text-center
              "
            >

              <div
                class="
                  text-xl
                  font-black
                  text-purple-700
                "
              >
                P
              </div>

              <div
                class="
                  mt-1
                  text-xs
                  text-gray-500
                "
              >
                34 kg
              </div>

            </div>


            <div
              class="
                bg-green-50
                rounded-2xl
                p-3
                text-center
              "
            >

              <div
                class="
                  text-xl
                  font-black
                  text-green-700
                "
              >
                K
              </div>

              <div
                class="
                  mt-1
                  text-xs
                  text-gray-500
                "
              >
                56 kg
              </div>

            </div>

          </div>


          <div
            class="
              mt-4
              text-xs
              text-gray-500
            "
          >
            Recomendación de nutrientes
          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     DARK STATS
===================================================== -->

<section
  class="
    bg-agri-900
    text-white
    py-14
  "
>

  <div
    class="
      container-agri
      grid
      grid-cols-2
      md:grid-cols-4
      gap-8
    "
  >

    <div class="text-center">

      <div
        class="
          text-4xl
          font-black
        "
      >
        50 €
      </div>

      <div
        class="
          mt-2
          text-sm
          text-green-200
        "
      >
        desde / año
      </div>

    </div>


    <div class="text-center">

      <div
        class="
          text-4xl
          font-black
        "
      >
        3
      </div>

      <div
        class="
          mt-2
          text-sm
          text-green-200
        "
      >
        modelos predictivos
      </div>

    </div>


    <div class="text-center">

      <div
        class="
          text-4xl
          font-black
        "
      >
        7 días
      </div>

      <div
        class="
          mt-2
          text-sm
          text-green-200
        "
      >
        previsión meteorológica
      </div>

    </div>


    <div class="text-center">

      <div
        class="
          text-4xl
          font-black
        "
      >
        N · P · K
      </div>

      <div
        class="
          mt-2
          text-sm
          text-green-200
        "
      >
        nutrientes calculados
      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     INTRO
===================================================== -->

<section
  class="
    py-28
    bg-white
  "
>

  <div class="container-agri">

    <div
      class="
        grid
        lg:grid-cols-2
        gap-16
        items-center
      "
    >

      <div>

        <div
          class="
            text-agri-600
            text-sm
            uppercase
            tracking-[.3em]
            font-black
          "
        >
          Gestión de precisión
        </div>


        <h2
          class="
            mt-5
            text-4xl
            md:text-5xl
            font-black
            leading-tight
            text-agri-900
          "
        >
          El agua y los
          nutrientes, justo
          cuando hacen falta.
        </h2>


        <p
          class="
            mt-7
            text-lg
            leading-8
            text-gray-600
          "
        >

          Gestionar el agua y los nutrientes en el
          momento adecuado y con la dosis correcta
          ayuda a acompañar el desarrollo del cultivo
          y limitar los desperdicios.

        </p>


        <p
          class="
            mt-5
            text-lg
            leading-8
            text-gray-600
          "
        >

          Agricolus integra modelos predictivos,
          previsiones meteorológicas, datos de
          estaciones y operaciones registradas
          para ofrecer una visión completa del campo.

        </p>


        <div
          class="
            mt-8
            inline-flex
            items-center
            gap-3
            text-agri-700
            font-bold
          "
        >

          <span
            class="
              w-10
              h-10
              rounded-full
              bg-agri-100
              flex
              items-center
              justify-center
            "
          >
            ✓
          </span>

          Datos convertidos en decisiones

        </div>

      </div>


      <div
        class="
          water-image
          h-[540px]
          rounded-[45px]
          relative
          overflow-hidden
          soft-shadow
        "
      >

        <div
          class="
            absolute
            inset-x-7
            bottom-7
            bg-white/95
            backdrop-blur
            rounded-3xl
            p-6
          "
        >

          <div
            class="
              text-xs
              uppercase
              tracking-widest
              font-bold
              text-gray-400
            "
          >
            Balance del cultivo
          </div>


          <div
            class="
              mt-2
              text-xl
              font-black
              text-agri-900
            "
          >
            Información agronómica en un solo lugar.
          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     WHAT YOU CAN DO
===================================================== -->

<section
  class="
    py-28
    bg-agri-50
  "
>

  <div class="container-agri">

    <div
      class="
        max-w-3xl
      "
    >

      <div
        class="
          text-agri-600
          text-sm
          uppercase
          tracking-[.3em]
          font-black
        "
      >
        Qué puedes hacer
      </div>


      <h2
        class="
          mt-5
          text-4xl
          md:text-5xl
          font-black
          text-agri-900
        "
      >
        Gestiona cada recurso
        con mayor precisión.
      </h2>


      <p
        class="
          mt-6
          text-lg
          text-gray-600
          leading-8
        "
      >
        El módulo combina información del cultivo
        y del entorno para ayudarte a organizar
        las intervenciones.
      </p>

    </div>


    <div
      class="
        mt-14
        grid
        md:grid-cols-2
        lg:grid-cols-4
        gap-6
      "
    >

      <article
        class="
          card
          bg-white
          rounded-[30px]
          p-7
          border
          border-agri-100
        "
      >

        <div
          class="
            w-14
            h-14
            rounded-2xl
            bg-green-100
            flex
            items-center
            justify-center
            text-2xl
          "
        >
          🌱
        </div>


        <h3
          class="
            mt-7
            text-xl
            font-black
            text-agri-900
          "
        >
          Controla el crecimiento
        </h3>


        <p
          class="
            mt-3
            text-gray-500
            leading-7
            text-sm
          "
        >
          El modelo fenológico permite evaluar
          las necesidades del cultivo durante
          cada fase de desarrollo.
        </p>

      </article>


      <article
        class="
          card
          bg-white
          rounded-[30px]
          p-7
          border
          border-agri-100
        "
      >

        <div
          class="
            w-14
            h-14
            rounded-2xl
            bg-blue-100
            flex
            items-center
            justify-center
            text-2xl
          "
        >
          💧
        </div>


        <h3
          class="
            mt-7
            text-xl
            font-black
            text-agri-900
          "
        >
          Optimiza el agua
        </h3>


        <p
          class="
            mt-3
            text-gray-500
            leading-7
            text-sm
          "
        >
          Estima las necesidades de riego y
          decide cuándo intervenir y con qué
          estrategia.
        </p>

      </article>


      <article
        class="
          card
          bg-white
          rounded-[30px]
          p-7
          border
          border-agri-100
        "
      >

        <div
          class="
            w-14
            h-14
            rounded-2xl
            bg-purple-100
            flex
            items-center
            justify-center
            text-2xl
          "
        >
          🧪
        </div>


        <h3
          class="
            mt-7
            text-xl
            font-black
            text-agri-900
          "
        >
          Gestiona la fertilización
        </h3>


        <p
          class="
            mt-3
            text-gray-500
            leading-7
            text-sm
          "
        >
          Calcula las necesidades de N, P y K
          y recibe recomendaciones sobre las
          dosis a aplicar.
        </p>

      </article>


      <article
        class="
          card
          bg-white
          rounded-[30px]
          p-7
          border
          border-agri-100
        "
      >

        <div
          class="
            w-14
            h-14
            rounded-2xl
            bg-yellow-100
            flex
            items-center
            justify-center
            text-2xl
          "
        >
          📉
        </div>


        <h3
          class="
            mt-7
            text-xl
            font-black
            text-agri-900
          "
        >
          Reduce desperdicios
        </h3>


        <p
          class="
            mt-3
            text-gray-500
            leading-7
            text-sm
          "
        >
          Gestiona los insumos de forma más
          racional y evita intervenciones
          innecesarias.
        </p>

      </article>

    </div>

  </div>

</section>



<!-- =====================================================
     MODELOS
===================================================== -->

<section
  id="modelos"
  class="
    py-28
    bg-white
  "
>

  <div class="container-agri">

    <div
      class="
        text-center
        max-w-3xl
        mx-auto
      "
    >

      <div
        class="
          text-agri-600
          text-sm
          uppercase
          tracking-[.3em]
          font-black
        "
      >
        Modelos predictivos
      </div>


      <h2
        class="
          mt-5
          text-4xl
          md:text-5xl
          font-black
          text-agri-900
        "
      >
        De los datos a las decisiones.
      </h2>


      <p
        class="
          mt-6
          text-lg
          text-gray-600
          leading-8
        "
      >
        Los modelos predictivos ayudan a transformar
        información agronómica y meteorológica en
        recomendaciones para la gestión del cultivo.
      </p>

    </div>


    <div
      class="
        mt-14
        grid
        lg:grid-cols-3
        gap-7
      "
    >

      <!-- PHENOLOGY -->

      <article
        class="
          card
          rounded-[35px]
          bg-agri-50
          p-8
          border
          border-agri-100
          relative
          overflow-hidden
        "
      >

        <div
          class="
            absolute
            -right-12
            -top-12
            w-40
            h-40
            rounded-full
            bg-green-200/40
          "
        ></div>


        <div
          class="
            relative
            w-16
            h-16
            rounded-2xl
            bg-white
            flex
            items-center
            justify-center
            text-3xl
            shadow-sm
          "
        >
          🌿
        </div>


        <h3
          class="
            mt-7
            text-2xl
            font-black
            text-agri-900
          "
        >
          Modelo fenológico
        </h3>


        <p
          class="
            mt-4
            text-gray-600
            leading-7
          "
        >
          Apoya la planificación de las
          operaciones siguiendo el desarrollo
          real del cultivo.
        </p>


        <div
          class="
            mt-7
            h-2
            bg-white
            rounded-full
            overflow-hidden
          "
        >

          <div
            class="
              h-full
              w-[78%]
              bg-agri-600
              rounded-full
            "
          ></div>

        </div>


        <div
          class="
            mt-3
            flex
            justify-between
            text-xs
            text-gray-500
          "
        >

          <span>
            Desarrollo
          </span>

          <span>
            Seguimiento
          </span>

        </div>

      </article>


      <!-- IRRIGATION -->

      <article
        class="
          card
          rounded-[35px]
          bg-blue-50
          p-8
          border
          border-blue-100
          relative
          overflow-hidden
        "
      >

        <div
          class="
            absolute
            -right-12
            -top-12
            w-40
            h-40
            rounded-full
            bg-blue-200/40
          "
        ></div>


        <div
          class="
            relative
            w-16
            h-16
            rounded-2xl
            bg-white
            flex
            items-center
            justify-center
            text-3xl
            shadow-sm
          "
        >
          💧
        </div>


        <h3
          class="
            mt-7
            text-2xl
            font-black
            text-agri-900
          "
        >
          Modelo de riego
        </h3>


        <p
          class="
            mt-4
            text-gray-600
            leading-7
          "
        >
          Ayuda a gestionar el agua de forma
          óptima, considerando el balance hídrico
          del cultivo.
        </p>


        <div
          class="
            mt-7
            h-2
            bg-white
            rounded-full
            overflow-hidden
          "
        >

          <div
            class="
              h-full
              w-[65%]
              bg-blue-500
              rounded-full
            "
          ></div>

        </div>


        <div
          class="
            mt-3
            flex
            justify-between
            text-xs
            text-gray-500
          "
        >

          <span>
            Déficit hídrico
          </span>

          <span>
            Aporte
          </span>

        </div>

      </article>


      <!-- NUTRITION -->

      <article
        class="
          card
          rounded-[35px]
          bg-purple-50
          p-8
          border
          border-purple-100
          relative
          overflow-hidden
        "
      >

        <div
          class="
            absolute
            -right-12
            -top-12
            w-40
            h-40
            rounded-full
            bg-purple-200/40
          "
        ></div>


        <div
          class="
            relative
            w-16
            h-16
            rounded-2xl
            bg-white
            flex
            items-center
            justify-center
            text-3xl
            shadow-sm
          "
        >
          🧪
        </div>


        <h3
          class="
            mt-7
            text-2xl
            font-black
            text-agri-900
          "
        >
          Modelo de nutrición
        </h3>


        <p
          class="
            mt-4
            text-gray-600
            leading-7
          "
        >
          Permite realizar fertilizaciones más
          precisas y coherentes con las necesidades
          del campo.
        </p>


        <div
          class="
            mt-7
            grid
            grid-cols-3
            gap-2
          "
        >

          <div
            class="
              bg-white
              rounded-xl
              py-3
              text-center
            "
          >

            <strong
              class="
                text-blue-700
                text-lg
              "
            >
              N
            </strong>

          </div>


          <div
            class="
              bg-white
              rounded-xl
              py-3
              text-center
            "
          >

            <strong
              class="
                text-purple-700
                text-lg
              "
            >
              P
            </strong>

          </div>


          <div
            class="
              bg-white
              rounded-xl
              py-3
              text-center
            "
          >

            <strong
              class="
                text-green-700
                text-lg
              "
            >
              K
            </strong>

          </div>

        </div>

      </article>

    </div>

  </div>

</section>



<!-- =====================================================
     IRRIGATION BALANCE
===================================================== -->

<section
  class="
    py-28
    bg-agri-50
    overflow-hidden
  "
>

  <div class="container-agri">

    <div
      class="
        grid
        lg:grid-cols-2
        gap-16
        items-center
      "
    >

      <!-- VISUAL -->

      <div
        class="
          relative
        "
      >

        <div
          class="
            bg-white
            rounded-[40px]
            p-6
            md:p-8
            soft-shadow
          "
        >

          <div
            class="
              flex
              items-center
              justify-between
            "
          >

            <div>

              <div
                class="
                  text-xs
                  uppercase
                  tracking-widest
                  font-bold
                  text-gray-400
                "
              >
                Modelo de riego
              </div>

              <h3
                class="
                  mt-2
                  text-2xl
                  font-black
                  text-agri-900
                "
              >
                Balance hídrico
              </h3>

            </div>


            <div
              class="
                w-12
                h-12
                rounded-2xl
                bg-blue-100
                flex
                items-center
                justify-center
                text-xl
              "
            >
              💧
            </div>

          </div>


          <!-- CHART -->

          <div
            class="
              mt-8
              h-[310px]
              relative
              rounded-3xl
              bg-gradient-to-b
              from-blue-50
              to-white
              overflow-hidden
            "
          >

            <!-- GRID -->

            <div
              class="
                absolute
                inset-0
                opacity-50
                grid-pattern
              "
            ></div>


            <!-- LINE -->

            <svg
              viewBox="0 0 700 280"
              class="
                absolute
                inset-0
                w-full
                h-full
              "
              preserveAspectRatio="none"
            >

              <defs>

                <linearGradient
                  id="area"
                  x1="0"
                  x2="0"
                  y1="0"
                  y2="1"
                >

                  <stop
                    offset="0%"
                    stop-color="#619037"
                    stop-opacity=".35"
                  />

                  <stop
                    offset="100%"
                    stop-color="#619037"
                    stop-opacity="0"
                  />

                </linearGradient>

              </defs>


              <path
                d="
                  M0 80
                  C80 65 100 105 160 92
                  S250 50 310 100
                  S400 155 450 125
                  S550 100 700 180
                  L700 280
                  L0 280
                  Z
                "
                fill="url(#area)"
              />


              <path
                d="
                  M0 80
                  C80 65 100 105 160 92
                  S250 50 310 100
                  S400 155 450 125
                  S550 100 700 180
                "
                fill="none"
                stroke="#619037"
                stroke-width="5"
                stroke-linecap="round"
              />

            </svg>


            <!-- POINT -->

            <div
              class="
                absolute
                right-[18%]
                top-[44%]
                w-4
                h-4
                rounded-full
                bg-agri-600
                ring-8
                ring-agri-600/10
              "
            ></div>


            <div
              class="
                absolute
                right-[10%]
                top-[48%]
                bg-agri-900
                text-white
                rounded-xl
                px-3
                py-2
                text-xs
                font-bold
              "
            >
              Regar 18.4 mm
            </div>

          </div>


          <!-- CHART FOOT -->

          <div
            class="
              mt-5
              grid
              grid-cols-3
              gap-3
            "
          >

            <div
              class="
                bg-blue-50
                rounded-2xl
                p-4
              "
            >

              <div
                class="
                  text-xs
                  text-gray-500
                "
              >
                Lluvia
              </div>

              <div
                class="
                  mt-1
                  font-black
                "
              >
                4.2 mm
              </div>

            </div>


            <div
              class="
                bg-green-50
                rounded-2xl
                p-4
              "
            >

              <div
                class="
                  text-xs
                  text-gray-500
                "
              >
                Déficit
              </div>

              <div
                class="
                  mt-1
                  font-black
                "
              >
                18.4 mm
              </div>

            </div>


            <div
              class="
                bg-purple-50
                rounded-2xl
                p-4
              "
            >

              <div
                class="
                  text-xs
                  text-gray-500
                "
              >
                Próximo
              </div>

              <div
                class="
                  mt-1
                  font-black
                "
              >
                Hoy
              </div>

            </div>

          </div>

        </div>

      </div>


      <!-- TEXT -->

      <div>

        <div
          class="
            text-agri-600
            text-sm
            uppercase
            tracking-[.3em]
            font-black
          "
        >
          Riego de precisión
        </div>


        <h2
          class="
            mt-5
            text-4xl
            md:text-5xl
            font-black
            text-agri-900
            leading-tight
          "
        >
          Saber cuándo regar
          y cuánto aportar.
        </h2>


        <p
          class="
            mt-7
            text-lg
            leading-8
            text-gray-600
          "
        >
          El modelo de riego utiliza información
          como el cultivo, las características del
          suelo, las precipitaciones y las operaciones
          registradas para construir un balance hídrico.
        </p>


        <div class="mt-8 space-y-4">

          <div
            class="
              flex
              items-center
              gap-4
            "
          >

            <span
              class="
                w-10
                h-10
                rounded-full
                bg-agri-100
                flex
                items-center
                justify-center
                text-agri-700
                font-black
              "
            >
              ✓
            </span>

            <span class="font-bold">
              Seguimiento del déficit hídrico
            </span>

          </div>


          <div
            class="
              flex
              items-center
              gap-4
            "
          >

            <span
              class="
                w-10
                h-10
                rounded-full
                bg-agri-100
                flex
                items-center
                justify-center
                text-agri-700
                font-black
              "
            >
              ✓
            </span>

            <span class="font-bold">
              Datos de precipitación y riego
            </span>

          </div>


          <div
            class="
              flex
              items-center
              gap-4
            "
          >

            <span
              class="
                w-10
                h-10
                rounded-full
                bg-agri-100
                flex
                items-center
                justify-center
                text-agri-700
                font-black
              "
            >
              ✓
            </span>

            <span class="font-bold">
              Recomendaciones de intervención
            </span>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     NUTRITION
===================================================== -->

<section
  class="
    py-28
    bg-white
  "
>

  <div class="container-agri">

    <div
      class="
        grid
        lg:grid-cols-2
        gap-16
        items-center
      "
    >

      <div>

        <div
          class="
            text-agri-600
            text-sm
            uppercase
            tracking-[.3em]
            font-black
          "
        >
          Nutrición de precisión
        </div>


        <h2
          class="
            mt-5
            text-4xl
            md:text-5xl
            font-black
            text-agri-900
            leading-tight
          "
        >
          Fertilización alineada
          con las necesidades
          del cultivo.
        </h2>


        <p
          class="
            mt-7
            text-lg
            leading-8
            text-gray-600
          "
        >
          El modelo de nutrición calcula las
          necesidades totales de nitrógeno,
          fósforo y potasio durante el ciclo
          productivo y ofrece recomendaciones
          sobre las dosis a aplicar.
        </p>


        <!-- NPK -->

        <div
          class="
            mt-9
            grid
            grid-cols-3
            gap-4
          "
        >

          <div
            class="
              rounded-3xl
              bg-blue-50
              p-5
              text-center
            "
          >

            <div
              class="
                text-3xl
                font-black
                text-blue-700
              "
            >
              N
            </div>

            <div
              class="
                mt-2
                text-xs
                uppercase
                tracking-widest
                text-gray-500
              "
            >
              Nitrógeno
            </div>

          </div>


          <div
            class="
              rounded-3xl
              bg-purple-50
              p-5
              text-center
            "
          >

            <div
              class="
                text-3xl
                font-black
                text-purple-700
              "
            >
              P
            </div>

            <div
              class="
                mt-2
                text-xs
                uppercase
                tracking-widest
                text-gray-500
              "
            >
              Fósforo
            </div>

          </div>


          <div
            class="
              rounded-3xl
              bg-green-50
              p-5
              text-center
            "
          >

            <div
              class="
                text-3xl
                font-black
                text-green-700
              "
            >
              K
            </div>

            <div
              class="
                mt-2
                text-xs
                uppercase
                tracking-widest
                text-gray-500
              "
            >
              Potasio
            </div>

          </div>

        </div>

      </div>


      <!-- VISUAL -->

      <div
        class="
          bg-agri-50
          rounded-[45px]
          p-7
          md:p-10
          soft-shadow
        "
      >

        <div
          class="
            bg-white
            rounded-3xl
            p-6
          "
        >

          <div
            class="
              flex
              justify-between
              items-center
            "
          >

            <div>

              <div
                class="
                  text-xs
                  uppercase
                  tracking-widest
                  font-bold
                  text-gray-400
                "
              >
                Recomendación
              </div>

              <div
                class="
                  mt-1
                  text-xl
                  font-black
                  text-agri-900
                "
              >
                Parcela 024
              </div>

            </div>


            <div
              class="
                px-3
                py-2
                rounded-full
                bg-green-100
                text-green-700
                text-xs
                font-bold
              "
            >
              Óptimo
            </div>

          </div>


          <div
            class="
              mt-7
              space-y-5
            "
          >

            <!-- N -->

            <div>

              <div
                class="
                  flex
                  justify-between
                  text-sm
                "
              >

                <span class="font-bold">
                  Nitrógeno
                </span>

                <span class="font-black">
                  82 kg/ha
                </span>

              </div>


              <div
                class="
                  mt-2
                  h-3
                  bg-gray-100
                  rounded-full
                  overflow-hidden
                "
              >

                <div
                  class="
                    h-full
                    w-[82%]
                    bg-blue-500
                    rounded-full
                  "
                ></div>

              </div>

            </div>


            <!-- P -->

            <div>

              <div
                class="
                  flex
                  justify-between
                  text-sm
                "
              >

                <span class="font-bold">
                  Fósforo
                </span>

                <span class="font-black">
                  34 kg/ha
                </span>

              </div>


              <div
                class="
                  mt-2
                  h-3
                  bg-gray-100
                  rounded-full
                  overflow-hidden
                "
              >

                <div
                  class="
                    h-full
                    w-[48%]
                    bg-purple-500
                    rounded-full
                  "
                ></div>

              </div>

            </div>


            <!-- K -->

            <div>

              <div
                class="
                  flex
                  justify-between
                  text-sm
                "
              >

                <span class="font-bold">
                  Potasio
                </span>

                <span class="font-black">
                  56 kg/ha
                </span>

              </div>


              <div
                class="
                  mt-2
                  h-3
                  bg-gray-100
                  rounded-full
                  overflow-hidden
                "
              >

                <div
                  class="
                    h-full
                    w-[65%]
                    bg-green-500
                    rounded-full
                  "
                ></div>

              </div>

            </div>

          </div>


          <div
            class="
              mt-8
              p-4
              rounded-2xl
              bg-agri-50
              text-sm
              text-gray-600
            "
          >

            La recomendación se adapta a las
            necesidades estimadas del cultivo
            durante su ciclo productivo.

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     WEATHER
===================================================== -->

<section
  class="
    py-28
    bg-agri-50
  "
>

  <div class="container-agri">

    <div
      class="
        grid
        lg:grid-cols-2
        gap-16
        items-center
      "
    >

      <!-- IMAGE -->

      <div
        class="
          weather-image
          h-[500px]
          rounded-[45px]
          overflow-hidden
          soft-shadow
          relative
        "
      >

        <div
          class="
            absolute
            top-7
            left-7
            right-7
            bg-white/95
            backdrop-blur
            rounded-3xl
            p-6
          "
        >

          <div
            class="
              flex
              justify-between
              items-center
            "
          >

            <div>

              <div
                class="
                  text-xs
                  uppercase
                  tracking-widest
                  text-gray-400
                  font-bold
                "
              >
                Previsión
              </div>

              <div
                class="
                  mt-1
                  text-xl
                  font-black
                "
              >
                Parcela 024
              </div>

            </div>


            <div
              class="
                text-3xl
              "
            >
              ☀️
            </div>

          </div>


          <div
            class="
              mt-5
              flex
              items-end
              gap-3
            "
          >

            <span
              class="
                text-5xl
                font-black
                text-agri-900
              "
            >
              27°
            </span>

            <span
              class="
                text-gray-500
                mb-2
              "
            >
              Soleado
            </span>

          </div>


          <div
            class="
              mt-5
              grid
              grid-cols-4
              gap-2
            "
          >

            <div
              class="
                rounded-xl
                bg-agri-50
                p-3
                text-center
              "
            >
              <div class="text-xs">L</div>
              <div class="mt-1">☀️</div>
              <strong class="text-xs">27°</strong>
            </div>

            <div
              class="
                rounded-xl
                bg-agri-50
                p-3
                text-center
              "
            >
              <div class="text-xs">M</div>
              <div class="mt-1">🌤️</div>
              <strong class="text-xs">25°</strong>
            </div>

            <div
              class="
                rounded-xl
                bg-agri-50
                p-3
                text-center
              "
            >
              <div class="text-xs">X</div>
              <div class="mt-1">🌧️</div>
              <strong class="text-xs">22°</strong>
            </div>

            <div
              class="
                rounded-xl
                bg-agri-50
                p-3
                text-center
              "
            >
              <div class="text-xs">J</div>
              <div class="mt-1">☀️</div>
              <strong class="text-xs">26°</strong>
            </div>

          </div>

        </div>

      </div>


      <!-- TEXT -->

      <div>

        <div
          class="
            text-agri-600
            text-sm
            uppercase
            tracking-[.3em]
            font-black
          "
        >
          Datos meteorológicos
        </div>


        <h2
          class="
            mt-5
            text-4xl
            md:text-5xl
            font-black
            text-agri-900
            leading-tight
          "
        >
          El tiempo también
          forma parte de la decisión.
        </h2>


        <p
          class="
            mt-7
            text-lg
            leading-8
            text-gray-600
          "
        >
          Consulta previsiones meteorológicas
          profesionales de hasta siete días,
          actualizadas cada hora, directamente
          para tus campos.
        </p>


        <div
          class="
            mt-8
            grid
            grid-cols-2
            gap-4
          "
        >

          <div
            class="
              bg-white
              rounded-2xl
              p-5
            "
          >

            <div class="text-2xl">
              🌡️
            </div>

            <div
              class="
                mt-3
                font-black
              "
            >
              Temperatura
            </div>

          </div>


          <div
            class="
              bg-white
              rounded-2xl
              p-5
            "
          >

            <div class="text-2xl">
              💧
            </div>

            <div
              class="
                mt-3
                font-black
              "
            >
              Humedad
            </div>

          </div>


          <div
            class="
              bg-white
              rounded-2xl
              p-5
            "
          >

            <div class="text-2xl">
              💨
            </div>

            <div
              class="
                mt-3
                font-black
              "
            >
              Viento
            </div>

          </div>


          <div
            class="
              bg-white
              rounded-2xl
              p-5
            "
          >

            <div class="text-2xl">
              🌧️
            </div>

            <div
              class="
                mt-3
                font-black
              "
            >
              Precipitación
            </div>

          </div>

        </div>


        <div
          class="
            mt-6
            p-5
            rounded-2xl
            bg-white
            border
            border-agri-100
          "
        >

          <span class="font-bold">
            Estaciones meteorológicas:
          </span>

          <span class="text-gray-600">
            puedes conectar tus propias estaciones
            para incorporar sus datos al sistema.
          </span>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     FEATURES
===================================================== -->

<section
  id="funciones"
  class="
    py-28
    bg-white
  "
>

  <div class="container-agri">

    <div
      class="
        text-center
        max-w-3xl
        mx-auto
      "
    >

      <div
        class="
          text-agri-600
          text-sm
          uppercase
          tracking-[.3em]
          font-black
        "
      >
        Funcionalidades
      </div>


      <h2
        class="
          mt-5
          text-4xl
          md:text-5xl
          font-black
          text-agri-900
        "
      >
        Todo lo que necesitas
        para gestionar el campo.
      </h2>


      <p
        class="
          mt-6
          text-lg
          text-gray-600
          leading-8
        "
      >
        El módulo de Riego y Nutrición se integra
        con las herramientas de Agricolus para
        centralizar la gestión agrícola.
      </p>

    </div>


    <div
      class="
        mt-14
        grid
        md:grid-cols-2
        lg:grid-cols-4
        gap-5
      "
    >

      <template
        x-for="feature in [
          {
            icon:'🗺️',
            title:'Campos',
            text:'Crea y geolocaliza parcelas con diferentes cultivos y variedades.'
          },
          {
            icon:'🌱',
            title:'Plan de cultivos',
            text:'Registra y visualiza el plan de cultivos de cada temporada.'
          },
          {
            icon:'📋',
            title:'Tareas',
            text:'Registra las operaciones agrícolas y organiza las actividades.'
          },
          {
            icon:'🌿',
            title:'Fenología',
            text:'Predicción fenológica para evaluar las necesidades del cultivo.'
          },
          {
            icon:'💧',
            title:'Riego',
            text:'Estimación de las necesidades hídricas para intervenir en el momento adecuado.'
          },
          {
            icon:'🧪',
            title:'Nutrición',
            text:'Calcula las necesidades de N, P y K durante el ciclo productivo.'
          },
          {
            icon:'☁️',
            title:'Tiempo',
            text:'Previsiones meteorológicas de hasta siete días actualizadas cada hora.'
          },
          {
            icon:'🔎',
            title:'Monitoreo en campo',
            text:'Registra fenología, plagas, enfermedades, anomalías y análisis.'
          },
          {
            icon:'📝',
            title:'Nota rápida',
            text:'Añade fotos, notas y notas de voz vinculadas a tus campos.'
          },
          {
            icon:'🚜',
            title:'Maquinaria',
            text:'Registra maquinaria, anomalías y operaciones de mantenimiento.'
          },
          {
            icon:'♻️',
            title:'Sostenibilidad',
            text:'Monitoriza indicadores económicos y ambientales de la explotación.'
          },
          {
            icon:'📦',
            title:'Lotes de producción',
            text:'Asigna lotes a cada cosecha para mejorar la trazabilidad.'
          }
        ]"
        :key="feature.title"
      >

        <article
          class="
            card
            bg-white
            rounded-3xl
            p-6
            border
            border-gray-100
          "
        >

          <div
            class="
              w-12
              h-12
              rounded-2xl
              bg-agri-100
              flex
              items-center
              justify-center
              text-xl
            "
            x-text="feature.icon"
          ></div>


          <h3
            class="
              mt-5
              text-lg
              font-black
              text-agri-900
            "
            x-text="feature.title"
          ></h3>


          <p
            class="
              mt-2
              text-sm
              text-gray-500
              leading-6
            "
            x-text="feature.text"
          ></p>

        </article>

      </template>

    </div>

  </div>

</section>



<!-- =====================================================
     BENEFITS
===================================================== -->

<section
  id="beneficios"
  class="
    py-28
    bg-agri-900
    text-white
  "
>

  <div class="container-agri">

    <div
      class="
        grid
        lg:grid-cols-2
        gap-16
        items-center
      "
    >

      <div>

        <div
          class="
            text-agri-300
            text-sm
            uppercase
            tracking-[.3em]
            font-black
          "
        >
          El valor añadido
        </div>


        <h2
          class="
            mt-5
            text-4xl
            md:text-5xl
            font-black
            leading-tight
          "
        >
          Gestiona recursos
          basándote en datos.
        </h2>


        <p
          class="
            mt-7
            text-lg
            text-green-100
            leading-8
          "
        >
          Los modelos predictivos ayudan a convertir
          los datos del cultivo, del suelo y del clima
          en información útil para organizar las
          operaciones.
        </p>


        <div class="mt-9 space-y-5">

          <div
            class="
              flex
              gap-4
              items-start
            "
          >

            <div
              class="
                flex-none
                w-11
                h-11
                rounded-full
                bg-white/10
                flex
                items-center
                justify-center
                text-agri-300
                font-black
              "
            >
              01
            </div>

            <div>

              <h3 class="font-black text-lg">
                Mayor precisión
              </h3>

              <p
                class="
                  mt-1
                  text-green-100/70
                "
              >
                Ajusta las intervenciones a las
                necesidades estimadas del cultivo.
              </p>

            </div>

          </div>


          <div
            class="
              flex
              gap-4
              items-start
            "
          >

            <div
              class="
                flex-none
                w-11
                h-11
                rounded-full
                bg-white/10
                flex
                items-center
                justify-center
                text-agri-300
                font-black
              "
            >
              02
            </div>

            <div>

              <h3 class="font-black text-lg">
                Menos desperdicio
              </h3>

              <p
                class="
                  mt-1
                  text-green-100/70
                "
              >
                Utiliza agua y fertilizantes
                de forma más racional.
              </p>

            </div>

          </div>


          <div
            class="
              flex
              gap-4
              items-start
            "
          >

            <div
              class="
                flex-none
                w-11
                h-11
                rounded-full
                bg-white/10
                flex
                items-center
                justify-center
                text-agri-300
                font-black
              "
            >
              03
            </div>

            <div>

              <h3 class="font-black text-lg">
                Mejor planificación
              </h3>

              <p
                class="
                  mt-1
                  text-green-100/70
                "
              >
                Organiza las operaciones teniendo
                en cuenta el desarrollo del cultivo.
              </p>

            </div>

          </div>

        </div>

      </div>


      <!-- DASHBOARD -->

      <div
        class="
          bg-white
          rounded-[40px]
          p-6
          md:p-8
          text-agri-900
          shadow-2xl
        "
      >

        <div
          class="
            flex
            justify-between
            items-center
          "
        >

          <div>

            <div
              class="
                text-xs
                uppercase
                tracking-widest
                font-bold
                text-gray-400
              "
            >
              Agricolus dashboard
            </div>

            <div
              class="
                mt-1
                text-xl
                font-black
              "
            >
              Resumen de parcela
            </div>

          </div>


          <div
            class="
              w-10
              h-10
              rounded-xl
              bg-agri-100
              flex
              items-center
              justify-center
            "
          >
            ✓
          </div>

        </div>


        <div
          class="
            mt-7
            grid
            grid-cols-2
            gap-4
          "
        >

          <div
            class="
              bg-agri-50
              rounded-2xl
              p-5
            "
          >

            <div
              class="
                text-xs
                text-gray-500
              "
            >
              Riego
            </div>

            <div
              class="
                mt-2
                text-2xl
                font-black
              "
            >
              18.4 mm
            </div>

            <div
              class="
                mt-2
                text-xs
                text-blue-600
                font-bold
              "
            >
              Recomendado
            </div>

          </div>


          <div
            class="
              bg-purple-50
              rounded-2xl
              p-5
            "
          >

            <div
              class="
                text-xs
                text-gray-500
              "
            >
              Nutrición
            </div>

            <div
              class="
                mt-2
                text-2xl
                font-black
              "
            >
              NPK
            </div>

            <div
              class="
                mt-2
                text-xs
                text-purple-600
                font-bold
              "
            >
              Calculado
            </div>

          </div>

        </div>


        <!-- MINI CHART -->

        <div
          class="
            mt-5
            bg-gray-50
            rounded-2xl
            p-5
          "
        >

          <div
            class="
              flex
              justify-between
              text-xs
              text-gray-400
            "
          >

            <span>
              Evolución
            </span>

            <span>
              Últimos 7 días
            </span>

          </div>


          <div
            class="
              mt-4
              flex
              items-end
              gap-2
              h-28
            "
          >

            <div
              class="
                flex-1
                rounded-t-lg
                bg-agri-200
                h-[35%]
              "
            ></div>

            <div
              class="
                flex-1
                rounded-t-lg
                bg-agri-300
                h-[48%]
              "
            ></div>

            <div
              class="
                flex-1
                rounded-t-lg
                bg-agri-400
                h-[42%]
              "
            ></div>

            <div
              class="
                flex-1
                rounded-t-lg
                bg-agri-500
                h-[65%]
              "
            ></div>

            <div
              class="
                flex-1
                rounded-t-lg
                bg-agri-600
                h-[72%]
              "
            ></div>

            <div
              class="
                flex-1
                rounded-t-lg
                bg-agri-700
                h-[83%]
              "
            ></div>

            <div
              class="
                flex-1
                rounded-t-lg
                bg-agri-800
                h-[90%]
              "
            ></div>

          </div>

        </div>


        <button
          @click="modal = true"
          class="
            mt-5
            w-full
            bg-agri-600
            hover:bg-agri-700
            text-white
            py-4
            rounded-full
            font-black
            transition
          "
        >
          SOLICITAR INFORMACIÓN
        </button>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     CTA
===================================================== -->

<section
  id="contacto"
  class="
    py-24
    bg-white
  "
>

  <div
    class="
      container-agri
    "
  >

    <div
      class="
        rounded-[45px]
        bg-agri-50
        p-10
        md:p-16
        text-center
        overflow-hidden
        relative
      "
    >

      <div
        class="
          absolute
          -top-24
          -right-24
          w-72
          h-72
          rounded-full
          bg-agri-200/50
        "
      ></div>


      <div
        class="
          absolute
          -bottom-32
          -left-20
          w-72
          h-72
          rounded-full
          bg-agri-100
        "
      ></div>


      <div
        class="
          relative
          max-w-4xl
          mx-auto
        "
      >

        <div
          class="
            text-agri-600
            text-sm
            uppercase
            tracking-[.35em]
            font-black
          "
        >
          Riego y Nutrición
        </div>


        <h2
          class="
            mt-6
            text-4xl
            md:text-6xl
            font-black
            text-agri-900
            leading-tight
          "
        >
          Convierte los datos
          en mejores decisiones.
        </h2>


        <p
          class="
            mt-7
            text-lg
            md:text-xl
            text-gray-600
            leading-8
            max-w-2xl
            mx-auto
          "
        >
          Descubre cómo los modelos predictivos
          pueden ayudarte a gestionar agua,
          nutrientes y operaciones de campo.
        </p>


        <div
          class="
            mt-9
            flex
            justify-center
            flex-wrap
            gap-4
          "
        >

          <button
            @click="modal = true"
            class="
              bg-agri-600
              hover:bg-agri-700
              text-white
              px-9
              py-4
              rounded-full
              font-black
              transition
            "
          >
            SOLICITAR MÁS INFORMACIÓN
          </button>


          <a
            href="#funciones"
            class="
              border
              border-agri-600
              text-agri-700
              px-9
              py-4
              rounded-full
              font-bold
              hover:bg-white
              transition
            "
          >
            VER FUNCIONALIDADES
          </a>

        </div>


        <div
          class="
            mt-8
            text-sm
            text-gray-500
          "
        >
          Desde 50 €/año
        </div>

      </div>

    </div>

  </div>

</section>

<?php
include __DIR__ . '/../includes/footer.php';
?>

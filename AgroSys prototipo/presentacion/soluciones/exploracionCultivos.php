<?php
$page_title = "Precisión en el campo | App Agricolus";
$base_url = "../";
$current_page = "soluciones";
$body_class = "bg-white";

$page_styles = <<<'CSS'
    .container-main {
      width: min(1180px, calc(100% - 40px));
      margin: auto;
    }

    .hero-gradient {
      background:
        linear-gradient(
          90deg,
          rgba(25,57,23,.90),
          rgba(25,57,23,.62) 48%,
          rgba(25,57,23,.15)
        );
    }

    .green-line {
      position: relative;
    }

    .green-line::after {
      content: "";
      position: absolute;
      width: 55px;
      height: 3px;
      background: #5ca64c;
      left: 0;
      bottom: -15px;
    }

    .phone {
      border: 8px solid #202520;
      border-radius: 38px;
      overflow: hidden;
      background: white;
      box-shadow: 0 35px 80px rgba(0,0,0,.25);
    }

    .phone-notch {
      width: 100px;
      height: 23px;
      background: #202520;
      border-radius: 0 0 18px 18px;
      margin: auto;
      position: relative;
      z-index: 10;
    }

    .feature-card {
      transition: .35s ease;
    }

    .feature-card:hover {
      transform: translateY(-7px);
      box-shadow: 0 20px 50px rgba(36,75,34,.13);
    }

    .step-line {
      position: absolute;
      top: 38px;
      left: 16%;
      right: 16%;
      height: 2px;
      background: #c9dfc0;
    }

    @media(max-width:768px) {
      .step-line {
        display:none;
      }
    }
CSS;

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/header.php';
?>

<!-- =====================================================
     HERO
====================================================== -->

<section
  class="relative min-h-[700px] flex items-center overflow-hidden"
  style="
    background-image:url('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?auto=format&fit=crop&w=2200&q=85');
    background-size:cover;
    background-position:center;
  "
>

  <div class="absolute inset-0 hero-gradient"></div>


  <div class="container-main relative z-10 pt-24">

    <div class="grid lg:grid-cols-2 gap-14 items-center">


      <!-- HERO TEXT -->

      <div class="text-white">

        <div
          class="uppercase tracking-[4px] text-agricolus-300
                 text-sm font-bold mb-7"
        >
          Agricultura de precisión
        </div>


        <h1
          class="text-5xl md:text-6xl lg:text-[66px]
                 font-bold leading-[1.05]"
        >

          Precisión en
          <span class="text-agricolus-300">
            el campo
          </span>

        </h1>


        <p
          class="mt-8 text-lg md:text-xl
                 leading-relaxed text-white/90 max-w-xl"
        >
          Agricolus apoya tu trabajo con una app para
          agricultura de precisión y gestión completa de
          las fincas.
        </p>


        <p
          class="mt-5 text-white/75
                 leading-relaxed max-w-xl"
        >
          Monitorea tus parcelas, optimiza el uso de los
          insumos y organiza de la mejor manera las
          actividades en campo.
        </p>


        <div class="mt-9 flex flex-wrap gap-4">

          <a
            href="#descarga"
            class="bg-agricolus-500
                   hover:bg-agricolus-400
                   px-7 py-4 rounded-full
                   font-bold transition"
          >
            Descargar la app
          </a>

          <a
            href="#funciones"
            class="border border-white/60
                   hover:bg-white hover:text-agricolus-900
                   px-7 py-4 rounded-full
                   font-bold transition"
          >
            Ver funcionalidades
          </a>

        </div>

      </div>


      <!-- PHONE -->

      <div
        class="hidden md:flex justify-center lg:justify-end
               relative"
      >

        <div class="relative w-[300px]">

          <div class="phone">

            <div class="phone-notch"></div>

            <img
              src="https://images.unsplash.com/photo-1551650975-87deedd944c3?auto=format&fit=crop&w=700&q=85"
              class="w-full h-[560px] object-cover"
              alt="App Agricolus"
            />

          </div>


          <!-- FLOATING -->

          <div
            class="absolute -left-20 bottom-20
                   bg-white rounded-2xl
                   p-5 shadow-xl w-[210px]"
          >

            <div class="text-agricolus-600 font-bold text-xl">
              Agricultura inteligente
            </div>

            <p class="text-gray-500 text-sm mt-2">
              Todos tus datos de campo en un solo lugar.
            </p>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     DOWNLOAD
====================================================== -->

<section id="descarga" class="py-20 bg-white">

  <div class="container-main">

    <div
      class="max-w-4xl mx-auto
             text-center"
    >

      <p
        class="uppercase tracking-[3px]
               text-agricolus-600
               font-bold text-sm"
      >
        App Agricolus
      </p>


      <h2
        class="text-4xl md:text-5xl
               font-bold mt-4"
      >
        Lleva la agricultura de precisión
        contigo
      </h2>


      <p
        class="mt-6 text-gray-600 text-lg
               leading-relaxed"
      >
        Comienza mapeando los campos directamente
        desde tu smartphone y descubre todas las
        funciones de Agricolus.
      </p>


      <div
        class="mt-9 flex flex-wrap
               justify-center gap-4"
      >

        <a
          href="#"
          class="bg-black text-white
                 rounded-xl px-6 py-4
                 flex items-center gap-3"
        >

          <span class="text-2xl"></span>

          <div class="text-left">

            <div class="text-[10px]">
              Descargar en
            </div>

            <div class="font-bold">
              App Store
            </div>

          </div>

        </a>


        <a
          href="#"
          class="bg-black text-white
                 rounded-xl px-6 py-4
                 flex items-center gap-3"
        >

          <span class="text-2xl">▶</span>

          <div class="text-left">

            <div class="text-[10px]">
              Disponible en
            </div>

            <div class="font-bold">
              Google Play
            </div>

          </div>

        </a>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     THREE STEPS
====================================================== -->

<section class="bg-agricolus-50 py-24">

  <div class="container-main">

    <div class="text-center">

      <p
        class="text-agricolus-600
               uppercase tracking-[3px]
               text-sm font-bold"
      >
        Cómo funciona
      </p>

      <h2 class="mt-4 text-4xl md:text-5xl font-bold">
        Del campo al dato
      </h2>

    </div>


    <div
      class="relative grid md:grid-cols-3
             gap-10 mt-16"
    >

      <div class="step-line"></div>


      <!-- STEP 1 -->

      <div class="relative text-center">

        <div
          class="relative z-10 mx-auto
                 w-20 h-20 rounded-full
                 bg-agricolus-600 text-white
                 flex items-center justify-center
                 text-2xl font-bold
                 shadow-lg"
        >
          01
        </div>

        <h3 class="font-bold text-xl mt-7">
          Mapea y registra
        </h3>

        <p class="text-gray-600 mt-4 leading-relaxed">
          Dibuja tus campos, geolocaliza parcelas
          y registra las actividades directamente
          desde el smartphone.
        </p>

      </div>


      <!-- STEP 2 -->

      <div class="relative text-center">

        <div
          class="relative z-10 mx-auto
                 w-20 h-20 rounded-full
                 bg-agricolus-600 text-white
                 flex items-center justify-center
                 text-2xl font-bold
                 shadow-lg"
        >
          02
        </div>

        <h3 class="font-bold text-xl mt-7">
          Recopila los datos
        </h3>

        <p class="text-gray-600 mt-4 leading-relaxed">
          Recoge observaciones, fotografías,
          notas, trampas y datos de monitoreo
          durante el trabajo en campo.
        </p>

      </div>


      <!-- STEP 3 -->

      <div class="relative text-center">

        <div
          class="relative z-10 mx-auto
                 w-20 h-20 rounded-full
                 bg-agricolus-600 text-white
                 flex items-center justify-center
                 text-2xl font-bold
                 shadow-lg"
        >
          03
        </div>

        <h3 class="font-bold text-xl mt-7">
          Monitorea los cultivos
        </h3>

        <p class="text-gray-600 mt-4 leading-relaxed">
          Consulta imágenes satelitales,
          meteorología y modelos predictivos
          para tomar mejores decisiones.
        </p>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     FEATURES
====================================================== -->

<section id="funciones" class="py-24">

  <div class="container-main">

    <div class="max-w-3xl">

      <p
        class="uppercase tracking-[3px]
               text-agricolus-600
               text-sm font-bold"
      >
        Funcionalidades
      </p>

      <h2
        class="green-line mt-5
               text-4xl md:text-5xl
               font-bold leading-tight"
      >
        Todas las funcionalidades
        para la agricultura de precisión
        en una sola app
      </h2>

    </div>


    <div
      class="grid md:grid-cols-2
             lg:grid-cols-3 gap-6 mt-16"
    >


      <!-- CARD -->

      <div class="feature-card bg-white rounded-3xl p-8 border border-gray-100">

        <div
          class="w-14 h-14 rounded-2xl
                 bg-agricolus-100
                 flex items-center justify-center
                 text-2xl"
        >
          🗺️
        </div>

        <h3 class="font-bold text-xl mt-7">
          Campos y parcelas
        </h3>

        <p class="text-gray-600 mt-4 leading-relaxed">
          Dibuja los campos en el mapa o utiliza
          la detección automática y geolocaliza
          parcelas con diferentes cultivos.
        </p>

      </div>


      <!-- CARD -->

      <div class="feature-card bg-white rounded-3xl p-8 border border-gray-100">

        <div
          class="w-14 h-14 rounded-2xl
                 bg-agricolus-100
                 flex items-center justify-center
                 text-2xl"
        >
          📝
        </div>

        <h3 class="font-bold text-xl mt-7">
          Notas
        </h3>

        <p class="text-gray-600 mt-4 leading-relaxed">
          Añade notas, fotografías y notas de voz
          y asócialas a campos o posiciones
          específicas.
        </p>

      </div>


      <!-- CARD -->

      <div class="feature-card bg-white rounded-3xl p-8 border border-gray-100">

        <div
          class="w-14 h-14 rounded-2xl
                 bg-agricolus-100
                 flex items-center justify-center
                 text-2xl"
        >
          📍
        </div>

        <h3 class="font-bold text-xl mt-7">
          Mapa
        </h3>

        <p class="text-gray-600 mt-4 leading-relaxed">
          Visualiza los datos de los campos,
          geolocaliza la información y compara
          parcelas según diferentes parámetros.
        </p>

      </div>


      <!-- CARD -->

      <div class="feature-card bg-white rounded-3xl p-8 border border-gray-100">

        <div
          class="w-14 h-14 rounded-2xl
                 bg-agricolus-100
                 flex items-center justify-center
                 text-2xl"
        >
          🚜
        </div>

        <h3 class="font-bold text-xl mt-7">
          Actividades
        </h3>

        <p class="text-gray-600 mt-4 leading-relaxed">
          Registra operaciones agrícolas y
          planifica las actividades que deben
          realizarse en el campo.
        </p>

      </div>


      <!-- CARD -->

      <div class="feature-card bg-white rounded-3xl p-8 border border-gray-100">

        <div
          class="w-14 h-14 rounded-2xl
                 bg-agricolus-100
                 flex items-center justify-center
                 text-2xl"
        >
          📦
        </div>

        <h3 class="font-bold text-xl mt-7">
          Almacén
        </h3>

        <p class="text-gray-600 mt-4 leading-relaxed">
          Sigue los movimientos de la explotación,
          desde las compras hasta las cosechas,
          manteniendo las existencias bajo control.
        </p>

      </div>


      <!-- CARD -->

      <div class="feature-card bg-white rounded-3xl p-8 border border-gray-100">

        <div
          class="w-14 h-14 rounded-2xl
                 bg-agricolus-100
                 flex items-center justify-center
                 text-2xl"
        >
          🌾
        </div>

        <h3 class="font-bold text-xl mt-7">
          Exploración de cultivos
        </h3>

        <p class="text-gray-600 mt-4 leading-relaxed">
          Registra y geolocaliza datos sobre
          fenología, plagas, enfermedades
          y anomalías.
        </p>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     SECTION 1 — MAP
====================================================== -->

<section class="bg-[#f4f5ee] py-24">

  <div class="container-main">

    <div class="grid lg:grid-cols-2 gap-16 items-center">


      <div>

        <p
          class="text-agricolus-600
                 uppercase tracking-[3px]
                 text-sm font-bold"
        >
          Gestión de campo
        </p>

        <h2
          class="mt-5 text-4xl md:text-5xl
                 font-bold leading-tight"
        >
          Mapea los campos
          y registra las actividades
        </h2>


        <p
          class="mt-7 text-gray-600
                 text-lg leading-relaxed"
        >
          Toda la información de tus parcelas
          directamente en el mapa. Visualiza
          cultivos, variedades, actividades
          y observaciones desde un único lugar.
        </p>


        <div class="mt-9 space-y-5">

          <div class="flex gap-4">

            <div
              class="w-9 h-9 shrink-0
                     rounded-full bg-agricolus-600
                     text-white flex items-center
                     justify-center"
            >
              ✓
            </div>

            <div>
              <h3 class="font-bold">
                Dibuja tus campos
              </h3>

              <p class="text-gray-500 mt-1">
                Crea y actualiza fácilmente
                la cartografía de tu explotación.
              </p>
            </div>

          </div>


          <div class="flex gap-4">

            <div
              class="w-9 h-9 shrink-0
                     rounded-full bg-agricolus-600
                     text-white flex items-center
                     justify-center"
            >
              ✓
            </div>

            <div>
              <h3 class="font-bold">
                Registra las operaciones
              </h3>

              <p class="text-gray-500 mt-1">
                Mantén actualizado tu registro
                agrícola digital.
              </p>
            </div>

          </div>

        </div>

      </div>


      <!-- IMAGE -->

      <div class="relative">

        <div
          class="rounded-[35px]
                 overflow-hidden
                 shadow-soft"
        >

          <img
            src="https://images.unsplash.com/photo-1560493676-04071c5f467b?auto=format&fit=crop&w=1400&q=85"
            class="w-full h-[500px] object-cover"
            alt="Mapeo agrícola"
          >

        </div>

        <div
          class="absolute
                 -bottom-7 -left-7
                 bg-white rounded-2xl
                 shadow-xl p-6"
        >

          <div class="text-agricolus-600 text-3xl font-bold">
            GPS
          </div>

          <p class="text-gray-500 text-sm mt-1">
            Datos geolocalizados
          </p>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     SECTION 2 — DATA
====================================================== -->

<section class="py-24">

  <div class="container-main">

    <div class="grid lg:grid-cols-2 gap-16 items-center">


      <!-- PHONE IMAGE -->

      <div class="relative order-2 lg:order-1">

        <div
          class="rounded-[35px]
                 overflow-hidden
                 shadow-soft"
        >

          <img
            src="https://images.unsplash.com/photo-1581092921461-eab62e97a780?auto=format&fit=crop&w=1400&q=85"
            class="w-full h-[520px] object-cover"
            alt="Recopilación de datos"
          >

        </div>

      </div>


      <!-- TEXT -->

      <div class="order-1 lg:order-2">

        <p
          class="text-agricolus-600
                 uppercase tracking-[3px]
                 text-sm font-bold"
        >
          Datos en campo
        </p>

        <h2
          class="mt-5 text-4xl md:text-5xl
                 font-bold leading-tight"
        >
          Recopila los datos
          directamente en campo
        </h2>


        <p
          class="mt-7 text-gray-600
                 text-lg leading-relaxed"
        >
          Convierte las observaciones realizadas
          durante el trabajo diario en información
          digital y geolocalizada.
        </p>


        <div class="mt-9 grid sm:grid-cols-2 gap-5">

          <div class="bg-agricolus-50 rounded-2xl p-5">

            <div class="text-2xl">
              🐛
            </div>

            <h3 class="font-bold mt-3">
              Trampas y capturas
            </h3>

            <p class="text-sm text-gray-500 mt-2">
              Registra las trampas y capturas
              de insectos plaga.
            </p>

          </div>


          <div class="bg-agricolus-50 rounded-2xl p-5">

            <div class="text-2xl">
              🌿
            </div>

            <h3 class="font-bold mt-3">
              Exploración
            </h3>

            <p class="text-sm text-gray-500 mt-2">
              Registra fenología, plagas,
              enfermedades y anomalías.
            </p>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     SECTION 3 — SATELLITE
====================================================== -->

<section class="bg-[#263a25] text-white py-24">

  <div class="container-main">

    <div class="grid lg:grid-cols-2 gap-16 items-center">


      <div>

        <p
          class="text-agricolus-300
                 uppercase tracking-[3px]
                 text-sm font-bold"
        >
          Agricultura conectada
        </p>

        <h2
          class="mt-5 text-4xl md:text-5xl
                 font-bold leading-tight"
        >
          Monitorea tus cultivos
          con datos avanzados
        </h2>


        <p
          class="mt-7 text-white/70
                 text-lg leading-relaxed"
        >
          Combina satélites, sensores de campo,
          meteorología y modelos de predicción
          para obtener una visión completa del
          estado de tus cultivos.
        </p>


        <div class="mt-10 space-y-6">

          <div class="flex gap-4">

            <div
              class="w-11 h-11 rounded-full
                     bg-agricolus-500
                     flex items-center justify-center
                     shrink-0"
            >
              🛰️
            </div>

            <div>

              <h3 class="font-bold text-lg">
                Imágenes de satélite
              </h3>

              <p class="text-white/60 mt-1">
                Consulta índices de vigor,
                clorofila y estrés hídrico.
              </p>

            </div>

          </div>


          <div class="flex gap-4">

            <div
              class="w-11 h-11 rounded-full
                     bg-agricolus-500
                     flex items-center justify-center
                     shrink-0"
            >
              ☁️
            </div>

            <div>

              <h3 class="font-bold text-lg">
                Meteorología
              </h3>

              <p class="text-white/60 mt-1">
                Consulta previsiones de siete días
                y datos de estaciones conectadas.
              </p>

            </div>

          </div>


          <div class="flex gap-4">

            <div
              class="w-11 h-11 rounded-full
                     bg-agricolus-500
                     flex items-center justify-center
                     shrink-0"
            >
              📊
            </div>

            <div>

              <h3 class="font-bold text-lg">
                Modelos predictivos
              </h3>

              <p class="text-white/60 mt-1">
                Detecta estrés y recibe información
                sobre las necesidades del cultivo.
              </p>

            </div>

          </div>

        </div>

      </div>


      <!-- IMAGE -->

      <div>

        <div
          class="rounded-[35px]
                 overflow-hidden
                 border border-white/10"
        >

          <img
            src="https://images.unsplash.com/photo-1523742817394-9f5f9c1b4e9a?auto=format&fit=crop&w=1400&q=85"
            class="w-full h-[560px] object-cover"
            alt="Agricultura de precisión"
          >

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     WEATHER / SATELLITE CARDS
====================================================== -->

<section class="py-24 bg-white">

  <div class="container-main">

    <div class="text-center max-w-3xl mx-auto">

      <p
        class="uppercase tracking-[3px]
               text-agricolus-600
               text-sm font-bold"
      >
        Información inteligente
      </p>

      <h2
        class="mt-4 text-4xl md:text-5xl
               font-bold"
      >
        Una visión completa
        de tus cultivos
      </h2>

    </div>


    <div
      class="grid md:grid-cols-3
             gap-6 mt-16"
    >

      <div
        class="relative h-[390px]
               rounded-[30px] overflow-hidden
               group"
      >

        <img
          src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1000&q=85"
          class="absolute inset-0 w-full h-full
                 object-cover group-hover:scale-105
                 transition duration-700"
        >

        <div
          class="absolute inset-0
                 bg-gradient-to-t
                 from-black/80 to-transparent"
        ></div>

        <div class="absolute bottom-7 left-7 right-7 text-white">

          <div class="text-3xl">
            🛰️
          </div>

          <h3 class="text-2xl font-bold mt-3">
            Imágenes de satélite
          </h3>

          <p class="text-white/70 mt-2 text-sm">
            Analiza el vigor y el estado
            de tus cultivos.
          </p>

        </div>

      </div>


      <div
        class="relative h-[390px]
               rounded-[30px] overflow-hidden
               group"
      >

        <img
          src="https://images.unsplash.com/photo-1534088568595-a066f410bcda?auto=format&fit=crop&w=1000&q=85"
          class="absolute inset-0 w-full h-full
                 object-cover group-hover:scale-105
                 transition duration-700"
        >

        <div
          class="absolute inset-0
                 bg-gradient-to-t
                 from-black/80 to-transparent"
        ></div>

        <div class="absolute bottom-7 left-7 right-7 text-white">

          <div class="text-3xl">
            🌦️
          </div>

          <h3 class="text-2xl font-bold mt-3">
            Tiempo
          </h3>

          <p class="text-white/70 mt-2 text-sm">
            Consulta las condiciones meteorológicas
            de tus parcelas.
          </p>

        </div>

      </div>


      <div
        class="relative h-[390px]
               rounded-[30px] overflow-hidden
               group"
      >

        <img
          src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1000&q=85"
          class="absolute inset-0 w-full h-full
                 object-cover group-hover:scale-105
                 transition duration-700"
        >

        <div
          class="absolute inset-0
                 bg-gradient-to-t
                 from-black/80 to-transparent"
        ></div>

        <div class="absolute bottom-7 left-7 right-7 text-white">

          <div class="text-3xl">
            📈
          </div>

          <h3 class="text-2xl font-bold mt-3">
            Modelos predictivos
          </h3>

          <p class="text-white/70 mt-2 text-sm">
            Anticipa las necesidades
            de tus cultivos.
          </p>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     ADVANTAGES
====================================================== -->

<section class="bg-agricolus-50 py-24">

  <div class="container-main">

    <div class="grid lg:grid-cols-2 gap-16 items-center">


      <div>

        <p
          class="text-agricolus-600
                 uppercase tracking-[3px]
                 text-sm font-bold"
        >
          ¿Por qué utilizar la app?
        </p>

        <h2
          class="mt-5 text-4xl md:text-5xl
                 font-bold leading-tight"
        >
          Del trabajo de campo
          a una gestión digital
        </h2>


        <p
          class="mt-7 text-gray-600
                 text-lg leading-relaxed"
        >
          Simplifica la gestión agrícola registrando
          las operaciones y observaciones en tiempo
          real desde tu smartphone.
        </p>


        <div class="mt-10 space-y-5">

          <div
            class="bg-white rounded-2xl p-5
                   flex gap-4 shadow-sm"
          >

            <div
              class="w-10 h-10 rounded-full
                     bg-agricolus-100
                     flex items-center justify-center"
            >
              ✓
            </div>

            <div>

              <h3 class="font-bold">
                Menos papel
              </h3>

              <p class="text-gray-500 mt-1 text-sm">
                Todos tus registros agrícolas
                digitalizados.
              </p>

            </div>

          </div>


          <div
            class="bg-white rounded-2xl p-5
                   flex gap-4 shadow-sm"
          >

            <div
              class="w-10 h-10 rounded-full
                     bg-agricolus-100
                     flex items-center justify-center"
            >
              ✓
            </div>

            <div>

              <h3 class="font-bold">
                Datos sincronizados
              </h3>

              <p class="text-gray-500 mt-1 text-sm">
                La información se sincroniza
                con la plataforma web.
              </p>

            </div>

          </div>


          <div
            class="bg-white rounded-2xl p-5
                   flex gap-4 shadow-sm"
          >

            <div
              class="w-10 h-10 rounded-full
                     bg-agricolus-100
                     flex items-center justify-center"
            >
              ✓
            </div>

            <div>

              <h3 class="font-bold">
                Decisiones basadas en datos
              </h3>

              <p class="text-gray-500 mt-1 text-sm">
                Utiliza mapas, meteorología y
                modelos para planificar.
              </p>

            </div>

          </div>

        </div>

      </div>


      <div class="relative">

        <div
          class="rounded-[35px]
                 overflow-hidden
                 shadow-soft"
        >

          <img
            src="https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&w=1400&q=85"
            class="w-full h-[560px] object-cover"
            alt="Agricultor usando smartphone"
          >

        </div>


        <div
          class="absolute
                 -bottom-8 -right-8
                 bg-agricolus-600
                 text-white rounded-3xl
                 p-7 w-[230px]"
        >

          <div class="text-4xl font-bold">
            100%
          </div>

          <p class="mt-2 text-white/80 text-sm">
            Gestión digital y conectada
            de tus actividades.
          </p>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     FINAL APP DOWNLOAD
====================================================== -->

<section class="py-24">

  <div class="container-main">

    <div
      class="relative overflow-hidden
             rounded-[40px]
             bg-agricolus-700
             text-white"
    >

      <div
        class="absolute inset-0 opacity-20"
        style="
          background-image:url('https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1800&q=85');
          background-size:cover;
          background-position:center;
        "
      ></div>


      <div
        class="relative z-10
               grid lg:grid-cols-2
               gap-10 items-center
               px-8 md:px-16
               py-20"
      >

        <div>

          <p
            class="uppercase tracking-[3px]
                   text-agricolus-200
                   text-sm font-bold"
          >
            App Agricolus
          </p>

          <h2
            class="mt-5 text-4xl md:text-5xl
                   font-bold leading-tight"
          >
            Lleva la precisión
            al campo
          </h2>

          <p
            class="mt-6 text-white/80
                   text-lg leading-relaxed"
          >
            Mapea, registra, monitoriza y toma
            decisiones directamente desde
            tu smartphone.
          </p>


          <div class="mt-8 flex flex-wrap gap-4">

            <a
              href="#"
              class="bg-white text-agricolus-800
                     px-7 py-4 rounded-full
                     font-bold hover:bg-agricolus-50
                     transition"
            >
              Descargar la app
            </a>

            <a
              href="<?= $base_url ?>contacto.php"
              class="border border-white/60
                     px-7 py-4 rounded-full
                     font-bold hover:bg-white
                     hover:text-agricolus-800
                     transition"
            >
              Reserva una demo
            </a>

          </div>

        </div>


        <div class="flex justify-center">

          <div class="phone w-[230px]">

            <div class="phone-notch"></div>

            <img
              src="https://images.unsplash.com/photo-1551650975-87deedd944c3?auto=format&fit=crop&w=600&q=85"
              class="w-full h-[430px] object-cover"
              alt="Aplicación Agricolus"
            >

          </div>

        </div>

      </div>

    </div>

  </div>

</section>



<!-- =====================================================
     NEWSLETTER
====================================================== -->

<section class="py-20 bg-white">

  <div class="container-main">

    <div
      class="max-w-3xl mx-auto
             text-center"
    >

      <p
        class="uppercase tracking-[3px]
               text-agricolus-600
               text-sm font-bold"
      >
        Newsletter
      </p>

      <h2
        class="mt-4 text-4xl
               font-bold"
      >
        ¿Quieres profundizar
        en la agricultura de precisión?
      </h2>

      <p class="mt-5 text-gray-600">
        Mantente actualizado sobre tecnología,
        agricultura de precisión y novedades
        de Agricolus.
      </p>


      <form
        class="mt-8 flex flex-col sm:flex-row
               gap-3 max-w-xl mx-auto"
        @submit.prevent="
          alert('Gracias por suscribirte')
        "
      >

        <input
          type="email"
          required
          placeholder="Tu correo electrónico"
          class="flex-1 px-5 py-4
                 rounded-full
                 border border-gray-200
                 outline-none
                 focus:border-agricolus-500"
        >

        <button
          class="bg-agricolus-600
                 hover:bg-agricolus-700
                 text-white px-7 py-4
                 rounded-full font-bold"
        >
          Suscribirme
        </button>

      </form>

    </div>

  </div>

</section>

<?php
include __DIR__ . '/../includes/footer.php';
?>

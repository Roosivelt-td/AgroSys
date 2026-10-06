<?php
$page_title = "Agricolus Academy — Prototype";
$base_url = "./";
$current_page = "academy";

$page_styles = <<<'CSS'
    .container-custom {
      width: min(1180px, calc(100% - 40px));
      margin: 0 auto;
    }

    .hero-grid {
      background:
        radial-gradient(circle at 80% 20%, rgba(124,171,67,.14), transparent 30%),
        linear-gradient(180deg, #f5f8ef 0%, #ffffff 100%);
    }

    .leaf-bg {
      background:
        linear-gradient(rgba(38,60,29,.68), rgba(38,60,29,.68)),
        url('https://images.unsplash.com/photo-1492496913980-501348b61469?auto=format&fit=crop&w=1800&q=85');
      background-size: cover;
      background-position: center;
    }

    .academy-card {
      box-shadow: 0 20px 60px rgba(40, 65, 25, .10);
    }

    .soft-shadow {
      box-shadow: 0 15px 45px rgba(42, 70, 30, .08);
    }

    .btn {
      transition: all .25s ease;
    }

    .btn:hover {
      transform: translateY(-2px);
    }
CSS;

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>

  <!-- =====================================================
       HERO / ACADEMY
  ====================================================== -->

  <main id="academy">

    <section class="hero-grid pt-[82px]">

      <div class="container-custom">

        <div
          class="grid lg:grid-cols-2 gap-12 items-center min-h-[650px] py-20"
        >

          <!-- Text -->
          <div>

            <div
              class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-semibold mb-7"
            >
              <span class="w-2 h-2 bg-green-500 rounded-full"></span>
              Agricolus Academy
            </div>

            <h1
              class="text-5xl md:text-6xl lg:text-[68px] leading-[.98] font-extrabold tracking-tight text-green-900"
            >
              Nuestra
              <span class="text-green-600">
                Academy
              </span>
            </h1>

            <p
              class="mt-8 text-lg leading-8 text-gray-600 max-w-xl"
            >
              La difusión de tecnologías innovadoras para la agricultura
              requiere la formación de profesionales del sector.
            </p>

            <p
              class="mt-5 text-lg leading-8 text-gray-600 max-w-xl"
            >
              Aprende a utilizar nuevas herramientas digitales e interpretar
              los datos para optimizar la producción agrícola y reducir los
              residuos.
            </p>

            <div class="mt-9 flex flex-wrap gap-4">

              <a
                href="#curso"
                class="btn bg-green-600 hover:bg-green-700 text-white px-7 py-4 rounded-full font-bold"
              >
                Quiero saber más
              </a>

              <a
                href="#dedicado"
                class="px-7 py-4 rounded-full border border-green-600 text-green-700 font-bold hover:bg-green-50"
              >
                Ver programa
              </a>

            </div>

          </div>


          <!-- Image -->
          <div class="relative">

            <div
              class="absolute -top-8 -right-5 w-32 h-32 bg-green-100 rounded-full"
            ></div>

            <div
              class="absolute -bottom-8 -left-5 w-40 h-40 bg-green-200 rounded-full opacity-60"
            ></div>

            <div
              class="relative overflow-hidden rounded-[45px] rounded-bl-[110px] shadow-2xl"
            >
              <img
                src="https://images.unsplash.com/photo-1530507629858-e4977d30e9e0?auto=format&fit=crop&w=1200&q=85"
                class="w-full h-[540px] object-cover"
                alt="Agricultura tecnológica"
              />

              <div
                class="absolute bottom-7 left-7 right-7 bg-white/95 backdrop-blur rounded-2xl p-5 shadow-xl"
              >
                <div class="flex items-center gap-4">

                  <div
                    class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-2xl"
                  >
                    🌱
                  </div>

                  <div>
                    <div class="font-bold text-green-900">
                      Agricultura 4.0
                    </div>

                    <div class="text-sm text-gray-500">
                      Formación profesional
                    </div>
                  </div>

                </div>
              </div>

            </div>
          </div>

        </div>

      </div>

    </section>



    <!-- =====================================================
         INTRO CARD
    ====================================================== -->

    <section class="py-24 bg-white">

      <div class="container-custom">

        <div
          class="academy-card bg-green-900 text-white rounded-[40px] overflow-hidden"
        >

          <div class="grid lg:grid-cols-2">

            <div class="p-10 md:p-16 lg:p-20">

              <span
                class="text-green-300 uppercase tracking-[.25em] text-xs font-bold"
              >
                Professional Academy
              </span>

              <h2
                class="mt-5 text-4xl md:text-5xl font-extrabold leading-tight"
              >
                Incrementa tus habilidades
                en nuevas tecnologías
              </h2>

              <p class="mt-6 text-green-100 text-lg leading-8">
                Fórmate en herramientas digitales para agricultura y
                conviértete en un profesional preparado para los retos
                de la Agricultura 4.0.
              </p>

              <a
                href="#curso"
                class="inline-flex mt-8 bg-white text-green-900 px-7 py-4 rounded-full font-bold hover:bg-green-50"
              >
                Descubre el curso
              </a>

            </div>


            <div class="min-h-[350px]">

              <img
                src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1200&q=85"
                class="w-full h-full object-cover"
                alt="Campo agrícola"
              />

            </div>

          </div>

        </div>

      </div>

    </section>



    <!-- =====================================================
         A QUIÉN ESTÁ DEDICADO
    ====================================================== -->

    <section id="dedicado" class="py-24 bg-[#f5f8ef]">

      <div class="container-custom">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

          <!-- Image -->
          <div class="relative">

            <img
              src="https://images.unsplash.com/photo-1592982537447-6f2a6a0a6e6b?auto=format&fit=crop&w=1100&q=85"
              class="rounded-[40px] w-full h-[570px] object-cover"
              alt="Agrónomo trabajando"
            />

            <div
              class="absolute -bottom-8 -right-8 bg-white rounded-3xl p-7 shadow-xl max-w-[240px]"
            >

              <div class="text-4xl font-extrabold text-green-600">
                4
              </div>

              <div class="text-sm text-gray-600 mt-1">
                lecciones online
              </div>

            </div>

          </div>


          <!-- Content -->
          <div>

            <span
              class="text-green-600 font-bold uppercase tracking-[.22em] text-sm"
            >
              Formación profesional
            </span>

            <h2
              class="mt-4 text-4xl md:text-5xl font-extrabold text-green-900"
            >
              A quién está
              <span class="text-green-600">
                dedicado
              </span>
            </h2>

            <p class="mt-6 text-gray-600 text-lg leading-8">
              El curso está dirigido a cualquier persona que quiera
              profundizar en sus conocimientos sobre herramientas
              digitales para la agricultura y formarse en su uso práctico.
            </p>


            <div class="mt-8 grid sm:grid-cols-2 gap-4">

              <div
                class="bg-white p-5 rounded-2xl flex items-center gap-4 soft-shadow"
              >
                <span class="text-2xl">🌾</span>
                <span class="font-semibold">Agrónomos</span>
              </div>

              <div
                class="bg-white p-5 rounded-2xl flex items-center gap-4 soft-shadow"
              >
                <span class="text-2xl">🧑‍🌾</span>
                <span class="font-semibold">Expertos agrícolas</span>
              </div>

              <div
                class="bg-white p-5 rounded-2xl flex items-center gap-4 soft-shadow"
              >
                <span class="text-2xl">📐</span>
                <span class="font-semibold">Técnicos agrícolas</span>
              </div>

              <div
                class="bg-white p-5 rounded-2xl flex items-center gap-4 soft-shadow"
              >
                <span class="text-2xl">🚜</span>
                <span class="font-semibold">Agricultores</span>
              </div>

              <div
                class="bg-white p-5 rounded-2xl flex items-center gap-4 soft-shadow sm:col-span-2"
              >
                <span class="text-2xl">🎓</span>
                <span class="font-semibold">Nuevos graduados</span>
              </div>

            </div>

          </div>

        </div>

      </div>

    </section>



    <!-- =====================================================
         BENEFITS
    ====================================================== -->

    <section id="curso" class="py-24 bg-white">

      <div class="container-custom">

        <div class="text-center max-w-3xl mx-auto">

          <span
            class="text-green-600 uppercase tracking-[.25em] font-bold text-sm"
          >
            El curso
          </span>

          <h2
            class="mt-4 text-4xl md:text-5xl font-extrabold text-green-900"
          >
            Todo lo que necesitas
            para empezar
          </h2>

          <p class="mt-5 text-gray-600 text-lg">
            Una formación online sencilla, práctica y completamente
            accesible.
          </p>

        </div>


        <div class="mt-16 grid md:grid-cols-2 lg:grid-cols-5 gap-5">

          <div
            class="bg-[#f5f8ef] p-7 rounded-3xl hover:-translate-y-2 transition-all"
          >
            <div class="text-3xl mb-5">💻</div>
            <h3 class="font-bold text-green-900">
              Curso online
            </h3>
            <p class="text-gray-500 text-sm mt-2">
              Duración: 4 lecciones
            </p>
          </div>


          <div
            class="bg-[#f5f8ef] p-7 rounded-3xl hover:-translate-y-2 transition-all"
          >
            <div class="text-3xl mb-5">🎁</div>
            <h3 class="font-bold text-green-900">
              Gratis
            </h3>
            <p class="text-gray-500 text-sm mt-2">
              Disponible para todos
            </p>
          </div>


          <div
            class="bg-[#f5f8ef] p-7 rounded-3xl hover:-translate-y-2 transition-all"
          >
            <div class="text-3xl mb-5">🎬</div>
            <h3 class="font-bold text-green-900">
              Material educativo
            </h3>
            <p class="text-gray-500 text-sm mt-2">
              Videos y material digital
            </p>
          </div>


          <div
            class="bg-[#f5f8ef] p-7 rounded-3xl hover:-translate-y-2 transition-all"
          >
            <div class="text-3xl mb-5">🏆</div>
            <h3 class="font-bold text-green-900">
              Certificado
            </h3>
            <p class="text-gray-500 text-sm mt-2">
              Emitido al finalizar
            </p>
          </div>


          <div
            class="bg-green-600 text-white p-7 rounded-3xl hover:-translate-y-2 transition-all"
          >
            <div class="text-3xl mb-5">🌱</div>
            <h3 class="font-bold">
              Agricolus Observa
            </h3>
            <p class="text-green-100 text-sm mt-2">
              Gratis durante un año
            </p>
          </div>

        </div>

      </div>

    </section>



    <!-- =====================================================
         EDUCATIONAL ACADEMY
    ====================================================== -->

    <section class="py-24 bg-[#f5f8ef]">

      <div class="container-custom">

        <div
          class="grid lg:grid-cols-2 bg-white rounded-[45px] overflow-hidden soft-shadow"
        >

          <div class="order-2 lg:order-1 p-10 md:p-16 lg:p-20">

            <div
              class="inline-flex px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-bold"
            >
              Para estudiantes
            </div>

            <h2
              class="mt-6 text-4xl md:text-5xl font-extrabold text-green-900 leading-tight"
            >
              Agricolus
              <span class="text-green-600">
                Educational
              </span>
              Academy
            </h2>

            <p class="mt-6 text-gray-600 text-lg leading-8">
              Preparamos a los profesionales del mañana para hacer
              frente a un mundo en constante cambio y afrontar los
              retos de las nuevas tecnologías en el ámbito agronómico.
            </p>

            <p class="mt-5 text-gray-600 leading-7">
              Cursos dirigidos a estudiantes de Universidades Agrarias
              y Forestales, Institutos Agrarios e Institutos Técnicos
              Superiores.
            </p>

            <a
              href="<?= $base_url ?>contacto.php"
              class="btn inline-flex mt-8 bg-green-600 hover:bg-green-700 text-white px-7 py-4 rounded-full font-bold"
            >
              Quiero saber más
            </a>

          </div>


          <div class="order-1 lg:order-2 min-h-[480px]">

            <img
              src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1300&q=85"
              class="w-full h-full object-cover"
              alt="Educational Academy"
            />

          </div>

        </div>

      </div>

    </section>



    <!-- =====================================================
         GREEN STRIP
    ====================================================== -->

    <section class="leaf-bg py-28">

      <div class="container-custom text-center text-white">

        <div
          class="text-sm uppercase tracking-[.45em] font-bold opacity-80"
        >
          Making Agritech Sustainable
        </div>

        <h2
          class="mt-6 text-4xl md:text-6xl font-extrabold"
        >
          Making Agritech Sustainable
        </h2>

      </div>

    </section>



    <!-- =====================================================
         NEWSLETTER
    ====================================================== -->

    <section id="contacto" class="py-24 bg-green-900 text-white">

      <div class="container-custom">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

          <div>

            <span
              class="text-green-300 uppercase tracking-[.25em] text-sm font-bold"
            >
              Newsletter
            </span>

            <h2
              class="mt-5 text-4xl md:text-5xl font-extrabold"
            >
              ¿Quieres profundizar
              en el mundo de la
              agricultura de precisión?
            </h2>

            <p class="mt-6 text-green-100 text-lg">
              Mantente actualizado sobre innovación, tecnología
              agrícola y agricultura de precisión.
            </p>

          </div>


          <form
            @submit.prevent="alert('¡Gracias por suscribirte!')"
            class="bg-white rounded-[30px] p-7 md:p-9 text-gray-800"
          >

            <label class="block text-sm font-semibold mb-2">
              Tu nombre
            </label>

            <input
              type="text"
              required
              placeholder="Nombre"
              class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-green-500"
            />


            <label class="block text-sm font-semibold mt-5 mb-2">
              Tu correo electrónico
            </label>

            <input
              type="email"
              required
              placeholder="correo@email.com"
              class="w-full px-5 py-4 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-green-500"
            />


            <label class="flex gap-3 mt-5 text-sm text-gray-500">

              <input
                type="checkbox"
                required
                class="mt-1 accent-green-600"
              />

              <span>
                He leído y acepto la Política de Privacidad.
              </span>

            </label>


            <button
              type="submit"
              class="w-full mt-6 bg-green-600 hover:bg-green-700 text-white py-4 rounded-full font-bold"
            >
              SUSCRIBIRME
            </button>

          </form>

        </div>

      </div>

    </section>

  </main>

<?php
include __DIR__ . '/includes/footer.php';
?>

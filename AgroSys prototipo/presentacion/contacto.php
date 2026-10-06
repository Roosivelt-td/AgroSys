<?php
$page_title = "Contactos | Agricolus";
$base_url = "./";
$current_page = "contacto";
$body_attrs = 'x-data="{ mobileMenu: false, selected: null, submitted: false }"';

$page_styles = <<<'CSS'
    .container-agri {
      width: min(1180px, calc(100% - 40px));
      margin: 0 auto;
    }

    .hero-bg {
      background:
        radial-gradient(
          circle at 85% 15%,
          rgba(120,169,74,.14),
          transparent 30%
        ),
        linear-gradient(
          180deg,
          #f4f8ef 0%,
          #ffffff 100%
        );
    }

    .contact-card {
      transition:
        transform .3s ease,
        box-shadow .3s ease;
    }

    .contact-card:hover {
      transform: translateY(-7px);
      box-shadow:
        0 25px 60px
        rgba(38,61,32,.12);
    }

    .green-banner {
      background:
        linear-gradient(
          rgba(38,61,32,.72),
          rgba(38,61,32,.72)
        ),
        url(
          'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1800&q=85'
        );
      background-size: cover;
      background-position: center;
    }

    .contact-image {
      background:
        linear-gradient(
          rgba(38,61,32,.05),
          rgba(38,61,32,.05)
        ),
        url(
          'https://images.unsplash.com/photo-1523742810-5a0a9b3b5a9e?auto=format&fit=crop&w=1500&q=85'
        );
      background-size: cover;
      background-position: center;
    }

    .soft-shadow {
      box-shadow:
        0 20px 60px
        rgba(38,61,32,.09);
    }
CSS;

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>

<!-- ======================================================
     MAIN
====================================================== -->

<main>

<!-- ======================================================
     HERO
====================================================== -->

<section
  class="
    hero-bg
    pt-[82px]
  "
>

  <div class="container-agri">

    <div
      class="
        min-h-[500px]
        flex
        items-center
        justify-center
        text-center
        py-24
      "
    >

      <div
        class="
          max-w-4xl
          mx-auto
        "
      >

        <div
          class="
            inline-flex
            items-center
            gap-2
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

          Estamos aquí para ayudarte

        </div>


        <h1
          class="
            mt-7
            text-5xl
            md:text-6xl
            lg:text-[70px]
            leading-[.95]
            font-black
            tracking-tight
            text-agri-900
          "
        >

          ¿Qué
          <span class="text-agri-600">
            necesitas?
          </span>

        </h1>


        <p
          class="
            mt-8
            max-w-2xl
            mx-auto
            text-lg
            md:text-xl
            leading-8
            text-gray-600
          "
        >

          Selecciona el área sobre la que quieres
          información y nuestro equipo se pondrá
          en contacto contigo.

        </p>

      </div>

    </div>

  </div>

</section>



<!-- ======================================================
     CONTACT CARDS
====================================================== -->

<section
  id="contacto"
  class="
    py-20
    bg-white
  "
>

  <div class="container-agri">


    <div
      class="
        grid
        md:grid-cols-2
        lg:grid-cols-3
        gap-6
      "
    >


      <!-- =================================================
           PRODUCT
      ================================================== -->

      <article
        class="
          contact-card
          bg-white
          rounded-[30px]
          overflow-hidden
          border
          border-gray-100
          soft-shadow
        "
      >

        <div class="h-52 overflow-hidden">

          <img
            src="
              https://images.unsplash.com/photo-1586771107445-d3ca888129ce
              ?auto=format&fit=crop&w=900&q=85
            "
            alt="Producto Agricolus"
            class="
              w-full
              h-full
              object-cover
              hover:scale-105
              transition
              duration-700
            "
          >

        </div>


        <div class="p-7">

          <div
            class="
              w-12
              h-12
              rounded-xl
              bg-agri-100
              flex
              items-center
              justify-center
              text-2xl
            "
          >
            📱
          </div>


          <h2
            class="
              mt-5
              text-2xl
              font-black
              text-agri-900
            "
          >
            Tengo una pregunta
            acerca del producto
          </h2>


          <p
            class="
              mt-3
              text-gray-500
              text-sm
              leading-6
            "
          >
            ¿Quieres conocer mejor las soluciones
            de Agricolus?
          </p>


          <button
            @click="selected = 'producto'"
            class="
              mt-6
              w-full
              bg-agri-600
              hover:bg-agri-700
              text-white
              py-3.5
              rounded-full
              font-bold
              transition
            "
          >
            ESCRÍBENOS
          </button>

        </div>

      </article>



      <!-- =================================================
           ACADEMY
      ================================================== -->

      <article
        class="
          contact-card
          bg-white
          rounded-[30px]
          overflow-hidden
          border
          border-gray-100
          soft-shadow
        "
      >

        <div class="h-52 overflow-hidden">

          <img
            src="
              https://images.unsplash.com/photo-1523240795612-9a054b0db644
              ?auto=format&fit=crop&w=900&q=85
            "
            alt="Academy"
            class="
              w-full
              h-full
              object-cover
              hover:scale-105
              transition
              duration-700
            "
          >

        </div>


        <div class="p-7">

          <div
            class="
              w-12
              h-12
              rounded-xl
              bg-blue-50
              flex
              items-center
              justify-center
              text-2xl
            "
          >
            🎓
          </div>


          <h2
            class="
              mt-5
              text-2xl
              font-black
              text-agri-900
            "
          >
            Quiero información
            sobre Agricolus Academy
          </h2>


          <p
            class="
              mt-3
              text-gray-500
              text-sm
              leading-6
            "
          >
            Descubre nuestros cursos y programas
            de formación.
          </p>


          <button
            @click="selected = 'academy'"
            class="
              mt-6
              w-full
              bg-agri-600
              hover:bg-agri-700
              text-white
              py-3.5
              rounded-full
              font-bold
            "
          >
            ESCRÍBENOS
          </button>

        </div>

      </article>



      <!-- =================================================
           PARTNER
      ================================================== -->

      <article
        class="
          contact-card
          bg-white
          rounded-[30px]
          overflow-hidden
          border
          border-gray-100
          soft-shadow
        "
      >

        <div class="h-52 overflow-hidden">

          <img
            src="
              https://images.unsplash.com/photo-1556761175-b413da4baf72
              ?auto=format&fit=crop&w=900&q=85
            "
            alt="Partner"
            class="
              w-full
              h-full
              object-cover
              hover:scale-105
              transition
              duration-700
            "
          >

        </div>


        <div class="p-7">

          <div
            class="
              w-12
              h-12
              rounded-xl
              bg-purple-50
              flex
              items-center
              justify-center
              text-2xl
            "
          >
            🤝
          </div>


          <h2
            class="
              mt-5
              text-2xl
              font-black
              text-agri-900
            "
          >
            Me gustaría ser
            Socio de Agricolus
          </h2>


          <p
            class="
              mt-3
              text-gray-500
              text-sm
              leading-6
            "
          >
            Conoce nuestro programa de Partners
            y colabora con nosotros.
          </p>


          <button
            @click="selected = 'partner'"
            class="
              mt-6
              w-full
              bg-agri-600
              hover:bg-agri-700
              text-white
              py-3.5
              rounded-full
              font-bold
            "
          >
            ESCRÍBENOS
          </button>

        </div>

      </article>



      <!-- =================================================
           DISTRIBUTOR
      ================================================== -->

      <article
        class="
          contact-card
          bg-white
          rounded-[30px]
          overflow-hidden
          border
          border-gray-100
          soft-shadow
        "
      >

        <div class="h-52 overflow-hidden">

          <img
            src="
              https://images.unsplash.com/photo-1586528116493-da8b0f7c6d9e
              ?auto=format&fit=crop&w=900&q=85
            "
            alt="Distribuidor"
            class="
              w-full
              h-full
              object-cover
              hover:scale-105
              transition
              duration-700
            "
          >

        </div>


        <div class="p-7">

          <div
            class="
              w-12
              h-12
              rounded-xl
              bg-orange-50
              flex
              items-center
              justify-center
              text-2xl
            "
          >
            🌍
          </div>


          <h2
            class="
              mt-5
              text-2xl
              font-black
              text-agri-900
            "
          >
            Me gustaría ser
            distribuidor
          </h2>


          <p
            class="
              mt-3
              text-gray-500
              text-sm
              leading-6
            "
          >
            Descubre las oportunidades del
            programa de distribución.
          </p>


          <button
            @click="selected = 'distribuidor'"
            class="
              mt-6
              w-full
              bg-agri-600
              hover:bg-agri-700
              text-white
              py-3.5
              rounded-full
              font-bold
            "
          >
            ESCRÍBENOS
          </button>

        </div>

      </article>



      <!-- =================================================
           SUPPORT
      ================================================== -->

      <article
        class="
          contact-card
          bg-white
          rounded-[30px]
          overflow-hidden
          border
          border-gray-100
          soft-shadow
        "
      >

        <div class="h-52 overflow-hidden">

          <img
            src="
              https://images.unsplash.com/photo-1556742049-0cfed4f6a45d
              ?auto=format&fit=crop&w=900&q=85
            "
            alt="Soporte Agricolus"
            class="
              w-full
              h-full
              object-cover
              hover:scale-105
              transition
              duration-700
            "
          >

        </div>


        <div class="p-7">

          <div
            class="
              w-12
              h-12
              rounded-xl
              bg-green-50
              flex
              items-center
              justify-center
              text-2xl
            "
          >
            🛠️
          </div>


          <h2
            class="
              mt-5
              text-2xl
              font-black
              text-agri-900
            "
          >
            Estoy usando Agricolus
            y necesito soporte
          </h2>


          <p
            class="
              mt-3
              text-gray-500
              text-sm
              leading-6
            "
          >
            Nuestro equipo está disponible para
            ayudarte con la plataforma.
          </p>


          <button
            @click="selected = 'soporte'"
            class="
              mt-6
              w-full
              bg-agri-600
              hover:bg-agri-700
              text-white
              py-3.5
              rounded-full
              font-bold
            "
          >
            ESCRÍBENOS
          </button>

        </div>

      </article>



      <!-- =================================================
           PRESS
      ================================================== -->

      <article
        class="
          contact-card
          bg-white
          rounded-[30px]
          overflow-hidden
          border
          border-gray-100
          soft-shadow
        "
      >

        <div class="h-52 overflow-hidden">

          <img
            src="
              https://images.unsplash.com/photo-1504711434969-e33886168f5c
              ?auto=format&fit=crop&w=900&q=85
            "
            alt="Oficina de prensa"
            class="
              w-full
              h-full
              object-cover
              hover:scale-105
              transition
              duration-700
            "
          >

        </div>


        <div class="p-7">

          <div
            class="
              w-12
              h-12
              rounded-xl
              bg-yellow-50
              flex
              items-center
              justify-center
              text-2xl
            "
          >
            📰
          </div>


          <h2
            class="
              mt-5
              text-2xl
              font-black
              text-agri-900
            "
          >
            Oficina de prensa
          </h2>


          <p
            class="
              mt-3
              text-gray-500
              text-sm
              leading-6
            "
          >
            Para entrevistas, notas de prensa y
            solicitudes de comunicación.
          </p>


          <button
            @click="selected = 'prensa'"
            class="
              mt-6
              w-full
              bg-agri-600
              hover:bg-agri-700
              text-white
              py-3.5
              rounded-full
              font-bold
            "
          >
            ESCRÍBENOS
          </button>

        </div>

      </article>

    </div>

  </div>

</section>



<!-- ======================================================
     MODAL CONTACT FORM
====================================================== -->

<div
  x-show="selected"
  x-transition.opacity
  class="
    fixed
    inset-0
    z-[100]
    bg-black/50
    backdrop-blur-sm
    flex
    items-center
    justify-center
    p-5
  "
  @keydown.escape.window="selected = null"
>

  <div
    @click.outside="selected = null"
    x-transition
    class="
      bg-white
      rounded-[30px]
      max-w-xl
      w-full
      p-8
      md:p-10
      shadow-2xl
    "
  >

    <div
      class="
        flex
        items-start
        justify-between
        gap-5
      "
    >

      <div>

        <div
          class="
            text-sm
            uppercase
            tracking-[.2em]
            text-agri-600
            font-bold
          "
        >
          Contacto
        </div>


        <h2
          class="
            mt-2
            text-3xl
            font-black
            text-agri-900
          "
        >

          <span
            x-show="selected === 'producto'"
          >
            Información del producto
          </span>

          <span
            x-show="selected === 'academy'"
          >
            Agricolus Academy
          </span>

          <span
            x-show="selected === 'partner'"
          >
            Programa Partner
          </span>

          <span
            x-show="selected === 'distribuidor'"
          >
            Programa Distribuidor
          </span>

          <span
            x-show="selected === 'soporte'"
          >
            Soporte
          </span>

          <span
            x-show="selected === 'prensa'"
          >
            Oficina de prensa
          </span>

        </h2>

      </div>


      <button
        @click="selected = null"
        class="
          w-10
          h-10
          rounded-full
          bg-gray-100
          hover:bg-gray-200
          text-gray-500
        "
      >
        ✕
      </button>

    </div>



    <form
      class="mt-8"
      @submit.prevent="
        submitted = true;
        setTimeout(() => {
          selected = null;
          submitted = false;
        }, 1800)
      "
    >

      <div
        class="
          grid
          md:grid-cols-2
          gap-5
        "
      >

        <div>

          <label
            class="
              block
              text-sm
              font-bold
              mb-2
            "
          >
            Nombre
          </label>

          <input
            type="text"
            required
            placeholder="Tu nombre"
            class="
              w-full
              px-5
              py-4
              rounded-xl
              border
              border-gray-200
              outline-none
              focus:ring-2
              focus:ring-agri-500
            "
          >

        </div>


        <div>

          <label
            class="
              block
              text-sm
              font-bold
              mb-2
            "
          >
            Apellidos
          </label>

          <input
            type="text"
            required
            placeholder="Tus apellidos"
            class="
              w-full
              px-5
              py-4
              rounded-xl
              border
              border-gray-200
              outline-none
              focus:ring-2
              focus:ring-agri-500
            "
          >

        </div>

      </div>


      <label
        class="
          block
          text-sm
          font-bold
          mt-5
          mb-2
        "
      >
        Email
      </label>

      <input
        type="email"
        required
        placeholder="tu@email.com"
        class="
          w-full
          px-5
          py-4
          rounded-xl
          border
          border-gray-200
          outline-none
          focus:ring-2
          focus:ring-agri-500
        "
      >


      <label
        class="
          block
          text-sm
          font-bold
          mt-5
          mb-2
        "
      >
        Mensaje
      </label>

      <textarea
        rows="4"
        required
        placeholder="¿En qué podemos ayudarte?"
        class="
          w-full
          px-5
          py-4
          rounded-xl
          border
          border-gray-200
          outline-none
          focus:ring-2
          focus:ring-agri-500
          resize-none
        "
      ></textarea>


      <label
        class="
          flex
          gap-3
          mt-5
          text-sm
          text-gray-500
        "
      >

        <input
          type="checkbox"
          required
          class="mt-1 accent-green-600"
        >

        <span>
          He leído y acepto la Política de Privacidad.
        </span>

      </label>


      <button
        type="submit"
        class="
          mt-6
          w-full
          bg-agri-600
          hover:bg-agri-700
          text-white
          py-4
          rounded-full
          font-black
        "
      >

        <span x-show="!submitted">
          ENVIAR MENSAJE
        </span>

        <span x-show="submitted">
          ✓ MENSAJE ENVIADO
        </span>

      </button>

    </form>

  </div>

</div>



<!-- ======================================================
     GREEN BANNER
====================================================== -->

<section
  class="
    green-banner
    py-28
  "
>

  <div
    class="
      container-agri
      text-center
      text-white
    "
  >

    <div
      class="
        uppercase
        tracking-[.4em]
        text-sm
        font-bold
        text-green-200
      "
    >
      Agricolus
    </div>


    <h2
      class="
        mt-6
        text-4xl
        md:text-6xl
        font-black
      "
    >
      MAKING AGRITECH
      SUSTAINABLE
    </h2>

  </div>

</section>



<!-- ======================================================
     INFO / R&D
====================================================== -->

<section
  class="
    py-24
    bg-white
  "
>

  <div class="container-agri">

    <div
      class="
        grid
        lg:grid-cols-2
        gap-14
        items-center
      "
    >

      <div>

        <span
          class="
            text-agri-600
            uppercase
            tracking-[.25em]
            text-sm
            font-bold
          "
        >
          Agricolus
        </span>


        <h2
          class="
            mt-5
            text-4xl
            md:text-5xl
            font-black
            text-agri-900
          "
        >
          Investigación
          y desarrollo
        </h2>


        <p
          class="
            mt-6
            text-lg
            text-gray-600
            leading-8
          "
        >

          Trabajamos en tecnologías capaces de mejorar
          la productividad, sostenibilidad y eficiencia
          del sector agrícola.

        </p>


        <a
          href="<?= $base_url ?>quieneSomos/investigacionIndustrial.php"
          class="
            inline-flex
            mt-8
            border
            border-agri-600
            text-agri-700
            px-7
            py-4
            rounded-full
            font-bold
            hover:bg-agri-50
          "
        >
          DESCUBRE MÁS
        </a>

      </div>


      <div
        class="
          contact-image
          h-[430px]
          rounded-[40px]
          overflow-hidden
          shadow-xl
        "
      ></div>

    </div>

  </div>

</section>



<!-- ======================================================
     NEWSLETTER
====================================================== -->

<section
  class="
    py-24
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

        <span
          class="
            text-agri-300
            uppercase
            tracking-[.25em]
            text-sm
            font-bold
          "
        >
          Newsletter
        </span>


        <h2
          class="
            mt-5
            text-4xl
            md:text-5xl
            font-black
          "
        >
          ¿Quieres profundizar
          en el mundo de la
          agricultura de precisión?
        </h2>


        <p
          class="
            mt-6
            text-green-100
            text-lg
            leading-8
          "
        >
          Mantente actualizado sobre tecnología,
          innovación y agricultura de precisión.
        </p>

      </div>



      <form
        @submit.prevent="
          submitted = true;
          setTimeout(() => submitted = false, 2500)
        "
        class="
          bg-white
          rounded-[30px]
          p-8
          text-gray-800
        "
      >

        <label
          class="
            block
            text-sm
            font-bold
            mb-2
          "
        >
          Tu nombre
        </label>

        <input
          type="text"
          required
          placeholder="Nombre"
          class="
            w-full
            px-5
            py-4
            rounded-xl
            border
            border-gray-200
            outline-none
            focus:ring-2
            focus:ring-agri-500
          "
        >


        <label
          class="
            block
            text-sm
            font-bold
            mt-5
            mb-2
          "
        >
          Tu correo electrónico
        </label>

        <input
          type="email"
          required
          placeholder="correo@email.com"
          class="
            w-full
            px-5
            py-4
            rounded-xl
            border
            border-gray-200
            outline-none
            focus:ring-2
            focus:ring-agri-500
          "
        >


        <label
          class="
            flex
            gap-3
            mt-5
            text-sm
            text-gray-500
          "
        >

          <input
            type="checkbox"
            required
            class="mt-1 accent-green-600"
          >

          <span>
            He leído y acepto la Política de Privacidad.
          </span>

        </label>


        <button
          type="submit"
          class="
            w-full
            mt-6
            bg-agri-600
            hover:bg-agri-700
            text-white
            py-4
            rounded-full
            font-black
          "
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

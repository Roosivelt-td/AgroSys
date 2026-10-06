<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AgroSys — Agricultura Digital</title>

    <!-- Favicon / Logo Icon -->
    <link rel="icon" type="image/png" href="{{ asset('AgroSys_logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('AgroSys_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('AgroSys_logo.png') }}">

    <!-- =====================================================
         TAILWIND
    ====================================================== -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        agricolus: '#78B82A',
                        agricolusDark: '#609B20',
                        darkgreen: '#173B27',
                        dark: '#10291C',
                        cream: '#F7F8F2',
                        softgreen: '#EEF6E5',
                        textgray: '#657067'
                    },
                    boxShadow: {
                        soft: '0 20px 60px rgba(23,59,39,.10)',
                        card: '0 25px 70px rgba(23,59,39,.12)'
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .grid-bg {
            background-image:
                linear-gradient(rgba(23, 59, 39, .045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(23, 59, 39, .045) 1px, transparent 1px);
            background-size: 48px 48px;
        }
        .hero-image {
            border-radius: 180px 35px 35px 35px;
        }
        .reveal {
            opacity: 0;
            transform: translateY(35px);
            transition: opacity .8s ease, transform .8s ease;
        }
        .reveal.show {
            opacity: 1;
            transform: translateY(0);
        }
        .float {
            animation: float 5s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .solution-card {
            transition: transform .35s ease, box-shadow .35s ease;
        }
        .solution-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px rgba(23, 59, 39, .11);
        }
        .solution-card img {
            transition: transform .7s ease;
        }
        .solution-card:hover img {
            transform: scale(1.06);
        }
        .marquee {
            overflow: hidden;
            white-space: nowrap;
        }
        .marquee-track {
            display: inline-flex;
            animation: marquee 32s linear infinite;
        }
        @keyframes marquee {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }
        .green-line {
            position: relative;
        }
        .green-line::before {
            content: "";
            position: absolute;
            width: 55px;
            height: 4px;
            background: #78B82A;
            border-radius: 20px;
            top: -15px;
            left: 0;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-white text-darkgreen antialiased overflow-x-hidden">

<!-- =====================================================
     HEADER INCLUDE
====================================================== -->
@include('presentacion.includes.header')

<!-- =====================================================
     MAIN CONTENT
====================================================== -->
<main class="pt-[82px]">
    {{ $slot }}
</main>

<!-- =====================================================
     FOOTER INCLUDE
====================================================== -->
@include('presentacion.includes.footer')

<script>
    const header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                header.classList.add('shadow-sm');
            } else {
                header.classList.remove('shadow-sm');
            }
        });
    }
</script>

<!-- Livewire Scripts -->
@livewireScripts

</body>
</html>

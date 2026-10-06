<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>AgroSys — Agricultura Digital</title>

        <!-- Favicon / Logo Icon -->
        <link rel="icon" type="image/png" href="{{ asset('AgroSys_logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('AgroSys_logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('AgroSys_logo.png') }}">

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Tailwind CDN for guest pages -->
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
                        }
                    }
                }
            }
        </script>

        <!-- Livewire Styles -->
        @livewireStyles

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }

            .grid-bg {
                background-image: linear-gradient(rgba(23,59,39,.045) 1px, transparent 1px), linear-gradient(90deg, rgba(23,59,39,.045) 1px, transparent 1px);
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
        </style>
    </head>
    <body class="antialiased text-[#173B27] bg-[#F7F8F2] overflow-x-hidden relative selection:bg-[#78B82A] selection:text-white">

        <!-- =====================================================
             HEADER INCLUDE
        ====================================================== -->
        @include('presentacion.includes.header')

        <!-- =====================================================
             MAIN CONTENT SLOT
        ====================================================== -->
        <main class="pt-[82px]">
            {{ $slot }}
        </main>

        <!-- =====================================================
             FOOTER INCLUDE
        ====================================================== -->
        @include('presentacion.includes.footer')

        <!-- Livewire Scripts -->
        @livewireScripts

    </body>
</html>

<?php
$base_url = $base_url ?? './';
$page_title = $page_title ?? 'Agricolus — Agricultura digital';
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($page_title) ?></title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        agricolus: {
                            DEFAULT: '#78B82A',
                            50:  '#f4f8f2',
                            100: '#e5f0e1',
                            200: '#cce1c5',
                            300: '#abd09f',
                            400: '#83bb74',
                            500: '#5da44d',
                            600: '#478d3c',
                            700: '#397331',
                            800: '#2d5c29',
                            900: '#244b21'
                        },
                        agricolusDark: '#609B20',
                        darkgreen: '#173B27',
                        dark: '#10291C',
                        cream: '#F7F8F2',
                        softgreen: '#EEF6E5',
                        palegreen: '#F7F9F5',
                        textgray: '#657067',
                        agri: {
                            50: '#f4f8ef',
                            100: '#e8f1dc',
                            200: '#d4e5bd',
                            300: '#b8d497',
                            400: '#94bc67',
                            500: '#78a94a',
                            600: '#619037',
                            700: '#4d762d',
                            800: '#385a27',
                            900: '#263d20'
                        },
                        green: {
                            50: '#f3f8ed',
                            100: '#e8f2dc',
                            200: '#c8e2bf',
                            300: '#a5cf98',
                            400: '#91b958',
                            500: '#7cab43',
                            600: '#638f32',
                            700: '#507629',
                            800: '#2c5529',
                            900: '#263c1d'
                        }
                    },
                    boxShadow: {
                        soft: '0 20px 60px rgba(23,59,39,.10)',
                        card: '0 25px 70px rgba(23,59,39,.12)',
                        phone: '0 30px 80px rgba(0,0,0,.20)'
                    },
                    borderRadius: {
                        '4xl': '2rem',
                        '5xl': '2.5rem'
                    },
                    fontFamily: {
                        sans: ['Inter', 'Arial', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
    </style>

    <?php if (isset($page_styles)): ?>
    <style>
        <?= $page_styles ?>
    </style>
    <?php endif; ?>

</head>

<body <?= isset($body_attrs) ? $body_attrs : '' ?> class="<?= isset($body_class) ? $body_class : '' ?>">

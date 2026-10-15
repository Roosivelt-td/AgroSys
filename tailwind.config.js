import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.jsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                agricolus: '#78B82A',
                agricolusDark: '#609B20',
                darkgreen: '#173B27',
                dark: '#10291C',
                cream: '#F7F8F2',
                softgreen: '#EEF6E5',
                textgray: '#657067',
                agri: {
                    green: '#00ba2e',      /* 1: Brand Green */
                    emerald: '#10b981',    /* Emerald Accent */

                    /* Modo Día (Light Mode ☀️) */
                    l_bg: '#f4f9f5',       /* Fondo Principal Día */
                    l_sidebar: '#ffffff',  /* Sidebar Día */
                    l_card: '#ffffff',     /* Tarjetas Día */
                    l_accent: '#eef8f1',   /* Acento Activo Día */

                    /* Modo Noche (Dark Mode 🌙 - Suave, Tono Slate Nocturno) */
                    d_bg: '#0f172a',       /* Fondo Principal Noche (Slate 900 Suave) */
                    d_sidebar: '#112220',  /* Sidebar Noche */
                    d_card: '#1e293b',     /* Tarjetas Noche (Slate 800) */
                    d_accent: '#1e3833',   /* Acento Activo Noche */
                }
            },
            boxShadow: {
                soft: '0 20px 60px rgba(23,59,39,.10)',
                card: '0 25px 70px rgba(23,59,39,.12)'
            }
        },
    },

    plugins: [forms],
};

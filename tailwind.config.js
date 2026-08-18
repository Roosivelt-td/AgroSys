import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                agri: {
                    green: '#00ba2e',      /* 1: Brand Green */

                    l_sidebar: '#f5fffb',  /* 2: Light Sidebar */
                    l_bg: '#fbf7f6',       /* 5: Light Background */
                    l_card: '#d2e3d5',     /* 4: Light Card/Active */
                    l_accent: '#ffffff',   /* 3: light Accent */

                    d_sidebar: '#003d3a',  /* 2: Dark Sidebar */
                    d_bg: '#e7e9eb',       /* 5: Dark Background */
                    d_card: '#003d3a',     /* 4: Dark Card/Active */
                    d_accent: '#d5d9de',   /* 3: Dark Accent */
                }
            }
        },
    },

    plugins: [forms],
};

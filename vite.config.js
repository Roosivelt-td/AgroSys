import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        react(),
    ],
    server: {
        host: '0.0.0.0', // Escuchar en todas las interfaces del contenedor
        //port: 5173, // con dev
        //strictPort: true, // con dev
        hmr: {
            host: 'localhost', // El navegador buscará a Vite en localhost
        },
        watch: {
            usePolling: true, // Necesario para detectar cambios de archivos en Docker/Windows/Linux
        },
    },
});

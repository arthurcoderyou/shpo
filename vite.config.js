import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
 
    // uncomment this when using the wifi
    // server: {
    //     host: '0.0.0.0',
    //     port: 5173,
    //     strictPort: true,

    //     // IMPORTANT: make mobile use your PC IP, not "localhost"
    //     hmr: {
    //         host: '192.168.1.4', // <-- replace with YOUR PC IP
    //         port: 5173,
    //     },
    // },
});

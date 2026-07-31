import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/error.css',
                'resources/js/app.js',
            ],
            refresh: [
                ...refreshPaths,
                'app/Http/Livewire/**',
            ],
        }),

    ],
    // server: {
    //     port: 3000,
    //     https: true,
    //     hmr: {
    //         host: "hopexito.com",
    //         port: 3001,
    //         protocol: "wss",
    //     },
    // },
});

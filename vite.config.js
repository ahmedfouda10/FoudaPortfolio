import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Downloaded at build time and served from /build: no third-party font requests.
            fonts: [
                bunny('Inter', {
                    weights: [400, 500, 600, 700],
                    subsets: ['latin'],
                    preload: [{ weight: 400 }, { weight: 600 }],
                }),
                bunny('JetBrains Mono', {
                    weights: [400, 500],
                    subsets: ['latin'],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

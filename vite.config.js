import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/customers/index.js',
                'resources/js/orders/index.js',
            ],
            refresh: true,
        }),
    ],
});

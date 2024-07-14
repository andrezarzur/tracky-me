import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            css: {
              additionalData: '@import "./resources/css/app.scss";',
            },
        },
    },
    server: {
      host: 'localhost',
      https: false, // Ensure HTTPS is disabled
      hmr: {
          host: 'localhost',
      },
  }
});

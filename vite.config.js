// vite.config.ts or vite.config.js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";
import i18n from 'laravel-vue-i18n/vite';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
        i18n(),
    ],
    resolve: {
        alias: {
            '@/Components': '/resources/js/components',
            '@/Pages': '/resources/js/pages',
            '@/Layouts': '/resources/js/layouts',
        },
    },
    server: {
        host: "0.0.0.0",
        cors: true,
        strictPort: true,
        hmr: {
            host: "192.168.0.13",
            clientPort: 5173,
        },
        watch: {
            usePolling: true,
            interval: 100,
            ignored: ["**/storage/framework/views/**"],
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes("node_modules")) {
                        return id
                            .toString()
                            .split("node_modules/")[1]
                            .split("/")[0]
                            .toString();
                    }
                },
            },
        },
    },
});

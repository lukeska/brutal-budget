import { sentryVitePlugin } from "@sentry/vite-plugin";
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { watch } from "vite-plugin-watch";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.ts",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        watch(
            {
                pattern: "app/{Data,Enums}/**/*.php",
                command: "php artisan typescript:transform",
            },
            {
                pattern: "routes/*.php",
                command: "php artisan trail:generate",
            },
        ),
        sentryVitePlugin({
            org: "brutal-budget",
            project: "javascript-vue",
        }),
    ],
});

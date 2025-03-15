import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createPinia } from "pinia";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import * as Sentry from "@sentry/vue";
import { autoAnimatePlugin } from "@formkit/auto-animate/vue";

const appName = import.meta.env.VITE_APP_NAME || "Brutal Budget";
const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue")),
    setup({ el, App, props, plugin }) {
        const vm = createApp({ render: () => h(App, props) });

        Sentry.init({
            app: vm,
            dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC,
            tracesSampleRate: 1.0,
            logErrors: true,
        });

        vm.use(pinia).use(autoAnimatePlugin).use(plugin).use(ZiggyVue).mount(el);

        return vm;
    },
    progress: {
        color: "#4B5563",
    },
});

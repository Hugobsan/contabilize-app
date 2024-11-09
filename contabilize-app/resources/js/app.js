import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { Ziggy } from "./ziggy";
import '@mdi/font/css/materialdesignicons.css';

import "vuetify/styles"; // Importa estilos do Vuetify
import { createVuetify } from "vuetify";
import { aliases, mdi } from "vuetify/lib/iconsets/mdi"; // Usa ícones do Material Design
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";

import toastr from "toastr";
import "toastr/build/toastr.min.css";

const vuetify = createVuetify({
    components,
    directives,
    icons: {
        iconfont: "mdi",
        defaultSet: "mdi",
        aliases,
        sets: {
            mdi,
        },
    },
    theme: {
        defaultTheme: "customTheme",
        themes: {
            customTheme: {
                dark: false,
                colors: {
                    primary: "#4CAF50",
                    secondary: "#212121",
                },
            },
        },
    },
});

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(vuetify) 
            .use(ZiggyVue, Ziggy)
        return vueApp.mount(el);
    },
    progress: {
        color: "#4CAF50",
    },
});

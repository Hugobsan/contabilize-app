import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { Ziggy } from "./ziggy";

import "vuetify/styles"; // Importa estilos do Vuetify
import { createVuetify } from "vuetify";
import { aliases, mdi } from "vuetify/lib/iconsets/mdi"; // Usa ícones do Material Design
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";

import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

const vuetify = createVuetify({
    components,
    directives,
    icons: {
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
            .use(vuetify) // Usa Vuetify
            .use(ZiggyVue, Ziggy)
            .mount(el);

        // Exibe mensagens de sucesso e erro com Toastify
        const flashMessages = props.initialPage.props.flash;
        if (flashMessages) {
            if (flashMessages.success) {
                Toastify({
                    text: flashMessages.success,
                    duration: 5000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#4CAF50", // Cor para mensagens de sucesso
                    stopOnFocus: true,
                }).showToast();
            }

            if (flashMessages.error) {
                Toastify({
                    text: flashMessages.error,
                    duration: 5000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#F44336", // Cor para mensagens de erro
                    stopOnFocus: true,
                }).showToast();
            }

            if (flashMessages.warning) {
                Toastify({
                    text: flashMessages.warning,
                    duration: 5000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#FFC107", // Cor para mensagens de alerta
                    stopOnFocus: true,
                }).showToast();

                if (flashMessages.info) {
                    Toastify({
                        text: flashMessages.info,
                        duration: 5000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#2196F3", // Cor para mensagens de informação
                        stopOnFocus: true,
                    }).showToast();
                }
            }
        }

        return vueApp;
    },
    progress: {
        color: "#4B5563",
    },
});

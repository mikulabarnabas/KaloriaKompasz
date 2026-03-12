import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { i18nVue } from "laravel-vue-i18n";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },

    title: (title) => `${title} - KalóriaKompasz`,

    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        vueApp.use(plugin);

        vueApp.use(i18nVue, {
            resolve: async (lang) => {
                const langs = import.meta.glob("../../lang/*.json");
                const path = `../../lang/${lang}.json`;

                if (langs[path]) {
                    return await langs[path]();
                }
            },
        });

        vueApp.mount(el);
    },
});

document.documentElement.classList.toggle(
  "dark",
  localStorage.theme === "dark" ||
    (!("theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches),
);

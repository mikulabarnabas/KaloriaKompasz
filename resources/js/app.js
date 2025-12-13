import { createApp, h } from 'vue';
import { createInertiaApp, Link } from '@inertiajs/vue3';

import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import 'primeicons/primeicons.css';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    title: title => `${title} - KalóriaKompasz`,
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        vueApp.use(plugin);

        vueApp.use(PrimeVue, {
            theme: { preset: Aura },
            ripple: true,
        });

        vueApp.mount(el);
    },
});
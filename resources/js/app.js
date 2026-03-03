import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createI18n } from 'vue-i18n'



createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },

    title: (title) => `${title} - KalóriaKompasz`,

    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })

        vueApp.use(plugin)

        const { locale, translations } = props.initialPage.props

        const i18n = createI18n({
            legacy: false,
            locale,
            fallbackLocale: 'en',
            messages: {
                [locale]: translations,
            },
        })

        vueApp.use(i18n)

        const saved = localStorage.getItem('theme') || 'light'
        document.documentElement.classList.toggle(
            'my-app-dark',
            saved === 'dark'
        )

        vueApp.mount(el)
    },
})
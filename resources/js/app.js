import { createApp, h } from 'vue';
import ConfirmationService from 'primevue/confirmationservice';
import { createInertiaApp } from '@inertiajs/vue3';

import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import { definePreset } from '@primeuix/themes';
import { createI18n } from 'vue-i18n'

import 'primeicons/primeicons.css';

/*
const MyPreset = definePreset(Aura, {
    semantic: {
        colorScheme: {
            light: {
                // LIGHT: primary = #3DD652
                primary: {
                    50: '#E9FDF1',
                    100: '#CFFAE0',
                    200: '#A8E6CF',
                    300: '#8EF166',
                    400: '#57E066',
                    500: '#3DD652', // <- LIGHT fő szín
                    600: '#2EBF46',
                    700: '#22A13A',
                    800: '#178330',
                    900: '#0F6627',
                    950: '#09421A',
                },

                surface: {
                    0: '#ffffff',
                    50: '#F6FFFA',
                    100: '#EAFBF2',
                    200: '#D7F5E5',
                    300: '#BFECD5',
                    400: '#95D8BE',
                    500: '#6BBFA4',
                    600: '#3E9D84',
                    700: '#1E7B69',
                    800: '#0B5A4E',
                    900: '#063D35',
                    950: '#032621',
                },
            },

            dark: {
                // DARK: primary = #00695C
                primary: {
                    50: '{indigo.50}',
                    100: '{indigo.100}',
                    200: '{indigo.200}',
                    300: '{indigo.300}',
                    400: '{indigo.400}',
                    500: '{indigo.500}',
                    600: '{indigo.600}',
                    700: '{indigo.700}',
                    800: '{indigo.800}',
                    900: '{indigo.900}',
                    950: '{indigo.950}'
                },

                surface: {
                    50: '#4D968D',
                    100: '#408F85',
                    200: '#33877D',
                    300: '#004F45',
                    400: '#004A40',
                    500: '#00443C',
                    600: '#003F37',
                    700: '#003A33',
                    800: '#00352e',
                    900: '#00352E',
                    950: '#004F45'
                },
                text: {
                    color: '#EAFBF2',
                    mutedColor: '#A8E6CF',
                    hoverColor: '#F3FFFA',
                }
            }
        },
    },
});
*/

/*
const MyPreset = definePreset(Aura, {
    semantic: {
        surface: {
            0: '#00695C',
            50: '#006457',
            100: '#005F53',
            200: '#00544A',
            300: '#004A40',
            400: '#003F37',
            500: '#00352E',
            600: '#002A25',
            700: '#00201C',
            800: '#001512',
            900: '#000B09',
            950: '#000505',
        }
    }
});
*/


const MyPreset = definePreset(Aura, {
    semantic: {
        colorScheme: {
            light: {
                surface: {
                    0: '#ffffff',
                    50: '{green.50}',
                    100: '{green.100}',
                    200: '{green.200}',
                    300: '{green.300}',
                    400: '{green.400}',
                    500: '{green.500}',
                    600: '{green.600}',
                    700: '{green.700}',
                    800: '{green.800}',
                    900: '{green.900}',
                    950: '{green.950}'
                }
            },
            dark: {
                surface: {
                    0: '#ffffff',
                    50: '{green50}',
                    100: '{green.100}',
                    200: '{green.200}',
                    300: '{green.300}',
                    400: '{green.400}',
                    500: '{green.500}',
                    600: '{green.600}',
                    700: '{green.700}',
                    800: '{green.800}',
                    900: '{green.900}',
                    950: '{green.950}'
                }
            }
        }
    }
});



createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },

    title: (title) => `${title} - KalóriaKompasz`,

    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })

        vueApp.use(plugin)
        vueApp.use(ConfirmationService)

        vueApp.use(PrimeVue, {
            theme: {
                preset: MyPreset,
                options: {
                    darkModeSelector: '.my-app-dark',
                },
            },
            ripple: true,
        })

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
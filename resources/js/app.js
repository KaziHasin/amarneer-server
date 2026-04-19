import './bootstrap';

import '../assets/scss/styles.scss';
import '@fontsource/roboto/400.css'
import '@fontsource/roboto/500.css'
import '@fontsource/roboto/700.css'
import '@fontsource/montserrat/400.css'
import '@fontsource/montserrat/600.css'
import '@fortawesome/fontawesome-free/css/all.min.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import BootstrapVue3 from 'bootstrap-vue-3'
import { ZiggyVue } from 'ziggy-js'

// Needed for Bootstrap JS behaviors (modal, dropdown, tooltip, etc.)
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import 'bootstrap/dist/js/bootstrap';
import 'bootstrap/dist/css/bootstrap.css'

const pages = import.meta.glob('./Pages/**/*.vue')

createInertiaApp({
    resolve: (name) => {
        const page = pages[`./Pages/${name}.vue`]

        if (!page) {
            throw new Error(`Inertia page not found: ${name}`)
        }

        return page().then((module) => module.default)
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(BootstrapVue3)
            .use(ZiggyVue)
            .mount(el)
    },
})


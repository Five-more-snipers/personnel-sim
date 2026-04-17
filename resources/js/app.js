import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

window.Ziggy = window.Ziggy || {};

const route = window.route || (() => {});

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .provide('route', route)
            .mount(el);
    },
});
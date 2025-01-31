import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import '../css/app.css';
import './bootstrap';

import Multiselect from 'vue-multiselect';
import VueTheMask from 'vue-the-mask';
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import VueSelect from 'vue-select';

DataTable.use(DataTablesLib);

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.use(ZiggyVue);
        app.use(VueTheMask);
        app.component('Multiselect', Multiselect);
        app.component('DataTable', DataTable);
        app.component('v-select', VueSelect);
        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

import './bootstrap';
import { InertiaApp } from '@inertiajs/inertia-vue';
import Vue from 'vue';
import moment from 'moment';

// Plugins.
Vue.use(InertiaApp);

// Properties / methods.
Vue.prototype.$moment = moment;

// Create Vue.
const app = document.getElementById('app');
const vue = new Vue({
    el: app,
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => import(`./Pages/${name}`).then(module => module.default),
        },
    }),
});

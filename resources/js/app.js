import './bootstrap';
import { InertiaApp } from '@inertiajs/inertia-vue';
import Vue from 'vue';
import PortalVue from 'portal-vue';
import moment from 'moment';
import formatSeconds from './Filters/formatSeconds.js';

// Plugins.
Vue.use(InertiaApp);
Vue.use(PortalVue);

// Properties / methods.
Vue.prototype.$moment = moment;

// Adding the formatSeconds filter
Vue.filter('formatSeconds', formatSeconds);

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

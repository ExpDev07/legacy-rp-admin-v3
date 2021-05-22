import './bootstrap';
import { InertiaApp } from '@inertiajs/inertia-vue';
import Vue from 'vue';
import PortalVue from 'portal-vue';
import moment from 'moment';
import humanizeSeconds from './Filters/humanizeSeconds.js';
import formatTime from './Filters/formatTime.js';

// Plugins.
Vue.use(InertiaApp);
Vue.use(PortalVue);

// Properties / methods.
Vue.prototype.$moment = moment;

// Adding the humanizeSeconds filter
Vue.filter('humanizeSeconds', humanizeSeconds);

// Adding the formatTime filter
Vue.filter('formatTime', formatTime);

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

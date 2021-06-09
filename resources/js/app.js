import './bootstrap';
import { InertiaApp } from '@inertiajs/inertia-vue';
import Vue from 'vue';
import PortalVue from 'portal-vue';
import moment from 'moment';
import Localization from './Plugins/localization.js';
import humanizeSeconds from './Filters/humanizeSeconds.js';
import formatTime from './Filters/formatTime.js';
import formatGender from './Filters/formatGender.js';

// Plugins.
Vue.use(InertiaApp);
Vue.use(PortalVue);
Vue.use(Localization);

// Properties / methods.
Vue.prototype.$moment = moment;

// Custom filters.
Vue.filter('humanizeSeconds', humanizeSeconds);
Vue.filter('formatTime', formatTime);
Vue.filter('formatGender', formatGender);

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

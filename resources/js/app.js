import './bootstrap';
import { InertiaApp } from '@inertiajs/inertia-vue';
import Vue from 'vue';
import PortalVue from 'portal-vue';
import moment from 'moment';
import Localization from './Plugins/localization.js';
import Copy from './Plugins/copy-text.js';
import Permissions from './Plugins/permissions.js';
import humanizeSeconds from './Filters/humanizeSeconds.js';
import formatTime from './Filters/formatTime.js';
import formatGender from './Filters/formatGender.js';
import linkify from 'vue-linkify';
import 'leaflet/dist/leaflet.css';
import "leaflet-gesture-handling/dist/leaflet-gesture-handling.css";
import "leaflet-fullscreen/dist/leaflet.fullscreen.css";
const momentDuration = require("moment-duration-format");
import Toast from "vue-toastification";
import 'vue-toastification/dist/index.css';
import 'vue-search-select/dist/VueSearchSelect.css';

// Directives.
Vue.directive('linkified', linkify);

// Get page
const app = document.getElementById('app'),
    page = JSON.parse(app.dataset.page);

// Plugins.
Vue.use(InertiaApp);
Vue.use(PortalVue);
Vue.use(Localization);
Vue.use(Copy);
Vue.use(Permissions, page);
Vue.use(Toast, {
    transition: "Vue-Toastification__slideBlurred",
    maxToasts: 10,
    newestOnTop: true
});

momentDuration(moment);

// Properties / methods.
Vue.prototype.$moment = moment;

// Custom filters.
Vue.filter('humanizeSeconds', humanizeSeconds);
Vue.filter('formatTime', formatTime);
Vue.filter('formatGender', formatGender);

// Create Vue.
const vue = new Vue({
    el: app,
    render: h => h(InertiaApp, {
        props: {
            initialPage: page,
            resolveComponent: name => import(`./Pages/${name}`).then(module => module.default),
        },
    }),
});

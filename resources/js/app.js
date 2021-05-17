import { InertiaApp } from '@inertiajs/inertia-vue';
import Vue from 'vue';
import moment from 'moment';

// Use inertia plugin.
Vue.use(InertiaApp);

// Global properties / methods.
Vue.prototype.$moment = moment;

// Formatting seconds to human readable time
Vue.filter("fmtSeconds", function(value) {
    return [['d', 86400], ['h', 3600], ['m', 60], ['s', 1]].reduce((r, s) => {
        let t = Math.floor(r[1]/s[1]);
        return [r[0] + (t + s[0]), r[1] - t * s[1]];
    }, ['', parseInt(value)])[0].replace(/^(0.\s)+/gm, '');
});

// App element.
const app = document.getElementById('app');

// Mount app to Vue instance.
new Vue({
    el: app,
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => import(`./Pages/${name}`).then(module => module.default),
        },
    }),
});

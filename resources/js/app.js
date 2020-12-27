import { InertiaApp } from '@inertiajs/inertia-vue'
import Vue from 'vue'

// Use inertia plugin.
Vue.use(InertiaApp);

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

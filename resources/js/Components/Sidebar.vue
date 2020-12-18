<template>
    <div class="bg-indigo-900 text-white font-semibold w-56 overflow-y-auto px-3 py-10">
        <!-- General stuff -->
        <nav>
            <ul>
                <li v-for="link in links" v-bind:key="link.label">
                    <inertia-link class="flex items-center rounded hover:bg-gray-900 hover:text-white px-5 py-2 mb-3" v-bind:class="isUrl(link.url) ? [ 'bg-gray-900', 'text-white' ] : ''" v-bind:href="link.url">
                        <icon class="w-4 h-4 mr-3 fill-current" v-bind:name="link.icon"></icon>
                        {{ link.label }}
                    </inertia-link>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
import Icon from './Icon';

export default {
    components: {
        Icon,
    },
    data() {
        return {
            url: this.$page.url,
            links: [{
                    label: 'Dashboard',
                    icon: 'dashboard',
                    url: '/',
                },
                {
                    label: 'Players',
                    icon: 'user',
                    url: '/players',
                },
                {
                    label: 'Server Logs',
                    icon: 'printer',
                    url: '/logs',
                },
            ],
        };
    },
    watch: {
        '$page.url': function (url) {
            this.url = url;
        }
    },
    methods: {
        isUrl (url) {
            if (this.url === url) return true;
            if (this.url.substring(1) === '' || url.substring(1) === '') return false;
            return this.url.startsWith(url);
        }
    }
};
</script>

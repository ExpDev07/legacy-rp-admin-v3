<template>
    <div class="flex flex-col bg-indigo-900 text-white font-semibold w-56 overflow-y-auto px-3 py-10">
        <!-- General stuff -->
        <nav>
            <ul>
                <li v-for="link in links" :key="link.label">
                    <inertia-link
                        class="flex items-center rounded hover:bg-gray-900 hover:text-white px-5 py-2 mb-3"
                        :class="isUrl(link.url) ? [ 'bg-gray-900', 'text-white' ] : ''"
                        :href="link.url"
                    >
                        <icon class="w-4 h-4 mr-3 fill-current" :name="link.icon"></icon>
                        {{ link.label }}
                    </inertia-link>
                </li>
            </ul>
        </nav>

        <!-- Suggest a feature -->
        <a 
            class="mt-auto rounded text-center bg-yellow-400 text-black px-5 py-3" 
            target="_blank" 
            href="https://github.com/ExpDev07/legacy-rp-admin-v3/issues/new/choose"
        >
            <i class="fas fa-bug mr-2"></i> Report a bug!
        </a>
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
            links: [
                {
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
                {
                    label: 'Servers',
                    icon: 'office',
                    url: '/servers',
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

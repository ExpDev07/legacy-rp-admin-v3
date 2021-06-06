<template>
    <div class="flex flex-col w-56 px-3 py-10 overflow-y-auto font-semibold text-white bg-indigo-900">
        <!-- General stuff -->
        <nav>
            <ul>
                <li v-for="link in links" :key="link.label">
                    <inertia-link
                        class="flex items-center px-5 py-2 mb-3 rounded hover:bg-gray-900 hover:text-white"
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
            class="px-5 py-3 mt-auto text-center text-black bg-yellow-400 rounded"
            target="_blank"
            href="https://github.com/ExpDev07/legacy-rp-admin-v3/issues/new/choose"
        >
            <i class="mr-2 fas fa-bug"></i> {{ t("nav.report") }}
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
                    label: this.t('home.title'),
                    icon: 'dashboard',
                    url: '/',
                },
                {
                    label: this.t('players.title'),
                    icon: 'user',
                    url: '/players',
                },
                {
                    label: this.t('characters.title'),
                    icon: 'book',
                    url: '/characters',
                },
                {
                    label: this.t('logs.title'),
                    icon: 'printer',
                    url: '/logs',
                },
                {
                    label: this.t('servers.title'),
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

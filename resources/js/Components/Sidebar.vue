<template>
    <div class="flex flex-col w-56 px-3 py-10 overflow-y-auto font-semibold text-white bg-indigo-900 mobile:w-full mobile:py-4">
        <!-- General stuff -->
        <nav>
            <ul v-if="!isMobile()">
                <li v-for="link in links" :key="link.label" v-if="!('private' in link && link.private) || $page.auth.player.isSuperAdmin">
                    <inertia-link
                        class="flex items-center px-5 py-2 mb-3 rounded hover:bg-gray-900 hover:text-white"
                        :class="isUrl(link.url) ? [ 'bg-gray-900', 'text-white' ] : ''"
                        :href="link.url"
                        v-if="!('sub' in link)"
                    >
                        <icon class="w-4 h-4 mr-3 fill-current" :name="link.icon"></icon>
                        {{ link.label }}
                    </inertia-link>
                    <a
                        href="#"
                        class="flex flex-wrap items-center px-5 py-2 mb-3 -mt-1 rounded hover:bg-indigo-700 hover:text-white overflow-hidden"
                        :class="len(link.sub.length)"
                        @click="$event.preventDefault()"
                        v-else-if="$page.auth.player.isSuperAdmin"
                    >
                        <span class="block w-full mb-2">
                            <icon class="w-4 h-4 mr-3 fill-current" :name="link.icon"></icon>
                            {{ link.label }}
                        </span>
                        <ul class="w-full">
                            <li v-for="sub in link.sub" :key="sub.label">
                                <inertia-link
                                    class="flex items-center px-5 py-2 mt-1 rounded hover:bg-gray-900 hover:text-white"
                                    :class="isUrl(sub.url) ? [ 'bg-gray-900', 'text-white' ] : ''"
                                    :href="sub.url"
                                >
                                    <icon class="w-4 h-4 mr-3 fill-current" :name="sub.icon"></icon>
                                    {{ sub.label }}
                                </inertia-link>
                            </li>
                        </ul>
                    </a>
                    <inertia-link
                        class="flex items-center px-5 py-2 mb-3 rounded hover:bg-gray-900 hover:text-white"
                        :class="isUrl(sub.url) ? [ 'bg-gray-900', 'text-white' ] : ''"
                        :href="sub.url"
                        v-if="'sub' in link && !$page.auth.player.isSuperAdmin"
                        v-for="sub in link.sub"
                        :key="sub.label"
                    >
                        <icon class="w-4 h-4 mr-3 fill-current" :name="sub.icon"></icon>
                        {{ sub.label }}
                    </inertia-link>
                </li>
            </ul>
            <ul v-else class="mobile:flex mobile:flex-wrap mobile:justify-between">
                <template v-for="link in links">
                    <inertia-link
                        class="flex items-center px-5 py-2 mb-3 rounded hover:bg-gray-900 hover:text-white text-sm"
                        :class="isUrl(link.url) ? [ 'bg-gray-900', 'text-white' ] : ''"
                        :href="link.url"
                        v-if="!('sub' in link) && (!('private' in link && link.private) || $page.auth.player.isSuperAdmin)"
                    >
                        {{ link.label }}
                    </inertia-link>
                    <inertia-link
                        v-for="sub in link.sub"
                        class="flex items-center px-5 py-2 mb-3 rounded hover:bg-gray-900 hover:text-white text-sm"
                        :class="isUrl(sub.url) ? [ 'bg-gray-900', 'text-white' ] : ''"
                        :href="sub.url"
                        :key="sub.label"
                        v-if="'sub' in link && (!('private' in link && link.private) || $page.auth.player.isSuperAdmin)"
                    >
                        {{ sub.label }}
                    </inertia-link>
                </template>
            </ul>
        </nav>

        <!-- Suggest a feature -->
        <a
            class="px-5 py-3 mt-auto text-center text-black bg-yellow-400 rounded"
            target="_blank"
            href="https://github.com/ExpDev07/legacy-rp-admin-v3/issues/new/choose"
            v-if="!isMobile()"
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
                    label: this.t('sidebar.management'),
                    icon: 'users',
                    sub: [
                        {
                            label: this.t('players.title'),
                            icon: 'user',
                            url: '/players',
                        },
                        {
                            label: this.t('characters.title'),
                            icon: 'book',
                            url: '/characters',
                        }
                    ]
                },
                {
                    label: this.t('sidebar.logs'),
                    icon: 'boxes',
                    sub: [
                        {
                            label: this.t('logs.title'),
                            icon: 'printer',
                            url: '/logs',
                        },
                        {
                            label: this.t('panel_logs.title'),
                            icon: 'paperstack',
                            url: '/panel_logs',
                        }
                    ]
                },
                {
                    label: this.t('twitter.title'),
                    icon: 'twitter',
                    url: '/twitter',
                },
                {
                    label: this.t('servers.title'),
                    icon: 'office',
                    url: '/servers',
                },
                {
                    label: this.t('sidebar.advanced'),
                    icon: 'cogs',
                    private: true,
                    sub: [
                        {
                            label: this.t('sidebar.advanced_search'),
                            icon: 'search',
                            url: '/advanced',
                        },
                        {
                            label: this.t('sidebar.suspicious'),
                            icon: 'heart',
                            url: '/suspicious',
                        }
                    ]
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
        },
        len(length) {
            switch(length) {
                case 2:
                    return 'h-side-close hover:h-side-open-two';
                default:
                    return '';
            }
        },
        isMobile() {
            return $(window).width() <= 640;
        }
    }
};
</script>
